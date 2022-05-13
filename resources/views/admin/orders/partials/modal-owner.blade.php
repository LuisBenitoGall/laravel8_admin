<div class="modal fade modal-edit-owner" id="modal-edit-owner" tabindex="-1" role="dialog" aria-labelledby="newOwner" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newOwner">{!! ucfirst(trans('textos.propietario_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route($ow_action) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $ow_id }}">

                    <div class="form-group row">
                        {{-- Propietarios --}}
                        <label for="owner" class="col-12">{!! ucfirst(trans('textos.propietario')) !!}*</label>
                        <div class="col-12">
                            @php
                            $owners_ = [];
                            @endphp

                            @foreach($owners as $owner)
                                @php
                                $owners_[$owner->id] = $owner->name.' '.$owner->surname;
                                @endphp
                            @endforeach

                            <x-select name="owner" class="select2" :options="$owners_" required label="propietario_selec"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save_owner" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>