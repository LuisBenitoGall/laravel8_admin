@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if($tab != 'read') active @endif">
			    		<a class="nav-link @if($tab != 'read') active @endif" href="#tab-unread" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.notificaciones_no_leidas')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'read') active @endif">
			    		<a class="nav-link @if($tab == 'read') active @endif" href="#tab-read" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.notificaciones_leidas')) !!}</a>
			  		</li>
				</ul>

				<div class="tab-content pt-5">
					{{-- Notificaciones NO leídas --}}
					<div class="tab-pane fade @if($tab != 'read') show active @endif" role="tabpanel" id="tab-unread">
						@include('admin.notifications.partials.unread')	
					</div>

					{{-- Notificaciones leídas --}}
					<div class="tab-pane fade @if($tab == 'read') show active @endif" role="tabpanel" id="tab-read">
						@include('admin.notifications.partials.read')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection