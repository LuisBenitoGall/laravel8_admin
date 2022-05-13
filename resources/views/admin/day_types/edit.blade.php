@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
    {{-- Acciones: --}}
    <div class="col-sm-12">
        <a href="{{ route('day-types.index') }}" class="btn btn-info">
            <i class="fa fa-angle-left"></i>
            {!! trans('textos.volver_tipos_jornada') !!}
        </a>

        {{-- Nuevo tipo de jornada --}}
        @can('calendar-settings.create')
            <a href="{{ route('day-types.create') }}" class="btn btn-info">
                <i class="fa fa-plus"></i>
                {!! ucfirst(trans('textos.tipo_jornada_nuevo')) !!}
            </a>
        @endcan
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <form method="post" autocomplete="off" action="{{ route('day-types.update', $dt) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    @method('PUT')
                    {{ csrf_field() }}

                    @include('admin.day_types.partials.form', ['day_type' => $dt])

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>             
</div>

@endsection