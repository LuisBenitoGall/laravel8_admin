<div class="ibox">
    <div class="ibox-content">
        <div class="lightBoxGallery">
            <div class="row">
                @foreach($images as $img)
                    <div class="gallery-item col-sm-3 mb-4">
                        <div class="gallery-img">
                            <a href="{{ asset('img/questions/'.$img->image) }}" data-gallery="">
                                <img src="{{ asset('img/questions/'.$img->image) }}">
                            </a>    
                        </div>
                          
                        <div class="gallery-controls">
                            {{-- Destacada: --}}
                            <span>
                                <button class="btn-controls btnDestacar @if($img->featured == 1) destacado @endif" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_destacada')) }}" data-img="{{ $img->id }}" data-question="{{ $img->question_id }}" data-modulo="question-images">
                                    <i class="fa fa-star"></i>  
                                </button>
                            </span>   

                            {{-- Thumbnail: --}}
                            <span>
                                <button class="btn-controls btnThumb @if($img->thumbnail == 1) destacado @endif" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_thumbnail')) }}" data-img="{{ $img->id }}" data-question="{{ $img->question_id }}">
                                    <i class="fa fa-image"></i> 
                                </button>
                            </span>

                            {{-- Eliminar: --}}
                            <span>
                                <a href="{{ route('question-images.destroy', [$img->question_id, $img]) }}" class="btn-confirm btn-controls" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </span> 
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
        var question = $(this).data('question');
        var modulo = $(this).data('modulo');

        if(img && question){
            $.get("/admin/"+modulo+"/set-featured/"+question+"/"+img, function(response, state){
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
        var question = $(this).data('question');

        if(img && question){
            $.get("/admin/question-images/set-thumb/"+question+"/"+img, function(response, state){
                $('.btnThumb').removeClass('destacado');
                este.addClass('destacado');
            });
        }
    });
});
</script>
@endpush
@endonce