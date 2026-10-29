@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a templates --}}
		@can('briefing-templates.index')
			<a href="{{ route('briefing-templates.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_plantillas') !!}
			</a>
		@endcan

		{{-- Nuevo template --}}
		@can('briefing-templates.create')
    		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-template">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.plantilla_nueva')) !!}
    		</a>
        @endcan

        {{-- Nuevo item --}}
        @can('briefing-templates.edit')
        	<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-item">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.item_nuevo')) !!}
			</a>
		@endcan

		{{-- Eliminar --}}
		<form method="POST" action="{{ route('briefing-templates.destroy', $template) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    	<input name="_method" type="hidden" value="DELETE">
	    	{{ csrf_field() }}
	    	<button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.plantilla_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-3">{{ ucfirst(trans('textos.plantilla_editar')) }} <u>{{ ucfirst($template->name) }}</u>
				</h3>

				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $template->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Creado por --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.creado_por')) !!}: 
						<b>
							{{ $template->createdBy->name }} {{ $template->createdBy->surname }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $template->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Actualizado por --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.actualizado_por')) !!}: 
						<b>
							{{ $template->updatedBy->name }} {{ $template->updatedBy->surname }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">	
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-template-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			    		<a class="nav-link @if($tab == 'items') active @endif" href="#tab-template-items" role="tab" data-toggle="tab">Items</a>
			  		</li>
			  	</ul>

			  	{{-- Panels --}}
			  	<div class="tab-content pt-5">
					{{-- Info --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-template-info">
						@include('admin.briefing_templates.partials.template-info')	
					</div>

					{{-- Items --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-template-items">
						@include('admin.briefing_templates.partials.template-items')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- New template: --}}
@include('admin.briefing_templates.partials.modal-new-template')

{{-- New item: --}}
@include('admin.briefing_templates.partials.modal-new-item')

@endsection