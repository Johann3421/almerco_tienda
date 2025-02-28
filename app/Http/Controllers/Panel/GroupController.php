<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\Internal\CategoryService;
use App\Services\Internal\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class GroupController extends Controller
{
    protected $groupService;
    protected $categoryService;

    public function __construct(GroupService $groupService, CategoryService $categoryService)
    {
        $this->groupService = $groupService;
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

    public function store(Request $request): JsonResponse       
    {
        $group = $this->groupService->storeGroup($request);
        if ($group) {

            $category = $this->categoryService->getCategoryWithGroupAndSubgroupsById($group->category_id);

            return response()->json([
                'message' => 'Grupo creado correctamente',
                'category' => $category
            ], 201);
        } else {
            return response()->json(['message' => 'Error al crear el grupo'], 500);
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

    public function update(Request $request, string $category_id, string $group_id): JsonResponse
    {
        $group = $this->groupService->updateGroup($request, $group_id);

        if ($group) {
            $category = $this->categoryService->getCategoryWithGroupAndSubgroupsById($category_id);

            return response()->json([
                'message' => 'Grupo actualizado correctamente',
                'category' => $category
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar el grupo'], 500);
        }
    }

    public function destroy(string $category_id, string $group_id): JsonResponse
    {
        $result = $this->groupService->deleteGroup($group_id);

        if ($result) {
            return response()->json(['message' => 'Grupo eliminado correctamente'], 200);
        } else {
            return response()->json(['message' => 'Error al eliminar el grupo'], 500);
        }
    }
}
