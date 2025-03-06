<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\Product\ProductVisibilityRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Internal\CategoryService;
use App\Services\Internal\FilterItemService;
use App\Services\Internal\ProductService;
use App\Services\Internal\SubGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $productService, $subGroupService, $categoryService, $filterItemService;
    public function __construct(ProductService $productService, CategoryService $categoryService, SubGroupService $subGroupService, FilterItemService $filterItemService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->subGroupService = $subGroupService;
        $this->filterItemService = $filterItemService;
    }

    public function index()
    {
        return Inertia::render('Panel/Products/Index');
    }

    public function load(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($sortBy = $request->input('sortBy')) {
            foreach ($sortBy as $sort) {
                $query->orderBy($sort['key'], $sort['order']);
            }
        }

        $products = $query->paginate($request->input('itemsPerPage', 10));

        return [
            'products' => $products
        ];
    }

    public function uploadImageChunk(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'chunk' => 'required|file',
            'field' => 'required|string',
            'product_id' => 'required|integer',
            'old_image_id' => 'integer',
            'chunk_id' => 'required|uuid',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
            'file_extension' => 'required|string|in:jpeg,jpg,png,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al validar los datos',
                'errors' => $validator->errors()
            ], 422);
        }

        $chunkId = $request->chunk_id;
        $chunkIndex = $request->chunk_index;
        $totalChunks = $request->total_chunks;
        $chunk = $request->file('chunk');

        // Carpeta temporal para almacenar los chunks
        $tempDir = "temp/{$chunkId}/";
        Storage::put("{$tempDir}/chunk_{$chunkIndex}", file_get_contents($chunk));

        if ($chunkIndex == $totalChunks) {

            DB::beginTransaction();

            $product = Product::find(id: $productId);

            if (!$product) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $route = 'products/' . $product->token;
            $filename = time() . '-' . Str::random(10) . '.' . $request->file_extension;

            $finalPath = "public/{$route}/{$filename}";

            // Crear directorio si no existe

            if (!Storage::exists("public/products/{$product->token}")) {
                Storage::makeDirectory("public/products/{$product->token}");
            }

            $finalFile = fopen(filename: storage_path(path: "app/{$finalPath}"), mode: 'w');

            for ($i = 1; $i <= $totalChunks; $i++) {
                $chunkPath = storage_path("app/{$tempDir}/chunk_{$i}");
                $chunkContent = file_get_contents($chunkPath);
                fwrite($finalFile, $chunkContent);
                unlink($chunkPath); // Eliminar cada chunk una vez se ha unido
            }

            fclose($finalFile);

            $imagen = ProductImage::where('id', $request->old_image_id)->first();

            if ($imagen) {
                if (Storage::exists('public/' . $imagen->file_path . '/' . $imagen->file)) {
                    Storage::delete('public/' . $imagen->file_path . '/' . $imagen->file);
                }

                $imagen->file_path = $route;
                $imagen->file = $filename;
                $imagen->save();
            } else {
                $imagen = new ProductImage();
                $imagen->product_id = $product->id;
                $imagen->file_path = $route;
                $imagen->file = $filename;
                $imagen->save();
            }

            DB::commit();

            Storage::deleteDirectory($tempDir);

            return response()->json(['message' => "{$request->field} subida correctamente"]);
        }

        // return response()->json(['message' => "Chunk {$chunkIndex} subido correctamente"]);
    }

    public function create()
    {

        $categories = $this->subGroupService->getAllSubgroupsWithGroupAndCategory();
        $filters = $this->filterItemService->getAllFilterItemsWithFilter();
        $lastId = $this->productService->getLastInsertedProductId();

        if ($lastId === null) {
            $lastId = 0;
        }

        return Inertia::render('Panel/Products/Create', [
            'categories' => $categories,
            'filters' => $filters,
            'lastId' => $lastId
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku' => ['required', 'string', 'max:255', 'unique:products'],
            'part_number' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'previous_price' => ['required', 'numeric'],
            'manufacturer_url' => ['required', 'string', 'url'],

            // Campos booleanos
            'on_sale' => ['required', 'boolean'],
            'on_promotion' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],

            'specifications' => ['array'],
            'subgroups' => ['required', 'array'],
            'filters' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al validar los datos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $productId = $this->productService->store($request);

            if ($productId === null) {
                return response()->json(data: [
                    'message' => 'No se ha podido crear el producto',
                ], status: 400);
            }

            return response()->json(data: [
                'message' => 'El producto ha sido creado',
                'productId' => $productId
            ], status: 201);
        } catch (\Throwable $th) {
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $th->getMessage());
            return response()->json([
                'message' => $th->getMessage(),
            ], 400);
        }

    }

    public function updateVisibilityOnStore(ProductVisibilityRequest $request)
    {
        try {
            if ($this->productService->updateVisibility($request->id, $request->visibility)) {
                return response()->json(['message' => 'La visibilidad del producto ha sido actualizada'], 200);
            } else {
                return response()->json(['message' => 'No se ha podido actualizar la visibilidad del producto'], 500);
            }
        } catch (\Throwable $th) {
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $th->getMessage());
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $product = $this->productService->getProductByIdForEdit($id);

        $categories = $this->subGroupService->getAllSubgroupsWithGroupAndCategory();
        $filters = $this->filterItemService->getAllFilterItemsWithFilter();

        return Inertia::render('Panel/Products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $id],
            'part_number' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'previous_price' => ['required', 'numeric'],
            'manufacturer_url' => ['required', 'string', 'url'],

            // Campos booleanos
            'on_sale' => ['required', 'boolean'],
            'on_promotion' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],

            'specifications' => ['array'],
            'subgroups' => ['required', 'array'],
            'filters' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al validar los datos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $productId = $this->productService->update(request: $request, id: $id);

            if ($productId === null) {
                return response()->json(data: [
                    'message' => 'No se ha podido actualizar el producto',
                ], status: 400);
            }

            return response()->json([
                'message' => 'El producto ha sido actualizado',
                'productId' => $productId
            ], 200);

        } catch (\Throwable $th) {
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $th->getMessage());
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
{
    try {
        // Buscar el producto por ID
        $product = Product::findOrFail($id);

        // Eliminar el producto de la base de datos
        $product->delete();

        // Retornar una respuesta exitosa
        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ], 200);
    } catch (\Exception $e) {
        // Retornar un error si algo falla
        return response()->json([
            'message' => 'Error al eliminar el producto.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
