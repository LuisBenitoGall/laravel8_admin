@php
$budgetNum = $budget->budget_num? $budget->budget_num:$budget->id;
$bg_color = $company_settings->base_color_budgets? $company_settings->base_color_invoices:'#FFF';
@endphp

@include('pdf.partials.head', ['header_title' => ucfirst(trans('textos.presupuesto')).' #'.$budgetNum])

@include('pdf.partials.header', [
	'standard' => true, 
	'company_settings' => $company_settings, 
	'bg_color' => $bg_color, 
	'left_h1' => ucfirst(trans('textos.presupuesto')).' Nº '.$budgetNum,
	'left_title' => $budget->name? $budget->name:false,
	'left_manager' => $provider_manager? $provider_manager->name.' '.$provider_manager->surname:false,
	'right_h1' => ucfirst(trans('textos.cliente')),

])








@include('pdf.partials.footer')