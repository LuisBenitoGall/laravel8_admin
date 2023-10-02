@extends('admin.partials.layout')

@section('content')

{{-- Avisos --}}
<div class="alert alert-warning" role="alert" id="economicBalanceWarning">

</div>

<input type="hidden" id="dtController" value="projects">
<input type="hidden" id="myProject" value="{{ $project->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a proyectos: --}}
		@can('projects.index')
			<a href="{{ route('projects.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proyectos') !!}
			</a>
		@endcan

		{{-- Nuevo proyecto --}}
		@can('projects.create')
    		<a href="{{ route('projects.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.proyecto_nuevo')) !!}
    		</a>
        @endcan

        {{-- Nueva tarea --}}
        @can('projects.edit')
        	<a href="#" class="btn btn-info btnResetStatus" data-toggle="modal" data-target="#modal-add-task">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.tarea_nueva')) !!}
			</a>	
        @endcan

        {{-- Nuevo presupuesto --}}
        @can('budgets.create')
        	<a target="_blank" href="{{ route('budgets.create', $project->customer_id) }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.presupuesto_nuevo')) !!}
			</a>		
        @endcan

        {{-- Añadir miembro al equipo --}}
		@can('projects.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-member">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.miembro_equipo_add')) !!}
			</a>
		@endcan

		{{-- Adjuntar venta --}}
		@can(['projects.edit', 'orders.edit'])
        	<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-order">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.venta_add')) !!}
			</a>	
        @endcan

		{{-- Adjuntar compra --}}
		@can(['projects.edit', 'orders.edit'])
        	<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-purchase">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.compra_add')) !!}
			</a>	
        @endcan

        {{-- Adjuntar gasto --}}
        @can(['projects.edit', 'orders.edit'])
        	<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-expense">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.gastos_add')) !!}
			</a>	
        @endcan

        {{-- Adjuntar factura de compra --}}
        @can('invoices.create')
	        <a target="_blank" href="{{ route('invoices.create-from-provider', $project->id) }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.factura_compra_introducir')) !!}
			</a>
		@endcan

		{{-- Agrupaciones de proyectos --}}
		@can('projects.create')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-set-group">
				<i class="fa fa-object-group"></i>
				{!! ucfirst(trans('textos.proyectos_vinculados')) !!}
			</a>
		@endcan

        {{-- Estados de tareas --}}
        @can('projects.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-task-status">
				<i class="fa fa-columns"></i>
				{!! ucfirst(trans('textos.tareas_estados')) !!}
			</a>
		@endcan

		{{-- Balance económico --}}
		@can('orders.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-economic-balance">
				<i class="fa fa-chart-bar"></i>
				{!! ucfirst(trans('textos.balance_economico')) !!}
			</a>
		@endcan

		{{-- Favorito --}}
		<a href="#" class="btn btn-info btnFav @if($favourite)) selected @endif" rel="tooltip" title="{{ ucfirst(trans('textos.favorito')) }}" data-project="{{ $project->id }}"><i class="fa fa-star"></i></a>

        {{-- Eliminar proyecto --}}
        @can('projects.destroy')
        	<form method="POST" action="{{ route('projects.destroy', $project) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.proyecto_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.proyecto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
            </form>
        @endcan

        {{-- Presupuestos y pedidos --}}
        @if($project_orders->count())
        	@php
        	$budgets_ = [];
        	$orders_ = [];
        	foreach($project_orders as $po){
        		if($po->is_budget){
        			//$budgets_[$bo->id] = $bo->budget_num? $bo->budget_num:trans('textos.presupuesto').' '.$bo->id;
        			array_push($budgets_, $po->id);
        		}
        		if($po->is_order){
        			//$orders_[$bo->id] = $bo->order_num? $bo->order_num:trans('textos.pedido').' '.$bo->id;
        			array_push($orders_, $po->id);
        		}
        	}
        	@endphp

        	{{-- Presupuestos 
        		09/06/23: se oculta pues ya se muestra una pestaña con el listado de pedidos.
        	--}}
        	{{-- @if(!empty($budgets_))
        		<div class="d-inline-block align-top">
        			<x-select name="status" class="select2" id="linkBudget" label="presupuesto_ir" :options="$budgets_"/>
        		</div>
        	@endif --}}

        	{{-- Pedidos 
				26/07/22: se oculta pues ya se muestra una pestaña con el listado de pedidos.
        	--}}
        	{{-- @if(!empty($orders))
        		<div class="d-inline-block align-top">
        			<x-select name="status" class="select2" id="linkOrder" label="pedido_ir" :options="$orders_"/>
	        	</div>
        	@endif --}}
        @endif
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.proyecto')) !!} 
					<u>
						@if($project->name)
							{{ $project->name }}

							@if($project->project_num)
								- {{ $project->project_num }}
							@endif
						@else
							{{ $project->id }}
						@endif
					</u>
				</h3>	

				{{-- Info --}}
				<div class="mb-3">
					{{-- Cliente --}}
					@if($customer)
						<label class="mr-5">
							{!! ucfirst(trans('textos.cliente')) !!}: <b>
								<a target="_blank" href="{{ route('companies.show', $customer->id) }}">{{ $customer->name }}</a>
							</b>
						</label>
					@endif

					{{-- Propietario --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.propietario')) !!}: <b>{{ $owner->name }} {{ $owner->surname }}</b>

						@if(Auth::user()->id == $project->owner)
							<a href="#" data-toggle="modal" data-target="#modal-edit-owner" class="ml-2" rel="tooltip" title="{!! ucfirst(trans('textos.propietario_editar')) !!}">
								<i class="fa fa-edit"></i>
							</a>
						@endif
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}: <b>{{ $author->name }} {{ $author->surname }}</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $project->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $project->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-project-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.proyecto')) !!}</a>
			  		</li>
			  		@if(!empty($budgets_))
				  		<li class="nav-item @if($tab == 'budgets') active @endif">
				    		<a class="nav-link @if($tab == 'budgets') active @endif" href="#tab-project-budgets" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.presupuestos_ventas')) !!}</a>
				  		</li>
				  	@endif
				  	@if(!empty($orders_))
				  		<li class="nav-item @if($tab == 'orders') active @endif">
				    		<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-project-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos_venta')) !!}</a>
				  		</li>
				  	@endif
			  		<li class="nav-item @if($tab == 'tasks') active @endif">
			    		<a class="nav-link @if($tab == 'tasks') active @endif" href="#tab-project-tasks" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.tareas')) !!}</a>
			  		</li>
			  		<li class="nav-item @if($tab == 'team') active @endif">
			    		<a class="nav-link @if($tab == 'team') active @endif" href="#tab-project-team" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.equipo')) !!}</a>
			  		</li>
			  		<li class="nav-item @if($tab == 'docs') active @endif">
			    		<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-project-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.documentos')) !!}</a>
			  		</li>
			  		@if($project_purchases->count())
				  		<li class="nav-item @if($tab == 'purchases') active @endif">
				    		<a class="nav-link @if($tab == 'purchases') active @endif" href="#tab-project-purchases" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.compras')) !!}</a>
				  		</li>
				  	@endif
				  	@if($project_expenses->count())
				  		<li class="nav-item @if($tab == 'expenses') active @endif">
				    		<a class="nav-link @if($tab == 'expenses') active @endif" href="#tab-project-expenses" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.gastos')) !!}</a>
				  		</li>
				  	@endif

				  	@if($groups_in->count())
				  		<li class="nav-item @if($tab == 'groups') active @endif">
				    		<a class="nav-link @if($tab == 'groups') active @endif" href="#tab-project-groups" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.proyectos_grupos')) !!}</a>
				  		</li>
				  	@endif
				</ul>

				<div class="tab-content pt-4">
					{{-- Info del proyecto: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-project-info">
						@include('admin.projects.partials.project-info')	
					</div>	

					{{-- Presupuestos --}}
					@if(!empty($budgets_))
						<div class="tab-pane fade @if($tab == 'budgets') show active @endif" role="tabpanel" id="tab-project-budgets">
							@include('admin.projects.partials.project-budgets')	
						</div>
					@endif

					{{-- Pedidos de venta --}}
					@if(!empty($orders_))
						<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-project-orders">
							@include('admin.projects.partials.project-orders')	
						</div>
					@endif

					{{-- Tareas del proyecto: --}}
					<div class="tab-pane fade @if($tab == 'tasks') show active @endif" role="tabpanel" id="tab-project-tasks">
						@include('admin.projects.partials.project-tasks')	
					</div>

					{{-- Equipo --}}
					<div class="tab-pane fade @if($tab == 'team') show active @endif" role="tabpanel" id="tab-project-team">
						@include('admin.projects.partials.project-team')	
					</div>

					{{-- Documentos --}}
					<div class="tab-pane fade @if($tab == 'docs') show active @endif" role="tabpanel" id="tab-project-docs">
						@include('admin.projects.partials.project-docs')	
					</div>

					{{-- Compras --}}
					@if($project_purchases->count())
						<div class="tab-pane fade @if($tab == 'purchases') show active @endif" role="tabpanel" id="tab-project-purchases">
							@include('admin.projects.partials.project-purchases')	
						</div>
					@endif

					{{-- Gastos --}}
					@if($project_expenses->count())
						<div class="tab-pane fade @if($tab == 'expenses') show active @endif" role="tabpanel" id="tab-project-expenses">
							@include('admin.projects.partials.project-expenses')	
						</div>
					@endif

					{{-- Grupos de proyectos --}}
					@if($groups_in->count())
						<div class="tab-pane fade @if($tab == 'groups') show active @endif" role="tabpanel" id="tab-project-groups">
							@include('admin.projects.partials.project-groups')	
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Edit owner: --}}
@include('admin.orders.partials.modal-owner', ['owners' => $users, 'ow_id' => $project->id, 'ow_action' => 'projects.update-owner'])

{{-- Add task: --}}
@include('admin.tasks.partials.modal-add-task')

{{-- Add member: --}}
@include('admin.projects.partials.modal-add-member')

{{-- Add order: --}}
@include('admin.projects.partials.modal-add-order')

{{-- Add purchase: --}}
@include('admin.projects.partials.modal-add-purchase')

{{-- Add expense: --}}
@include('admin.projects.partials.modal-add-expense')

{{-- Tasks status: --}}
@include('admin.task_status.partials.modal-task-status')

{{-- Add working time: --}}
@include('admin.tasks.partials.modal-add-working-time', ['return' => 'project'])

{{-- Member tasks: --}}
@include('admin.projects.partials.modal-member-tasks')	

{{-- Economic balance: --}}
@include('admin.projects.partials.modal-economic-balance')	

{{-- Set groups: --}}
@include('admin.project_groups.partials.modal-set-group')

@push('scripts')
<script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>
<script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>

<script>
$(function(){
	// Ancho automático capa tasks.
	if($('#tasks').length > 0){
        var w = $('.task-column').outerWidth();
        var columns = $('.task-column').length / 2;
        var new_w = (w * columns) + 5; 
        $('#tasks .tasks-wrapper, .scroll-top-row').width(new_w);
    }

    // Sortable tasks.
    if($('.droptrue').length > 0){
        $('.droptrue').sortable({
            connectWith: ".droptrue",
            //Actualizar cambio de columna a columna:
            receive: function(event, ui){
                var id = $(ui.item).attr('id');
                var status = $(this).attr('id');
                $.ajax({
                    url: '/admin/tasks/'+id+'/status/'+status,
                    type: 'GET',
                    //data: data,
                    processing: true,
                    dataType: "JSON",
                    success:function(data){

                    }
                });
            },
            //Actualizar orden en columna:
            update: function(event, ui){
                var data = $(this).sortable('toArray');
                $.ajax({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        data:data
                    },
                    type: 'POST',
                    url: '/admin/tasks/order-column'
                });
            }
        }).disableSelection();
    }

    // Igualar altura de columnas de tareas:
    var highestBox = 0;
    $('.droptrue', this).each(function(){
    	if($(this).height() > highestBox){
        	highestBox = $(this).height(); 
        }
    });
    if(highestBox > 0){
    	$('.droptrue',this).height(highestBox);
    	$('.tasks-wrapper').css('min-height', highestBox+'px');
    }

    // Pre-seleccionar status para nueva tarea.
    $(document).on('click', '.btnAddTask', function(){
        $('#selectTaskStatus').val(null).trigger('change');
        var status = $(this).data('status');
        //Atención al modo de marcar como selected las opciones en un Select2:
        $('#selectTaskStatus').val(status);
        $('#selectTaskStatus').trigger('change');
    });
    $(document).on('click', '.btnResetStatus', function(){
        $('#selectTaskStatus').val(null).trigger('change');
    });

    // Link a budgets.
    $('#linkBudget').on('change', function(event){
    	var id = event.target.value;
    	window.open("/admin/budgets/"+id+"/edit", "_blank");
    });

    // Link a pedidos.
    $('#linkOrder').on('change', function(event){
    	var id = event.target.value;
    	window.open("/admin/orders/"+id+"/edit", "_blank");
    });

    // Modal tareas de miembro de equipo de proyecto.
    $(document).on('click', '.btnMemberTask', function(){
        var user_id = $(this).data('user');
        var project_id = {{ $project->id }};

        $.get("/admin/tasks/"+user_id+"/member-tasks/"+project_id, function(response, state){
            if(response){ 
                $('#memberTasks b').text(response.user.name+' '+response.user.surname);
                
                var html = '';
                if(response.tasks.length > 0){
	                for(i=0; i<response.tasks.length; i++){
	                    html += '<tr>';
	                        html += '<td>'+response.tasks[i].name+'</td>';
	                        html += '<td class="text-right">'+response.tasks[i].fechaEntrega+'</td>';
	                        var hours = response.tasks[i].hours > 0? response.tasks[i].hours / 60:'0';
	                        html += '<td class="text-right">'+hours+'h.</td>';
	                        html += '<td>'+response.tasks[i].estado.name+'</td>';
	                        html += '<td class="text-right">';
	                            //Editar:
	                            html += '<a href="/admin/tasks/'+response.tasks[i].id+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';;
	                        html += '</td>';
	                    html += '</tr>';
	                }
	            }else{
	            	html = '<tr><td colspan="5" class="pt-4 text-center text-warning">'+txtUsuarioSinTareas+'</td></tr>';
	            }

                $('#tblMemberTasks tbody').html(html);
            }
        });
    });

    // Porcentaje completado del proyecto:
    if($('#chartProjectPercent').length > 0){
	    var pending = 100 - {{ $project->completed }};
		var chartProjectPercent = {
	    	labels: [txtHorasPrevistas+' {{ $project->hours_forecast }} ', txtCompletado+' {{ number_format($project->completed,2) }}%'],
	    	datasets: [{
	            data: [pending, {{ number_format($project->completed, 2) }}],
	            backgroundColor: [color_chart_danger, color_chart_success]
	        }]
	    }

	    var chartProjectPercentOptions = {
	        responsive: true,
	        //animateRotate: true
	    };

	    var ProjectPercent = document.getElementById("chartProjectPercent").getContext("2d");
	    new Chart(ProjectPercent, {
	    	type: 'pie', 
	    	data: chartProjectPercent, 
	    	options: chartProjectPercentOptions
	    });
	}

    // Estadística ingresos y gastos. Tomamos los data y options del componente Livewire projects-economic-balance:
    if($('#chartEconomicBalance_').length > 0){
	    var economicBalance = document.getElementById("chartEconomicBalance_").getContext("2d");
	    new Chart(economicBalance, {
	        type: 'bar', 
	        data: barData, 
	        options:barOptions
	    });
	}
});
</script>
@endpush

@endsection