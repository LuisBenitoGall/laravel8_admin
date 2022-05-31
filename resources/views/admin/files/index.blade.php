@extends('admin.partials.layout')

@section('content')

{{-- <div class="row mb-3">
	<div class="col-12">


	</div>
</div> --}}

<div class="row mb-3">
	<div class="col-12">
		{{-- Livewire component --}}
		@livewire('files-gallery')
	</div>
</div>

@endsection