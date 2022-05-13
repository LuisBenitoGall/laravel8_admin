<div class="div-toggle">
	<a href="#" rel="tooltip" title="{{ ucfirst(trans('textos.vista_listado')) }}" class="btn-toggle list @if($view == 'list') active @endif" data-cookie="{{ $cookie }}">
		<i class="fa fa-list"></i>
	</a>
	<a href="#" rel="tooltip" title="{{ ucfirst(trans('textos.vista_cuadricula')) }}" class="btn-toggle grid @if($view == 'grid') active @endif" data-cookie="{{ $cookie }}">
		<i class="fa fa-th"></i>
	</a>
	<div class="clearfix"></div>
</div>