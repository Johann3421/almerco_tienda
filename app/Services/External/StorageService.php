<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Storage;

class StorageService
{
    public function store($ruta, $nombreArchivo, $archivo)
    {
        Storage::put('public/'. $ruta . '/' . $nombreArchivo, file_get_contents($archivo));

        return [
            'file_path' => $ruta,
            'file' => $nombreArchivo
        ];
    }


}
