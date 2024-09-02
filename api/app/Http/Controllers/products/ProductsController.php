<?php

namespace App\Http\Controllers\products;

use App\Http\Controllers\Controller;
use App\Http\Dto\ProductsDto;
use App\Models\Products;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (!auth()->user()->tokenCan('product-index')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $products = Products::with('category')->where('active', 1)->get();

            return $this->response('Product list.', 200, ProductsDto::contentList($products->toArray()));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('Unlisted products.', 400, []);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!auth()->user()->tokenCan('product-store')) {
                return $this->error('Not Authorized', 403, []);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'description' => 'nullable|string|max:200',
                'price' => ['required', 'string','decimal:2',
                    function (string $attribute, mixed $value, \Closure $fail) {
                        if ($value == 0) {
                            $fail("The {$attribute} cannot be zero.");
                        }
                    }
                ],
                'active' => 'nullable|boolean',
                'image' => 'required|mimes:png,jpeg,jpg|max:2048|unique:products,image',
                'expiration_date' => 'required|string|date_format:d/m/Y|after:'.Carbon::now()->format('d/m/Y'),
                'category_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid', 422, $validator->errors()->toArray());
            }

            $filePath = public_path('uploads');
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();

                $file->move($filePath, $fileName);
            }

            Products::create([
                ...$request->all(),
                'image' => $fileName,
                'expiration_date' => Carbon::make($request->expiration_date)->format('Y-m-d')
            ]);

            return $this->response('Product created', 200);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('Product not found', 400, []);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if (!(auth()->user()->tokenCan('product-update')) && !(auth()->user()->tokenCan('product-status'))) {
                return $this->error('Not Authorized', 403, []);
            }

            $requestAll = $request;

            if (auth()->user()->tokenCan('product-status')) {
                $requestAll = $request->only('active');
            }

            if (auth()->user()->tokenCan('product-update')){
                $requestAll = $request->all();
            }

            $product = Products::find($id);

            if (is_null($product)) {
                return $this->error('Product not found.', 422, []);
            }

            $validator = Validator::make($requestAll, [
                'name' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:200',
                'price' => ['nullable', 'string','decimal:2',
                    function (string $attribute, mixed $value, \Closure $fail) {
                        if ($value == 0) {
                            $fail("The {$attribute} cannot be zero.");
                        }
                    }
                ],
                'active' => 'nullable|boolean',
                'expiration_date' => 'nullable|string|date_format:d/m/Y|after:'.Carbon::now()->format('d/m/Y'),
                'category_id' => 'nullable|numeric',
            ]);


            if ($validator->fails()) {
                return $this->error('Data Invalid', 422, $validator->errors()->toArray());
            }

            if (auth()->user()->tokenCan('product-update')) {
                $requestAll = [
                    ...$request->except('expiration_date'),
                    'expiration_date' => Carbon::make($request->expiration_date)->format('Y-m-d')
                ];
            }

            $product->update($requestAll);

            $updated = Products::with('category')->firstWhere('id', $product->id);

            return $this->response(
                'Product updated',
                200,
                (array) ProductsDto::content($updated->toArray())
            );
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('Product not found', 400, []);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if (!auth()->user()->tokenCan('product-destroy')) {
                return $this->error('Not Authorized.', 403, []);
            }

            $product = Products::find($id);

            if (is_null($product)) {
                return $this->error('Product not found.', 422, []);
            }

            $product->delete();

            return $this->response($product->name . ' product has been deleted.', 200, []);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('Product not found.', 400, []);
        }
    }

    public function findById(string $id)
    {
        try {
            if (!auth()->user()->tokenCan('product-find')) {
                return $this->error('Not Authorized.', 403, []);
            }


            $product = Products::with('category')->find($id);

            if (is_null($product)) {
                return $this->error('Product not found.', 422, []);
            }

            return $this->response('Product ' . $product->name, 200, (array) ProductsDto::content($product->toArray()));
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('Product not found.', 400, []);
        }
    }

    public function pageable(Request $request)
    {
        try {
            if (!auth()->user()->tokenCan('product-pageable')) {
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

            $roles = Products::with('category')
                ->where('description', 'LIKE', '%' . $query->getString('search') . '%')
                ->orWhere('name', 'LIKE', '%' . $query->getString('search') . '%')
                ->orWhere('price', 'LIKE', '%' . $query->getString('search') . '%')
                ->paginate(
                    $perPage = $query->getInt('perPage'),
                    $columns = ['*'],
                    $pageName = 'page',
                    $page = $query->getInt('page')
                );
            $roles->setCollection(collect(ProductsDto::contentList($roles->items())));

            return $this->response('Product list.', 200, $roles->toArray());
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return $this->error('Unlisted products.', 400, []);
        }
    }

    public function upload(Request $request, string $id)
    {
        try {
            if (!auth()->user()->tokenCan('product-upload')) {
                return $this->error('Not Authorized', 403, []);
            }

            $product = Products::find($id);

            if (is_null($product)) {
                return $this->error('Product not found.', 422, []);
            }

            $validator = Validator::make($request->all(), [
                'image' => 'required|mimes:png,jpeg,jpg|max:2048|unique:products,image',
            ]);

            if ($validator->fails()) {
                return $this->error('Data Invalid', 422, $validator->errors()->toArray());
            }

            $filePath = public_path('uploads');
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($filePath, $fileName);

            if (!is_null($product->image)) {
                $oldImage = public_path('uploads/'.$product->image);
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }

            $product->update([
                'image' => $fileName
            ]);

            return $this->response(
                'Image registered for '.$product->name.' product',
                200,
                []
            );
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->error('Image not found', 400, []);
        }
    }
}

