@extends('admin.partials.layout')

@section('content')

{{-- Livewire Component --}}
@livewire('module-edit', ['module' => $module, 'tab' => $tab])

{{-- Modals --}}
{{-- New functionality: --}}
@include('admin.modules.partials.modal-new-functionality')

{{-- Edit functionality: --}}
@include('admin.modules.partials.modal-edit-functionality')    

@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

<script>
// Modal editar funcionalidad
$(document).on('click', '.btnFunctionality', function(){
    var id = $(this).data('id');
    var label = $(this).data('label');

    $('#functionalityId').val(id);
    $('#functionalityLabel').val(label);
});
</script>
@endpush

@endsection
