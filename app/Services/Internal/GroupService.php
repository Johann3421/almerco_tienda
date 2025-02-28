<?php

namespace App\Services\Internal;

use App\Models\Group;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupService
{
    public function getAllGroups()
    {
        return Group::all();
    }

    public function getGroupById($id): Group
    {
        return Group::findOrFail($id);
    }

    public function getGroupWithSubgroupsById($id): Group
    {
        return Group::with('subgroups')->findOrFail($id);
    }

    public function getAllGroupsWithSubgroups()
    {
        return Group::with('subgroups')->get();
    }

    public function storeGroup($request): Group | null
    {
        try {
            $token = Str::uuid();

            DB::beginTransaction();

            $group = new Group();
            $group->category_id = $request->category_id;
            $group->name = $request->name;
            $group->description = $request->description;
            $group->slug = $request->slug;
            $group->icon = $request->icon;
            $group->token = $token;
            $group->featured = $request->featured;
            $group->active = $request->active;

            $group->save();

            DB::commit();

            return $group;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function updateGroup($request, $id): Group | null
    {
        try {
            DB::beginTransaction();

            $group = Group::findOrFail($id);
            $group->category_id = $request->category_id;
            $group->name = $request->name;
            $group->description = $request->description;
            $group->slug = $request->slug;
            $group->icon = $request->icon;
            $group->featured = $request->featured;
            $group->active = $request->active;

            $group->save();

            DB::commit();

            return $group;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function deleteGroup($id): bool
    {
        try {
            DB::beginTransaction();

            $group = Group::findOrFail($id);
            $group->delete();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return false;
        }
    }
}
