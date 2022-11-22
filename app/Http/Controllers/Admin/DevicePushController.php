<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DevicePushResource;
use App\Models\DevicePush;
use Illuminate\Http\Request;

class DevicePushController extends Controller
{
    public function store(Request $request)
    {
        if (isset($request->token)) {
            $devicePush = DevicePush::updateOrCreate(
                ['user_id' => $request->user_id],
                ['token' => $request->token,'signal'=>true],
            );

            return new DevicePushResource($devicePush);
        }

        return response()->noContent();
    }
}
