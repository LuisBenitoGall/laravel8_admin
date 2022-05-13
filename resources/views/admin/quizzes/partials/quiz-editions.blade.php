<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblQuizEditions_">
			<thead>
				<tr>
					<td>{{ ucfirst(trans('textos.edicion')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.logo')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.inicio')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.fin')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.duracion')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.acciones')) }}</td>
				</tr>
			</thead>

			<tbody>
				@foreach($editions as $row)
					<tr>
						<td>{{ $row->name }}</td>
						<td class="text-center">
							@if($row->logo)
		                        <img src="{{ asset('img/quizzes/'.$row->logo) }}" width="50">
		                    @else
		                        <i class="fa fa-puzzle-piece icono"></i>
		                    @endif   
						</td>	
						<td class="text-right">
							@php
							$start = $row->start? \Carbon\Carbon::parse($row->start)->format($languages[session()->get('locale')][4]):'';
							@endphp
							{{ $start }}	
						</td>
						<td class="text-right">
							@php
							$finish = $row->finish? \Carbon\Carbon::parse($row->finish)->format($languages[session()->get('locale')][4]):'';
							@endphp
							{{ $finish }}
						</td>
						<td class="text-right">
							{{ $row->time / 60 }} {{ trans('textos.minutos') }}
						</td>
						<td class="text-right">
							@can('quizzes.edit')
								{{-- Edición activa --}}
								@if($row->status == 1)
									<i class="fa fa-star text-warning ml-2 mr-2" rel="tooltip" title="{{ ucfirst(trans('textos.edicion_activa')) }}"></i>
								@else
									<a href="{{ route('quiz-editions.status', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.edicion_activar')) }}"><i class="fa fa-star"></i></a>
								@endif

								{{-- Editar --}}
								<a href="{{ route('quiz-editions.edit', $row) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
							@endcan

							{{-- Eliminar --}}
							@can('quizzes.destroy')
								<form method="POST" action="{{ route('quiz-editions.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
			                        <input name="_method" type="hidden" value="DELETE">
			                        {{ csrf_field() }}
			                        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ trans('textos.edicion_confirm_eliminar') }}"><i class="fa fa-trash"></i></button>
			                    </form>
			                @endcan
						</td>
					</tr>
				@endforeach		
			</tbody>
		</table>
	</div>
</div>
