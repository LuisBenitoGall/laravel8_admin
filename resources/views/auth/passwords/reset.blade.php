@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('textos.recuperar_pwd') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}" accept-charset="utf-8" role="form">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- Email --}}
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email *</label>
                            <div class="col-md-6">
                                <x-input type="email" name="email" id="email" autocomplete="off" maxlength="150" autofocus="true" required/>
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password *</label>
                            <div class="col-md-6">
                                <x-input type="password" name="password" id="password" autocomplete="new-password" maxlength="12" required/>
                            </div>
                        </div>

                        {{-- Confirmar password --}}
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ ucfirst(trans('textos.confirm_pwd')) }} *</label>
                            <div class="col-md-6">
                                <x-input type="password" name="password_confirmation" id="password-confirm" autocomplete="new-password" maxlength="12" required/>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('textos.pwd_resetear') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
