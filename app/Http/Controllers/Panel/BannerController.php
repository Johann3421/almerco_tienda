<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\Internal\BannerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }
    public function index(): Response
    {
        $banners = $this->bannerService->getAllBanner();
        return Inertia::render('Panel/Banners/Index', [
            'banners' => $banners
        ]);
    }

    public function create()
    {
        //
    }

    public function find($search)
    {
        return $this->bannerService->getAllBanner();
    }

    public function store(Request $request)
    {
        $banner = $this->bannerService->store($request);
        if ($banner) {
            return response()->json([
                'message' => 'Banner creado correctamente',
                'banner' => $banner
            ], 201);
        } else {
            return response()->json(['message' => 'Error al crear el banner'], 500);
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

    public function update(Request $request, string $id)
    {
        $banner = $this->bannerService->update($request, $id);
        if ($banner) {
            return response()->json([
                'message' => 'Banner actualizado correctamente',
                'banner' => $banner
            ], 200);
        } else {
            return response()->json(['message' => 'Error al actualizar el banner'], 500);
        }
    }

    public function destroy(string $id)
    {
        $result = $this->bannerService->destroy($id);

        if ($result) {
            return response()->json(['message' => 'Banner eliminado correctamente'], 200);
        } else {
            return response()->json(['message' => 'Error al eliminar el banner'], 500);
        }
    }
}
