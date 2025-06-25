<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('users.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!} </label>
						<div class="col-lg-2">
							@php
							$status_options = [
								't' => trans('textos.todos'),
								1 => trans('textos.activos'),
								2 => trans('textos.inactivos')
							];
							@endphp

							<x-select name="status" class="select2" :options="$status_options" value="{{ $status }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>