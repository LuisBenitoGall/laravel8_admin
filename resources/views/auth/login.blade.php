@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Login</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" accept-charset="utf-8" role="form">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email *</label>

                            <div class="col-md-6">
                                {{-- Email --}}
                                <x-input class="" datas="" type="email" name="email" id="email" autocomplete="off" maxlength="150" autofocus="true" value="" disabled="false" groupvalue="" group=""/>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{-- Password --}}
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password *</label>

                            <div class="col-md-6">
                                <x-input type="password" name="password" id="password" autocomplete="current-password" maxlength="12" required/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ trans('textos.recuerdame') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                {{-- Recuperar contraseña --}}
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ trans('textos.olvidaste_pwd') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
