@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('timetable-items.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_conceptos') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('timetable-items.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					@include('admin.timetable_items.partials.form', ['item' => false])

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Opción de salida exclusiva:
	$('#type').on('change', function(){
		var type = $(this).val();
		if(type == 1){
			$('.addOut').fadeIn();	
		}else{
			$('.addOut').fadeOut();	
		}
	});
});
</script>
@endpush

@endsection