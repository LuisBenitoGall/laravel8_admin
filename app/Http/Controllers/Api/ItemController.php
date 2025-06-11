<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Models:
use App\Models\Item;

//Resources:
use App\Http\Resources\ItemCollection;

/**
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST, 
 *     description="Servidor local"
 * )
 *
 * @OA\Tag(
 *     name="Items de pedidos",
 *     description="Endpoints para items por pedidos."
 * )
 */
class ItemController extends Controller{
    /**
     * 
     */
    
    /**
     * @OA\Get(
     *     path="/items/providers",
     *     summary="Obtener líneas de pedidos filtradas por uno o más company_id",
     *     tags={"Líneas de pedido"},
     *
     *     @OA\Parameter(
     *         name="company_ids",
     *         in="query",
     *         description="IDs de las compañías, separados por comas. Ejemplo: `?company_ids=1,2,3`",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer", format="int64", example=4)
     *         ),
     *         style="form",
     *         explode=false
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Fecha de inicio para filtrar (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-02-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Fecha de fin para filtrar (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-06-30")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Listado de items devuelto correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Items")
     *         )
     *     ),
     *     @OA\Response(response=404, description="No se encontraron líneas para los company_id indicados")
     * )
     */
    public function index(Request $request){
        //Empresas:
        $companyIds = array_filter(explode(',', $request->query('company_ids')));

        $start_date = $request->start_date? $request->start_date:'2022-01-01';
        $end_date = $request->end_date? $request->end_date:date('Y-m-d');

        $items = Item::select(DB::raw("CONCAT(pm.name,' ',pm.surname) AS provider_manager"),
            DB::raw("CONCAT(co.name,' ',co.surname) AS commercial"),
            DB::raw("CONCAT(cm.name,' ',cm.surname) AS customer_manager"),
            DB::raw("CONCAT(fcm.name,' ',fcm.surname) AS final_customer_manager"),
            DB::raw("items.qty * items.price AS subtotal"),
            'items.id', 'p.name AS provider', 'c.name AS customer', 'fc.name AS final_customer', 'orders.is_order', 'orders.is_budget', 'orders.budget_num', 'orders.order_num', 'orders.name AS order_title', 'orders.created_at', 'orders.accepted_date', 'orders.delivery_date', 'products.name AS product', 'items.concept', 'items.qty', 'items.price', 'items.iva', 'products.billable_percentage', 'pcc.name AS provider_cost_center')
        ->join('orders', 'items.order_id', '=', 'orders.id')
        ->join('companies AS p', 'orders.provider_id', '=', 'p.id')
        ->join('companies AS c', 'orders.customer_id', '=', 'c.id')
        ->leftJoin('companies AS fc', 'orders.final_customer_id', '=', 'fc.id')
        ->leftJoin('users AS pm', 'orders.provider_manager_id', '=', 'pm.id')
        ->leftJoin('users AS co', 'orders.commercial_id', '=', 'co.id')
        ->leftJoin('users AS cm', 'orders.customer_manager_id', '=', 'cm.id')
        ->leftJoin('users AS fcm', 'orders.final_customer_manager_id', '=', 'fcm.id')
        ->leftJoin('products', 'items.product_id', '=', 'products.id')
        ->leftJoin('cost_centers AS pcc', 'items.provider_cost_center_id', '=', 'pcc.id')
        ->whereNotNull('orders.is_order')
        ->whereNotIn('orders.order_status', [30,70])
        ->whereIn('orders.provider_id', $companyIds)
        ->when($start_date && $end_date, function($q) use($start_date, $end_date){
            return $q->whereBetween('orders.created_at', [$start_date, $end_date]);
        })
        ->get();
        
        return new ItemCollection($items);
    }

}
