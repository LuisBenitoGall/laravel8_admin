<div class="modal fade" id="modal-add-provider" tabindex="-1" role="dialog" aria-labelledby="addProvider" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addProvider">{!! ucfirst(trans('textos.proveedor_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="#" id="frm-add-provider" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="guardian" value="true">
                    <input type="hidden" name="business_relationship" value="providers">

                    <div class="form-group row">
                        {{-- Razón social de la empresa --}}
                        <label for="name" class="col-12">{!! ucfirst(trans('textos.empresa')) !!} *</label>
                        <div class="col-12">
                            <x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" autocomplete="off" maxlength="150" required info="company-name"/>
                        </div>  
                    </div>

                    <div class="form-group row">
                        {{-- Nombre comercial --}}
                        <label for="tradename" class="col-12">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
                        <div class="col-12">
                            <x-input type="text" name="tradename" placeholder="{!! ucfirst(trans('textos.nombre_comercial')) !!}" autocomplete="off" maxlength="150" required info="company-tradename"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- NIF --}}
                        <label for="nif" class="col-12">NIF *</label>
                        <div class="col-12">
                            @php
                            $is_invalid = '';
                            @endphp
                            @error('nif')
                                @php
                                $is_invalid = 'is-invalid';
                                @endphp
                            @enderror

                            <x-input type="text" name="nif" class="{{ $is_invalid }}" placeholder="NIF" autocomplete="off" maxlength="11" required info="company-nif"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save_from_expense" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
    //Guardar nuevo proveedor:
    $(document).on('submit', '#frm-add-provider', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $('#modal-add-provider').modal('toggle');

        $.ajax({
            url: "/admin/companies/store-from-expense",
            type: "POST",
            data: data, 
            cache: false,
            datatype: "JSON",
            success: function(response){
                var newOption = new Option(response.name, response.id, true, true);
                var arr = [response.id];
                $('#expenseProvider').append(newOption).val(arr).trigger('change');
            }
        });
    });
});
</script>
@endpush