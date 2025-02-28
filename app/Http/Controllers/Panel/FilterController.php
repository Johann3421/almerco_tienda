<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Filter\FilterRequest;
use App\Services\Internal\FilterItemService;
use App\Services\Internal\FilterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FilterController extends Controller
{
    protected $filterService;
    protected $filterItemService;

    public function __construct(FilterService $filterService, FilterItemService $filterItemService)
    {
        $this->filterService = $filterService;
        $this->filterItemService = $filterItemService;
    }
    public function index()
    {
        $filters = $this->filterService->getAllFilters();
        return Inertia::render('Panel/Filters/Index', [
            'filters' => $filters
        ]);
    }

    public function find(string $search)
    {
        return response()->json($this->filterService->getAllFilters());
    }

    public function create()
    {
        //
    }

    public function store(FilterRequest $request): JsonResponse
    {
        $filter = $this->filterService->storeFilter($request);
        if ($filter) {

            $filter = $this->filterService->getFilterById($filter->id);
            return response()->json([
                'message' => 'Filtro creado correctamente',
                'filter' => $filter
            ], 201);
        } else {
            return response()->json(['message' => 'Error al crear el filtro'], 500);
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

    public function update(Request $request, string $id)
    {
        $filter = $this->filterService->updateFilter($request, $id);

        if ($filter) {

            $filter_with_items = $this->filterService->getFilterById($id);

            return response()->json([
                'message' => 'Filtro actualizado correctamente',
                'filter' => $filter_with_items
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar el filtro'], 500);
        }
    }

    public function destroy(string $id)
    {
        $result = $this->filterService->deleteFilter($id);

        if ($result) {
            return response()->json(['message' => 'Filtro eliminado correctamente'], 200);
        } else {
            return response()->json(['message' => 'Error al eliminar el filtro'], 500);
        }
    }
}
