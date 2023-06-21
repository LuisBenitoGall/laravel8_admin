<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblUserProtocols">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.protocolo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.firma')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($protocols as $p)
					<tr>
						<td>{{ $p->name }}</td>
						<td class="text-right">
							{{ $p->created_at->format($languages[session()->get('locale')][4]) }}	
						</td>
						<td>{{ $p->description }}</td>
						{{-- Firma --}}
						<td>
							@if($p->signed_at)
								{{ Carbon\Carbon::parse($p->signed_at)->format($languages[session()->get('locale')][4]) }}
							@else
								<form method="post" action="#" class="frmAcceptWithPass" accept-charset="utf-8" enctype="multipart/form-data" role="form">
									{{ csrf_field() }}
									<input type="hidden" name="id" value="{{ $p->puId }}">

									<div class="row form-group" style="margin-bottom: 0;">
										<div class="col-10 hold-pwd sinpadding-right">
											<input type="password" name="password" class="form-control" placeholder="{{ trans('textos.acepta_con_pwd') }}" required>
										</div>
										<div class="col-2 pt-1 sinpadding-left">
											<button type="submit" class="btn btn-sm btn-circle btn-warning ml-1" rel="tooltip" title="{{ ucfirst(trans('textos.guardar')) }}">
												<i class="fa fa-check"></i>
											</button>
										</div>
									</div>
								</form>
							@endif
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Ver protocolo --}}
							<a target="_blank" href="{{ route('protocols.show', $p->id) }}" class="btn btn-info btn-xs" rel="tooltip" title="{{ trans('textos.protocolo_ver') }}">
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

