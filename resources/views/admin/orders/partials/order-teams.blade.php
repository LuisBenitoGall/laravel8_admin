<div class="row">
	{{-- Equipo cliente --}}
	<div class="col-lg-6">
		<div class="row">
			<div class="col-12">
				<h4 class="text-center mb-3" style="height: 35px;">
					{{ trans('textos.cliente_equipo') }}

					@if($shared->customer_id == session('currentCompany'))
						{{-- Debe estar activo el módulo de RRHH --}}
						@if($module_rrhh)
							<a href="#" class="btn btn-xs btn-warning ml-3 btnAddMember" data-toggle="modal" data-target="#modal-add-member" rel="tooltip" title="{{ trans('textos.miembro_add') }}" data-company="{{ $shared->customer_id }}" data-environment="customer">
								<i class="fa fa-plus"></i>
							</a>
						@else
							<p class="mt-2">
								<a target="_blank" href="{{ route('company-modules.index') }}" class="text-warning"><u>{{ trans('textos.modulo_rrhh_activar_aviso') }}</u></a>
							</p>
						@endif
					@endif
				</h4>
			</div>

			<div class="table-responsive">
				<table class="table table-hover dtabla" id="tblOrderCustomerTeam">
					<thead>
						<tr>
							<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
							<th>{!! ucfirst(trans('textos.role')) !!}</th>
							<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

	{{-- Equipo proveedor --}}
	<div class="col-lg-6">
		<div class="row">
			<div class="col-12">
				<h4 class="text-center mb-3" style="height: 35px;">
					{{ trans('textos.proveedor_equipo') }}

					@if($shared->provider_id == session('currentCompany'))
						{{-- Debe estar activo el módulo de RRHH --}}
						@if($module_rrhh)
							<a href="#" class="btn btn-xs btn-warning ml-3 btnAddMember" data-toggle="modal" data-target="#modal-add-member" rel="tooltip" title="{{ trans('textos.miembro_add') }}" data-company="{{ $shared->provider_id }}" data-environment="provider">
								<i class="fa fa-plus"></i>
							</a>
						@else
							<p class="mt-2">
								<a target="_blank" href="{{ route('company-modules.index') }}" class="text-warning"><u>{{ trans('textos.modulo_rrhh_activar_aviso') }}</u></a>
							</p>
						@endif
					@endif
				</h4>
			</div>

			<div class="table-responsive">
				<table class="table table-hover dtabla" id="tblOrderProviderTeam">
					<thead>
						<tr>
							<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
							<th>{!! ucfirst(trans('textos.role')) !!}</th>
							<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>