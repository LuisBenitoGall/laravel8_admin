@extends('admin.partials.layout')

@section('content')

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAgencies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.agencia')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($transport_agencies as $ta)
					<tr>
						<td>{{ $ta->name }}</td>
						<td class="text-right">
							@if(in_array($ta->id, $my_agencies))
								{{-- Desvincular --}}
								<a href="{{ route('agencies.destroy', $ta->id) }}" class="btn btn-xs btn-danger btn-confirm" data-msg="{{ trans('textos.agencia_desvincular_confirm') }}" rel="tooltip" title="{{ ucfirst(trans('textos.desvincular')) }}"><i class="fa fa-trash"></i></a>

								{{-- Editar --}}
								<a href="{{ route('providers.edit', $ta->id) }}" class="btn btn-xs btn-info @if(!in_array($ta->id, $my_agencies)) d-none @endif" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

							@else
								{{-- Vincular --}}
								<a href="{{ route('agencies.store', $ta->id) }}" class="btn btn-xs btn-success btn-confirm" data-msg="{{ trans('textos.agencia_vincular_confirm') }}" rel="tooltip" title="{{ ucfirst(trans('textos.vincular')) }}"><i class="fa fa-check"></i></a>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Agencies:
    if($('#tblAgencies').length > 0){
        $('#tblAgencies').DataTable();
    }
});
</script>
@endpush

@endsection