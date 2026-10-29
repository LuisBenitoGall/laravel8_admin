<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('contacts.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Empresa --}}
						<label for="company_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
						<div class="col-lg-4">
							@php
							$company_name = $related_company? $related_company->name:'';
							$company_id = $related_company? $related_company->id:'';
							@endphp
							<x-input type="text" name="company_id" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150" value="{!! $company_name !!}"/>
						</div>

						<input type="hidden" name="company_id" id="companyId" value="{{ $company_id }}">

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!} </label>
						<div class="col-lg-4">
							<x-select name="status" class="select2" :options="$status_options" label="estado_selec" value="{{ $status }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}" reset="contacts.index"/>
				</form>
			</div>
		</div>
	</div>
</div>