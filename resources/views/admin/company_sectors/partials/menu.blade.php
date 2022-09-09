<div class="col-md-3">
	<div class="card mb-3">
		<div class="card-body">
			<p>{{ trans('textos.sectores_indice') }}</p>	

			<div id="jstree">
				<ul class="handbook-menu">
					@foreach($sectors as $row)
						@if($row->subcategory == 0)
							@php
							$suboptions = false;
							foreach($sectors as $subrow){
								if($subrow->subcategory == $row->id){
									$suboptions .= '<li data-jstree=\'"type":"css"}\'><a href="#" data-id="'.$subrow->id.'" class="getCompanies">'.$subrow->name.'</a></li>';
								}
							}
							@endphp

							<li class="@if($suboptions) jstree-open @endif">
	                			<a href="#" class="@if($suboptions) hasSubmenu @else getCompanies @endif" data-id="{{ $row->id }}">
	                				<i class="fa fa-fw fa-caret-right"></i>
	                				{!! $row->name !!}
	                			</a>

	                			@if($suboptions)
	                				<ul>
	                					{!! $suboptions !!}
	                				</ul>
	                			@endif
	                		</li>
						@endif
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>