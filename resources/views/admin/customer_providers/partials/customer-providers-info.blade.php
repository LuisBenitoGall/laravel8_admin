<form method="post" autocomplete="off" action="{{ route('companies.update', $company->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

	@include('admin.companies.partials.form-edit', ['company' => $company])
</form>