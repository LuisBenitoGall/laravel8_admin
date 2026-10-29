@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
    {{-- Acciones: --}}
    <div class="col-12">
        {{-- Volver a consultas --}}
        @include('admin.order_queries.partials.return-queries')
    </div>
</div>

{{-- Filtro --}}
<div class="row">
    <div class="col-12">                        
        <div class="card mb-3">
            <div class="card-body">
                <form method="post" autocomplete="off" action="{{ route('order-queries.by-orders') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                
                    <div class="form-group row">
                        {{-- Selector de empresas --}}
                        <label for="companies" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresas')) !!}*</label>
                        <div class="col-lg-4">
                            @if(count($user_companies) > 1)
                                <x-select name="companies[]" id="testSelect" class="select2" multiple="true" :options="$user_companies" :values="$companies_" required/> 
                            @else
                                <x-select name="companies[]" id="testSelect" class="select2" :options="$user_companies" disabled="true" :values="$user_companies" required/> 
                            @endif
                        </div>

                        {{-- Ventas o compras --}}
                        <label for="order_type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.ventas')) !!}</label>
                        <div class="col-lg-1 sinpadding">
                            <label class="custom-chk-rdb mr-4" style="margin-bottom: 6px;">
                                <input type="radio" name="order_type" value="v" @if($order_type == 'v') checked @endif required>
                                <span class="rdb chksmall"></span>
                            </label> 
                        </div>  

                        <label for="order_type" class="col-lg-1 col-form-label sinpadding-left">{!! ucfirst(trans('textos.compras')) !!}</label>
                        <div class="col-lg-1 sinpadding">
                            <label class="custom-chk-rdb mr-4" style="margin-bottom: 6px;">
                                <input type="radio" name="order_type" value="c" @if($order_type == 'c') checked @endif required>
                                <span class="rdb chksmall"></span>
                            </label> 
                        </div>  
                    </div>

                    <div class="form-group row">
                        {{-- Desde --}}
                        <label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
                        <div class="col-lg-2">
                            @php
                            $from = \Carbon\Carbon::parse($from_)->format($languages[session()->get('locale')][4]);
                            @endphp

                            <x-date-picker name="from" value="{{ $from }}" :format="$languages[config('app.locale')][4]"/> 
                        </div>

                        {{-- Hasta --}}
                        <label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
                        <div class="col-lg-2">
                            @php
                            $to = \Carbon\Carbon::parse($to_)->format($languages[session()->get('locale')][4]);
                            @endphp

                            <x-date-picker name="to" value="{{ $to }}" :format="$languages[config('app.locale')][4]"/> 
                        </div>
                    
                        {{-- Estado --}}
                        <label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!} </label>
                        <div class="col-lg-2">
                            @php
                            $status = $status_ == 'all'? '':$status_;
                            @endphp
                            <x-select name="status" class="select2" :options="$order_status" label="estado_selec" value="{{ $status }}"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- / Filtro --}}

