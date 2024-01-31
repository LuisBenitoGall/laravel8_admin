@props(['docs' => false, 'other_files' => []])

@php
$image_mimes = App\Helpers\helpers::imgMimeTypes();
@endphp

<div class="ibox">
    <div class="ibox-content">
        <div class="lightBoxGallery">
            <div class="row">
                @foreach($docs as $doc)
                    {{-- Filtramos las imágenes --}}
                    @if(in_array($doc->type, $image_mimes))
                        {{-- Método render de la imagen --}}
                        @php
                        $html = App\Helpers\helpers::renderImg($doc, 'orders', true);
                        @endphp
                        {!! $html !!}
                    @else
                        @php
                        array_push($other_files, $doc);
                        @endphp
                    @endif
                @endforeach

                {{-- Render otros archivos --}}
                @foreach($other_files as $doc_)
                    {{-- Método render de la imagen --}}
                     @php
                    $html_ = App\Helpers\helpers::renderImg($doc_, 'orders', false);
                    @endphp
                    {!! $html_ !!}
                @endforeach
            </div>

            {{-- Controls --}}
            <div id="blueimp-gallery" class="blueimp-gallery">
                <div class="slides"></div>
                <h3 class="title"></h3>
                <a class="prev">‹</a>
                <a class="next">›</a>
                <a class="close">×</a>
                <a class="play-pause"></a>
                <ol class="indicator"></ol>
            </div>
        </div>
    </div>    
</div>

@php

@endphp

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/blueimp/css/blueimp-gallery.min.css') }}">
@endpush
@endonce

@once
@push('scripts')
<script src="{{ asset('js/plugins/blueimp/jquery.blueimp-gallery.min.js') }}"></script>
@endpush
@endonce