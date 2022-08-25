<div class="modal fade modal-edit-attribute" id="modal-edit-attribute" tabindex="-1" role="dialog" aria-labelledby="editAttribute" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editAttribute">{!! ucfirst(trans('textos.atributo_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route($action) }}" id="frmAddItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="id" id="paId">
                    @if($serial_id)
                        <input type="hidden" name="product_serial_id" value="{{ $serial_id }}">
                    @endif

					<div class="form-group row">
						{{-- Valor --}}
                    	<label for="value" class="col-12">{!! ucfirst(trans('textos.valor')) !!}</label>

                        {{-- Booleano --}}
                        <div class="col-12 dataType" id="type-boolean">
                            <label class="custom-chk-rdb pt-2">
                                <input class="rdb-access" type="radio" name="value_boolean" id="rdbTrue" value="1">
                                <span class="rdb" style="top: 5px;"></span>
                                {!! ucfirst(trans('textos.verdadero')) !!}
                            </label>    

                            <label class="custom-chk-rdb pt-2 ml-5">
                                <input class="rdb-access" type="radio" name="value_boolean" id="rdbFalse" value="0">
                                <span class="rdb" style="top: 5px;"></span>
                                {!! ucfirst(trans('textos.falso')) !!}
                            </label>
                        </div>

                        {{-- Texto --}}
                        <div class="col-12 dataType" id="type-textarea">
                            <x-input-textarea name="value_textarea" class="editor" id="paValueTextarea"/>
                        </div>

                        {{-- Fecha --}}
                        <div class="col-4 dataType" id="type-datepicker">
                            <x-date-picker name="value_datepicker" id="paValueDatepicker" :format="$languages[config('app.locale')][4]"/>
                        </div>

                        {{-- Opciones --}}
                        <div class="col-12 dataType" id="type-select">
                            <x-select name="value_select[]" class="select2" id="paValueSelect" :options="[]" multiple/>
                        </div>

                        {{-- Color --}}
                        <div class="col-12 dataType" id="type-color">
                            <div class="row" id="paValueColor">
                                
                            </div>
                        </div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

