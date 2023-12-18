@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('timetable-signed.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_horarios_firma') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.firmas')) }} <u>{{ ucfirst($month_) }} {{ $year }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblTimetableSignedReports">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_firma')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.documento')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>	
				@foreach($data as $row)
					<tr>
						{{-- Usuario --}}
						<td>
							{{ $row->name }} {{ $row->surname }}
						</td>
						{{-- Estado --}}
						<td>
							@if($row->signed == 1)
								<span style="display: none">1</span>
								<span class="text-success">{{ trans('textos.firmado') }}</span>
							@else
								<span style="display: none">2</span>
								<span class="text-danger">{{ ucfirst(trans('textos.pendiente')) }}</span>
							@endif	
						</td>
						{{-- Fecha --}}
						<td class="text-center">
							@if($row->signed == 1)
								{!! $row->updated_at->format($languages[session()->get('locale')][4]) !!}
							@endif	
						</td>
						{{-- Documento --}}
						<td class="text-center">
							@if($row->doc && file_exists('docs/timetables_signed/'.$row->doc))
								<a target="_blank" href="{{ asset('docs/timetables_signed/'.$row->doc) }}" class="btn btn-info btn-sm" rel="tooltip" title="{{ ucfirst(trans('textos.documento_ver')) }}">
									<i class="fa fa-calendar-alt"></i>
								</a>
							@endif	
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Eliminar --}}
							<form method="POST" action="{{ route('timetable-signed.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
						        <input name="_method" type="hidden" value="DELETE">
						        {{ csrf_field() }}
						        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.firma_eliminar")) !!}" data-msg="{{ ucfirst(trans("textos.firma_eliminar_confirm")) }}"><i class="fa fa-trash"></i></button>
						    </form>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

@endsection