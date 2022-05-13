<div class="modal fade" id="modal-export-values" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.exportar_valores')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route($action) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    <div class="form-group row">
                        <div class="col-12">
                            <p class="text-warning">{!! trans('textos.exportar_valores_aviso') !!}</p>

                            <b>{!! ucfirst(trans('textos.empresas')) !!}</b>
                        </div>

                        <div class="col-12">
                            <ul class="ul-list">
                                @foreach(session('companies') as $c)
                                    @if($c->id != session('currentCompany'))
                                        <li>
                                            {{ $c->name }}
                                            <label class="custom-chk-rdb">
                                                <input type="checkbox" name="companies[]" value="{{ $c->id }}">
                                                <span class="chk chksmall"></span>
                                            </label>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="exportar"/>
                </form>   
            </div>
        </div>
    </div>
</div>