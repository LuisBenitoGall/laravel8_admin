@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('currencies.create')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-currency"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.moneda_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCurrencies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.moneda')) !!}</th>
					<th>{!! ucfirst(trans('textos.codigo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.numero')) !!} (ISO 4217)</th>
					<th>{!! ucfirst(trans('textos.simbolo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Nueva moneda: --}}
@include('admin.currencies.partials.modal-add-currency')

{{-- Editar moneda: --}}
@include('admin.currencies.partials.modal-edit-currency')

@push('scripts')
<script>
$(function(){
	//Tabla Currencies:
    if($('#tblCurrencies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblCurrencies thead tr').clone(true).appendTo('#tblCurrencies thead');
        $('#tblCurrencies thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableCurrencies.column(i).search() !== this.value){
                    tableCurrencies
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableCurrencies = $('#tblCurrencies').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/currencies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'code'},
                {data: 'number', className: 'text-right'},
                {data: 'symbol', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = row['status'] == 1? 'checked':'';
                        var activo = row['status'] == 1? 1:'0';
                        estado = '<span style="display: none">'+activo+'</span><label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="currencies/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditCurrency" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-currency" data-id="'+row['id']+'" data-currency="'+row['name']+'" data-symbol="'+row['symbol']+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="currencies/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtMonedaEliminar+'" data-msg="'+txtMonedaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }
});

{{-- Modal editar moneda --}}
$(document).on('click', '.btnEditCurrency', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    var currency = $(this).data('currency');
    var symbol = $(this).data('symbol');
    var action = '/admin/currencies/'+id;

    $('#frm-edit-currency').attr('action', action);
    $('#currencyName').val(currency);
    $('#currencyId').val(id);
    $('#currencySymbol').val(symbol);
});
</script>
@endpush

@endsection