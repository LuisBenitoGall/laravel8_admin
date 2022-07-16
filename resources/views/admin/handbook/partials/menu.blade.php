@php
// Menú:
$pathMenu = storage_path()."/json/basic-menu.json";
$handbook_menu = json_decode(file_get_contents($pathMenu), true); 

$pathMenuOp = storage_path()."/json/secondary-menu.json";
$handbook_secondary_menu = json_decode(file_get_contents($pathMenuOp), true); 

$prefix = 'hb-';
@endphp

<div class="col-md-3 col-lg-2">
	<div class="card mb-3">
		<div class="card-body">
			<p>{{ trans('textos.indice_contenidos') }}</p>

			<div id="jstree">
				<ul class="handbook-menu">
					@foreach($handbook_menu as $option)
	                	@if($option['active'])
	                		<li class="@if(!empty($option['suboptions'])) jstree-open @endif">
	                			<a href="#" class="@if(!empty($option['suboptions'])) hasSubmenu @else getHbContent @endif" data-hb="{{ $prefix }}{{ $option['option'] }}">
	                				<i class="fa fa-fw fa-caret-right"></i>
	                				{!! ucfirst(trans('textos.'.$option['label'])) !!}
	                			</a>

            					<ul>
	            					@foreach($option['suboptions'] as $suboption)
	                					<li data-jstree='"type":"css"}'>
	                						<a href="#" class="getHbContent" data-hb="{{ $prefix }}{{ $suboption['suboption'] }}">
	                                            {!! ucfirst(trans('textos.'.$suboption['label'])) !!}
	                                        </a>   	
	                					</li>
	                				@endforeach
                				</ul>
	                		</li>
	                	@endif
	                @endforeach

	                @foreach($handbook_secondary_menu as $option)
	                	@if(session('modules') && in_array($option['id'], session('modules')))
	                		<li class="@if(!empty($option['functionalities'])) jstree-open @endif">
	                			<a href="#" class="@if(is_array($option['functionalities'])) hasSubmenu @else getHbContent @endif" data-hb="{{ $prefix }}{{ $option['slug'] }}">
	                				<i class="fa fa-fw fa-caret-right"></i>
	                				{!! ucfirst(trans('textos.'.$option['label'])) !!}
	                			</a>

                				<ul>
                					@foreach($option['functionalities'] as $func)
                						<li data-jstree='"type":"css"}'>
	                						<a href="#" class="getHbContent" data-hb="{{ $prefix }}{{ $func['label'] }}">
	                                            {!! ucfirst(trans('textos.'.$func['label'])) !!}
	                                        </a>   	
	                					</li>
                					@endforeach
                				</ul>
	                		</li>
	                	@endif
	                @endforeach
				</ul>
			</div>
		</div>
	</div>
</div>