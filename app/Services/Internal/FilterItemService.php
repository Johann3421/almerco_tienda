<?php

namespace App\Services\Internal;

use App\Models\FilterItem;
use App\Services\External\StorageService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilterItemService
{
    private $storageService;
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function getAllFilterItems()
    {
        return FilterItem::all();
    }

    public function getAllFilterItemsByFilterId($filterId)
    {
        return FilterItem::where('filter_id', $filterId)->get();
    }
    public function getAllFilterItemsWithFilter()
    {
        return FilterItem::with('filter')
            ->select('id', 'name', 'filter_id')
            ->where('active', 1)
            ->get();
    }

    public function getFilterItemById($id): FilterItem
    {
        return FilterItem::findOrFail($id);
    }

    public function storeFilterItem($request): FilterItem | null
    {
        try {

            DB::beginTransaction();

            $filterItem = new FilterItem();
            $filterItem->name = $request->name;
            $filterItem->active = $request->active;
            $filterItem->filter_id = $request->filter_id;

            if ($request->hasFile('image')) {
                $filename =  time() . '-' . Str::random(10) . '.' . $request->image->getClientOriginalExtension();

                $route = 'filters';

                $image = $request->file('image');

                Storage::put('public/'. $route . '/' . $filename, file_get_contents($image));

                $filterItem->file_path = $route;
                $filterItem->file = $filename;
            }

            $filterItem->save();

            DB::commit();

            return $filterItem;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error($qe->getMessage());
            return null;
        }
    }

    public function updateFilterItem($request, $id): FilterItem | null
    {
        try {
            DB::beginTransaction();

            $filterItem = FilterItem::findOrFail($id);
            $filterItem->name = $request->name;
            $filterItem->active = $request->active;
            $filterItem->filter_id = $request->filter_id;

            if ($request->hasFile('image')) {

                if (Storage::exists('public/' . $filterItem->file_path . '/' . $filterItem->file)) {
                    Storage::delete('public/' . $filterItem->file_path . '/' . $filterItem->file);
                }

                $filename =  time() . '-' . Str::random(10) . '.' . $request->image->getClientOriginalExtension();

                $route = 'filters';

                $image = $request->file('image');

                Storage::put('public/'. $route . '/' . $filename, file_get_contents($image));

                $filterItem->file_path = $route;
                $filterItem->file = $filename;
            }

            $filterItem->save();

            DB::commit();

            return $filterItem;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error($qe->getMessage());
            return null;
        }
    }

    public function deleteFilterItem($id): bool | null
    {
        try {
            DB::beginTransaction();

            $filterItem = FilterItem::findOrFail($id);
            $filterItem->delete();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error($qe->getMessage());
            return null;
        }
    }
}
