<?php

namespace App\Http\Controllers\API;

use App\Municipality;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class MunicipalityController extends Controller
{

    public function get($slug)
    {
        try {
            $municipality = Municipality::where('name', 'LIKE', "%{$slug}%")->firstOrFail();
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('api.municipality.notfound', [
                    'name' => $slug
                ])
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json($municipality);
    }

    public function getNotifications($slug)
    {
        try {
            $municipality = Municipality::where('name', $slug)->firstOrFail();
        } catch(ModelNotFoundException $e) {
            return response()->json([]);
        }

        return response()->json($municipality->notifications()->get());
    }

}