{{-- Tabla --}}
<div class="row">
    @if($with_query)
        {{-- Columnas --}}
        @php
        $columns = [
            ['id', 'text-center wdt50'],
            ['proveedor', 'wdt100'],
            ['cliente', 'wdt100'],
            ['cliente_final', 'wdt100'],
            ['pedido_es', 'wdt100'],
            ['presupuesto_es', 'wdt100'],
            ['agrupacion', 'wdt100'],
            ['presupuesto', 'text-center wdt100'],
            ['pedido', 'text-center wdt100'],
            ['importe', 'text-center wdt100'],
            ['titulo', 'wdt100'],
            ['creado', 'text-center wdt100'],
            ['aceptado', 'text-center wdt100'],
            ['estado', 'wdt100'],
            ['facturado', 'wdt100'],
            ['proveedor_manager', 'wdt150'],
            ['comercial', 'wdt100'],
            ['cliente_manager', 'wdt150'],
            ['cliente_final_manager', 'wdt150'],
            ['bolsa', 'text-center wdt100']
        ]
        @endphp 

        <div class="col-12 col-md-4 col-lg-3">
            <div class="ibox collapsed dt-columns">
                <div class="ibox-title bg-success">
                    <h5>{{ trans('textos.columnas_mostrar_ocultar') }}</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" style="display: none;">
                    <ul>
                        @foreach($columns as $i => $col)
                            <li class="row-{{ $i }}" data-visible="">
                                <a href="#" class="toggle-vis" data-column="{{ $i }}">{{ ucfirst(trans('textos.'.$col[0])) }}</a>

                                {{-- <i class="fa fa-check text-success"></i> --}}
                            </li>
                        @endforeach 
                    </ul>                    
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover dtabla" id="tblByOrders" style="width:1500%">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th class="{{ $col[1] }} nowrap">{{ ucfirst(trans('textos.'.$col[0])) }}</th>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    @else
        <div class="col-12 text-center pt-4">
            <h2>{!! trans('textos.filtra_resultados') !!}.</h2>
        </div>
    @endif
</div>

@push('scripts')
<script>
$(function(){
    //Tabla Order Queries:
    if($('#tblByOrders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var companies = {!! json_encode($companies_) !!};
        var order_type = '{{ $order_type }}';
        var from = {{ strtotime($from_) }};
        var to = {{ strtotime($to_) }};
        var status = '{{ $status_ }}';

        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblByOrders_'+window.location.pathname));

        var tableByOrders = $('#tblByOrders').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            scrollX: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                type: "GET",
                url: '/api/admin/order-queries/by-orders-dt/'+companies+'/'+order_type+'/'+status+'/'+from+'/'+to,
                error: function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'id', name: "orders.id", className: 'text-right'},
                {data: 'provider', name: "cp.name"},
                {data: 'customer', name: "cc.name"},
                {data: 'final_customer', name: "cfc.name"},
                {data: 'is_order', name: "orders.is_order", className: 'text-center'},
                {data: 'is_budget', name: "orders.is_budget", className: 'text-center'},
                {data: 'is_group', name: "orders.is_group", className: 'text-center'},
                {data: 'budget', name: "orders.budget_num", className: 'text-right'},
                {data: 'order', name: "orders.order_num", className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'order_name', name: "orders.name"},
                {data: 'created_at', name: 'orders.created_at', className: 'text-right'},
                {data: 'accepted', name: 'orders.accepted_date', className: 'text-right'},
                {data: 'status', name: 'orders.order_status'},
                {data: 'invoiced', name: 'orders.invoiced', className: 'text-center'},
                {data: 'provider_manager'},
                {data: 'commercial'},
                {data: 'customer_manager'},
                {data: 'final_customer_manager'},
                {data: 'bag', className: 'text-right'}
            ]
        });

        //Obtener número de columnas:
        var num_cols = $('#tblByOrders th').length;

        //Seteando estado de vista inicial de la columna:
        for (var i = 0; i < num_cols; i++){
            if(tableByOrders.column(i).visible()){
                $('.row-'+i).append('<i class="fa fa-check text-success"></i>');
                $('.row-'+i).data('visible', 1);
            }else{
                $('.row-'+i).data('visible', 2);    
            }
        }

        //Mostrar / ocultar columnas:
        document.querySelectorAll('a.toggle-vis').forEach((el) => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var parent = $(this).closest('li');
                is_ok = parent.find('.fa');

                if(parent.data('visible') == 1){
                    parent.find('.fa').remove();
                    parent.data('visible', 2);
                }else{
                    parent.append('<i class="fa fa-check text-success"></i>');
                    parent.data('visible', 1);
                }
         
                let columnIdx = e.target.getAttribute('data-column');
                let column = tableByOrders.column(columnIdx);

                // Toggle the visibility
                column.visible(!column.visible());
            });
        });
    }
});
</script>
@endpush

@endsection