<div class="mini-modal">
	<a href="#" class="btn btn-xs btn-info btnMiniModal" rel="tooltip" title="{{ ucfirst(trans('textos.estado')) }}"><i class="fa fa-filter"></i></a>

	<div class="mini-modal-content"><div class="mini-modal-items">
		<span class="arrow"></span>
		@foreach($estados as $i => $est)
			<p class="mm-option @if($item->status == $i) selected @endif" data-id="{{ $item->id }}" data-option="{{ $i }}">{{ ucfirst($est) }}</p>	
		@endforeach
	</div>
</div>