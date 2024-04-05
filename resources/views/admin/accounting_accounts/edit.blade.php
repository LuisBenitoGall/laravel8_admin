@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myAccountingAccountId" value="{{ $account->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Volver a cuentas contables. --}}
		<a href="{{ route('accounting-accounts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_cuentas_contables') !!}
		</a>

		{{-- Nueva cuenta --}}
		@can('accounting-accounts.create')
			<a class="btn btn-info" href="{{ route('accounting-accounts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuenta_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{{ trans('textos.cuenta_contable') }} <b>{{ $account->account }} - {{ $account->name }}</b>
				</h3>

				{{-- Info --}}
				<div class="mb-4">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $account->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $account->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Tipo de cuenta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.cuenta_tipo')) !!}:
						@php
						$type = '';
						@endphp
						@if($account->type == 'a')
							@php
							$type = ucfirst(trans('textos.activos'));
							@endphp
						@elseif($account->type == 'p')
							@php
							$type = ucfirst(trans('textos.pasivos'));
							@endphp
						@elseif($account->type == 'g')
							@php
							$type = ucfirst(trans('textos.ganancias_perdidas'));
							@endphp
						@endif

						<b>{{ $type }}</b>
					</label>

					{{-- Modalidad --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.modalidad')) !!}:
						<b>{!! $modes[$account->mode] !!}</b>
					</label>

					{{-- Aplica IVA --}}
					<label class="mr-5">
						{{ trans('textos.iva_aplica') }}:
						<b>
							@if($account->with_iva)
								<i class="fa fa-check text-success ml-2"></i>
							@else
								<i class="fa fa-ban text-danger ml-2"></i>	
							@endif
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
					<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.datos_generales')) !!}</a>
					</li>

					<li class="nav-item @if($tab == 'seats') active @endif">
						<a class="nav-link @if($tab == 'seats') active @endif" href="#tab-seats" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.asientos_contables')) !!}</a>	
					</li>
				</ul>

				{{-- Panels --}}
				<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.accounting_accounts.partials.accounting-info')	
					</div>

					{{-- Items --}}
					<div class="tab-pane fade @if($tab == 'seats') show active @endif" role="tabpanel" id="tab-seats">
						@include('admin.accounting_accounts.partials.accounting-seats')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection