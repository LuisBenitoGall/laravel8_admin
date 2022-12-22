<div class="modal fade modal-favourites" id="modal-favourites" tabindex="-1" role="dialog" aria-labelledby="modalFavourites" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalFavourites">{!! ucfirst(trans('textos.favoritos')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    @if($favourites->count())
                        @foreach($favourites as $fav)
                            <div class="col-md-6">
                                <div class="widget style1 lazur-bg">
                                    <div class="row vertical-align">
                                        <div class="col-sm-10">
                                            <a href="{{ route('projects.edit', $fav->id) }}">
                                                <b>@if($fav->num) {{ $fav->num }} - @endif {{ $fav->name }}</b>
                                            </a>
                                        </div>
                                        <div class="col-sm-2 text-right">
                                            <a href="{{ route('project-favourites.delete', $fav->id) }}" class="btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.favorito_confirm_eliminar')) }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-warning text-center py-4">
                            <b>{{ trans('textos.favoritos_sin') }}</b>
                        </div>
                    @endif
                </div> 
            </div>
        </div>
    </div>
</div>
