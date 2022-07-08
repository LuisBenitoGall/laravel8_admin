@can($join.'.edit')
	{{-- Estado --}}
	@php
	$title = $join == 'customers'? 'cliente_on_off':'proveedor_on_off';
	@endphp

	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.'.$title) }}">
        <input type="checkbox" name="status" @if($row->status) checked @endif><span class="chk chk-action" data-id="{{ $row->cp_id }}" data-ctrl="customers/status"></span>
    </label>

    {{-- Ver --}}
    <a target="_blank" href="{{ route($join.'.show', $row->cp_id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

    {{-- Nueva venta o compra --}}
    @php
    if($join == 'customers'){
    	$type = 'sale';
    }else{

    }
    @endphp
    @if($join == 'customers')
    	<a href="{{ route('orders.create', [$type, $row->id]) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ trans('textos.venta_nueva') }}"><i class="fa fa-cart-plus"></i></a>
    @else

    @endif

    {{--Condiciones: 
		1. La empresa está tutelada por la empresa en session.
    --}}
    @if($row->guardian == session('currentCompany'))
    	{{-- Editar --}}
		<a target="_blank" href="{{ route($join.'.edit', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

		{{-- Add manager --}}
    	<a href="#" class="btn btn-xs btn-info btn-module btnAddManager" rel="tooltip" title="{{ trans('textos.responsable_add') }}" data-toggle="modal" data-target="#modal-add-manager" data-id="{{ $row->id }}" data-company="{{ $row->name }}"><i class="fa fa-user-plus"></i></a>
	@endif

	{{-- Catálogo --}}
	<a href="{{ route('customer-products.index', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.catalogo')) }}"><i class="fa fa-gift"></i></a>

	{{-- Configuración del cliente --}}
	<a href="#" class="btn btn-xs btn-info btn-module btnCustomerConfig" rel="tooltip" title="{!! trans("textos.cliente_configuraciones") !!}" data-toggle="modal" data-target="#modal-edit-config" data-customer="{{ $row->id }}" data-baid="{{ $row->ba_id }}" data-workplaceid="{{ $row->workplace_id }}" data-ccid="{{ $row->cc_id }}" data-discount="{{ $row->discount }}" data-credit="{{ $row->credit_risk }}"><i class="fa fa-cog"></i></a>

	{{-- Condiciones de pago --}}
	<a href="{{ route('customers.pay-conditions', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{!! trans("textos.condiciones_pago") !!}"><i class="fa fa-money-check-alt"></i></a>
@endcan

@can($join.'.destroy')
	@php
	if($join == 'customers'){
		$title = 'cliente_eliminar';
		$msg = 'cliente_confirm_eliminar';
	}else{
		$title = 'proveedor_eliminar';
		$msg = 'proveedor_confirm_eliminar';
	}
	@endphp

	<form method="POST" action="{{ route('customers.destroy', $row->cp_id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <input type="hidden" name="tipo" value="{{ $join }}">
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.".$title)) !!}" data-msg="{!! trans("textos.".$msg) !!}"><i class="fa fa-trash"></i></button>
    </form>
@endcan

