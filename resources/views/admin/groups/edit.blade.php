@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('groups.index')
			<a href="{{ route('groups.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_grupos') !!}
			</a>
		@endcan

		<a href="#" class="btn btn-info" rel="tooltip" title="{!! ucfirst(trans('textos.miembro_nuevo')) !!}" data-toggle="modal" data-target="#modal-new-member">
			<i class="fa fa-user-plus"></i>
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{{ ucfirst(trans('textos.grupo')) }} <u>{{ ucfirst($group->name) }}</u></h3>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
			  		<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-grupo" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.grupo_info')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'members') active @endif">
			    		<a class="nav-link @if($tab == 'members') active @endif" href="#tab-miembros" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.miembros')) !!}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-5">
					{{-- Grupo: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-grupo">
						@include('admin.groups.partials.group-info')	
					</div>

					{{-- Miembros: --}}
					<div class="tab-pane fade @if($tab == 'members') show active @endif" role="tabpanel" id="tab-miembros">
						@include('admin.groups.partials.group-members')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Add member: --}}
@include('admin.group_members.partials.modal-add-member')

{{-- Edit member: --}}
@include('admin.group_members.partials.modal-edit-member')

@push('scripts')
<script>
$(function(){
	//Editar miembro:
	$(document).on('click', '.btnEditMember', function(){
		var id = $(this).data('id');
		var member = $(this).data('member');
		var rol = $(this).data('rol');

		$('#memberGroupId').val(id);
		$('#memberGroupRol').val(rol);
		$('#memberGroupTitle u').text(member);
	});

});
</script>
@endpush

@endsection