<?php

namespace App\Services\Internal;

use App\Models\Category;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function getAllSubCategories()
    {
        return Category::all();
    }

    public function getCategoryById($id): Category
    {
        return Category::findOrFail($id);
    }

    public function getCategoryWithGroupAndSubgroupsById($id): Category
    {
        return Category::with('groups.subgroups')->findOrFail($id);
    }

    public function getAllCategoriesWithGroupsAndSubgroups()
    {
        return Category::with('groups.subgroups')->get();
    }

    public function storeCategory($request)
    {
        try {
            $token = Str::uuid();

            DB::beginTransaction();

            $category = new Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->slug = $request->slug;
            $category->icon = $request->icon;
            $category->token = $token;
            $category->featured = $request->featured;
            $category->active = $request->active;

            $category->save();

            DB::commit();

            return $category;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function updateCategory($request, $id): Category | null
    {
        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->slug = $request->slug;
            $category->icon = $request->icon;
            $category->featured = $request->featured;
            $category->active = $request->active;

            $category->save();

            DB::commit();

            return $category;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }

    public function deleteCategory($id): bool
    {
        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);
            $category->delete();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return false;
        }
    }
}
