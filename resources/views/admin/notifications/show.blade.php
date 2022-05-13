@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('notifications.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_notificaciones') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">{!! ucfirst(trans('textos.notificacion')) !!} <u>@if($notification_data) {!! $notification_data->title !!} @endif</u>
				</h3>	

                {{-- Info --}}
				<div class="mb-3">
					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}: <b>{{ $notification_data->sender }}</b>
					</label>

					{{-- Fecha creación --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha')) !!}: <b>{{ $notification->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}</b>
					</label>

					{{-- Fecha leído --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.leido')) !!}: <b>{{ Carbon\Carbon::parse($notification->read_at)->format($languages[session()->get('locale')][4].' H:i:s') }}</b>
					</label>
				</div>

				{{-- Contenido --}}
				@if($notification_data)
					<div class="form-group row">
						<div class="col-12">
							<div class="card bg-light">
								<div class="card-body">
									{!! $notification_data->content !!}	

									@if($notification_data->url)
										<a target="_blank" href="{!! $notification_data->url !!}" rel="tooltip" title="{{ ucfirst(trans('textos.acceder')) }}">
											<i class="fa fa-chevron-circle-right text-warning ml-3"></i>
										</a>
									@endif
								</div>
							</div>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

@endsection