@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
    {{-- Acciones: --}}
	<div class="col-12">
        {{-- Nuevo tipo de jornada --}}
		@can('day-types.create')
            <a href="{{ route('day-types.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.tipo_jornada_nuevo')) !!}
    		</a>
        @endcan

        {{-- Exportar valores --}}
        @can('day-types.edit')
            @if(count(session('companies')) > 1)
                <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-export-values">
                    <i class="fa fa-file-export"></i> {!! ucfirst(trans('textos.exportar_valores')) !!} 
                </a>
            @endif
        @endcan
	</div>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-hover dtabla" id="tblDayTypes">
            <thead>
                <tr>
                    <th class="wdt150">{!! ucfirst(trans('textos.tipo')) !!}</th>
                    <th class="wdt100">{!! ucfirst(trans('textos.color')) !!}</th>
                    <th class="text-center">{!! ucfirst(trans('textos.compatible_laborable')) !!}</th>
                    <th class="text-center">{!! ucfirst(trans('textos.disponible_para_usuario')) !!}</th>
                    <th class="text-center">{!! ucfirst(trans('textos.acumulable')) !!}</th>
                    <th class="text-center">{!! ucfirst(trans('textos.modificable_manualmente')) !!}</th>
                    <th class="text-center">{!! ucfirst(trans('textos.repeticion_anual')) !!}</th>
                    <th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- Modals --}}
{{-- Export values --}}
@include('admin.partials.modal-export-values', ['action' => 'day-types.export'])

{{-- Config day type --}}
@include('admin.day_types.partials.modal-config-day-type')

{{-- Set criterions --}}
@include('admin.day_types.partials.modal-set-criterions')

@push('scripts')
<script>
$(function(){
    //Modal configuración tipo de jornada.
    $(document).on('click', '.btnDayTypeConfig', function(){
        var id = $(this).data('id');
        $('#typeId').val(id);

        var type = $(this).data('type');
        $('#configDayType strong').text(type);
        $('#typesConfigList').empty();

        $('#divRanks select').prop('disabled', true)
        $('#divRanks').fadeOut();

        $.get("/admin/day-types-config/"+id+"/index", function(response, state){
            if(response.length > 0){ 
                var html = '<table class="table mt-5">';
                    html += '<thead><tr><th class="text-center">'+txtAny+'</th><th class="text-center">'+txtMinDias+'</th><th class="text-center">'+txtMaxDias+'</th><th class="text-center">'+txtMaxMes+'</th><th class="text-center">'+txtEliminar+'</th></tr></thead>';
                        //<th class="text-center">'+txtRangoFechas+'</th>
                    html += '<tbody>';
                        for(j=0; j<response.length; j++){
                            var min = response[j].min? response[j].min:'0';
                            var max = response[j].max? response[j].max:'0';
                            var max_per_month = response[j].max_per_month? response[j].max_per_month:'0';
                            var ranks = response[j].rangos;

                            html += '<tr>';   
                                html += '<td class="text-right">'+response[j].year+'</td>';
                                html += '<td class="text-right">'+min+'</td>';
                                html += '<td class="text-right">'+max+'</td>';
                                html += '<td class="text-right">'+max_per_month+'</td>';
                                html += '<td class="text-right"><a href="/admin/day-types-config/'+response[j].id+'/destroy" class="text-danger btn-confirm" data-msg="'+txtJornadaConfigConfirmEliminar+'"><i class="fa fa-trash"></i></a></td>';
                            html += '</tr>';
                        }
                    html += '</tbody>';
                html += '</table>';

                $('#typesConfigList').html(html);
            }
        });
    });

    //Modal agregar filtros:
    $(document).on('click', '.btnSetCriterion', function(){
        var id = $(this).data('id');
        var criterion = $(this).data('criterion');
        criterion = criterion != ''? criterion:'';
        $('#setTypeId').val(id);
        $("#setTypeCriterion").val(criterion).trigger('change');
    });
});
</script>
@endpush

@endsection