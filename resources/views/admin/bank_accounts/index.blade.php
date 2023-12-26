@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Nueva cuenta --}}
		@can('companies.edit')
			<a class="btn btn-info" href="{{ route('bank-accounts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuenta_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblBankAccounts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.banco')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.iban')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.entidad')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.oficina')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.digitos_control')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.cuenta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.cuenta_principal')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($accounts as $row)
					<tr>
						{{-- Banco --}}
						<td>{{ $row->name }}</td>
						{{-- IBAN --}}
						<td class="text-right">{{ $row->iban }}</td>
						{{-- Entidad --}}
						<td class="text-right">{{ $row->entity }}</td>
						{{-- Oficina --}}
						<td class="text-right">{{ $row->office }}</td>
						{{-- DC --}}
						<td class="text-right">{{ $row->dc }}</td>
						{{-- Cuenta --}}
						<td class="text-right">{{ $row->digits }}</td>
						{{-- Principal --}}
						<td class="text-center">
							@if($row->featured)
								<i class="fa fa-check text-success"></i>
							@endif
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Estado --}}
							<label class="custom-chk-rdb" rel="tooltip">
	                            <input type="checkbox" name="status" @if($row->status) checked @endif>
	                            <span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="bank-accounts/status"></span>
	                        </label>

							{{-- Editar --}}
							<a href="{{ route('bank-accounts.edit', $row) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

							{{-- Eliminar --}}
							<form method="POST" action="{{ route('bank-accounts.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                        		<input name="_method" type="hidden" value="DELETE">
                        		{{ csrf_field() }}
                        		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.banco_cuenta_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
                    		</form>
						</td>
					</tr>
				@endforeach	
			</tbody>
		</table>
	</div>
</div>

@endsection