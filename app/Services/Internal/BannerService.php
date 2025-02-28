<?php

namespace App\Services\Internal;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerService
{
    public function getAllBanner()
    {
        return Banner::all();
    }

    public function store($request): ?Banner
    {
        try {
            $token = Str::uuid();

            if ($request->hasFile('image')) {
                $filename =  time() . '-' . Str::random(10) . '.' . $request->image->getClientOriginalExtension();

                $route = 'banners/' . $token;

                $image = $request->file('image');

                Storage::put('public/'. $route . '/' . $filename, file_get_contents($image));
            }

            DB::beginTransaction();
            $banner = new Banner();
            $banner->name = $request->name;
            $banner->file_path = $route;
            $banner->file = $filename;
            $banner->active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            $banner->token = $token;
            $banner->save();

            DB::commit();

            return $banner;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return null;
        }
    }

    public function update($request, $id): ?Banner
    {
        try {

            DB::beginTransaction();

            $banner = Banner::find($id);
            $banner->name = $request->name;

            if ($request->hasFile('image')) {
                $filename =  time() . '-' . Str::random(10) . '.' . $request->image->getClientOriginalExtension();

                $route = 'banners/' . $banner->token;

                $image = $request->file('image');

                Storage::put('public/'. $route . '/' . $filename, file_get_contents($image));

                if (Storage::exists('public/' . $banner->file_path . '/' . $banner->file)) {
                    Storage::delete('public/' . $banner->file_path . '/' . $banner->file);
                }
                
                $banner->file_path = $route;
                $banner->file = $filename;
            }

            $banner->active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            $banner->save();

            DB::commit();

            return $banner;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return null;
        }
    }

    public function destroy($id): bool
    {
        try {
            DB::beginTransaction();

            $banner = Banner::find($id);

            if (Storage::exists('public/' . $banner->file_path . '/' . $banner->file)) {
                Storage::delete('public/' . $banner->file_path . '/' . $banner->file);
            }

            $banner->delete();

            DB::commit();

            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }
}
