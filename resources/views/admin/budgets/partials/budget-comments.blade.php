<form method="post" autocomplete="off" action="{{ route('budgets.update-comments') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	{{ csrf_field() }}
	<input type="hidden" name="budget_id" value="{{ $budget->id }}">

	{{-- Notas --}}
	<div class="form-group row">
		<label for="comments" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.notas')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="comments" class="editor" value="{{ $budget->comments }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update_comments" label="{{ trans('textos.guardar') }}"/>
</form>	