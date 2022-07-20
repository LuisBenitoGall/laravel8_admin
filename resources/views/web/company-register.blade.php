@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{!! trans('textos.empresa_registro') !!}</div>

                <div class="card-body">
                    <form method="post" action="{{ route('company.store') }}" accept-charset="utf-8" role="form">
                        {{ csrf_field() }}
                        <input type="hidden" name="account_id" id="accountId" @if($accounts->count() == 1) value="{{ $accounts[0]->id }}" @endif>
                        <input type="hidden" name="guardian" value="{{ $guardian }}">
                        <input type="hidden" name="business_relationship" value="companies">

                        {{-- Empresa --}}
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{!! ucfirst(trans('textos.empresa')) !!} *</label>
                            <div class="col-md-6">
                                <x-input type="text" name="name" id="name" autocomplete="name" maxlength="191" autofocus="true" required/>
                            </div>
                        </div>

                        {{-- Nombre comercial --}}
                        <div class="form-group row">
                            <label for="tradename" class="col-md-4 col-form-label text-md-right">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
                            <div class="col-md-6">
                                <x-input type="text" name="tradename" id="tradename" autocomplete="tradename" maxlength="191"/>
                            </div>
                        </div>

                        {{-- NIF --}}
                        <div class="form-group row">
                            <label for="nif" class="col-md-4 col-form-label text-md-right">NIF</label>
                            <div class="col-md-6">
                                <x-input type="text" name="nif" id="nif" autocomplete="nif" maxlength="11"/>
                            </div>
                        </div>

                        @if($accounts->count() > 1)
                            <div class="form-group row">
                                @foreach($accounts as $account)
                                    <div class="card col-ms-6 col-md-4 mb-3 mt-3 account" data-id="{{ $account->id }}">    
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $account->name }}</h5>   

                                            <p class="card-text">{{ $account->rate }}{{ config('constants.CURRENCY_') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <x-btn-submit name="submit_save" label="{{ trans('textos.continuar') }}"/>
                    </form>    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
