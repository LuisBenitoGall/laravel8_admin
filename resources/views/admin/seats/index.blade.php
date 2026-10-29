@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="seats">
<input type="hidden" id="seatsYear" value="{{ $year }}">
<input type="hidden" id="seatsAccountingAccount" value="@if($accounting_account){{ $accounting_account->id }}@endif">
<input type="hidden" id="seatsFrom" value="{{ $from }}">
<input type="hidden" id="seatsTo" value="{{ $to }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Ver todos los asientos --}}
		@if($accounting_account)
			<a href="{{ route('seats.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_asientos_contables') !!}
			</a>
		@endif

		{{-- Nuevo asiento  --}}
		@can('seats.create')
			<a class="btn btn-info" href="{{ route('seats.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.asiento_nuevo')) !!}</a>
		@endcan	

		{{-- Modelos --}}
		<a class="btn btn-info" href="{{ route('seat-models.index') }}"><i class="fa fa-list"></i> {!! ucfirst(trans('textos.modelos')) !!}</a>

		{{-- Importación saldos --}}
		<a class="btn btn-info" href="{{ route('seat-lines.import-balances') }}"><i class="fa fa-file-import"></i> {!! ucfirst(trans('textos.saldos_importar')) !!}</a>
	</div>
</div>

{{-- Filtros --}}
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('seats.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])

						{{-- Cuenta contable --}}
						@php
						$ac = $accounting_account? $accounting_account->account.' - '.$accounting_account->name:'';
						@endphp
						<label for="accounting_account_" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_contable')) !!}</label>
						<div class="col-lg-6 position-relative">
							{{-- <x-input type="text" name="accounting_account_" datas="id:1" class="search_accounting_account" id="searchAccountingAccount1" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250"/>

                            <input type="hidden" name="accounting_account_id" id="accountingAccountId1" value="@if($accounting_account) {{ $accounting_account->id }} @endif"> --}}

                            <x-input type="text" name="accounting_account_" id="search_accounting_account" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="150"/>

							<input type="hidden" name="accounting_account_id" id="accountingAccountId" value="@if($accounting_account) {{ $accounting_account->id }} @endif">
						</div>
					</div>

					<div class="form-group row">
						{{-- Desde --}}
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-lg-2">
							@php
							$from_ = $from != ''? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):\Carbon\Carbon::parse(date('Y').'-01-01')->format($languages[session()->get('locale')][4]);
							@endphp

							<x-date-picker name="from" value="{{ $from_ }}" id="accountFrom" :format="$languages[config('app.locale')][4]"/>	
						</div>

						{{-- Hasta --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-lg-2">
							@php
							$to_ = $to != ''? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):\Carbon\Carbon::parse(date('Y').'-12-31')->format($languages[session()->get('locale')][4]);
							@endphp

							<x-date-picker name="to" value="{{ $to_ }}" id="accountTo" :format="$languages[config('app.locale')][4]"/>	
						</div>	
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
{{-- / Filtro --}}

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			@if($accounting_account)
				{!! trans('textos.extracto') !!}	
				<b>{{ $accounting_account->account }} - {{ $accounting_account->name }}</b>
			@else
				{!! trans('textos.asientos_contables') !!} 
				<b>{{ $company->name }}</b>
			@endif 
		</h3>
	</div>

	<div class="table-responsive">
		{{-- Tabla extracto de líneas --}}
		@if($accounting_account)
			<table class="table table-hover dtabla" id="tblSeatLines">
				<thead>
					<tr>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.asiento')) !!}</th>
						<th class="wdt100">{!! ucfirst(trans('textos.descripcion')) !!}</th>
						<th class="wdt50">{!! ucfirst(trans('textos.documento')) !!}</th>
						<th class="wdt50 text-center">{!! ucfirst(trans('textos.debe')) !!}</th>
						<th class="wdt50 text-center">{!! ucfirst(trans('textos.haber')) !!}</th>
						<th class="wdt50 text-center">{!! ucfirst(trans('textos.saldo')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				@if($seat_lines && $seat_lines->count())
					@php
					$balance_ = !empty($balance)? $balance['amount']:'0';
					@endphp
					<tbody>
						{{-- Línea saldo --}}
						@if(!empty($balance))
							<tr class="featured">
								<td></td>
								<td></td>
								<td>{{ trans('textos.cuenta_anterior_saldo') }}</td>
								<td></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
								<td class="text-right">{{ $balance_ }}</td>
								<td></td>
							</tr>
						@endif

						@foreach($seat_lines as $sl)
							@php
							//Estado de la cuenta contable en el ejercicio buscado:
							$balance_status = $sl->accountBalanceByYear($sl->accounting_account_id, $sl->year);
							$status_ = $balance_status && $balance_status->status == 1? false:true;

							//Saldo:
							$balance_ = ($balance_ + $sl->debit) - $sl->credit;
							@endphp

							<tr>
								{{-- Fecha --}}
								<td class="text-right">
									{{ Carbon\Carbon::parse($sl->seat_date)->format($languages[session()->get('locale')][4]);}}	
								</td>
								{{-- Asiento --}}
								<td class="text-right">
									{{ $sl->id }}
								</td>
								{{-- Descripción --}}
								<td>
									{{ $sl->description }}	
								</td>
								{{-- Documento --}}
								<td>
									{{ $sl->doc_num }}	
								</td>
								{{-- Debe --}}
								<td class="text-right">
									@if($sl->debit && $sl->debit > 0) {{ number_format($sl->debit, 2) }} @else 0 @endif
								</td>
								{{-- Haber --}}
								<td class="text-right">
									@if($sl->credit && $sl->credit > 0) {{ number_format($sl->credit, 2) }} @else 0 @endif
								</td>
								{{-- Saldo --}}
								<td class="text-right">
									{{ $balance_ }}	
								</td>
								{{-- Acciones --}}
								<td class="text-right">
									{{-- Ver asiento --}}
									<a href="#" data-seatid="{{ $sl->seat_id }}" class="btn btn-xs btn-info btnSeatShow" data-toggle="modal" data-target="#modal-seat-show" rel="tooltip" title="{{ ucfirst(trans('textos.asiento_ver')) }}"><i class="fa fa-eye"></i></a>	

									{{-- Sólo pueden editarse o eliminarse líneas cuyo año de registro no esté cerrado --}}
									{{-- @if( (int)$sl->year != $year_ || ( (int)$sl->year == $year_ && $status_ == '0') ) --}}
									@if($status_)
										{{-- Editar líneas de asiento --}}
										<a href="#" data-lineid="{{ $sl->id }}" data-seatid="{{ $sl->seat_id }}" data-seat="{{ $sl->description }}" class="btn btn-xs btn-info btnSeatEdit" data-toggle="modal" data-target="#modal-seat-edit" rel="tooltip" title="{{ ucfirst(trans('textos.asiento_editar')) }}"><i class="fa fa-edit"></i></a>	
									@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				@endif
			</table>

		{{-- Tabla asientos --}}
		@else
			<table class="table table-hover dtabla" id="tblSeats">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.any')) !!}</th>
						<th class="wdt150">{!! ucfirst(trans('textos.concepto')) !!}</th>
						<th class="wdt50">{!! ucfirst(trans('textos.empresa')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.documento')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		@endif
	</div>
</div>

{{-- Modals: --}}
{{-- Modal show seat: --}}
@include('admin.seats.partials.modal-seat-show')

{{-- Modal edit seat --}}
@include('admin.seats.partials.modal-seat-edit')

@push('scripts')
<script>
$(function(){
	//Tabla Seats:
    if($('#tblSeats').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var year = $('#seatsYear').val();
        var accounting_account_id = $('#seatsAccountingAccount').val()? $('#seatsAccountingAccount').val():'x';
        var from = $('#seatsFrom').val();
        var to = $('#seatsTo').val();

        function format(d) {
            // `d` is the original data object for the row
            if (d.length > 0) {
                var tr = '';
                d.forEach(function (row, line) {
                    var debit = row.debit? parseFloat(row.debit)+row.symbol:'';
                    var credit = row.credit? parseFloat(row.credit)+row.symbol:'';
                    tr += '<tr>' +
                    '<td colspan="1" class="no-border text-right">' + row.line_num + '</td>' +
                    '<td colspan="1" class="no-border">' + row.account+' - '+row.name+'</td>' +
                    '<td colspan="1" class="no-border text-right">' + debit+'</td>' +
                    '<td colspan="1" class="no-border text-right">' + credit+'</td>' +
                    '</tr>';
                });
            } else {
                tr = '<tr>' +
                '<td colspan="4" class="no-border">' + txtObservacionesSin + '</td>' +
                '</tr>';
            }

            var html = '<table class="tbl-subrows" cellpadding="0" cellspacing="0" border="0" width="100%">' +
            '<thead>' +
            '<th class="text-center">#</th>' +
            '<th>'+txtCuentaContable+'</th>' +
            '<th class="text-center">'+txtDebe+'</th>' +
            '<th class="text-center">'+txtHaber+'</th>' +
            '</thead>' +
            tr +
            '</table>';

            return html;
        }

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblSeats_'+window.location.pathname));

        $('#tblSeats thead tr').clone(true).appendTo('#tblSeats thead');
        $('#tblSeats thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 6){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }

            $('input', this).on('keyup change', function(){
                if(tableSeats.column(i).search() !== this.value){
                    tableSeats
                    .column(i)
                    .search(this.value)
                    .draw();
                }
            });
        });

        var tableSeats = $('#tblSeats').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/seats/"+from+"/"+to+"/"+accounting_account_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', name: 'seats.seat_date', className: 'text-right', "defaultContent": ""},
                {data: 'seat_date', name: 'seats.seat_date', className: 'text-right', "defaultContent": ""},
                {data: 'year', name: 'seats.year', className: 'text-right', "defaultContent": ""},
                {data: 'concept', name: 'seats.concept', "defaultContent": ""},
                //{data: 'amount', className: 'text-right', "defaultContent": ""},
                // {data: 'debe', className: 'text-right', "defaultContent": ""},
                // {data: 'haber', className: 'text-right', "defaultContent": ""},
                // {data: 'saldo', className: 'text-right', "defaultContent": ""},
                {data: 'company_target', name: 'companies.name', "defaultContent": ""},
                {data: 'doc', className: 'text-center', name: 'seats.doc_num', "defaultContent": ""},
                {data: 'acciones', className: 'text-right'}
            ]
        });

        $('#tblSeats tbody').on('click', 'td .btnSeeItem', function () {
            var tr = $(this).closest('tr');
            var row = tableSeats.row(tr);
            var seat_id = $(this).data('seat-id');
            var route = "/api/admin/seat-lines/" + seat_id;

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.ajax({
                    url: route,
                    cache: true
                })
                .done(function (data) {
                    row.child(format(data)).show();
                    tr.addClass('shown');
                });
            }
        });
    }
	
	//Tabla Seat Lines:
    //14/11/2023: por ahora se cargarán los datos directamente desde PHP, pues no he hallado el modo de arrastrar el saldo de una fila a otra.
    if($('#tblSeatLines').length > 0){
        // var tokenDT = $('meta[name="csrf-token"]').attr('content');
        // var year = $('#seatsYear').val();
        // var accounting_account_id = $('#seatsAccountingAccount').val()? $('#seatsAccountingAccount').val():'x';
        // var from = $('#seatsFrom').val()? $('#seatsFrom').val():year+'-01-01';
        // var to = $('#seatsTo').val()? $('#seatsTo').val():year+'-12-31'; 

        var tableSeatLines = $('#tblSeatLines').DataTable({
            order: [[1, 'asc']]
            // serverSide: true,
            // processing: true,
            // ajax: {
            //  "type": "GET",
            //  "url": "/api/admin/seat-lines/"+accounting_account_id+"/dt/"+from+"/"+to,
            //  "error": function(reason){
            //      console.log(reason);
            //  }
            // },
            // columns: [
            //  {data: 'date', name: 'seats.seat_date', className: 'text-right', "defaultContent": ""},
            //  {data: 'seat', name: 'seats.year', className: 'text-right', "defaultContent": ""},
            //  {data: 'description', name: 'seats.description', "defaultContent": ""},
            //  {data: 'doc', "defaultContent": ""},
            //  {data: 'debe', className: 'text-right', "defaultContent": ""},
            //  {data: 'haber', className: 'text-right', "defaultContent": ""},
            //  {data: 'saldo', className: 'text-right', "defaultContent": ""},
            //  {data: 'acciones', className: 'text-right', "defaultContent": ""}
            // ]
        });

        //tableSeatLines.row.add(['','','','','','','xxxxxxx','yyyy']).draw();
    }
	
	var seat_line_types = [];

	//Selector de año:
	$('#selectorYear').on('change', function(){
		var year = $(this).val();
		$('#accountFrom').val('01/01/'+year);
		$('#accountTo').val('31/12/'+year);
	});

	//Modal mostrar asiento contable:
	$(document).on('click', '.btnSeatShow', function(){
		var seat_id = $(this).data('seatid');

		$('.btnGoSeat').prop('href', '/admin/seats/'+seat_id+'/edit');

		$.get("/admin/seats/"+seat_id+"/show", function(response, state){
			if(response){
				$('#seatId b, #showSeat b').text(response.seat.id);
				var date_ = response.seat.seat_date.split('-');
				$('#seatDate b').text(date_[2]+'-'+date_[1]+'-'+date_[0]);
				$('#seatDescription b').text(response.seat.description);
				if(response.diary){
					$('#seatDiary').show();
					$('#seatDiary b').text(response.diary);		
				}else{
					$('#seatDiary').hide();
				}
				if(response.concept){
					$('#seatConcept').show();
					$('#seatConcept b').text(response.concept);		
				}else{
					$('#seatConcept').hide();
				}

				//Líneas:
				if(response.lines.length > 0){
					var html = '';
					for(i=0; i<response.lines.length; i++){
						var invoice_ = response.lines[i].invoice_num? response.lines[i].invoice_num:(response.lines[i].invoice_old_num? response.lines[i].invoice_old_num:false);
						var debit = response.lines[i].debit > 0? response.lines[i].debit+response.lines[i].symbol:'0';
						var credit = response.lines[i].credit > 0? response.lines[i].credit+response.lines[i].symbol:'0';
						var invoice = response.lines[i].invoice? response.lines[i].invoice:'';

						html += '<tr>';
						html += '<td>'+response.lines[i].account+' - '+response.lines[i].name+'</td>';
						html += '<td class="text-right">'+debit+'</td>';
						html += '<td class="text-right">'+credit+'</td>';
						html += '<td class="text-right">'+invoice+'</td>';
						html += '</tr>';	
					}

					$('#tblSeatLinesDetail tbody').html(html);
				}
			}
		});
	});

	//Modal editar línea de asiento contable.
	$(document).on('click', '.btnSeatEdit', function(){
		var seat_id = $(this).data('seatid');
		var seat = $(this).data('seat');
		$('#editSeat b').text(seat);
		$('#frmEditSeat').attr('action', '/admin/seat-lines/'+seat_id+'/update');
		$('.btnGoSeat').prop('href', '/admin/seats/'+seat_id+'/edit');

		$.get("/admin/seat-lines/"+seat_id+"/edit", function(response, state){
			if(response){
				console.log(response);
				if(response.length > 0){
					var html = '';
					for(i=0; i<response.length; i++){
						html += '<div class="form-group row">';	
							html += '<input type="hidden" name="id[]" value="'+response[i].id+'">'
							html += '<div class="col-1 text-right line-num" style="padding-top: 7px;">'+response[i].line_num+'.</div>';
							html += '<div class="col-md-5"><input type="text" class="form-control" value="'+response[i].account+'"></div>';
							html += '<input type="hidden" name="accounting_account[]" value="'+response[i].accounting_account_id+'">'
							var type = seat_line_types[response[i].type] !== undefined? seat_line_types[response[i].type]:'';
							html += '<div class="col-md-2"><input type="text" class="form-control" value="'+type+'" readonly></div>';
							html += '<input type="hidden" name="type[]" value="'+response[i].type+'">';
							var debit = response[i].debit? response[i].debit:'0';
							html += '<div class="col-md-2"><input type="text" name="debit[]" class="form-control text-right from-lines" value="'+debit+'"></div>';
							var credit = response[i].credit? response[i].credit:'0';
							html += '<div class="col-md-2"><input type="text" name="credit[]" class="form-control text-right from-lines" value="'+credit+'"></div>';
						html += '</div>';
					}

					$('#modal-seat-edit #editSeatLines').html(html);
				}
			}
		});
	});
});
</script>
@endpush

@endsection