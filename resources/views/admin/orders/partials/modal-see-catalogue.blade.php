<div class="modal fade modal-see-catalogue" id="modal-see-catalogue" tabindex="-1" role="dialog" aria-labelledby="seeCatalogue" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="seeCatalogue">{!! ucfirst(trans('textos.catalogo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	{{-- Componente Products Grid --}}
				@livewire('products-grid', ['currency' => $currency, 'order_' => true, 'company' => false, 'customer' => $order->customer_id])
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	// Catálogo productos.
    $(document).on('click', '.btnSeeCatalogue', function(e){
        e.preventDefault();
    });

    // Añadir producto desde el catálogo.
    $(document).on('click', '.btnAddProduct', function(e){
        e.preventDefault();
        var product = [$(this).data('product')];
        $('#productId').val(product);
        $('#productId').trigger('change');
        $('#modal-see-catalogue').modal('toggle');
    });
});
</script>
@endpush