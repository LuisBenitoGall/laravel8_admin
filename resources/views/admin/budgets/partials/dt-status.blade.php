<span class="xoption-{{ $budget->id }}">
    @if(isset($estados[$budget->budget_status]))
        {!! ucfirst($estados[$budget->budget_status]) !!}
    @endif
</span>
               