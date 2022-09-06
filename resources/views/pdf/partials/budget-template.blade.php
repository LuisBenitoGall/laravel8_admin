@if($template)
	<div style="page-break-after:always;"></div>
	
	<table class="textos">
		<tbody>
			<tr>
				<td>{!! $template['conditions'] !!}</td>
			</tr>
			<tr>
				<td>{!! $template['payment_method'] !!}</td>
			</tr>
			<tr>
				<td>{!! $template['observations'] !!}</td>
			</tr>
			<tr>
				<td>{!! $template['footer_legend'] !!}</td>
			</tr>
		</tbody>
	</table>
@endif