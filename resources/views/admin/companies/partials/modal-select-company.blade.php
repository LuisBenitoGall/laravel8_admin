{{-- Session Company: --}}
@if(!session('currentCompany') && session('companies'))
    <div class="modal fade" id="modal-select-company" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{!! ucfirst(trans('textos.empresa_selec')) !!}</h4>
                </div>

                <div class="modal-body">
                    <form method="post" autocomplete="off" action="{{ route('companies.select') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                        {{ csrf_field() }}

                        <div class="form-group row">
                            @foreach(session('companies') as $c)

                                <div class="col-lg-12 pt-2 pb-2">
                                    <label class="custom-chk-rdb mr-4" style="margin-bottom: 0; margin-top: 7px;">
                                        <input type="radio" name="company" value="{{ $c->id }}">
                                        <span class="rdb"></span>
                                        {{ $c->name }}
                                    </label>                            
                                </div>
                            
                            @endforeach  
                        </div>                  
                        <br>

                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <input type="submit" class="btn btn-info" value="{!! ucfirst(trans('textos.enviar')) !!}">
                            </div>
                        </div>
                    </form>    
                </div>
            </div>
        </div>
    </div>
@endif