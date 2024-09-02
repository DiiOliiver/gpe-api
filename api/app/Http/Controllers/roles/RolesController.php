<?php

namespace App\Http\Controllers\roles;

use App\Http\Dto\RolesDto;
use App\Models\Roles;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (!auth()->user()->tokenCan('role-index')) {
                return $this->error('Not Authorized', 403, []);
            }

            $roles = Roles::where('active', 1)->get();

            return $this->response('Role list', 200, RolesDto::contentList($roles->toArray()));
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
            if (!auth()->user()->tokenCan('role-store')) {
                return $this->error('Not Authorized', 403, []);
            }

            $validator = Validator::make($request->all(), [
                'name' => [
                    'required', 'max:50',
                    function ($attribute, $value, $fail) {
                        $role = Roles::where('name', $value)->whereNull('deleted_at')->first();
                        if (!is_null($role)) {
                            $fail("The {$attribute} is already being used.");
                        }
                    }
                ],
                'active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid', 422, $validator->errors()->toArray());
            }

            Roles::create($validator->validated());

            return $this->response('Role created', 200);
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
            if (!(auth()->user()->tokenCan('role-status')) && !(auth()->user()->tokenCan('role-update'))) {
                return $this->error('Not Authorized', 403, []);
            }

            $requestAll = $request;

            if (auth()->user()->tokenCan('role-status')) {
                $requestAll = $request->only('active');
            }

            if (auth()->user()->tokenCan('role-update')) {
                $requestAll = $request->all();
            }

            $role = Roles::find($id);

            if (is_null($role)) {
                return $this->error('Role not found.', 422, []);
            }

            $validator = Validator::make($requestAll, [
                'name' => [
                    'nullable', 'max:50',
                    function ($attribute, $value, $fail) use ($id) {
                        $role = Roles::where('name', $value)->whereNull('deleted_at')->first();
                        if ($role && $role->id !== $id) {
                            $fail("The {$attribute} is already being used.");
                        }
                    }
                ],
                'active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid', 422, $validator->errors()->toArray());
            }

            $role->update($requestAll);

            return $this->response(
                'Role ' . $role->name . ' updated',
                200,
                (array) RolesDto::content($role->toArray())
            );
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
                return $this->error('Not Authorized', 403, []);
            }

            $role = Roles::find($id);

            if (is_null($role)) {
                return $this->error('Role not found.', 422, []);
            }

            if (Str::upper($role->name) === 'ADMINISTRADOR') {
                return $this->error('Deleting the administrator role is not allowed.', 422, []);
            }

            $role->delete();

            return $this->response($role->name . ' role has been deleted.', 200, []);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }

    public function findById(int $id)
    {
        try {
            if (!auth()->user()->tokenCan('role-find')) {
                return $this->error('Not Authorized', 403, []);
            }

            $role = Roles::find($id);

            if (is_null($role)) {
                return $this->error('Role not found.', 422, []);
            }

            return $this->response(
                'Role ' . $role->name,
                200,
                (array) RolesDto::content($role->toArray())
            );
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }

    public function pageable(Request $request)
    {
        try {
            if (!auth()->user()->tokenCan('role-pageable')) {
                return $this->error('Not Authorized', 403, []);
            }

            $validator = Validator::make($request->query(), [
                'perPage' => 'required',
                'page' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid', 422, $validator->errors()->toArray());
            }

            $query = $request->query;
            $roles = Roles::where('name', 'LIKE', '%' . $query->getString('search') . '%')
                ->paginate(
                    $perPage = $query->getInt('perPage'),
                    $columns = ['*'],
                    $pageName = 'page',
                    $page = $query->getInt('page')
                );
            $roles->setCollection(collect(RolesDto::contentList($roles->items())));

            return $this->response('Role list', 200, $roles->toArray());
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }
}
