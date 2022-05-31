<div class="modal fade modal-add-member" id="modal-add-member-provider" tabindex="-1" role="dialog" aria-labelledby="addMember" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addMember">{!! ucfirst(trans('textos.miembro_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('budget-orders.add-member', $shared) }}" id="frmSaveChangeRound" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="environment" id="myEnvironment">

                    <div class="form-group row">
                    	{{-- Usuario --}}
                    	<label for="member" class="col-12">{{ ucfirst(trans('textos.usuario')) }}</label>
                    	<div class="col-12">
                    		<x-select name="member" id="member_id" class="select2" label="usuario_selec" :options="[]"/>
                    	</div>
                    </div>
                    
                    <x-btn-submit name="submit_member" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>