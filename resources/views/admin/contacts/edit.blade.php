@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a contactos --}}
		<a href="{{ route('contacts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_contactos') !!}
		</a>

		{{-- Nueva nota --}}
		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-comment"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.nota_nueva')) !!}</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">
					{{ trans('textos.contacto') }} <u>{{ ucwords($user->name) }} {{ ucwords($user->surname) }}</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $contact->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $contact->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
			  		<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-datos-personales" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.datos_personales')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'comments') active @endif">
			    		<a class="nav-link @if($tab == 'comments') active @endif" href="#tab-comments" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.bitacora')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'schedule') active @endif">
			    		<a class="nav-link @if($tab == 'schedule') active @endif" href="#tab-schedule" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.agenda')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'docs') active @endif">
			    		<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.documentos')) !!}</a>
			  		</li>
			  	</ul>

			  	{{-- Panels --}}
				<div class="tab-content pt-5">
					{{-- Datos personales: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-datos-personales">
						@include('admin.contacts.partials.contact-info', ['user' => $user])	
					</div>	

					{{-- Diario --}}
					<div class="tab-pane fade @if($tab == 'comments') show active @endif" role="tabpanel" id="tab-comments">
						@include('admin.contacts.partials.contact-comments', ['user' => $user])	
					</div>

					{{-- Agenda --}}
					<div class="tab-pane fade @if($tab == 'schedule') show active @endif" role="tabpanel" id="tab-schedule">
						@include('admin.contacts.partials.contact-schedule')	
					</div>

					{{-- Documentos --}}
					<div class="tab-pane fade @if($tab == 'docs') show active @endif" role="tabpanel" id="tab-docs">
						@include('admin.contacts.partials.contact-docs')	
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>

{{-- Modals: --}}
{{-- Add comment --}}
@include('admin.contacts.partials.modal-add-comment')

{{-- Edit comment --}}
@include('admin.contacts.partials.modal-edit-comment')

@push('scripts')
<script>
$(function(){
	//Modal editar comentario:
	$(document).on('click', '.btnEditComment', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var $row = $(this).closest('tr');
		var comment = $row.find('.contact-comment').html();
		var category = $row.find('.contact-category').data('category');
		//var tags = $row.find('.contact-tags').data('tags');

		var tagsData = $row.find('.contact-tags').attr('data-tags');
		try {
		    var tags = JSON.parse(tagsData);
		} catch(e) {
		    console.error('Error al parsear tags:', e);
		    var tags = [];
		}

		$('#commentId').val(id);
		$('#editComment').trumbowyg('html', comment);

		var arr = [category];
		$('#editCategory').val(arr);
        $('#editCategory').trigger('change');

        var selectize = $('#editTags')[0].selectize;

	    // Limpiar el input antes de añadir nuevos tags
	    selectize.clearOptions();
	    selectize.clear();

	    // Añadir cada tag
	    tags.forEach(function(tag){
	        selectize.addOption({ value: tag, text: tag }); // por si no existe aún
	        selectize.addItem(tag);
	    });
	});
});
</script>
@endpush

@endsection