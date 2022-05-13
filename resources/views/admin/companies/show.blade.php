@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@php
		if($origin == 'customers'){
			$label_return = 'volver_clientes';
			$url_return = 'customers.index';
		}elseif($origin == 'providers'){
			$label_return = 'volver_proveedores';
			$url_return = 'providers.index';
		}else{
			$label_return = 'volver_empresas';
			$url_return = 'companies.index';
		}
		@endphp

		<a href="{{ route($url_return) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.'.$label_return) !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<div class="row">
					{{-- Info empresa --}}
					<div class="col-lg-4">
						<h4 class="mb-4"><u>{{ ucfirst($company->name) }}</u></h4>

						{{-- Logo --}}
						@if($company->logo)
							<div class="row mt-4">
								<div class="col-sm-6 col-md-3 col-lg-2 foto">
									<img class="img-fluid" src="{{ asset('img/companies/'.$company->logo) }}">	
								</div>
							</div>	
						@endif

						{{-- Info --}}
						<div class="row mt-4">
							<div class="col-12">
								<p>· {{ trans('textos.nombre_comercial') }}: {{ $company->tradename }}</p>
								<p>· NIF: {{ $company->nif }}</p>
							</div>	
						</div>

						{{-- Centros de trabajo --}}
						@if($workplaces->count() && in_array('wp', $public_info))
							<div class="row mt-4">
								<div class="table-responsive col-12">
									<h3 class="mb-2">{{ trans('textos.centros_trabajo') }}</h3>

									<table class="table">
										<thead>
											<tr>
												<th>{!! ucfirst(trans('textos.centro_trabajo')) !!}</th>
												<th>{!! ucfirst(trans('textos.poblacion')) !!}</th>
												<th class="text-center">Logo</th>
												<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
											</tr>	
										</thead>

										<tbody>
											@foreach($workplaces as $wp)
												<tr>
													<td>{{ $wp->name }}</td>
													<td>{{ $wp->Town }}</td>
													<td class="text-center">
														<img class="img-fluid" src="{{ asset('img/companies/'.$wp->logo) }}">	
													</td>
													<td></td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						@endif

						{{-- Áreas de negocio --}}
						@if($workplaces->count() && in_array('ba', $public_info))
							<div class="row mt-4">
								<div class="table-responsive col-12">
									<h3 class="mb-2">{{ trans('textos.areas_negocio') }}</h3>

									<table class="table">
									</table>
								</div>
							</div>
						@endif

						{{-- Centros de coste --}}
						@if($workplaces->count() && in_array('cc', $public_info))
							<div class="row mt-4">
								<div class="table-responsive col-12">
									<h3 class="mb-2">{{ trans('textos.centros_coste') }}</h3>

									<table class="table">
									</table>
								</div>
							</div>
						@endif
					</div>

					{{-- Info contactos --}}
					@if($employees)
						<div class="col-lg-8">
							<h4 class="mb-4">{{ trans('textos.contactos') }}</h4>

							<div class="row">
								<div class="table-responsive">
									<table class="table table-hover dTable" id="tblContacts">
										<thead>
											<tr>
												<th></th>
												<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
												<th>{!! ucfirst(trans('textos.email')) !!}</th>
												<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
											</tr>
										</thead>

										<tbody>
											@foreach($employees as $epy)
												<tr>
													<td>
														<span class="avatar">
															@if($epy->Image)
							                                    <img alt="{{ $epy->name }} {{ $epy->surname }}" class="img-circle" src="{{ asset('img/users/'.$epy->Image) }}">
							                                @else
							                                    <img src="{{ Avatar::create($epy->name.' '.$epy->surname)->toBase64() }}" />
							                                @endif
	                                					</span>
													</td>
													<td>{{ $epy->name }} {{ $epy->surname }}</td>
													<td>
														<a target="_blank" href="mailto:{{ $epy->email }}">{{ $epy->email }}</a>
													</td>
													<td class="text-right">
														{{-- Ver usuario --}}
														<a href="{{ route('users.show', $epy->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ trans('textos.usuario_ver') }}">
															<i class="fa fa-eye"></i>
														</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div> 
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection