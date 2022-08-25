<div class="modal fade modal-set-color" id="modal-set-color" tabindex="-1" role="dialog" aria-labelledby="setColor" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setColor">{!! ucfirst(trans('textos.color_definir')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="#" accept-charset="utf-8" enctype="multipart/form-data" role="form" id="frmSetColor">
                    {{ csrf_field() }}
                    <input type="hidden" name="color" id="colorCode">

                    <div class="form-group row">
                        {{-- Nombre color --}}
                        <label for="color" class="col-12">{!! ucfirst(trans('textos.color_nombre')) !!}</label>
                        <div class="col-8">
                            <div class="input-group m-b">
                                <div class="input-group-prepend">
                                    <span class="input-group-addon">&nbsp;</span>
                                </div>
                                
                                <x-input type="text" name="name_color" id="colorName" autocomplete="off" maxlength="50"/>
                            </div>
                        </div>
                    
                        <x-btn-submit label="guardar" inline="true"/>  
                    </div> 
                </form>    
            </div>
        </div>
    </div>
</div>