<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\Internal\CategoryService;
use App\Services\Internal\SubGroupService;
use Illuminate\Http\Request;

class SubGroupController extends Controller
{
    protected $subGroupService;
    protected $categoryService;

    public function __construct(SubGroupService $subGroupService, CategoryService $categoryService)
    {
        $this->subGroupService = $subGroupService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request, string $category_id, string $group_id)
    {
        $existSlug = $this->subGroupService->getSugroupBySlug($request->slug);

        if ($existSlug) {
            return response()->json(['message' => 'Ya existe un subgrupo con el mismo slug'], 500);
        }

        $subgroup = $this->subGroupService->storeSubgroup($request);

        if ($subgroup) {
            $category = $this->categoryService->getCategoryWithGroupAndSubgroupsById($subgroup->category_id);

            return response()->json([
                'message' => 'Subgrupo creado correctamente',
                'category' => $category
            ], 201);
        } else {
            return response()->json(['message' => 'Error al crear el subgrupo'], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $category_id, string $group_id, string $subgroup_id)
    {
        $existSlug = $this->subGroupService->getSugroupBySlug($request->slug);

        if ($existSlug) {
            return response()->json(['message' => 'Ya existe un subgrupo con el mismo slug'], 500);
        }

        $subgroup = $this->subGroupService->updateSubgroup($request, $subgroup_id);

        if ($subgroup) {
            $category = $this->categoryService->getCategoryWithGroupAndSubgroupsById($category_id);

            return response()->json([
                'message' => 'Subgrupo actualizado correctamente',
                'category' => $category
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar el subgrupo'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $category_id, string $group_id, string $subgroup_id)
    {
        $result = $this->subGroupService->deleteSubgroup($subgroup_id);

        if ($result) {

            $category = $this->categoryService->getCategoryWithGroupAndSubgroupsById($category_id);

            return response()->json([
                'message' => 'Subgrupo eliminado correctamente',
                'category' => $category
            ], 200);
        } else {
            return response()->json(['message' => 'Error al eliminar el subgrupo'], 500);
        }
    }
}
