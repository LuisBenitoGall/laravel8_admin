@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('attributes.create')
			<a class="btn btn-info" href="{{ route('attributes.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.atributo_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAttributes">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.atributo')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th>{!! ucfirst(trans('textos.tipo_dato')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Attributes:
    if($('#tblAttributes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var dataTypes = {!! json_encode($datatypes) !!};

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblAttributes_'+window.location.pathname));

        $('#tblAttributes thead tr').clone(true).appendTo('#tblAttributes thead');
        $('#tblAttributes thead tr:eq(1) th').each(function(i){
            var title = $(this).text();

            if(i == 2) { // Asegúrate de que este índice corresponde a la columna datatype
                // Crear un selector con los tipos de datos
                var selectHtml = '<select class="DTsearch"><option value="">'+txtBuscar+' '+title+'</option>';
                $.each(dataTypes, function(key, value) {
                    selectHtml += '<option value="'+key+'">'+value+'</option>';
                });
                selectHtml += '</select>';
                $(this).html(selectHtml);

                // Manejar el evento change del selector
                $('select.DTsearch', this).on('change', function(){
                    var selectedValue = this.value; // Obtener el valor seleccionado
                    console.log("Selected Value: ", selectedValue);
                    // Filtrar por el valor seleccionado
                    tableAttributes
                        .column(i)
                        .search(selectedValue ? '^' + selectedValue + '$' : '', true, false) // Búsqueda exacta
                        .draw();
                });

            } else if(i == 3){
                $(this).html('');
            } else {
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableAttributes.column(i).search() !== this.value){
                    tableAttributes
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableAttributes = $('#tblAttributes').DataTable({
            serverSide: true,
            processing: true, 
            //scrollX: true,
            //fixedColumns: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/attributes",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'attributes.name'},
                {data: 'description', name: 'attributes.description'},
                {data: 'datatype', name: 'attributes.datatype'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
