@php
$procedureNum = $procedure->procedure_num? $procedure->procedure_num:$procedure->id;
$bg_color = $company_settings->base_color_budgets? $company_settings->base_color_invoices:'#FFF';
@endphp

@include('pdf.partials.head', ['header_title' => ucfirst(trans('textos.procedimiento')).' #'.$procedureNum])

<table class="tblHeader" id="subheader">
	<tbody>
		<tr>
			<th class="tbl-header-left">
				<h1>{{ $procedure->name }}</h1>
				<br><br>

				<p>
					{!! $revision->content !!}
				</p>


				{{-- 23/05/22 Falta definir diseño del contenido. --}}
			</th>
		</tr>
	</tbody>
</table>

@include('pdf.partials.footer')