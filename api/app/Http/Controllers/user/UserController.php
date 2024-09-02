<?php

namespace App\Http\Controllers\user;

use App\Http\Dto\UsersDto;
use App\Models\Roles;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (!auth()->user()->tokenCan('user-index')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $users = User::with('role')
                ->where('active', 1)
                ->get();

            return $this->response('User list.', 200, UsersDto::contentList($users->toArray()));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!auth()->user()->tokenCan('user-store')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50',
                'email' => [
                    'email','required','max:100',
                    function ($attribute, $value, $fail) {
                        $user = User::where('email', $value)->whereNull('deleted_at')->first();
                        if (!is_null($user)) {
                            $fail("The {$attribute} is already being used.");
                        }
                    }
                ],
                'password' => 'required',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid.', 422, $validator->errors()->toArray());
            }

            $created = User::firstOrCreate($validator->validated());

            return $this->response('User created.', 200);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            if (!(auth()->user()->tokenCan('user-status')) && !(auth()->user()->tokenCan('user-update'))) {
                return $this->error('Not Authorized', 403, []);
            }

            $requestAll = $request;

            if (auth()->user()->tokenCan('user-status')) {
                $requestAll = $request->only('active');
            }

            if (auth()->user()->tokenCan('user-update')){
                $requestAll = $request->all();
            }

            $user = User::with('role')->find($id);

            if (is_null($user)) {
                return $this->error('User not found.', 422, []);
            }

            $validator = Validator::make($requestAll, [
                'name' => 'nullable|max:50',
                'email' => [
                    'email','nullable','max:100',
                    function ($attribute, $value, $fail) use ($id) {
                        $user = User::where('email', $value)->whereNull('deleted_at')->first();
                        if ($user && $user->id !== $id) {
                            $fail("The {$attribute} is already being used.");
                        }
                    }
                ],
                'password' => 'nullable|string',
                'role_id' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid.', 422, $validator->errors()->toArray());
            }

            $user->update($requestAll);

            return $this->response('User ' . $user->name . ' updated', 200, UsersDto::content($user->toArray()));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            if (!auth()->user()->tokenCan('role-destroy')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $user = User::with('role')->find($id);

            if (is_null($user)) {
                return $this->error('User not found.', 422, []);
            }

            $roleAdminId = Roles::firstWhere('name', 'Administrador');
            if (
                auth()->user()->role->name === $user->role->name &&
                Str::upper($roleAdminId->name) === Str::upper(auth()->user()->role->name) &&
                User::where('role_id', $roleAdminId->id)->count() === 1)
            {
                return $this->error('Must have at least one administrator user.', 422, []);
            }

            $user = User::where('id', $id)->firstOrFail();
            $user->delete();

            return $this->response($user->name . ' user has been deleted.', 200, []);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }

    public function findById(int $id)
    {
        try {
            if (!auth()->user()->tokenCan('user-find')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $user = User::with('role')->where('id', $id)->firstOrFail();

            return $this->response('User ' . $user->name, 200, UsersDto::content($user->toArray()));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }

    public function pageable(Request $request)
    {
        try {
            if (!auth()->user()->tokenCan('user-pageable')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $validator = Validator::make($request->query(), [
                'perPage' => 'required',
                'page' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid.', 422, $validator->errors()->toArray());
            }

            $query = $request->query;
            $roles = User::with('role')
                ->where('name', 'LIKE', '%' . $query->getString('search') . '%')
                ->orWhere('email', 'LIKE', '%' . $query->getString('search') . '%')
                ->paginate(
                    $perPage = $query->getInt('perPage'),
                    $columns = ['*'],
                    $pageName = 'page',
                    $page = $query->getInt('page')
                );
            $roles->setCollection(collect(UsersDto::contentList($roles->items())));

            return $this->response('user list.', 200, $roles->toArray());
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }
}
