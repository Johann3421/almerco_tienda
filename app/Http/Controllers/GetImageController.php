<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GetImageController extends Controller
{
    public function getImage(Request $request)
    {
        $uri = 'public/'.$request->uri;

        $extention = pathinfo($uri, PATHINFO_EXTENSION);

        try {
            if(Storage::exists($uri)){
                $file = Storage::get($uri);

                $headers = [
                    'Content-Type' => 'image/' . $extention,
                ];

                return new Response($file, 200, $headers);
            }else{
                return response()->json(['message' => 'No se encontró la imagen'], 404);
            }
        } catch (\Throwable $th) {
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $th->getMessage());
        }
    }
}
