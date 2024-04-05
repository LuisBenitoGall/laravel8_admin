<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblCompanyBankAccounts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.entidad')) !!}</th>
					<th>{!! ucfirst(trans('textos.cuenta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($bank_accounts as $ba)
					<tr>
						<td>{{ $ba->name }}</td>
						<td>{{ $ba->account }}</td>
						<td class="text-right">
							@can('companies.edit')
								@if(!$company_account)
									{{-- Estado --}}
									<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.cuenta_on_off') }}">
								        <input type="checkbox" name="status" @if($ba->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $ba->id }}" data-ctrl="/admin/bank-accounts/status"></span>
								    </label>

									{{-- Editar --}}
									{{-- <a href="#" class="btn btn-xs btn-info btnEdit" data-toggle="modal" data-target="#modal-edit-bank-account" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-id="{{ $ba->id }}" data-account="{{ $ba->account }}" data-bank="{{ $ba->bank_id }}"> --}}

									<a target="_blank" href="{{ route('bank-accounts.edit', $ba->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}">
										<i class="fa fa-edit"></i>
									</a>

									{{-- Cuenta principal --}}
									@if($ba->featured == 1)
                            			<i class="fa fa-star text-warning ml-1 mr-1" rel="tooltip" title="{{ trans('textos.cuenta_principal') }}"></i>
                            		@else
                            			<a href="{{ route('bank-accounts.featured', [$ba->id, $business_relationship]) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ trans('textos.cuenta_principal_marcar') }}"><i class="fa fa-star"></i></a>    
                        			@endif 

									{{-- Eliminar --}}
									<form method="POST" action="{{ route('bank-accounts-modal.destroy', $ba->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
									    <input name="_method" type="hidden" value="DELETE">
									    {{ csrf_field() }}
									    <input type="hidden" name="return" value="{{ $business_relationship }}">
									    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.cuenta_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
									</form>
								@endif
							@endcan
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>