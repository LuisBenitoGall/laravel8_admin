<span class="xoption-{{ $row->id }}">
    @if(isset($estados[$row->budget_status]))
        {!! ucfirst($estados[$row->budget_status]) !!}
    @endif
</span>
               