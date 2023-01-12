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
                        @php
                        $info_ = '';
                        $projects_ = '';
                        $forecast = 0;
                        $worked = 0;
                        @endphp

                        @foreach($favourites as $fav)
                            @php
                            $forecast = $fav->hours_forecast? $forecast + $fav->hours_forecast:$forecast;
                            $worked = $fav->hours_worked? $worked + $fav->hours_worked:$worked;

                            $projects_ .= '<div class="col-md-6">';
                                $projects_ .= '<div class="widget style1 lazur-bg">';
                                    $projects_ .= '<div class="row vertical-align">';
                                        $projects_ .= '<div class="col-sm-10">';
                                            $projects_ .= '<a href="'.route('projects.edit', $fav->id).'">';
                                                $projects_ .= '<b>';
                                                    if($fav->num){ 
                                                        $projects_ .= $fav->num.' - ';
                                                    } 
                                                    $projects_ .= $fav->name;
                                                $projects_ .= '</b>';
                                            $projects_ .= '</a>';
                                        $projects_ .= '</div>';
                                        $projects_ .= '<div class="col-sm-2 text-right">';
                                            $projects_ .= '<a href="'.route('project-favourites.delete', $fav->id).'" class="btn-confirm" rel="tooltip" title="'.ucfirst(trans('textos.eliminar')).'" data-msg="'.ucfirst(trans('textos.favorito_confirm_eliminar')).'">';
                                                $projects_ .= '<i class="fa fa-trash"></i>';
                                            $projects_ .= '</a>';
                                        $projects_ .= '</div>';
                                    $projects_ .= '</div>';
                                $projects_ .= '</div>';
                            $projects_ .= '</div>';
                            @endphp
                        @endforeach

                        {{-- Info --}}
                        <div class="col-12 mb-3">
                            {{ trans('textos.horas_previstas') }}
                            <b class="mr-5">{!! $forecast !!}</b>  
                            {{ ucfirst(trans('textos.horas_trabajadas')) }}
                            <b class="mr-5">{!! $worked !!}</b>   
                            {{ ucfirst(trans('textos.horas_pendientes')) }} 
                            <b class="text-warning">{!! $forecast - $worked !!}</b>
                        </div>

                        {{-- Projects --}}
                        {!! $projects_ !!}
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
