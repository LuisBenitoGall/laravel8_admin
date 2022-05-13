<div class="modal fade" id="modal-phones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.telefonos')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="" id="frmSavePhone" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="workplace_id" value="{{ $workplace_id }}">

                    <div class="form-group row">
                        {{-- Número --}}
                        <div class="col-md-12">
                            <label for="phone_number">{!! trans('textos.nuevo_numero') !!} *</label>
                        </div>
                        <div class="col-md-9">
                            <x-input type="text" name="phone_number" autocomplete="off" maxlength="15" required/>
                        </div>
                    
                        <div class="col-md-3 text-right">
                            <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}" inline="true"/>
                        </div>
                    </div>
                </form>   
                <hr>

                <div class="form-group row phones-list">
                    @foreach($phones as $phone)
                        <div class="col-lg-12 pt-2 mb-3 phone">
                            <a href="tel:{{ $phone->phone_number }}">{{ $phone->phone_number }}</a>    

                            <button class="btn btn-sm btn-danger btn-confirm-ajax float-right btnDeletePhone" data-msg="{!! trans('textos.telefono_confirm_eliminar') !!}" data-id="{{ $phone->id }}"><i class="fa fa-trash"></i></button>                           
                        </div>
                    @endforeach  
                </div>     
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
$(function(){
    //Guardar número de teléfono.
    $('#frmSavePhone').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            url: "/admin/phones/store",
            type: "POST",
            data: data, 
            cache: false,
            datatype: "JSON",
            success: function(response){
                $('#frmSavePhone input[name="phone_number"]').val('');   
                var html = '<div class="col-lg-12 pt-2 mb-3 phone">';
                    html += '<a href="tel:'+response.phone_number+'">'+response.phone_number+'</a>';
                    html += '<button class="btn btn-sm btn-danger btn-confirm-ajax float-right btnDeletePhone" data-msg="'+txtTelefonoConfirmEliminar+'" data-id="'+response.id+'"><i class="fa fa-trash"></i></button>';
                html += '</div>';  
                $('.phones-list').prepend(html);   
            }
        });
    });

    // Eliminar número de teléfono.
    $(document).on('click', '.btnDeletePhone', function(e){
        e.preventDefault();
        var msg = $(this).data('msg');
        var id = $(this).data('id');   
        var padre = $(this).parent();

        swal.fire({
            title: msg,
            text: "",
            icon: "warning",
            iconColor: color_warning,
            showCancelButton: true,
            confirmButtonColor: color_primary,
            cancelButtonColor: color_danger,
            confirmButtonText: txtEliminar,
            cancelButtonText: txtCancelar
        }).then((result) => {
            if(result.isConfirmed){
                $.get("/admin/phones/"+id+"/delete", function(response, state){
                    if(response){
                        padre.remove();   
                    }
                });    
            }
        });
    });
});
</script>
@endpush
@endonce