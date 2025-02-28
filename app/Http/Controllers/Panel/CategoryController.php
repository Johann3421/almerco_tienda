<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Category\CategoryRequest;
use App\Services\Internal\CategoryService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return Inertia::render('Panel/Categories/Index');
    }

    public function find($search)
    {
        return $this->categoryService->getAllCategoriesWithGroupsAndSubgroups();
    }

    public function create()
    {
        //
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->storeCategory($request);
        if ($category) {

            $category = $this->categoryService->getCategoryWithGroupAndSubgroupsById($category->id);

            return response()->json([
                'message' => 'Categoría creada correctamente',
                'category' => $category
            ], 201);
        } else {
            return response()->json(['message' => 'Error al crear la categoría'], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $category = $this->categoryService->updateCategory($request, $id);
        if ($category) {

            $category_with_groups_and_subgroups = $this->categoryService->getCategoryWithGroupAndSubgroupsById($id);

            return response()->json([
                'message' => 'Categoría actualizada correctamente',
                'category' => $category_with_groups_and_subgroups
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar la categoría'], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $category = $this->categoryService->deleteCategory($id);
        if ($category) {
            return response()->json([
                'message' => 'Categoría eliminada correctamente',
                'category' => $category
            ], 200);
        } else {
            return response()->json(['message' => 'Error al eliminar la categoría'], 500);
        }
    }
}
