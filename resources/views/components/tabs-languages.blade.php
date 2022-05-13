@props(['languages'])

<div>
    <ul class="nav nav-tabs" role="tablist">
        @php
        $i = 0;
        @endphp
        @foreach($languages as $lang)
            <li class="nav-item active">
                <a class="nav-link @if($i == 0) active @endif" href="#tab-{{ $lang[0] }}" role="tab" data-toggle="tab">{{ $lang[3] }}</a>
            </li>

            @php
            $i++;
            @endphp
        @endforeach 
    </ul>            
</div>