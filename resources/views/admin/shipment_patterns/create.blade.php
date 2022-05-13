@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('shipment-patterns.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_patrones') !!}
		</a>
	</div>
</div>

@include('admin.partials.form-create-patterns', ['ruta' => 'shipment-patterns.store', 'info_text' => 'albaranes_numeracion_texto', 'yearly_reset' => true])

@endsection