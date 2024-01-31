<div class="modal fade modal-settings" id="modal-settings" tabindex="-1" role="dialog" aria-labelledby="timetableSettings" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="timetableSettings">{!! ucfirst(trans('textos.horarios_configuracion')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('timetable-settings.update') }}" id="frmAddItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    <div class="form-group row">
                        {{-- Minutos descuento --}}
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <label for="minute_discount">{!! trans('textos.minutos_descuento') !!}</label>
                                </div>
                                <div class="col-12">
                                    @php
                                    $minute_discount = $settings? $settings->minute_discount:'';
                                    @endphp
                                    <x-input type="text" class="text-right" name="minute_discount" autocomplete="off" maxlength="4" value="{{ $minute_discount }}" info="timetable-minute-discount"/>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Minutos descuento inicio --}}
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <label for="md_from">{!! ucfirst(trans('textos.desde')) !!}</label>
                                </div>
                                <div class="col-12">
                                    @php
                                    $md_from = $settings && $settings->md_from != ''? \Carbon\Carbon::parse($settings->md_from)->format($languages[session()->get('locale')][4]):'';
                                    @endphp
                                    <x-date-picker name="md_from" value="{{ $md_from }}" :format="$languages[config('app.locale')][4]"/>
                                </div>
                            </div>
                        </div>

                        {{-- Minutos descuento hasta --}}
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <label for="md_to">{!! ucfirst(trans('textos.hasta')) !!}</label>
                                </div>
                                <div class="col-12">
                                    @php
                                    $md_to = $settings && $settings->md_to != ''? \Carbon\Carbon::parse($settings->md_to)->format($languages[session()->get('locale')][4]):'';
                                    @endphp
                                    <x-date-picker name="md_to" value="{{ $md_to }}" :format="$languages[config('app.locale')][4]"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="message">{!! ucfirst(trans('textos.mensaje')) !!}</label>
                        </div>
                        <div class="col-12">
                            @php
                            $message = $settings && $settings->timetable_doc_message? $settings->timetable_doc_message:'';
                            @endphp
                            <x-input-textarea name="message" class="editor" value="{{ $message }}"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>     
                </form>    
            </div>
        </div>
    </div>
</div>