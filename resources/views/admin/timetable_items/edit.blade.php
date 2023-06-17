@extends('admin.partials.layout')

@section('content')

@php
//Data para condiciones de concepto obligatorio:
$fields = [
    'work_calendars' => [
        'date' => ucfirst(trans('textos.fecha')),
        'hours' => ucfirst(trans('textos.horas')) 
    ],
    'day_types' => [
    	'subtract_required_concept' => ucfirst(trans('textos.descuenta_conceptos_obligatorios'))	
    ]
];

//Reglas de operación:
$rules = [
    '='  => 'igual',
    '!=' => 'distinto_de',
    '>'  => 'mayor_que',
    '>=' => 'mayor_igual',
    '<'  => 'menor_que',
    '<=' => 'menor_igual'
];
@endphp

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('timetable-items.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_conceptos') !!}
		</a>

		{{-- Nuevo concepto --}}
		@can('timetable-items.create')
    		<a href="{{ route('timetable-items.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.concepto_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('timetable-items.update', $item) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					@include('admin.timetable_items.partials.form', ['item' => $item])

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Condiciones --}}
@if($item)
    @include('admin.timetable_items.partials.modal-required-conditions')
@endif

@push('scripts')
<script>
var fields = {!! json_encode($fields) !!}
console.log(fields);
$(function(){
    //Selector de tabla:
    $('#ruleTabla').on('change', function(e){
        var tabla = e.target.value;
        $("#ruleCampo").empty();
        $("#ruleCampo").append('<option value="">'+txtOpcionSelect+'</option>');
        for(var i in fields[tabla]){
            $("#ruleCampo").append('<option value="'+i+'">'+fields[tabla][i]+'</option>');
        }
    });

    //Editar condición:
    $('.btnEditRule').on('click', function(){
    	var rule = $(this).data('rule').split('|');

    	//Rellenamos las opciones del select Campo en virtud de la tabla definida:
    	for(var i in fields[rule[0]]){
    		var selected = i == rule[1]? 'selected':'';
            $("#ruleCampo").append('<option value="'+i+'" '+selected+'>'+fields[rule[0]][i]+'</option>');
        }

    	$('#ruleTabla').val(rule[0]);
    	$('#ruleRegla').val(rule[2]);
    	$('#ruleValor').val(rule[3]);
    });
});
</script>
@endpush

@endsection