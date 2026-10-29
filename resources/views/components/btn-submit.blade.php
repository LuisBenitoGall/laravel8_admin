@props(['class' => '', 'classdiv' => false, 'confirm_msg' => false, 'datas' => false, 'disabled' => false, 'id' => false, 'inline' => false, 'label', 'name' => false, 'reset' => false])

<div class="@if($inline) col-md-2 @else form-group row mt-4 @endif">
    @if(!$inline)
        <div class="@if($classdiv) {{ $classdiv }} @else col-12 text-right @endif">
    @endif
        {{-- <button type="submit" 
            @if($name) name="{{ $name }}" @endif 
            @if($id) id="{{ $id }}" @endif 
            class="ladda-button ladda-button_ btn btn-info {{ $class }}" 
            data-style="expand-right"
            @if($disabled) disabled @endif
        >{{ ucfirst(trans('textos.'.$label)) }}</button> --}}

        <input type="submit" 
            @if($name) name="{{ $name }}" @endif
            @if($id) id="{{ $id }}" @endif
            class="btn btn-info {{ $class }}"
            @if($disabled) disabled @endif
            value="{{ ucfirst(trans('textos.'.$label)) }}"
            @if($datas) {!! $datas !!} @endif
        >

        {{-- Reset --}}
        @if($reset)
            <a href="{{ route($reset) }}" class="btn btn-info">Reset</a>
        @endif
    @if(!$inline)
        </div>
    @endif
</div>
