<div class="row">
	<div class="col-12 mb-4">
    	<form method="post" autocomplete="off" action="{{ route('product-attributes.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
			{{ csrf_field() }}  
			<input type="hidden" name="product_id" value="{{ $product->id }}">

			<div class="form-group row">
				{{-- Atributos --}}
				<label for="attributes" class="col-lg-2 col-form-label">
                	{!! ucfirst(trans('textos.atributos')) !!}*
                </label>
                <div class="col-lg-7">
                	<x-select name="attributes[]" class="select2" :options="$attributes" multiple="true" required/>

                	<small class="text-warning">{{ trans('textos.atributos_vincula_producto') }}</small>
                </div>

				<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}" inline="true"/>
			</div>
		</form>  
    </div>

    <div class="col-12">
    	<div class="row">
			<div class="table-responsive">
				<table class="table table-hover dtabla" id="tblProductAttributes">
					<thead>
						<tr>
							<th>{!! ucfirst(trans('textos.atributo')) !!}</th>
							<th>{!! ucfirst(trans('textos.tipo_dato')) !!}</th>
							<th>{!! ucfirst(trans('textos.valor')) !!}</th>
							<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>    
    </div>
</div>