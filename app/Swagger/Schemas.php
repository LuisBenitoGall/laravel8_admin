<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Items",
 *     type="object",
 *     title="Items",
 *     description="Esquema de líneas de pedido por pedidos y proveedor",
 *     @OA\Property(property="id", type="integer", format="int64", example=1, description="ID único de la línea de pedido"),
 *     @OA\Property(property="provider", type="string", example="ADN COM", description="Nombre del proveedor"),
 *     @OA\Property(property="customer", type="string", example="ACME", description="Nombre del cliente"),
 *     @OA\Property(property="final_customer", type="string", example="ACME", description="Nombre del cliente final"),
 *     @OA\Property(property="is_order", type="string", example="", description="Línea de pedido"),
 *     @OA\Property(property="is_budget", type="string", example="", description="Línea de presupuesto"),
 *     @OA\Property(property="budget_num", type="string", example="Nº presupuesto", description="Número de presupuesto"),
 *     @OA\Property(property="order_num", type="string", example="Nº pedido", description="Número de pedido"),
 *     @OA\Property(property="order_title", type="string", example="Título del pedido", description="Título del pedido"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-01T12:00:00Z", description="Fecha de creación"),
 *     @OA\Property(property="provider_manager", type="string", example="Nombre + Apellidos", description="Manager del proveedor"),
 *     @OA\Property(property="commercial", type="string", example="Nombre + Apellidos", description="Comercial"),
 *     @OA\Property(property="customer_manager", type="string", example="Nombre + Apellidos", description="Manager del cliente"),
 *     @OA\Property(property="final_customer_manager", type="string", example="Nombre + Apellidos", description="Manager del cliente final"),
 *     @OA\Property(property="product", type="string", example="Producto", description="Nombre del producto"),
 *     @OA\Property(property="concept", type="string", example="Concepto", description="Concepto del producto"),
 *     @OA\Property(property="qty", type="number", format="float", description="Cantidad pedida"),
 *     @OA\Property(property="price", type="number", format="float", example=19.99, description="Precio unitario en el momento del pedido"),
 *     @OA\Property(property="iva", type="number", format="float", description="IVA"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=59.97, description="Cantidad * Precio unitario"),
 *     @OA\Property(property="billable_percentage", type="number", format="float", example="60%", description="Porcentaje facturable"),
 *     @OA\Property(property="provider_cost_center", type="string", example="Centro coste proveedor", description="Centro coste proveedor")
 * )
 *
 * @OA\Schema(
 *     schema="Producto",
 *     type="object",
 *     title="Producto",
 *     description="Esquema de un producto perteneciente a una compañía",
 *     @OA\Property(property="id", type="integer", example=123),
 *     @OA\Property(property="company_id", type="integer", example=45, description="ID de la compañía a la que pertenece"),
 *     @OA\Property(property="name", type="string", example="Producto A", description="Nombre del producto"),
 *     @OA\Property(property="slug", type="string", example="producto-a", description="Slug del producto"),
 *     @OA\Property(property="ref", type="string", example="REF123", description="Referencia interna"),
 *     @OA\Property(property="manual_ref", type="string", example="MANUAL-REF", description="Referencia manual"),
 *     @OA\Property(property="translations", type="string", example="Traducciones disponibles", description="Datos de traducciones"),
 *     @OA\Property(property="tags", type="string", example="etiqueta1,etiqueta2", description="Etiquetas asociadas"),
 *     @OA\Property(property="description", type="string", example="Descripción breve del producto"),
 *     @OA\Property(property="long_description", type="string", example="Descripción detallada del producto"),
 *     @OA\Property(property="type", type="string", example="A", description="Tipo de producto"),
 *     @OA\Property(property="price", type="number", format="float", example=9.99),
 *     @OA\Property(property="calculated_by_percent", type="number", format="float", example=5.50, description="Precio calculado por porcentaje"),
 *     @OA\Property(property="iva", type="number", format="float", example=21.00, description="IVA aplicado"),
 *     @OA\Property(property="batch", type="boolean", example=true, description="Indica si se vende en lote"),
 *     @OA\Property(property="stock_management", type="boolean", example=true, description="Gestión de stock habilitada"),
 *     @OA\Property(property="on_sale", type="boolean", example=false, description="Indica si está en oferta"),
 *     @OA\Property(property="status", type="integer", example=1, description="Estado del producto"),
 *     @OA\Property(property="user_id", type="integer", example=5, description="ID del usuario que creó el producto"),
 *     @OA\Property(property="product_pattern_id", type="integer", example=2, description="ID del patrón de producto"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-15T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-20T14:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="ProductoCreateRequest",
 *     type="object",
 *     title="ProductoCreateRequest",
 *     required={"name","slug","price","batch","stock_management","on_sale","status","user_id","product_pattern_id"},
 *     @OA\Property(property="name", type="string", example="Producto A"),
 *     @OA\Property(property="slug", type="string", example="producto-a"),
 *     @OA\Property(property="ref", type="string", example="REF123"),
 *     @OA\Property(property="manual_ref", type="string", example="MANUAL-REF"),
 *     @OA\Property(property="translations", type="string", example="Traducciones disponibles"),
 *     @OA\Property(property="tags", type="string", example="etiqueta1,etiqueta2"),
 *     @OA\Property(property="description", type="string", example="Descripción breve del producto"),
 *     @OA\Property(property="long_description", type="string", example="Descripción detallada del producto"),
 *     @OA\Property(property="type", type="string", example="A"),
 *     @OA\Property(property="price", type="number", format="float", example=9.99),
 *     @OA\Property(property="calculated_by_percent", type="number", format="float", example=5.50),
 *     @OA\Property(property="iva", type="number", format="float", example=21.00),
 *     @OA\Property(property="batch", type="boolean", example=true),
 *     @OA\Property(property="stock_management", type="boolean", example=true),
 *     @OA\Property(property="on_sale", type="boolean", example=false),
 *     @OA\Property(property="status", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="product_pattern_id", type="integer", example=2)
 * )
 *
 * @OA\Schema(
 *     schema="ProductoUpdateRequest",
 *     type="object",
 *     title="ProductoUpdateRequest",
 *     @OA\Property(property="name", type="string", example="Producto A Modificado"),
 *     @OA\Property(property="slug", type="string", example="producto-a-modificado"),
 *     @OA\Property(property="ref", type="string", example="REF456"),
 *     @OA\Property(property="manual_ref", type="string", example="MANUAL-REF-2"),
 *     @OA\Property(property="translations", type="string", example="Traducciones actualizadas"),
 *     @OA\Property(property="tags", type="string", example="etiqueta3,etiqueta4"),
 *     @OA\Property(property="description", type="string", example="Descripción actualizada"),
 *     @OA\Property(property="long_description", type="string", example="Descripción detallada actualizada"),
 *     @OA\Property(property="type", type="string", example="B"),
 *     @OA\Property(property="price", type="number", format="float", example=12.50),
 *     @OA\Property(property="calculated_by_percent", type="number", format="float", example=7.00),
 *     @OA\Property(property="iva", type="number", format="float", example=18.00),
 *     @OA\Property(property="batch", type="boolean", example=false),
 *     @OA\Property(property="stock_management", type="boolean", example=false),
 *     @OA\Property(property="on_sale", type="boolean", example=true),
 *     @OA\Property(property="status", type="integer", example=2)
 * )
 */
class Schemas{
    // Este archivo sólo contiene los docblocks con @OA\Schema, no necesita código adicional.
}
