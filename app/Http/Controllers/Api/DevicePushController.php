<?php

namespace App\Http\Controllers\Api;

use App\Models\DevicePush;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DevicePushResource;
use App\Http\Resources\DevicePushCollection;
use App\Http\Requests\DevicePushStoreRequest;
use App\Http\Requests\DevicePushUpdateRequest;

class DevicePushController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @return \App\Http\Resources\DevicePushCollection
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', DevicePush::class);

        //$search = $request->get('search', '');

        $devicePushes = DevicePush::latest()->paginate();

        return new DevicePushCollection($devicePushes);
    }

    /**
     * @param \App\Http\Requests\DevicePushStoreRequest $request
     * @return \App\Http\Resources\DevicePushResource
     */
    public function store(Request $request)
    {
        // $this->authorize('create', DevicePush::class);
        if (isset($request->token)) {
            $devicePush = DevicePush::updateOrCreate(
                ['user_id' => $request->user_id],
                ['token' => $request->token]
            );

            return new DevicePushResource($devicePush);
        }

        return response()->noContent();


    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DevicePush $devicePush
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, DevicePush $devicePush)
    {
        // $this->authorize('view', $devicePush);

        return new DevicePushResource($devicePush);
    }

    /**
     * @param \App\Http\Requests\DevicePushUpdateRequest $request
     * @param \App\Models\DevicePush $devicePush
     * @return \App\Http\Resources\DevicePushResource
     */
    public function update(
        DevicePushUpdateRequest $request,
        DevicePush              $devicePush
    )
    {
        // $this->authorize('update', $devicePush);

        $validated = $request->validated();

        $devicePush->update($validated);

        return new DevicePushResource($devicePush);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DevicePush $devicePush
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, DevicePush $devicePush)
    {
        //$this->authorize('delete', $devicePush);

        $devicePush->delete();

        return response()->noContent();
    }
}
