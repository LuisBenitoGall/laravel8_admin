{{-- Líneas presupuesto básico --}}
@if($order->modality == config('constants.BUDGET_BASIC_MODALITY_'))
    @include('admin.budgets.partials.budget-items-basic')	
@endif

{{-- Componente líneas de presupuesto por formulario --}}
@if($order->modality == config('constants.BUDGET_FORM_MODALITY_'))
	@livewire('budget-item', ['budget' => $order, 'order' => $order, 'shared' => $shared])
@endif

{{-- Líneas presupuesto por catálogo --}}
@if($order->modality == config('constants.BUDGET_CATALOGUE_MODALITY_'))
    @include('admin.budgets.partials.budget-items-by-catalogue')
@endif
	