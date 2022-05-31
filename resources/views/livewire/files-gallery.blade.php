<div class="row">
    <div class="col-12">
        {{-- Dropzone --}}
        <div class="dropzone"></div>

        <input type="hidden" id="listenNewFile" value="false">
        <br>

        <div id="galeria">
            @php
            $i = 1;
            @endphp

            @foreach($files as $file)
                <div class="file-box">
                    <div class="file">
                        @php
                        $class = in_array($file->mime, $mimes_img)? 'image':'icon';
                        @endphp

                        <div class="{{ $class }}">
                            @if($class == 'image')
                                <img alt="{{ $file->alt }}" class="img-fluid" src="{{ asset($file->path.$file->file) }}">

                            @else
                                @php
                                $icon = isset($icons[$file->ext])? $icons[$file->ext]:'file';
                                @endphp

                                <i class="fa fa-{{ $icon }}"></i>
                            @endif                         
                        </div>   

                        <div class="file-name">
                            @php
                            $file_name = (strlen($file->file) > 30) ? substr($file->file,0,27).'...':$file->file;
                            @endphp

                            <small>{{ $file_name }}</small>
                        </div> 

                        <div class="file-controls">
                            {{-- Copiar URL --}}
                            @php
                            $url = env('APP_URL').$file->path.$file->file;
                            @endphp

                            {{-- data-text="{{ $url }}" 

                                data-clipboard-text="{{ $url }}"
                            --}}

                            <button class="copia2 text-info" data-text="{{ $url }}" rel="tooltip" title="{{ ucfirst(trans('textos.copiar_url')) }}">
                                <i class="fa fa-copy"></i>
                            </button>
                            {{-- <input type="hidden" id="elem{{ $i }}" value="{{ $url }}"> --}}

                            {{-- Descargar --}}
                            <a href="{{ $file->path.$file->file }}" class="text-info ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.descargar')) }}" download>
                                <i class="fa fa-download"></i>
                            </a>

                            {{-- Editar --}}
                            <a href="#" wire:click="edit({{ $file }})" class="text-info ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-toggle="modal" data-target="#modal-edit-file">
                                <i class="fa fa-edit"></i>
                            </a>

                            {{-- Eliminar --}}
                            <a href="#" wire:click="$emit('triggerDelete', {{ $file->id }})" class="text-danger ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ trans('textos.archivo_confirm_eliminar') }}">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div> 

                @php
                $i++;
                @endphp   
            @endforeach
        </div>
    </div>

    {{-- Modals --}}
    {{-- Edit file --}}
    <div wire:ignore.self class="modal fade modal-edit-file" id="modal-edit-file" tabindex="-1" role="dialog" aria-labelledby="editFile" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editFile">{!! ucfirst(trans('textos.archivo_editar')) !!}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form wire:submit.prevent="update" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                        {{ csrf_field() }}
                        <input type="hidden" name="file_id" wire:model.defer="file_id">

                        {{-- Imagen --}}
                        @if($image)
                            <div class="form-group row" id="add_image">
                                {{-- Imagen --}}
                                <div class="col-lg-12 text-center">
                                    <img src="{{ asset($image) }}" class="img-fluid" alt="{{ $alt }}">
                                </div>
                            </div>
                        @endif

                        <div class="form-group row">
                            {{-- Título --}}
                            <label for="title" class="col-lg-12">{!! ucfirst(trans('textos.titulo')) !!}</label>
                            <div class="col-lg-12">
                                <input type="text" class="form-control" name="title" wire:model.defer="title">
                            </div>
                        </div>

                        <div class="form-group row">
                            {{-- Alt --}}
                            <label for="alt" class="col-lg-12">Alt</label>
                            <div class="col-lg-12">
                                <input type="text" class="form-control" name="alt" wire:model.defer="alt">
                            </div>
                        </div>

                        <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/plugins/dropzone/dropzone.css') }}">
    @endpush
    @endonce

    {{-- Scripts --}}
    @once
    @push('scripts')
    <script src="{{ asset('js/plugins/dropzone/dropzone.js') }}"></script>
    {{-- <script src="{{ asset('js/plugins/clipboard/clipboard.min.js') }}"></script> --}}

    <script>
    //Sentencia para poder utilizar JQuery con Livewire:
    document.addEventListener('DOMContentLoaded', function(){
        //Dropzone imágenes:
        var myDropzone = new Dropzone('.dropzone', {
            url: '/admin/files/store',
            paramName: 'file',
            aceptedFiles: 'image/*',
            maxFilesize: 2,     //2MB
            maxFiles: 10,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dictDefaultMessage: '<i class="fa fa-file"></i> {{ trans("textos.arrastra_docs") }}'
        });

        //Envío correcto:
        myDropzone.on('success', function(file, res, done){
            if(res){
                //Recargar imágenes:
                @this.set('reload', true);
                Livewire.emit('getReload', true);
            }
        });

        //Error:
        myDropzone.on('error', function(file, res){
            var msg = res.errors.photo[0];
            $('.dz-error-message:last > span').text(msg);
        });

        //Sweet Alert eliminar archivo:
        @this.on('triggerDelete', file_id => {
            Swal.fire({
                title: txtArchivoConfirmEliminar,
                text: '',
                icon: "warning",
                iconColor: color_warning,
                showCancelButton: true,
                confirmButtonColor: color_primary,
                cancelButtonColor: color_danger,
                confirmButtonText: txtContinuar,
                cancelButtonText: txtCancelar
            }).then((result) => {
                //if user clicks on delete
                if(result.value){
                    // calling destroy method to delete
                    @this.call('delete', file_id)
                    // success response
                    Swal.fire({
                        title: txtArchivoEliminado, 
                        icon: 'success',
                        confirmButtonColor: color_primary,
                    });
                // }else{
                //     Swal.fire({
                //         title: 'Operation Cancelled!',
                //         icon: 'success'
                //     });
                }
            });
        });

        //Cerrar modal tras update:
        window.livewire.on('postUpdated', function(){
            $('#modal-edit-file').modal('hide');
        });
    });

    Dropzone.autoDiscover = false;
    </script>  
    @endpush
    @endonce
</div>

