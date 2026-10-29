@extends('admin.partials.layout')

@section('content')

@if($quizzes || $recommendation)
    <div class="row mb-3">
        {{-- Quizzes --}}
        @if($quizzes->count())
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12 mb-3">
                        <h3>{{ ucfirst(trans('textos.concursos')) }}</h3>
                    </div>   

                    @foreach($quizzes as $q)
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="widget p-lg text-center bg-info">
                                <div class="m-b-md">
                                    <a href="{{ route('quiz-editions.ranking', $q) }}" class="preference-link"> 
                                        <h3>{{ $q->name }}</h3>  
                                    </a>
                                </div>  
                            </div>
                        </div>
                    @endforeach    
                </div>              
            </div>
        @endif

        {{-- Recomendaciones --}}
        @if(session('companySettings') && session('companySettings')->recommendations)
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12 mb-3">
                        <h3>{{ ucfirst(trans('textos.recomendaciones')) }}</h3>
                    </div>   

                    <div class="col-sm-12 position-relative">
                        {{-- Recomendación del día --}}
                        @if($recommendation)
                            <input type="hidden" id="recommendationId" value="{{ $recommendation->id }}">

                            <a href="#" class="btn btn-warning btnTodayRecommendation">
                                <i class="fa fa-lightbulb"></i>
                                {!! trans('textos.recomendacion_dia') !!}
                            </a>  

                            {{-- Recomendación --}}
                            <div id="dashboard-recommendation" class="mt-2 p-3">
                                <h3 class="mb-4">
                                    {!! ucfirst(trans('textos.recomendacion_de')) !!} 
                                    <u>{{ $recommendation->name }} {{ $recommendation->surname }}</u>
                                </h3>

                                @if($recommendation->phrase)
                                    <div class="form-group row">
                                        {{-- Cita --}}
                                        <label class="col-md-2 col-form-label">
                                            {{ trans('textos.cita') }}
                                            <br>
                                            
                                            @php
                                            $voted = !$votes || !$votes->phrase? 'btn-gray':'btn-success';
                                            @endphp

                                            <small>@if($recommendation->phrase_votes > 0) {{ $recommendation->phrase_votes }} @endif</small>
                                            <a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="phrase" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
                                                <i class="fa fa-thumbs-up"></i>
                                            </a>    
                                        </label>
                                        <div class="col-10">
                                            <p id="recom-phrase" class="bg-gray p-3">
                                                <i>{!! $recommendation->phrase !!}</i>
                                                <br>
                                                <b class="text-right d-block">~ {{ $recommendation->author }} ~</b>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if($recommendation->song)
                                    <div class="form-group row">
                                        {{-- Canción --}}
                                        <label class="col-md-2 col-form-label">
                                            {{ trans('textos.cancion') }}
                                            <br>

                                            @php
                                            $voted = !$votes || !$votes->song? 'btn-gray':'btn-success';
                                            @endphp

                                            <small>@if($recommendation->song_votes > 0) {{ $recommendation->song_votes }} @endif</small>
                                            <a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="song" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
                                                <i class="fa fa-thumbs-up"></i>
                                            </a>    
                                        </label>
                                        <div class="col-10">
                                            <div class="video-yt">
                                                {!! $recommendation->song !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($recommendation->scene)
                                    <div class="form-group row">
                                        {{-- Video --}}
                                        <label class="col-md-2 col-form-label">
                                            {{ trans('textos.video') }}
                                            <br>

                                            @php
                                            $voted = !$votes || !$votes->scene? 'btn-gray':'btn-success';
                                            @endphp

                                            <small>@if($recommendation->scene_votes > 0) {{ $recommendation->scene_votes }} @endif</small>
                                            <a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="scene" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
                                                <i class="fa fa-thumbs-up"></i>
                                            </a>    
                                        </label>
                                        <div class="col-10">
                                            <div class="video-yt">
                                                {!! $recommendation->scene !!}    
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($recommendation->image)
                                    <div class="form-group row">
                                        {{-- Imagen --}}
                                        <label class="col-md-2 col-form-label">
                                            {{ ucfirst(trans('textos.imagen')) }}
                                            <br>

                                            @php
                                            $voted = !$votes || !$votes->image? 'btn-gray':'btn-success';
                                            @endphp

                                            <small>@if($recommendation->image_votes > 0) {{ $recommendation->image_votes }} @endif</small>
                                            <a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="image" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
                                                <i class="fa fa-thumbs-up"></i>
                                            </a>    
                                        </label>
                                        <div class="col-10">
                                            <img src="{{ asset('/img/recommendations/'.$recommendation->image) }}" alt="">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Mis recomendaciones --}}
                        <a href="{{ route('user-recommendations.by-user') }}" class="btn btn-info">
                            <i class="fa fa-lightbulb"></i>
                            {!! trans('textos.recomendaciones_mis') !!}
                        </a>  

                        {{-- Ver todas --}}
                        <a href="{{ route('user-recommendations.index') }}" class="btn btn-info">
                            <i class="fa fa-box-open"></i>
                            {!! ucfirst(trans('textos.ver_todas')) !!}
                        </a>  

                        {{-- Nueva recomendación --}}
                        <a href="{{ route('user-recommendations.create') }}" class="btn btn-info">
                            <i class="fa fa-plus"></i>
                            {!! ucfirst(trans('textos.recomendacion_nueva')) !!}
                        </a>  

                        {{-- Ranking --}}
                        <a href="{{ route('user-recommendations.ranking') }}" class="btn btn-info">
                            <i class="fa fa-trophy"></i>
                            {!! ucfirst(trans('textos.ranking')) !!}
                        </a>  
                    </div>
                </div>
            </div>
        @endif
    </div> 
@endif

{{-- Notas --}}
@livewire('notes-by-user')

{{-- Preferences --}}
<div class="row mb-3">
    <div class="col-12 mb-3">
        <h3>{{ trans('textos.opciones_preferidas') }}</h3>
    </div>
    
    @foreach($preferences as $row)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3 preference">
            <div class="widget p-lg text-center" @if($row->color) style="background-color: {{ $row->color }} !important" @endif>
                <div class="m-b-md">
                    <a href="{{ $row->url }}" class="preference-link">
                        @php
                        $icon = $row->icon? $row->icon:'check';
                        @endphp
                        <i class="fa fa-{{ $icon }} fa-4x text-white"></i>
                        <br><br>

                        <h3 class="font-bold no-margins">
                            {{ ucfirst($row->name) }}
                        </h3>
                    </a>  
                </div>  

                <a href="{{ route('preferences.destroy', $row->id) }}" class="preference-delete btn-confirm" data-msg="{!! trans('textos.preferencia_confirm_eliminar') !!}">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>  
    @endforeach
</div>

@push('scripts')
<script>
$(function(){
    //Recomendación del día.
    @if($recommendation)
        $('.btnTodayRecommendation').on('click', function(e){
            e.preventDefault();
            $("#dashboard-recommendation").slideToggle('slow');
        });

    @endif 
});
</script>
@endpush    
	
@endsection
