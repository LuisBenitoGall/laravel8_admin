<div class="row">
	@if($accounting_accounts->count())
		<div class="table-responsive">
			<table class="table table-hover dTable" id="tblCustomerProviderAccountingAccounts">
				<thead>
					<tr>
						<th>{!! ucfirst(trans('textos.entidad')) !!}</th>
						<th>{!! ucfirst(trans('textos.cuenta')) !!}</th>
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
							{{-- Acciones --}}
							<td class="text-right">
								
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
		<div class="col-12 pb-5">
			<form method="POST" action="{{ route('accounting-accounts.store-auto') }}" role="form" accept-charset="UTF-8">
			    {{ csrf_field() }}
			    <input type="hidden" name="company_target" value="{{ $company->id }}">
			    <input type="hidden" name="mode" value="p">
		    	<button type="submit" class="btn btn-info">
		    		<i class="fa fa-plus"></i>
		    		{!! ucfirst(trans('textos.cuenta_contable_generar')) !!}
		    	</button>
			</form>
		</div>
	@endif
</div>