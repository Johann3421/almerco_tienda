<?php

namespace App\Services\Internal;

use App\Models\Filter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FilterService
{
    public function getAllFilters()
    {
        return Filter::with('filterItems')->get();
    }

    public function getFilterById($id)
    {
        return Filter::with('filterItems')->findOrFail($id);
    }

    public function storeFilter($request): Filter | null
    {
        try {
            DB::beginTransaction();

            $filter = new Filter();
            $filter->name = $request->name;
            $filter->active = $request->active;
            $filter->save();

            DB::commit();

            return $filter;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function updateFilter($request, $id): Filter | null
    {
        try {
            DB::beginTransaction();

            $filter = Filter::findOrFail($id);
            $filter->name = $request->name;
            $filter->active = $request->active;

            $filter->save();

            DB::commit();

            return $filter;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function deleteFilter($id): bool | null
    {
        try {
            DB::beginTransaction();

            $filter = Filter::findOrFail($id);
            $filter->delete();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
}
