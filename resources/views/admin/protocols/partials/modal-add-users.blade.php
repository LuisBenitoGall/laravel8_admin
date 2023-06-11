<div class="modal fade modal-add-users" id="modal-add-users" tabindex="-1" role="dialog" aria-labelledby="addUser" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addUser">{!! ucfirst(trans('textos.usuarios_agregar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning mb-4">Selecciona los usuarios a los que concierne este protocolo. Puedes seleccionarlos todos, individualmente o por grupos.</p>

            	<form method="post" autocomplete="off" action="{{ route('protocol-users.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="protocol" value="{{ $protocol->id }}">

                    <div class="form-group row">
                        {{-- Seleccionar todos --}}
                        <label for="all_users" class="col-lg-3">{{ ucfirst(trans('textos.todos_selec')) }}</label>    
                        <div class="col-9 sinpadding-left">
                            <label class="custom-chk-rdb" rel="tooltip">
                                <input type="checkbox" name="all_users">
                                <span class="chk"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Usuarios --}}
                    	<label for="users" class="col-12">{{ ucfirst(trans('textos.usuarios')) }}</label>
                    	<div class="col-12">
                    		<x-select name="users[]" class="select2" :options="$users" multiple/>
                    	</div>
                    </div>

                    <div class="form-group row">
                        {{-- Grupos --}}
                        <label for="groups" class="col-12">{{ ucfirst(trans('textos.grupos')) }}</label>
                        <div class="col-12">
                            <x-select name="groups[]" class="select2" :options="$groups" multiple/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>