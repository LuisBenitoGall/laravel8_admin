@extends('admin.partials.layout')

@section('content')

@if(isset($user))
	<div class="row mb-3">
		<div class="col-12">
			{{-- Acciones --}}
			@can('users.index')
				<a href="{{ route($return.'.index') }}" class="btn btn-info">
					<i class="fa fa-angle-left"></i>
					{!! trans('textos.volver_usuarios') !!}
				</a>
			@endcan
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-12">						
			<div class="card mb-3">
				<div class="card-body">
					<h3 class="mb-4"><u>{{ $user->name }} {{ $user->surname }}</u></h3>

					{{-- Imágenes --}}
					@if(count($images))
						<div class="row mt-4">
							@foreach($images as $img)
								<div class="col-sm-6 col-md-3 col-lg-2 foto">
									<img class="img-fluid" src="{{ asset('img/users/'.$img->image) }}">	
								</div>
							@endforeach
						</div>
					@endif

					{{-- Info --}}
					<div class="row mt-4">
						<div class="col-sm-6 col-md-4 col-lg-3">	
							<p>· Email: <a target="_blank" href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>

							@if($phones->count())
								@php
								$i = 0;
								@endphp
								<p>· {{ ucfirst(trans('textos.telefonos')) }}:
									@foreach($phones as $phone)
										@if($i > 0)
											/ 
										@endif

										<a href="tel:{{ $phone->phone_number }}"> 
											{{ $phone->phone_number }}
										</a>
										
										@php
										$i++;
										@endphp
									@endforeach
								</p>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif

@endsection