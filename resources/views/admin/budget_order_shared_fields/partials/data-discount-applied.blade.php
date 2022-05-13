@if($shared->discount && $shared->discount > 0)
	<label class="mr-5">
		{{ ucfirst(trans('textos.descuento_aplicado')) }}:
		<b>{{ $shared->discount }}%</b>

		@if($shared->provider_id == session('currentCompany'))
			<a href="{{ route('budget-orders.remove-discount', [$shared->id, $return]) }}" class="text-danger ml-3 btn-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.descuento_eliminar')) !!}" data-msg="{!! ucfirst(trans('textos.descuento_confirm_eliminar')) !!}">
				<i class="fa fa-trash-alt"></i>
			</a>
		@endif
	</label>
@endif