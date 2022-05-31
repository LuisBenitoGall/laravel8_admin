@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado --}}
		<a href="{{ route('procedures.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_procedimientos') !!}
		</a>

		{{-- Nuevo procedimiento --}}
		@can('procedures.create')
    		<a href="{{ route('procedures.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.procedimiento_nuevo')) !!}
    		</a>
        @endcan

        {{-- Galería de archivos --}}
		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-gallery">
			<i class="fa fa-image"></i> {!! ucfirst(trans('textos.galeria_archivos')) !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.procedimiento')) !!} 
					<u>{{ $procedure->name }}</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $procedure->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $procedure->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Creado por --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.creado_por')) !!}: 
						<b>
							{{ $author->name }} {{ $author->surname }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.informacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'revisions') active @endif">
			  			<a class="nav-link @if($tab == 'revisions') active @endif" href="#tab-revisions" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.revisiones')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
			  		{{-- Info --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.procedures.partials.procedure-info')	
					</div>

					{{-- Revisions --}}
					<div class="tab-pane fade @if($tab == 'revisions') show active @endif" role="tabpanel" id="tab-revisions">
						@include('admin.procedures.partials.procedure-revisions')	
					</div>
			  	</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Galería de archivos --}}
@include('admin.files.partials.modal-gallery')

@push('scripts')
<script>
$(function(){
	//Revisiones:
	var revisions = {!! json_encode($revisions) !!};

	//Selección de revisión:
	$('.rev').on('click', function(){
		var rev = $(this).data('rev');
		$('.rev').removeClass('bold');
		$(this).addClass('bold');
		
		for(var i in revisions){
			if(revisions[i].id == rev){
				var url = '/admin/procedure-revisions/'+rev+'/print';
				$('#revisionPrint').attr("href", url).fadeIn();

				$('#revisionId').val(rev);
				$('#revisionContent').trumbowyg('html', revisions[i].content);
			}
		}
	});
});
</script>
@endpush
		
@endsection