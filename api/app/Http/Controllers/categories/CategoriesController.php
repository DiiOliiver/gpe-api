<?php

namespace App\Http\Controllers\categories;

use App\Http\Controllers\Controller;
use App\Http\Dto\CategoriesDto;
use App\Models\Categories;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (!auth()->user()->tokenCan('category-index')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $categories = Categories::where('active', 1)->get();

            return $this->response('Category list.', 200, CategoriesDto::contentList($categories->toArray()));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('Unlisted categories.', 400, []);
        }
    }

    public function pageable(Request $request)
    {
        try {
            if (!auth()->user()->tokenCan('category-pageable')) {
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
            $roles = Categories::where('name', 'LIKE', '%' . $query->getString('search') . '%')
                ->paginate(
                    $perPage = $query->getInt('perPage'),
                    $columns = ['*'],
                    $pageName = 'page',
                    $page = $query->getInt('page')
                );
            $roles->setCollection(collect(CategoriesDto::contentList($roles->items())));

            return $this->response('Category list', 200, $roles->toArray());
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('An error occurred in the process.', 400, []);
        }
    }
}
