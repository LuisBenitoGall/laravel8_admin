<div class="modal fade" id="modal-add-note" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {!! ucfirst(trans('textos.nota_nueva')) !!}
                    <small class="ml-5">
                        <a href="{{ route('dashboard') }}">{{ ucfirst(trans('textos.ver_todas')) }}</a>
                    </small>
                </h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="#" id="frmSaveNote" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    <div class="form-group row">
                        {{-- Título --}}
                        <div class="col-md-12">
                            <label for="title">{!! trans('textos.titulo') !!} *</label>
                        </div>
                        <div class="col-12">
                            <x-input type="text" name="title" autocomplete="off" maxlength="150" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Nota --}}
                        <div class="col-md-12">
                            <label for="note">{!! trans('textos.nota') !!} *</label>
                        </div>
                        <div class="col-12">
                            <x-input-textarea name="note"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
$(function(){
    //Limpiar formulario.
    $('*[data-target="#modal-add-note"]').on('click', function(){
        $('#frmSaveNote').trigger('reset');
    });

    //Guardar nota.
    $('#frmSaveNote').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            url: "/admin/notes/store",
            type: "POST",
            data: data, 
            cache: false,
            datatype: "JSON",
            success: function(response){
                if($('.wrapper .notes').length > 0){
                    var note = '<li>';
                        note += '<div>';
                            note += '<small>'+response.fecha+'</small>';
                            note += '<h4>'+response.title+'</h4>';
                            note += '<p>'+response.note+'</p>';
                            note += '<a href="#" wire:click="delete('+response.id+')">';
                                note += '<i class="fa fa-trash"></i>';
                            note += '</a>';
                        note += '</div>';
                    note += '</li>';

                    $('.wrapper .notes').prepend(note);
                }

                var qty = $('#notesQty').text();
                var new_qty = parseInt(qty) + 1;
                $('#notesQty').text(new_qty);
            	$('#modal-add-note').modal('toggle'); 
            }
        });
    });
});
</script>
@endpush
@endonce



