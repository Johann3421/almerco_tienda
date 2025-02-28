<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\Internal\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipo_de_cambio = $this->settingService->getSettingByKey('tipo_de_cambio');
        $img_superior = $this->settingService->getSettingByKey('img_superior');
        $img_superior_mobile = $this->settingService->getSettingByKey('img_superior_mobile');
        $img_medio = $this->settingService->getSettingByKey('img_medio');
        $img_medio_mobile = $this->settingService->getSettingByKey('img_medio_mobile');

        return Inertia::render('Panel/Settings/Index', [
            'tipo_de_cambio' => $tipo_de_cambio,
            'img_superior' => $img_superior,
            'img_superior_mobile' => $img_superior_mobile,
            'img_medio' => $img_medio,
            'img_medio_mobile' => $img_medio_mobile
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $setting = $this->settingService->store($request);

        if ($setting) {
            return response()->json([
                'message' => 'Configuración creada correctamente',
                'setting' => $setting
            ], 201);
        } else {
            return response()->json(['message' => 'Error al crear la configuración'], 500);
        }
    }

    public function storeTipoDeCambio(Request $request)
    {
        $data = $request->all();
        $data['active'] = $request->active ? true : false;
        $data = (object) $data;

        $setting = $this->settingService->storeTipoDeCambio($data);

        if ($setting) {
            return response()->json([
                'message' => 'Tipo de cambio actualizado correctamente',
                'data' => $setting
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar el tipo de cambio'], 500);
        }
    }

    public function storeImgSuperior(Request $request)
    {
        $setting = $this->settingService->storeImgSuperior($request);

        if ($setting) {
            return response()->json([
                'message' => 'Imagen superior actualizada correctamente',
                'data' => $setting
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar la imagen superior'], 500);
        }
    }

    public function storeImgSuperiorMobile(Request $request)
    {
        $setting = $this->settingService->storeImgSuperiorMobile($request);

        if ($setting) {
            return response()->json([
                'message' => 'Imagen superior mobile actualizada correctamente',
                'data' => $setting
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar la imagen superior mobile'], 500);
        }
    }

    public function storeImgMedio(Request $request)
    {
        $setting = $this->settingService->storeImgMedio($request);

        if ($setting) {
            return response()->json([
                'message' => 'Imagen media actualizada correctamente',
                'data' => $setting
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar la imagen media'], 500);
        }
    }

    public function storeImgMedioMobile(Request $request)
    {
        $setting = $this->settingService->storeImgMedioMobile($request);

        if ($setting) {
            return response()->json([
                'message' => 'Imagen media mobile actualizada correctamente',
                'data' => $setting
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar la imagen media mobile'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $setting = $this->settingService->update($request, $id);

        if ($setting) {
            return response()->json([
                'message' => 'Configuración actualizada correctamente',
                'setting' => $setting
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar la configuración'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
