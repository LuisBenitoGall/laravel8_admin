@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myUser" value="{{ $user->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('users.edit', $user) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_editar_usuario') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.empresas_de')) }} <u>{{ $user->name }} {{ $user->surname }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblUserCompanies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.empresa')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(document).on('change', '.btnAddCompany', function(){
    var status = '';
    var elem = $(this);
    //Atención!!: detecta el estado previo a la acción, de modo que :checked es si queda deseleccionado:
    if(elem.find('input').is(':checked')){
        status = 0;
    }else{
        status = 1;
    }  
    var company_id = elem.find('.chk').data('companyid');
    var user_id = elem.find('.chk').data('userid');
    $.get("/admin/user-companies/status/"+company_id+"/"+user_id+"/"+status+"", function(response, state){
        if(response){
        	//Recargando pantalla:
        	if(response == 'reload'){
        		location.reload();
        	}else{
	            $('.check-ko').remove();
	            elem.before('<i class="fa fa-check check-ok"></i>');
	            $('.check-ok').delay(1000).fadeOut();
	        }
        }else{
            elem.before('<i class="fa fa-exclamation-triangle check-ko"></i>');
        }
    });     
});
</script>
@endpush

@endsection