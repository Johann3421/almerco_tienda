<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Filter\FilterItemRequest;
use App\Http\Requests\Filter\FilterItemPatchRequest;
use App\Services\Internal\FilterItemService;
use App\Services\Internal\FilterService;
use Illuminate\Http\Request;

class FilterItemsController extends Controller
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
        //
    }

    public function create()
    {
        //
    }

    public function store(FilterItemRequest $request)
    {
        $filter_item = $this->filterItemService->storeFilterItem($request);
        if ($filter_item) {

            $filter = $this->filterService->getFilterById($request->filter_id);

            return response()->json([
                'message' => 'Opción de filtro creada correctamente',
                'filter' => $filter,
            ], 201);
        } else {
            return response()->json(['message' => 'Error al crear la opción del filtro'], 500);
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

    public function update(FilterItemPatchRequest $request, string $filter, string $item)
    {
        $filter_item = $this->filterItemService->updateFilterItem($request, $item);
        if ($filter_item) {

            $filter = $this->filterService->getFilterById($request->filter_id);

            return response()->json([
                'message' => 'Opción del filtro actualizada correctamente',
                'filter' => $filter,
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar la opción del filtro'], 500);
        }
    }

    public function destroy(string $filter, string $item)
    {
        $result = $this->filterItemService->deleteFilterItem($item);

        if ($result) {
            return response()->json(['message' => 'Opción del filtro eliminada correctamente'], 200);
        } else {
            return response()->json(['message' => 'Error al eliminar la opción del filtro'], 500);
        }
    }
}
