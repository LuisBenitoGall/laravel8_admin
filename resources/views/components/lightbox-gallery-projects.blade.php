@props(['docs' => false, 'image_mimes', 'other_files' => []])

{{-- <div class="ibox">
    <div class="ibox-content"> --}}
        <div class="lightBoxGallery mt-3">
            <p class="warning2" id="featured">
                    {{ trans('textos.sin_imagen_principal_aviso') }}
                </p>

                <p class="warning2" id="thumbnail">
                    {{ trans('textos.sin_imagen_thumbnail_aviso') }}    
                </p>

            <div class="row">
                @php
                $featured = false;
                $thumbnail = false;
                @endphp

                @foreach($docs as $doc)
                    @if($doc->featured == 1)
                        @php
                        $featured = true;
                        @endphp    
                    @endif

                    @if($doc->thumbnail == 1)
                        @php
                        $thumbnail = true;
                        @endphp    
                    @endif

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
    {{-- </div>    
</div> --}}

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/blueimp/css/blueimp-gallery.min.css') }}">
@endpush
@endonce

@once
@push('scripts')
<script src="{{ asset('js/plugins/blueimp/jquery.blueimp-gallery.min.js') }}"></script>

<script>
$(function(){
    // Destacar imagen.
    $('.btnDestacar').on('click', function(e){
        e.preventDefault();
        var este = $(this);
        var img = $(this).data('img');
        var project = $(this).data('project');
        var modulo = $(this).data('modulo');
        $('#featured').fadeOut();

        if(img && project){
            $.get("/admin/"+modulo+"/set-featured/"+project+"/"+img, function(response, state){
                $('.btnDestacar').removeClass('destacado');
                este.addClass('destacado');
            });
        }
    });  

    // Imagen thumbnail.
    $('.btnThumb').on('click', function(e){
        e.preventDefault();
        var este = $(this);
        var img = $(this).data('img');
        var project = $(this).data('project');
        $('#thumbnail').fadeOut();

        if(img && project){
            $.get("/admin/project-docs/set-thumb/"+project+"/"+img, function(response, state){
                $('.btnThumb').removeClass('destacado');
                este.addClass('destacado');
            });
        }
    });

    // Avisos:
    @if(!$featured)
        $('#featured').fadeIn();
    @endif

    @if(!$thumbnail)
        $('#thumbnail').fadeIn();
    @endif
});
</script>
@endpush
@endonce