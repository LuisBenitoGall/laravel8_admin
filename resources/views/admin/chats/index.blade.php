@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('chat.create')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-chat-room"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.chat_sala_nueva')) !!}</a>
		@endcan

		@can('chat.edit')
			@if($rooms->count())
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-invite-users"><i class="fa fa-users"></i> {!! ucfirst(trans('textos.chat_sala_invitar_usuarios')) !!}</a>
			@endif
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">

				{{-- Componente Chat --}}
				@livewire('chat-component')

			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- New chat room --}}
<div class="modal fade modal-new-chat-room" id="modal-new-chat-room" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.chat_sala_nueva')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('chat-rooms.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- Nombre sala --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.sala')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" maxlength="191" required/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.estado')) !!}</label>
								<div class="col-lg-12 text-center">
									<label class="custom-chk-rdb">
						        		<input type="checkbox" name="status" checked>
						        		<span class="chk" style="top: 5px;"></span>
						    		</label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-12">
							<x-input-textarea name="description"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>

{{-- Invite users --}}
<div class="modal fade modal-invite-users" id="modal-invite-users" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.chat_sala_invitar_usuarios')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('chat-rooms.invite-users') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Salas --}}
						<label for="chat_room_id" class="col-lg-12">{!! ucfirst(trans('textos.salas')) !!} *</label>
						<div class="col-lg-12">
							@php
							$rooms_ = [];
							foreach($rooms as $room){
								$rooms_[$room->id] = $room->name;
							}
							@endphp

							<x-select name="chat_room_id" id="selectChatRoom" class="select2" label="chat_sala_selec" :options="$rooms_" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Usuarios --}}
						<label for="users" class="col-lg-12">{!! ucfirst(trans('textos.usuarios')) !!} *</label>
						<div class="col-lg-12">
							@php
							$users_ = [];
							@endphp

							<x-select name="users[]" id="chatRoomUsers" class="select2" :options="$users_" multiple disabled required/>
						</div>
					</div>
					
					<x-btn-submit name="submit_invite" label="{{ trans('textos.enviar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>

{{-- Pending invitations --}}
@if($pending_invitations->count())
	<div class="modal fade" id="modal-pending-invitations" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{!! ucfirst(trans('textos.invitaciones_pendientes')) !!}</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    	<span aria-hidden="true">&times;</span>
                	</button>
                </div>	

                <div class="modal-body">
                	<p class="text-warning">{{ trans('textos.invitaciones_pendientes_texto') }}</p>

                	<form method="post" action="{{ route('chat-room-users.accept-invitations') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}

						<div class="form-group row">
							@foreach($pending_invitations as $pi)
								<div class="col-4 mb-2">
									· <label style="line-height: 36px;">{{ $pi ->name }}</label>
								</div>

								<div class="col-8 mb-2 text-right">
									{{-- Aceptar --}}
									<x-radio name="chat_room_{{ $pi->id }}" value="1" style="margin-bottom: 0; margin-top: 7px;" title="aceptar"/>

									{{-- Rechazar --}}
									<x-radio name="chat_room_{{ $pi->id }}" value="0" style="margin-bottom: 0; margin-top: 7px;" title="rechazar"/>
								</div>
							@endforeach
						</div>

						<x-btn-submit name="submit_accept" label="{{ trans('textos.guardar') }}"/>
					</form>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
$(function(){
	//Selector de salas:
	$('#selectChatRoom').on('change', function(event){
		$('#chatRoomUsers').empty().val('');	

		$('#selectChatRoom option:selected').each(function(){
            var id = event.target.value;
            if(id == null || id == 0){
                $('#chatRoomUsers').attr('disabled','disabled');
            }else{
                $('#chatRoomUsers').removeAttr('disabled');
                $.get("/admin/chat-room-users/"+id+"/get-new-users", function(response, state){
                    for(i=0; i<response.length; i++){
                        $("#chatRoomUsers").append('<option value="'+response[i].id+'">'+response[i].name+'</option>');    
                    }
                });
            }
        });
	});

	//Modal de invitaciones pendientes:
	if($('#modal-pending-invitations').length > 0){
        $('#modal-pending-invitations').modal({
            //backdrop: 'static', 
            keyboard: false,
            toggle: true
        });    
    }
});
</script>
@endpush

@endsection