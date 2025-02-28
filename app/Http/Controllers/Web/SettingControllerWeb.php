<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Internal\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingControllerWeb extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }
    public function index(Request $request)
    {
        $tipo_de_cambio = $this->settingService->getSettingByKey('tipo_de_cambio');
        $img_superior = $this->settingService->getSettingByKey('img_superior');
        $img_medio = $this->settingService->getSettingByKey('img_medio');
        $img_superior_mobile = $this->settingService->getSettingByKey('img_superior_mobile');
        $img_medio_mobile = $this->settingService->getSettingByKey('img_medio_mobile');

        $response = [
            'tipo_de_cambio' => $tipo_de_cambio,
            'img_superior' => $img_superior,
            'img_medio' => $img_medio,
            'img_superior_mobile' => $img_superior_mobile,
            'img_medio_mobile' => $img_medio_mobile,
        ];
        return response()->json($response);
    }
}
