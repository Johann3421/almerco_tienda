<?php

namespace App\Services\Internal;

use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class SettingService
{
    public function getAllSettings()
    {
        return Setting::all();
    }
    public function getSettingByKey($key)
    {
        return Setting::where('key', $key)->first();
    }
    public function getSettingById($id)
    {
        return Setting::findOrFail($id);
    }
    public function store($request): Setting | null
    {
        try {
            DB::beginTransaction();

            $setting = new Setting();
            $setting->key = $request->key;
            $setting->value = $request->value;
            $setting->save();

            DB::commit();

            return $setting;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
    public function storeTipoDeCambio($request): ?Setting
    {
        try {
            DB::beginTransaction();

            $setting = $this->getSettingByKey('tipo_de_cambio');
            $setting->value = $request->value;
            $setting->active = $request->active;
            $setting->save();

            DB::commit();

            return $setting;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
    public function storeImgSuperior($request): ?Setting
    {
        try {

            DB::beginTransaction();

            $setting = $this->getSettingByKey('img_superior');
            $setting->url = $request->url;

            $uuid = $setting->token ?? Str::uuid();

            $setting->token = $uuid;
            $setting->active = $request->active === 'true';

            if ($request->hasFile('imagen')) {

                if ($setting->file) {
                    Storage::delete('public/' . $setting->file_path . '/' . $setting->file);
                }

                $filename = time() . '-' . Str::random(10) . '.' . $request->imagen->getClientOriginalExtension();
                $route = 'settings/' . $setting->token;

                $image = $request->file('imagen');

                Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

                $setting->file_path = $route;
                $setting->file = $filename;
            }

            $setting->save();

            DB::commit();

            return $setting;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
    public function storeImgSuperiorMobile($request): ?Setting
    {
        try {

            DB::beginTransaction();

            $setting = $this->getSettingByKey('img_superior_mobile');
            $setting->url = $request->url;

            $uuid = $setting->token ?? Str::uuid();

            $setting->token = $uuid;
            $setting->active = $request->active === 'true';

            if ($request->hasFile('imagen')) {

                if ($setting->file) {
                    Storage::delete('public/' . $setting->file_path . '/' . $setting->file);
                }

                $filename = time() . '-' . Str::random(10) . '.' . $request->imagen->getClientOriginalExtension();
                $route = 'settings/' . $setting->token;

                $image = $request->file('imagen');

                Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

                $setting->file_path = $route;
                $setting->file = $filename;
            }

            $setting->save();

            DB::commit();

            return $setting;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
    public function storeImgMedio($request): ?Setting
    {
        try {


            DB::beginTransaction();

            $setting = $this->getSettingByKey('img_medio');
            $setting->url = $request->url;

            $uuid = $setting->token ?? Str::uuid();

            $setting->token = $uuid;
            $setting->active = $request->active === 'true';

            if ($request->hasFile('imagen')) {

                if ($setting->file) {
                    Storage::delete('public/' . $setting->file_path . '/' . $setting->file);
                }

                $filename = time() . '-' . Str::random(10) . '.' . $request->imagen->getClientOriginalExtension();
                $route = 'settings/' . $setting->token;

                $image = $request->file('imagen');

                Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

                $setting->file_path = $route;
                $setting->file = $filename;
            }

            $setting->save();

            DB::commit();

            return $setting;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
    public function storeImgMedioMobile($request): ?Setting
    {
        try {

            DB::beginTransaction();

            $setting = $this->getSettingByKey('img_medio_mobile');
            $setting->url = $request->url;

            $uuid = $setting->token ?? Str::uuid();

            $setting->token = $uuid;
            $setting->active = $request->active === 'true';

            if ($request->hasFile('imagen')) {

                if ($setting->file) {
                    Storage::delete('public/' . $setting->file_path . '/' . $setting->file);
                }

                $filename = time() . '-' . Str::random(10) . '.' . $request->imagen->getClientOriginalExtension();
                $route = 'settings/' . $setting->token;

                $image = $request->file('imagen');

                Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

                $setting->file_path = $route;
                $setting->file = $filename;
            }

            $setting->save();

            DB::commit();

            return $setting;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
    public function update($request, $id): Setting | null
    {
        try {
            DB::beginTransaction();

            $setting = Setting::findOrFail($id);
            // $setting->key = $request->key;
            $setting->value = $request->value;

            $setting->save();

            DB::commit();

            return $setting;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
    public function deleteSetting($id): bool | null
    {
        try {
            DB::beginTransaction();

            $setting = Setting::findOrFail($id);
            $setting->delete();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return null;
        }
    }
}
