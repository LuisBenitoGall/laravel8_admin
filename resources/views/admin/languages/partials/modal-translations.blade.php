{{-- Translations: --}}
@php
// Idiomas:
$languages = App\Helpers\helpers::languages();
@endphp

<div class="modal fade" id="modal-translations" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.traducciones')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" id="frm-translations" autocomplete="off" action="#" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="objId" id="objId">
                    <input type="hidden" name="schema" id="schema">

                    @foreach($languages as $lang)
                        <div class="form-group row">
                            <label for="idioma" class="col-lg-12">{!! $lang[3] !!}</label>
                            <div class="col-lg-12">
                                <input type="text" class="form-control input-translation" id="lang-{!! $lang[0] !!}" name="{!! $lang[0] !!}">
                            </div>
                        </div>
                    @endforeach 

                    <x-btn-submit label="guardar"/>                
                </form>    
            </div>
        </div>
    </div>
</div>