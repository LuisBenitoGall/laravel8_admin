<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PurchasePattern;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderCollection;

class PurchasePatternOrdersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PurchasePattern $purchasePattern
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, PurchasePattern $purchasePattern)
    {
        // $this->authorize('view', $purchasePattern);

        $search = $request->get('search', '');

        $orders = $purchasePattern
            ->orders()
            ->search($search)
            ->latest()
            ->paginate();

        return new OrderCollection($orders);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PurchasePattern $purchasePattern
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, PurchasePattern $purchasePattern)
    {
        //$this->authorize('create', Order::class);

        $validated = $request->validate([
            'is_budget' => ['required', 'boolean'],
            'is_order' => ['required', 'boolean'],
            'name' => ['nullable', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'recid' => ['nullable', 'max:255'],
            'from_customer' => ['nullable', 'boolean'],
            'budget_num' => ['nullable', 'max:255', 'string'],
            'budget_num_cardinal' => ['nullable', 'max:255', 'string'],
            'budget_pattern_id' => ['nullable', 'exists:budget_patterns,id'],
            'budget_old_num' => ['nullable', 'max:255', 'string'],
            'modality' => ['nullable', 'numeric'],
            'accepted_date' => ['nullable', 'date'],
            'accepted_by' => ['nullable', 'exists:users,id'],
            'budget_status' => ['nullable', 'numeric'],
            'description' => ['nullable', 'max:255', 'string'],
            'comments' => ['nullable', 'max:255', 'string'],
            'budget_file_name' => ['nullable', 'max:255', 'string'],
            'budget_template_id' => ['nullable', 'exists:budget_settings,id'],
            'budget_template' => ['nullable', 'max:255', 'string'],
            'order_parent' => ['nullable', 'max:255'],
            'is_group' => ['required', 'boolean'],
            'order_num' => ['nullable', 'max:255', 'string'],
            'order_num_cardinal' => ['nullable', 'max:255', 'string'],
            'order_pattern_id' => ['nullable', 'exists:order_patterns,id'],
            'order_old_num' => ['nullable', 'max:255', 'string'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'briefing' => ['nullable', 'max:255', 'string'],
            'observations' => ['nullable', 'max:255', 'string'],
            'observations_customer' => ['nullable', 'max:255', 'string'],
            'order_template_id' => ['nullable', 'exists:order_settings,id'],
            'order_template' => ['nullable', 'max:255', 'string'],
            'order_status' => ['nullable', 'numeric'],
            'pucharse_num' => ['nullable', 'max:255', 'string'],
            'pucharse_num_cardinal' => ['nullable', 'max:255', 'string'],
            'pucharse_old_num' => ['nullable', 'max:255', 'string'],
            'pucharse_doc' => ['nullable', 'max:255', 'string'],
            'pay_order' => ['nullable', 'max:255', 'string'],
            'pay_order_received' => ['nullable', 'date'],
            'customer_id' => ['nullable', 'exists:companies,id'],
            'customer_manager_id' => ['nullable', 'exists:users,id'],
            'customer_workplace_id' => ['nullable', 'exists:workplaces,id'],
            'customer_cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'customer_business_area_id' => [
                'nullable',
                'exists:business_areas,id',
            ],
            'customer_order' => ['nullable', 'max:255', 'string'],
            'customer_order_doc' => ['nullable', 'max:255', 'string'],
            'customer_team' => ['nullable', 'max:255', 'string'],
            'customer_products' => ['nullable', 'max:255', 'string'],
            'customer_items' => ['nullable', 'max:255', 'string'],
            'customer_confirmation' => ['nullable', 'date'],
            'confirmed_by' => ['nullable', 'exists:users,id'],
            'confirm_code' => ['nullable', 'max:255', 'string'],
            'confirm_code_date' => ['nullable', 'date'],
            'provider_id' => ['nullable', 'exists:companies,id'],
            'provider_manager_id' => ['nullable', 'exists:users,id'],
            'provider_workplace_id' => ['nullable', 'exists:workplaces,id'],
            'provider_cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'provider_business_area_id' => [
                'nullable',
                'exists:business_areas,id',
            ],
            'provider_team' => ['nullable', 'max:255', 'string'],
            'final_customer_id' => ['nullable', 'exists:companies,id'],
            'final_customer_manager_id' => ['nullable', 'exists:users,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'discount' => ['nullable', 'numeric'],
            'discount_amount' => ['nullable', 'numeric'],
            'bag_amount' => ['required', 'numeric'],
            'invoiced' => ['required', 'boolean'],
            'prepare_delivery' => ['required', 'numeric'],
            'old_budget_id' => ['nullable', 'max:255'],
            'old_order_id' => ['nullable', 'max:255'],
        ]);

        $order = $purchasePattern->orders()->create($validated);

        return new OrderResource($order);
    }
}
