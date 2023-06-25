@extends('admin.partials.layout')

@section('content')

@if(isset($serial) && isset($product))
	<input type="hidden" id="serialId" value="{{ $serial->id }}">

	<div class="row mb-3">
		{{-- Acciones: --}}
		<div class="col-12">
			<a href="{{ route('products.edit', [$product->id, 'serials']) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_producto') !!}
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-body">
					<h3 class="mb-3">
						{!! ucfirst(trans('textos.producto')) !!}
						<u>
							<b>{{ $product->name }}</b> - 
							{!! ucfirst(trans('textos.numero_serie')) !!} 
							<b>{{ $serial->serial }}</b>
						</u>
					</h3>	

					{{-- Info --}}
					<div class="mb-3">
						{{-- Fecha alta --}}
						<label class="mr-5">
							{!! ucfirst(trans('textos.fecha_alta')) !!}: 
							<b>
								{{ $serial->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
							</b>
						</label>

						{{-- Fecha última actualización --}}
						<label class="mr-5">
							{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
							<b>
								{{ $serial->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
							</b>
						</label>
					</div>

					<div class="row">
						{{-- Column left --}}
						<div class="col-lg-3">
							<h4 class="mb-4">{{ trans('textos.caracteristicas') }}</h4>	

							@if($cards->count())
								@foreach($cards as $card)
									<p>
										<b>· {{ $card->name }}:</b> {!! $card->value !!}
									</p>
								@endforeach
							@else
								<p class="text-warning">{{ trans('textos.valores_no_definidos') }}</p>
							@endif
						</div>

						{{-- Column right --}}
						<div class="col-lg-9">
							<div class="table-responsive">
								<table class="table table-hover dtabla" id="tblProductSerialAttributes">
									<thead>
										<tr>
											<th>{{ ucfirst(trans('textos.atributo')) }}</th>
											<th>{{ ucfirst(trans('textos.tipo_dato')) }}</th>
											<th>{{ ucfirst(trans('textos.descripcion')) }}</th>
											<th>{{ ucfirst(trans('textos.valor')) }}</th>
											<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
										</tr>
									</thead>
								</table>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Modals: --}}
	{{-- Edit attribute: --}}
	@include('admin.product_attributes.partials.modal-edit-attribute', ['action' => 'products-serials-cards.update', 'serial_id' => $serial->id])
@endif

@endsection