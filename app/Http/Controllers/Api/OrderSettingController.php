<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderSettingResource;
use App\Http\Resources\OrderSettingCollection;
use App\Http\Requests\OrderSettingStoreRequest;
use App\Http\Requests\OrderSettingUpdateRequest;

class OrderSettingController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', OrderSetting::class);

        $search = $request->get('search', '');

        $orderSettings = OrderSetting::search($search)
            ->latest()
            ->paginate();

        return new OrderSettingCollection($orderSettings);
    }

    /**
     * @param \App\Http\Requests\OrderSettingStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderSettingStoreRequest $request)
    {
        // $this->authorize('create', OrderSetting::class);

        $validated = $request->validated();

        $orderSetting = OrderSetting::create($validated);

        return new OrderSettingResource($orderSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\OrderSetting $orderSetting
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, OrderSetting $orderSetting)
    {
        // $this->authorize('view', $orderSetting);

        return new OrderSettingResource($orderSetting);
    }

    /**
     * @param \App\Http\Requests\OrderSettingUpdateRequest $request
     * @param \App\Models\OrderSetting $orderSetting
     * @return \Illuminate\Http\Response
     */
    public function update(
        OrderSettingUpdateRequest $request,
        OrderSetting $orderSetting
    ) {
        // $this->authorize('update', $orderSetting);

        $validated = $request->validated();

        $orderSetting->update($validated);

        return new OrderSettingResource($orderSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\OrderSetting $orderSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, OrderSetting $orderSetting)
    {
        //$this->authorize('delete', $orderSetting);

        $orderSetting->delete();

        return response()->noContent();
    }
}
