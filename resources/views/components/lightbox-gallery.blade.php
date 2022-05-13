<div class="ibox">
    <div class="ibox-content">
        <div class="lightBoxGallery">
            <div class="row">
                @foreach($images as $img)
                    <div class="gallery-item col-sm-3 mb-4">
                        <div class="gallery-img">
                            <a href="{{ asset('img/contents/'.$img->image) }}" data-gallery="">
                                <img src="{{ asset('img/contents/'.$img->image) }}">
                            </a>    
                        </div>
                          
                        <div class="gallery-controls">
                            {{-- Destacada: --}}
                            <span>
                                <button class="btn-controls btnDestacar @if($img->featured == 1) destacado @endif" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_destacada')) }}" data-img="{{ $img->id }}" data-content="{{ $img->content_id }}" data-modulo="contents-image">
                                    <i class="fa fa-star"></i>  
                                </button>
                            </span>   

                            {{-- Thumbnail: --}}
                            <span>
                                <button class="btn-controls btnThumb @if($img->thumbnail == 1) destacado @endif" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_thumbnail')) }}" data-img="{{ $img->id }}" data-content="{{ $img->content_id }}">
                                    <i class="fa fa-image"></i> 
                                </button>
                            </span>

                            {{-- Eliminar: --}}
                            <span>
                                <a href="{{ route('content-image.destroy', [$img->content_id, $img]) }}" class="btn-confirm btn-controls" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </span> 

                            {{-- Enlace --}}
                            <div class="copia doc-link">
                                {{ env('APP_URL') }}/img/contents/{{ $img->image }}
                            </div>
                        </div>
                    </div> 
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

<script>
$(function(){
    // Destacar imagen.
    $('.btnDestacar').on('click', function(e){
        e.preventDefault();
        var este = $(this);
        var img = $(this).data('img');
        var content = $(this).data('content');
        var modulo = $(this).data('modulo');

        if(img && content){
            $.get("/admin/"+modulo+"/set-featured/"+content+"/"+img, function(response, state){
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
        var content = $(this).data('content');

        if(img && content){
            $.get("/admin/contents-image/set-thumb/"+content+"/"+img, function(response, state){
                $('.btnThumb').removeClass('destacado');
                este.addClass('destacado');
            });
        }
    });
});
</script>
@endpush
@endonce