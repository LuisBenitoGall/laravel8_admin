@extends('admin.partials.layout')

@section('content')
    {{-- Quizzes --}}
    @if($quizzes->count())
        <div class="row mb-3">
            <div class="col-12 mb-3">
                <h3>{{ ucfirst(trans('textos.concursos')) }}</h3>
            </div>   

            @foreach($quizzes as $q)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3 preference">
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
	
@endsection


{{-- <div class="widget yellow-bg p-lg text-center">
                    <div class="m-b-md">
                        <i class="fa fa-thumbs-up fa-4x"></i>
                        <h1 class="m-xs">520</h1>
                        <h3 class="font-bold no-margins">
                            Likes
                        </h3>
                        <small>amount</small>
                    </div>
                </div>
 --}}