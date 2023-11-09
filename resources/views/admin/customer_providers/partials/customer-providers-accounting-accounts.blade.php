<div class="row">
	@if($accounting_accounts->count())
		<div class="table-responsive">
			<table class="table table-hover dTable" id="tblCustomerProviderAccountingAccounts">
				<thead>
					<tr>
						<th>{!! ucfirst(trans('textos.cuenta')) !!}</th>
						<th>{!! ucfirst(trans('textos.nombre')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.cuenta_principal')) !!}</th>
						<th>{!! ucfirst(trans('textos.autor')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				<tbody>
					@foreach($accounting_accounts as $aa)
						<tr>
							{{-- Cuenta contable --}}
							<td>
								{{ $aa->account }}
							</td>
							{{-- Nombre --}}
							<td>
								{{ $aa->name }}
							</td>
							{{-- Cuenta principal --}}
							<td class="text-center">
								@if($aa->featured == 1) <i class="fa fa-check text-success"></i> @endif
							</td>
							{{-- Autor --}}
							<td>
								{{ $aa->user->name }} {{ $aa->user->surname }} 
							</td>
							{{-- Fecha --}}
							<td class="text-right">
								{{ $aa->created_at->format($languages[session()->get('locale')][4]); }}	
							</td>
							{{-- Acciones --}}
							<td class="text-right">
								{{-- Estado --}}
								@can('accounting-accounts.edit')	
									<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.cuenta_on_off') }}">
								        <input type="checkbox" name="status" @if($aa->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $aa->id }}" data-ctrl="/admin/accounting-accounts/status"></span>
								    </label>
								@endcan

								@can('accounting-accounts.destroy')
									{{-- Eliminar --}}
									<form method="POST" action="{{ route('accounting-accounts.destroy', $aa->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
									    <input type="hidden" name="_method" value="DELETE">
									    <input type="hidden" name="return" value="{{ $business_relationship }}">
									    {{ csrf_field() }}
									    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.cuenta_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
									</form>
								@endcan
							</td>
						</tr>
					@endforeach	
				</tbody>
			</table>
		</div>

	@else
		<p class="col-12 text-warning">
			{{ trans('textos.cuenta_contable_no_existe') }}	
		</p>

		@php
		$mode = $business_relationship == 'customers'? 1:2;
		@endphp

		<div class="col-12 pb-5">
			<form method="POST" action="{{ route('accounting-accounts.store-auto') }}" role="form" accept-charset="UTF-8">
			    {{ csrf_field() }}
			    <input type="hidden" name="company_target" value="{{ $company->id }}">
			    <input type="hidden" name="mode" value="{{ $mode }}">
		    	<button type="submit" class="btn btn-info">
		    		<i class="fa fa-plus"></i>
		    		{!! ucfirst(trans('textos.cuenta_contable_generar')) !!}
		    	</button>
			</form>
		</div>
	@endif
</div>