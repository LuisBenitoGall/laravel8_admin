@props(['name',
    'accepted_files' => '
    application/vnd.ms-excel, 
    application/vnd.ms-excel, 
    application/vnd.ms-excel, 
    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
    application/vnd.oasis.opendocument.spreadsheet'])

<div class="input-group">
    {{-- No incluye preview del archivo --}}
    <input type="text" class="form-control image-preview-filename" disabled="disabled"> 
    <span class="input-group-btn">
        <!-- image-preview-clear button -->
        <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
            <i class="fa fa-times"></i> {!! ucfirst(trans('textos.quitar')) !!}
        </button><!-- 
        image-preview-input 
        --><div class="btn btn-default image-preview-input">
            <i class="fa fa-folder-open"></i>
            <span class="image-preview-input-title">{!! ucfirst(trans('textos.buscar')) !!}</span>
            <input type="file" accept="{{ $accepted_files }}" name="{{ $name }}"/>
        </div>
    </span>
</div>