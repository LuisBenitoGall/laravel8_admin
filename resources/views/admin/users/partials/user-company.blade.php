<div class="row">
	{{-- Areas de negocio: --}}
	<div class="col-12 col-md-4">
		<h5>{{ trans('textos.areas_negocio') }}</h5>	

		<form method="post" autocomplete="off" action="{{ route('user-business-areas.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
			{{ csrf_field() }}
			<input type="hidden" name="user_id" value="{{ $user->id }}">

			<div class="form-group row">
				<div class="col-9">
					<x-select name="business_area_id" class="select2" :options="$business_areas" label="area_negocio_selec" info="user-business-areas"/>
				</div>
			
				<div class="col-3 text-right">
					<input type="submit" name="submit_save" class="btn btn-info" value="{!! ucfirst(trans('textos.anadir')) !!}">
				</div>
			</div>
		</form>

		@if($user_business_areas->count())
			<div class="items-list">
				@foreach($user_business_areas as $uba)
					<p>
						{{ $uba->name }}
						<a href="{{ route('user-business-areas.delete', $uba->id) }}" class="text-danger btn-confirm" data-msg="{!! trans('textos.area_negocio_confirm_eliminar') !!}"><i class="fa fa-times mt-1 float-right"></i></a>
					</p>	
				@endforeach	
			</div>
		@endif
	</div>

	{{-- Centros de coste: --}}
	<div class="col-12 col-md-4">
		<h5>{{ trans('textos.centros_coste') }}</h5>	

		<form method="post" autocomplete="off" action="{{ route('user-cost-centers.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
			{{ csrf_field() }}
			<input type="hidden" name="user_id" value="{{ $user->id }}">

			<div class="form-group row">
				<div class="col-9">
					<x-select name="cost_center_id" class="select2" :options="$cost_centers" label="centro_coste_selec" info="user-cost-centers"/>
				</div>
			
				<div class="col-3 text-right">
					<input type="submit" name="submit_save" class="btn btn-info" value="{!! ucfirst(trans('textos.anadir')) !!}">
				</div>
			</div>
		</form>

		@if($user_cost_centers->count() > 0)
			<div class="items-list">
				@foreach($user_cost_centers as $ucc)
					<p>
						{{ $ucc->name }}
						<a href="{{ route('user-cost-centers.delete', $ucc->id) }}" class="text-danger btn-confirm" data-msg="{!! trans('textos.centro_coste_confirm_eliminar') !!}"><i class="fa fa-times mt-1 float-right"></i></a>
					</p>	
				@endforeach	
			</div>
		@endif
	</div>

	{{-- Centros de trabajo: --}}
	<div class="col-12 col-md-4">
		<h5>{{ trans('textos.centros_trabajo') }}</h5>	

		<form method="post" autocomplete="off" action="{{ route('user-workplaces.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
			{{ csrf_field() }}
			<input type="hidden" name="user_id" value="{{ $user->id }}">

			<div class="form-group row">
				<div class="col-9">
					<x-select name="workplace_id" class="select2" :options="$workplaces" label="centro_trabajo_selec" info="user-workplaces"/>
				</div>
			
				<div class="col-3 text-right">
					<input type="submit" name="submit_save" class="btn btn-info" value="{!! ucfirst(trans('textos.anadir')) !!}">
				</div>
			</div>
		</form>

		@if($user_workplaces->count() > 0)
			<div class="items-list">
				@foreach($user_workplaces as $uwp)
					<p>
						{{ $uwp->name }}
						<a href="{{ route('user-workplaces.delete', $uwp->id) }}" class="text-danger btn-confirm" data-msg="{!! trans('textos.centro_confirm_eliminar') !!}"><i class="fa fa-times mt-1 float-right"></i></a>
					</p>	
				@endforeach	
			</div>
		@endif
	</div>
</div>