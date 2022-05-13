{{-- Áreas de negocio --}}
@can('business-areas.index')
	<a class="btn btn-info" href="{{ route('business-areas.index', [$company, $business_relationship]) }}"><i class="fa fa-briefcase"></i> {!! ucfirst(trans('textos.areas_negocio')) !!}</a>
@endcan

{{-- Centros de coste --}}
@can('cost-centers.index')
	<a class="btn btn-info" href="{{ route('cost-centers.index', [$company, $business_relationship]) }}"><i class="fa fa-money-bill-alt"></i> {!! ucfirst(trans('textos.centros_coste')) !!}</a>
@endcan

{{-- Centros de trabajo --}}
@can('workplaces.index')
	<a class="btn btn-info" href="{{ route('workplaces.index', [$company, $business_relationship]) }}"><i class="fa fa-map-marker-alt"></i> {!! ucfirst(trans('textos.centros_trabajo')) !!}</a>
@endcan