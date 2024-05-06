@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
    {{-- Acciones: --}}
    <div class="col-12">
        {{-- Volver a consultas --}}
        <a href="{{ route('order-queries.index') }}" class="btn btn-info">
            <i class="fa fa-angle-left"></i>
            {!! trans('textos.volver_consultas') !!}
        </a>
    </div>
</div>

{{-- Filtro --}}
<div class="row">
    <div class="col-12">                        
        <div class="card mb-3">
            <div class="card-body">
                <form method="post" autocomplete="off" action="{{ route('order-queries.by-items') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                
                    <div class="form-group row">
                        {{-- Selector de empresas --}}
                        <label for="companies" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresas')) !!}*</label>
                        <div class="col-lg-4">
                            @if(count($user_companies) > 1)
                                <x-select name="companies[]" class="select2" multiple="true" :options="$user_companies" :values="$companies_" required/> 
                            @else
                                <x-select name="companies[]" class="select2" :options="$user_companies" disabled="true" :values="$user_companies" required/> 
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
                            <x-select name="companies[]" class="select2" :options="$order_status"/>
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
    {{-- <div class="col-12 mb-3">
        <h3>
            {!! trans('textos.resultados_consulta') !!} <b></b>
        </h3>
    </div> --}}

    @if($with_query)
        {{-- Columnas --}}
        @php
        $columns = [
            ['proveedor', '', 'wdt100'],
            ['cliente', '', 'wdt100'],
            ['cliente_final', '', 'wdt100'],
            ['presupuesto', 'text-center', 'wdt100'],
            ['pedido', 'text-center', 'wdt100'],
            ['titulo', '', 'wdt100'],
            ['creado', 'text-center', 'wdt100'],
            ['aceptado', 'text-center', 'wdt100'],
            ['entregado', 'text-center', 'wdt100'],
            ['estado', '', 'wdt100'],
            ['proveedor_manager', '', 'wdt100'],
            ['comercial', '', 'wdt100'],
            ['cliente_manager', '', 'wdt100'],
            ['cliente_final_manager', '', 'wdt100'],
            ['bolsa', '', 'wdt100'],
            ['producto', '', 'wdt100'],
            ['producto_contable', '', 'wdt100'],
            ['cantidad', 'text-center', 'wdt100'],
            ['precio', 'text-center', 'wdt100'],
            ['iva', 'text-center', 'wdt100'],
            ['irpf', 'text-center', 'wdt100'],
            ['descuento', '', 'wdt100'],
            ['por_porcentaje', '', 'wdt100'],
            ['dias', '', 'wdt100'],
            ['dias_continuos', '', 'wdt100'],
            ['dias_continuos_dto', '', 'wdt150'],
            ['importe', 'text-center', 'wdt100'],
            ['centro_coste_proveedor', '', 'wdt200'],
            ['centro_coste_cliente', '', 'wdt200']
        ]
        @endphp 

        <div class="col-12 col-md-4 mb-3">
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
                            <li>
                                <a href="#" class="toggle-vis" data-column="{{ $i }}">{{ ucfirst(trans('textos.'.$col[0])) }}</a>

                                <i class="fa fa-check text-success"></i>
                            </li>
                        @endforeach 
                    </ul>                    
                </div>
            </div>
        </div>

        <div class="table-responsive">
            
            
            <table class="table table-hover dtabla" id="tblOrderQueries" style="width:1500%">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th class="{{ $col[1] }} {{ $col[2] }} nowrap">{{ ucfirst(trans('textos.'.$col[0])) }}</th>
                        @endforeach

                        {{-- <td>{{ trans('textos.proveedor') }}</td>
                        <td>{{ trans('textos.cliente') }}</td>
                        <td>{{ trans('textos.cliente_final') }}</td>
                        <td>{{ trans('textos.presupuesto') }}</td>
                        <td>{{ trans('textos.pedido') }}</td>
                        <td>{{ trans('textos.titulo') }}</td>
                        
                        <td>{{ trans('textos.descripcion') }}</td>
                        <td>{{ trans('textos.aceptado') }}</td>
                        <td>{{ trans('textos.entregado') }}</td> --}}
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

{{ $from_ }} ---- {{ $to_ }}

@push('scripts')
<script>
$(function(){
    //Tabla Order Queries:
    if($('#tblOrderQueries').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var companies = {!! json_encode($companies_) !!};
        var order_type = '{{ $order_type }}';
        var from = {{ strtotime($from_) }};
        var to = {{ strtotime($to_) }};

        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrderQueries_'+window.location.pathname));

        var tableOrderQueries = $('#tblOrderQueries').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            scrollX: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                type: "GET",
                url: '/api/admin/order-queries/by-items-dt/'+companies+'/'+order_type+'/'+from+'/'+to,
                error: function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'provider', name: "cp.name"},
                {data: 'customer', name: "cc.name"},
                {data: 'final_customer', name: "cfc.name"},
                {data: 'budget', name: "o.budget_num", className: 'text-right'},
                {data: 'order', name: "o.order_num", className: 'text-right'},
                {data: 'order_name', name: "o.name"},
                {data: 'created_at', name: 'o.created_at', className: 'text-right'},
                {data: 'accepted', name: 'o.accepted_date', className: 'text-right'},
                {data: 'delivered', name: 'o.delivery_date', className: 'text-right'},
                {data: 'status', name: 'o.order_status'},
                {data: 'provider_manager'},
                {data: 'commercial'},
                {data: 'customer_manager'},
                {data: 'final_customer_manager'},
                {data: 'bag', className: 'text-right'},
                {data: 'product', name: "p.name"},
                {data: 'accounting_product', name: "p.name"},
                {data: 'qty', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'iva', className: 'text-right'},
                {data: 'irpf', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                {data: 'by_percent', className: 'text-right'},
                {data: 'days', className: 'text-right'},
                {data: 'days_continue', className: 'text-right'},
                {data: 'days_continue_discount', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'provider_cost_center'},
                {data: 'customer_cost_center'}
            ]
        });

        document.querySelectorAll('a.toggle-vis').forEach((el) => {
            el.addEventListener('click', function (e) {
                e.preventDefault();

                var is_ok = el.querySelector('.fa');


                // if(is_ok){
                //     alert('verdadero');
                // }else{
                //     alert('falso');
                // }
         
                let columnIdx = e.target.getAttribute('data-column');
                let column = tableOrderQueries.column(columnIdx);
         
                // Toggle the visibility
                column.visible(!column.visible());
            });
        });
    }
});
</script>
@endpush

@endsection