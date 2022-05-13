@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('users.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-rank">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.ip_rango_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblIpAccess">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.grupo')) !!}</th>
					<th>{!! ucfirst(trans('textos.ip_inicio')) !!}</th>
					<th>{!! ucfirst(trans('textos.ip_fin')) !!}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>	
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- Add Rank --}}
<div class="modal fade modal-add-rank" id="modal-add-rank" tabindex="-1" role="dialog" aria-labelledby="addRank" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addRank">{!! ucfirst(trans('textos.rango_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('ip-access.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    @if(count($groups))
	                    <div class="form-group row">
	                    	{{-- Grupos --}}
	                    	<label for="group_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.grupo')) !!} *</label>
							<div class="col-lg-9">
	                    		<x-select name="group_id" class="select2" label="grupo_selecciona" :options="$groups"/>
	                    	</div>
	                    </div>
	                @endif

                    <div class="form-group row">
						{{-- IP inicio --}}
                    	<label for="ip_start" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.ip_inicio')) !!} *</label>
						<div class="col-lg-9">
							<x-input type="text" name="ip_start" autocomplete="off" maxlength="20" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- IP fin --}}
                    	<label for="ip_end" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.ip_fin')) !!} *</label>
						<div class="col-lg-9">
							<x-input type="text" name="ip_end" autocomplete="off" maxlength="20" required/>
						</div>
					</div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>

@endsection