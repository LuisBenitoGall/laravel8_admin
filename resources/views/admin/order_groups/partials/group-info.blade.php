<form method="post" autocomplete="off" action="{{ route('order-groups.update', $group) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Agrupación --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agrupacion_pedidos')) !!} *</label>
		<div class="col-lg-3">
			<x-input type="text" name="name" value="{{ $group->name }}" autocomplete="off" maxlength="150" required/>
		</div>

		{{-- Status --}}
		<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-lg-2">
			@php
			$estados = App\Helpers\Helpers::orderGroupStatus();
			@endphp

			<x-select name="status" class="select2" :options="$estados" value="{{ $group->status }}"/>
		</div>



		@if($budget)
			<label class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.presupuesto')) !!}</label>
			<div class="col-lg-2">
				<label class="text-warning mt-2" style="font-size: 16px;"><b><u>@if($budget->bkey) {{ $budget->bkey }} @else {{ $budget->id }} @endif</u></b></label>
				<a href="{{ route('budgets.edit', $budget->id) }}" class="ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.presupuesto_editar')) }}">
					<i class="fa fa-edit"></i>
				</a>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Orden de pago --}}
		<label for="pay_order" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_pago')) !!}</label>
		<div class="col-lg-2">
			@php
			$readonly = $group->status != config('constants.ORDER_GROUPS_OPENED_')? true:false;
			@endphp

			<x-input type="text" name="pay_order" value="{{ $group->pay_order }}" autocomplete="off" maxlength="100" readonly="{{ $readonly }}" info="order-group-pay-order"/>
		</div>

		{{-- Fecha orden de pago --}}
		@if($group->pay_order_received)
			@php
			$pay_order_received = $group->pay_order_received && $group->pay_order_received > 0? \Carbon\Carbon::parse($group->pay_order_received)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<label class="col-lg-2 col-form-label">{{ trans('textos.recibida') }}</label>
			<div class="col-lg-2">
				<x-input type="text" name="pay_order_received" class="text-right" value="{{ $pay_order_received }}" autocomplete="off" readonly="true" info="order-group-pay-order-received"/>
			</div>
		@endif
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>