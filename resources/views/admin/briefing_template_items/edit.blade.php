@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a items --}}
		@can('briefing-templates.edit')
			<a href="{{ route('briefing-templates.edit', [$item->template_id, 'items']) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_plantilla') !!}
			</a>
		@endcan

		{{-- Eliminar --}}
		<form method="POST" action="{{ route('briefing-template-items.destroy', $item) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    	<input name="_method" type="hidden" value="DELETE">
	    	{{ csrf_field() }}
	    	<button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.item_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-3">
					{{ ucfirst(trans('textos.item_editar')) }} 
					<u>{{ ucfirst($item->name) }}</u>
					- {{ trans('textos.plantilla') }}
					<u>{{ ucfirst($template->name) }}</u>

					@if(!$item->recid)
						<small class="ml-4 text-warning">{{ ucfirst(trans('textos.bloque')) }}</small>
					@endif
				</h3>

				{{-- Info --}}
				<div class="mb-4">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $item->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $item->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('briefing-template-items.update', $item) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
                    	{{-- Item --}}
               			<label for="name" class="col-md-2 col-form-label">Item *</label>
		                <div class="col-lg-6">
		                   	<x-input type="text" name="name" autocomplete="off" maxlength="255" required value="{{ $item->name }}"/>
                    	</div>
                        
                        {{-- Estado --}}
               			<label for="status" class="col-md-2 col-form-label">{{ ucfirst(trans('textos.estado')) }}</label>
                    	<div class="col-md-1">
            				<label class="custom-chk-rdb" rel="tooltip">
    							<input type="checkbox" name="status" @if($item->status == 1) checked @endif>
    							<span class="chk" style="top: 5px;"></span>
							</label>
                    	</div>
                    </div>

                    @if($item->recid)
	                    <div class="form-group row">
	                    	{{-- Bloque --}}
	                    	<label for="recid" class="col-md-2 col-form-label">{{ ucfirst(trans('textos.bloque')) }}</label>
	                        <div class="col-lg-6">
	                    		<x-select name="recid" class="select2" label="bloque_selec" :options="$blocks" value="{{ $item->recid }}"/>
	                    	</div>
	                    </div>
                    @endif

                    <div class="form-group row">
                    	{{-- Observaciones --}}
                        <label for="observations" class="col-md-2 col-form-label">{{ ucfirst(trans('textos.observaciones')) }}</label>
                        <div class="col-md-10">
                        	<x-input-textarea name="observations" class="editor" value="{!! $item->observations !!}"/>
                        </div>
                    </div>

                    <div class="form-group row">
						{{-- Cantidad --}}
						<label for="expected_qty" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.cantidad_prevista')) !!}</label>
		                <div class="col-md-4">
		                   	<x-input type="text" name="expected_qty" class="text-right set-decimals" autocomplete="off" maxlength="20" info="briefing-template-item-quantity" value="{{ $item->expected_qty }}"/>
						</div>

						{{-- Unidad --}}
						<label for="time_unit" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.unidad')) !!}</label>
		                <div class="col-md-4">
		                    <x-select name="time_unit" class="select2" label="unidad_selec" :options="$time_units" value="{{ $item->time_unit }}"/>
		                </div>
		            </div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection