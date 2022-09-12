<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblProductPrices">
			<thead>
				<tr>
					<th class="text-center">{{ ucfirst(trans('textos.precio')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.desde')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.hasta')) }}</th>
				</tr>
			</thead>

			<tbody>
				{{-- Primer precio es el precio actual --}}
				@if(!$prices->count())
					@php
					$from_date = $product->created_at->format($languages[session()->get('locale')][4]);
					@endphp
				@else
					@php
					$from_date = $prices[0]->created_at->format($languages[session()->get('locale')][4]);
					@endphp
				@endif
				<tr>
					<td class="text-right">{{ $product->price }}{{ $currency->symbol }}</td>
					<td class="text-right">{{ $from_date }}</td>
					<td class="text-right">{{ trans('textos.precio_vigente') }}</td>
				</tr>

				@if($prices->count())
					@php
					//Guardamos la fecha de inicio del precio actual como fecha final del anterior.
					$prev_price = $prices->count() == 1? $product->created_at->format($languages[session()->get('locale')][4]):$from_date;
					$i = 1;
					@endphp

					@foreach($prices as $pr)
						@php
						//Si se trata del último registro, la fecha inicio corresponde a la fecha de alta del producto, si no a la fecha de finalización del anterior producto.
						$prev_price = $i == $prices->count()? $product->created_at->format($languages[session()->get('locale')][4]):$prices[$i]->created_at->format($languages[session()->get('locale')][4]);
						@endphp

						<tr>
							<td class="text-right">{{ $pr->price }}{{ $currency->symbol }}</td>
							<td class="text-right">{{ $prev_price }}</td>
							<td class="text-right">{{ $pr->created_at->format($languages[session()->get('locale')][4]) }}</td>
						</tr>

						@php
						$i++;
						@endphp
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
</div>