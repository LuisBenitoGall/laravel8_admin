@props(['docs', 'image_mimes'])

<div class="ibox">
    <div class="ibox-content">
        <div class="lightBoxGallery">
            <div class="row">
                @foreach($docs as $doc)
                    {{-- Filtramos las imágenes --}}
                    @if(in_array($doc->type, $image_mimes))
                        {{-- Método render de la imagen --}}
                        {!! $renderImg($doc, true) !!}
                    @else
                        @php
                        array_push($other_files, $doc);
                        @endphp
                    @endif
                @endforeach

                {{-- Render otros archivos --}}
                @foreach($other_files as $doc)
                    {{-- Método render de la imagen --}}
                    {!! $renderImg($doc) !!}   
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