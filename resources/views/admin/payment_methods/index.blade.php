@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
        @can('payment-methods.create')
		    <a href={{ route('payment-methods.create') }} class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.metodo_nuevo')) !!}</a>
        @endcan
    </div>
</div>

<div class="row">
    <div class="table-responsive">
    	<table class="table table-hover dtabla" id="tblPaymentMethods">
    		<thead>
    			<tr>
    				<th>{!! ucfirst(trans('textos.metodo')) !!}</th>
    				<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
    				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
    			</tr>
    		</thead>
    	</table>
    </div>
</div>

@endsection