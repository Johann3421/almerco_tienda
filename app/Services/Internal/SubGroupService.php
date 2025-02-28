<?php

namespace App\Services\Internal;

use App\Models\Subgroup;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubGroupService
{
    public function getAllSubgroups()
    {
        return Subgroup::all();
    }

    public function getAllSubgroupsWithGroupAndCategory()
    {
        return Subgroup::with('group.category')
            ->select('id', 'name', 'group_id', 'category_id')
            ->get();
    }

    public function getSugroupById($id): ?Subgroup
    {
        return Subgroup::findOrFail($id);
    }
    public function getSugroupBySlug($slug): ?Subgroup
    {
        return Subgroup::where('slug', $slug)->first();
    }

    public function storeSubgroup($request): ?Subgroup
    {
        try {
            $token = Str::uuid();

            DB::beginTransaction();

            $subgroup = new Subgroup();
            $subgroup->category_id = $request->category_id;
            $subgroup->group_id = $request->group_id;
            $subgroup->name = $request->name;
            $subgroup->slug = $request->slug;
            $subgroup->description = $request->description;
            $subgroup->icon = $request->icon;
            $subgroup->featured = $request->featured;
            $subgroup->token = $token;
            $subgroup->active = $request->active;
            $subgroup->save();

            DB::commit();

            return $subgroup;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function updateSubgroup($request, $id): ?Subgroup
    {
        try {
            DB::beginTransaction();

            $subgroup = Subgroup::findOrFail($id);
            $subgroup->category_id = $request->category_id;
            $subgroup->group_id = $request->group_id;
            $subgroup->name = $request->name;
            $subgroup->slug = $request->slug;
            $subgroup->description = $request->description;
            $subgroup->icon = $request->icon;
            $subgroup->featured = $request->featured;
            $subgroup->active = $request->active;
            $subgroup->save();

            DB::commit();

            return $subgroup;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function deleteSubgroup($id): bool
    {
        try {
            DB::beginTransaction();

            $subgroup = Subgroup::findOrFail($id);
            $subgroup->delete();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return false;
        }
    }
}
