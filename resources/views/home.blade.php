@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center pt-5">
            <h1 class="mb-5 mt-5">ADN Admin Systems</h1>
            <img src="{{ asset('img/logo.png') }}" alt="ADN Admin Systems" class="mb-5 mt-5">
            <div class="clearfix mb-5"></div>

            @guest
                <a href="{{ route('login') }}" class="btn btn-info btn-lg mr-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-info btn-lg ml-2">{{ trans('textos.registro') }}</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-info btn-lg">Admin</a>    
            @endguest
        </div>
    </div>
</div>

@endsection
