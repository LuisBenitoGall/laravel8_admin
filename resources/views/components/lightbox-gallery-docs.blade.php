@props(['docs' => false])

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
                        $html = $renderImg($doc, true);
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
                    $html_ = $renderImg($doc_);
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
public function renderImg($doc, $gallery = false){
    $render = '<div class="gallery-item col-sm-3 mb-4">
        <div class="gallery-img">';
        if($gallery){
            $render .= '<a href="/img/'.$this->schema.'/'.$doc->doc.'" data-gallery="">
                <img src="/img/'.$this->schema.'/'.$doc->doc.'">
                </a>';
        }else{
            $render .= '<span class="icon">
                <i class="fa fa-file mb-2"></i><br>
                '.$doc->doc.'
            </span>';    
        }

        $render .= '</div><div class="gallery-controls">';
            //Descargar:
            if(!$gallery){
                $render .= '<span>
                    <a href="/img/'.$this->schema.'/'.$doc->doc.'" download="'.$doc->doc.'" rel="tooltip" title="'.trans('textos.archivo_descargar').'">
                        <i class="fa fa-download"></i>
                    </a>
                </span>';
            }

            //Eliminar:
            $render .= '<span>
                <a href="/admin/'.$this->schema.'-docs/'.$doc->id.'/destroy" class="btn-confirm btn-controls" data-msg="'.ucfirst(trans('textos.archivo_confirm_eliminar')).'" rel="tooltip" title="'.ucfirst(trans('textos.archivo_eliminar')).'">
                    <i class="fa fa-trash"></i>
                </a>
            </span> 
        </div>
    </div>';   

    return $render; 
}
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