@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('product-patterns.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_patrones') !!}
		</a>
	</div>
</div>

@include('admin.partials.form-create-patterns', ['ruta' => 'product-patterns.store', 'info_text' => 'producto_patron_texto', 'yearly_reset' => false])

@endsection