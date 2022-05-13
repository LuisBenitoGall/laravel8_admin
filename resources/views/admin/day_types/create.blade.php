@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
    {{-- Acciones: --}}
    <div class="col-sm-12">
        <a href="{{ route('day-types.index') }}" class="btn btn-info">
            <i class="fa fa-angle-left"></i>
            {!! trans('textos.volver_tipos_jornada') !!}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <form method="post" autocomplete="off" action="{{ route('day-types.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    @include('admin.day_types.partials.form', ['day_type' => false])

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>             
</div>

@endsection