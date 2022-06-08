<div class="mini-modal">
    <a href="#" class="btn btn-xs btn-info btnMiniModal" rel="tooltip" title="{{ trans('textos.estado') }}">
        <i class="fa fa-filter"></i>
    </a>

    <div class="mini-modal-content">
        <div class="mini-modal-items">
            <span class="arrow"></span>

            @foreach($options as $i => $row)
                <p class="mm-option" data-id="{{ $id }}" data-option="{{ $i }}">{{ ucfirst($row) }}</p>
            @endforeach
        </div>
    </div>
</div>