@php
// Detectar IP:
$ip = request()->ip();

// Agent:
use Jenssegers\Agent\Agent;
$agent = new Agent();

// Menú:
$pathMenu = storage_path()."/json/basic-menu.json";
$menu = json_decode(file_get_contents($pathMenu), true);

$pathMenuOp = storage_path()."/json/secondary-menu.json";
$secondary_menu = json_decode(file_get_contents($pathMenuOp), true);

// Language:
if(session()->get('locale') == NULL){
	\Session::put('locale', App::getLocale());    
} 

//dd(Auth::user()->getRoleNames(), Auth::user()->getAllPermissions());
@endphp

<body onload="horaActual()" @yield('on-load')>
	<div id="wrapper">
		{{-- Left Aside --}}
		<nav class="navbar-default navbar-static-side" role="navigation">
	        <div class="sidebar-collapse">
	            <ul class="nav metismenu" id="side-menu">
	            	{{-- LOGO --}}
	                <li class="nav-header">
	                    <div class="dropdown profile-element">

	                    	@php
                    		$logo = false;
                    		@endphp

                    		@if(session('companies'))
		                        @foreach(session('companies') as $c)
		                            @if($c->id == session('currentCompany') && $c->logo)
		                                @php
		                                $logo = 'img/companies/'.$c->logo;
		                                break;
		                                @endphp
		                            @endif
		                        @endforeach
		                    @endif

                            @if($logo)
    	                    	<a href="{{ route('dashboard') }}" class="logo">
    	                        	<img alt="image" class="img-fluid" src="{!! asset($logo) !!}"/>
    	                        </a>
                            @endif
	                    </div>
	                    <div class="logo-element px-3">
                            @if($logo)
	                           <img alt="image" class="img-fluid" src="{!! asset($logo) !!}"/>
                            @endif
	                    </div>
	                </li>

                    {{-- Control horario --}}
                    @if(env('HOUR_CONTROL'))
                        <li>
                            <a href="{{ route('time-control.show') }}">
                                <i class="fa fa-clock"></i>
                                <span class="nav-label">{!! ucfirst(trans('textos.control_horario')) !!}</span>
                            </a>   
                        </li>
                    @endif

	                {{-- MAIN MENU --}}
	                {{-- Menu básico --}}
					@foreach($menu as $option)
                        @if($option['active'])
                            {{-- Acceso al módulo --}}
                            @can($option['permission'])
                            	<li @if(isset($header['module']) && $option['module'] == $header['module']) class="active" @endif>
                            		<a href="{{ ($option['url'] != '')? route($option['url']):'#' }}">
                            			<i class="fa fa-{{ $option['icon'] }}"></i>
                            			<span class="nav-label">{!! ucfirst(trans('textos.'.$option['label'])) !!}</span>

                            			@if(!empty($option['suboptions']))
                            				<span class="fa arrow"></span>
                            			@endif
                            		</a>

                            		@if(!empty($option['suboptions']))
                            			<ul class="nav nav-second-level">
                            				@foreach($option['suboptions'] as $suboption)
                                                @php
                                                $paintOption = true;
                                                @endphp

                                                @if($suboption['setted'] && session('companySettings') && session('companySettings')[$suboption['setted']] == 0) 
                                                    @php
                                                    $paintOption = false;
                                                    @endphp
                                                @endif

                                                @if($paintOption)
                                                    @can($suboption['slug'].'.index')
                                                        <li @if(isset($header['slug']) && isset($suboption['slug']) && $header['slug'] == $suboption['slug']) class="active" @endif>
                                                            @php
                                                            $ruta = $suboption['url'];
                                                            @endphp
                                                            <a href="{{ route($ruta) }}"> 
                                                                {!! ucfirst(trans('textos.'.$suboption['label'])) !!}
                                                            </a>   
                                                        </li>
                                                    @endcan
                                                @endif
                                            @endforeach
                            			</ul>
                            		@endif
                            	</li>
                            @endcan
                        @endif
                    @endforeach

                    @php
                    $slug_exceptions = ['projects-create'];
                    @endphp

                    {{-- Menú secundario --}}
                    @if($secondary_menu)
                        @foreach($secondary_menu as $option)
                        	{{-- El módulo debe estar activo para la empresa --}}
                        	@if(session('modules') && in_array($option['id'], session('modules')))
                        		{{-- El usuario debe tener acceso al modulo --}}
                                @can('module_'.$option['slug'])
                                    <li @if(isset($header['module']) && $option['slug'] == $header['module']) class="active" @endif>
                                        <a 
                                            class="@if(isset($header) && $header['option'] == $option['slug']) active @endif" href="{{ !is_array($option['functionalities'])? route($option['slug'].'.index'):'#' }}">
                                            <i class="fa fa-{{ $option['icon'] }}"></i>
                                            <span class="nav-label">@if($option['label']) {!! ucfirst(trans('textos.'.$option['label'])) !!} @else {!! ucfirst(trans('textos.'.$option['slug'])) !!} @endif</span>

                                            @if(is_array($option['functionalities']))
                                                <span class="fa arrow"></span>
                                            @endif
                                        </a>

                                        @if(is_array($option['functionalities']))
                                            <ul class="nav nav-second-level">
                                                @foreach($option['functionalities'] as $func)  
                                                    @php
                                                    $ruta = !in_array($func['slug'], $slug_exceptions)? $func['slug'].'.index':$func['slug'];
                                                    @endphp

                                                    @if($func['slug'] == 'company-settings')
                                                        {{ dd($func) }}
                                                    @endif

                                                    {{-- Funcionalidad --}}
                                                    @can($func['slug'].'.index')
                                                        <li @if(isset($header['slug']) && isset($func['slug']) && $header['slug'] == $func['slug']) class="active" @endif>
                                                            <a href="{{ route($ruta) }}">
                                                                {{ ucfirst(trans('textos.'.$func['label'])) }}      
                                                            </a> 
                                                        </li>  
                                                    @endcan

                                                    {{-- Subopciones especiales --}}
                                                    @if($func['slug'] == 'orders')
                                                        <li @if(isset($header['slug']) && $header['slug'] == 'sales-create') class="active" @endif>
                                                            <a href="{{ route('orders.create', 'sale') }}">
                                                                {{ ucfirst(trans('textos.venta_nueva')) }}      
                                                            </a> 
                                                        </li>  

                                                        <li @if(isset($header['slug']) && $header['slug'] == 'purchases-create') class="active" @endif>
                                                            <a href="{{ route('orders.create', 'purchase') }}">
                                                                {{ ucfirst(trans('textos.compra_nueva')) }}      
                                                            </a> 
                                                        </li>  
                                                    @endif

                                                    @if($func['slug'] == 'budgets')
                                                        <li @if(isset($header['slug']) && $header['slug'] == 'budgets-create') class="active" @endif>
                                                            <a href="{{ route('budgets.create') }}">
                                                                {{ ucfirst(trans('textos.presupuesto_nuevo')) }}      
                                                            </a> 
                                                        </li>  
                                                    @endif

                                                    @if($func['slug'] == 'schedules')
                                                        <li @if(isset($header['slug']) && $header['slug'] == 'schedules-create') class="active" @endif>
                                                            <a href="{{ route('schedules.create') }}">
                                                                {{ ucfirst(trans('textos.agenda_nueva')) }}      
                                                            </a> 
                                                        </li>  
                                                    @endif

                                                    @if($func['slug'] == 'projects')
                                                        <li @if(isset($header['slug']) && $header['slug'] == 'projects-create') class="active" @endif>
                                                            <a href="{{ route('projects.create') }}">
                                                                {{ ucfirst(trans('textos.proyecto_nuevo')) }}      
                                                            </a> 
                                                        </li>  
                                                    @endif

                                                    @if($func['slug'] == 'sat')
                                                        <li @if(isset($header['slug']) && $header['slug'] == 'sat-create') class="active" @endif>
                                                            <a href="{{ route('sat.create') }}">
                                                                {{ ucfirst(trans('textos.sat_nuevo')) }}      
                                                            </a> 
                                                        </li>  
                                                    @endif

                                                    @if($func['slug'] == 'cash')
                                                        <li @if(isset($header['slug']) && $header['slug'] == 'cash-create') class="active" @endif>
                                                            <a href="{{ route('cash.create') }}">
                                                                {{ ucfirst(trans('textos.caja')) }}      
                                                            </a> 
                                                        </li>  
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endcan
                        	@endif
                        @endforeach
                    @endif

                    {{-- Handbook --}}
                    {{-- @can('accounts.index') --}}
                        <li class="special-link @if(isset($header['module']) && $header['module'] == 'handbook') active @endif">
                            <a href="{{ route('handbook.index') }}">
                                <i class="fa fa-fw fa-book"></i>
                                <span>{!! ucfirst(trans('textos.manual')) !!}</span>
                            </a>
                        </li>
                    {{-- @endcan --}}
	            </ul>
	        </div>
	    </nav>

	    
	    	
            