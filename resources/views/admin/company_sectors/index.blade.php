@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Índice --}}
    @include('admin.company_sectors.partials.menu')

    {{-- Contenidos --}}
    <div class="col-md-9">
        <div class="card mb-3">
            <div class="card-body" id="seeCompanies">
                <h2 class="mb-5 mt-5 text-center text-warning">{{ ucfirst(trans('textos.sector_selec_empresas')) }}</h2>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/jsTree/style.min.css') }}">
@endpush
@endonce

@push('scripts')
<script src="{{ asset('js/plugins/jsTree/jstree.min.js') }}"></script>

<script>
$(function(){
    //Tabla Company Sectors:
    if($('#tblCompanySectors').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#dtCompanyId').val();
        
        $('#tblCompanySectors').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/company-sectors/"+company_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'level'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
    
	$('#jstree').jstree({
        'core' : {
            'check_callback' : true
        },
        'plugins' : [ 'types', 'dnd' ],
        'types' : {
            'default' : {
                'icon' : 'fa fa-folder'
            },
            'html' : {
                'icon' : 'fa fa-file-code-o'
            },
            'svg' : {
                'icon' : 'fa fa-file-picture-o'
            },
            'css' : {
                'icon' : 'fa fa-file-code-o'
            },
            'img' : {
                'icon' : 'fa fa-file-image-o'
            },
            'js' : {
                'icon' : 'fa fa-file-text-o'
            }
        }
    });

    // Obtener contenido del Handbook.
    $('.getCompanies').on('click', function(e){
        e.preventDefault();
        var id = $(this).data('id');

        if(id){
            $.get("/admin/company-sectors/"+id+"/show", function(response, state){
                if(response.length > 0){
                    var html = '<div class="row">';
                        for(var i in response){    
                            html += '<div class="col-md-3">';
                                html += '<div class="co-info">';
                                    if(response[i].logo){
                                        html += '<div class="co-img"><img src="/img/companies/'+response[i].logo+'"></div>';
                                    }
                                    html += '<div class="co-text">';
                                        html += '<a target="_blank" href="/admin/companies/'+response[i].id+'/show">'+response[i].name+'</a><br>';
                                        html += 'NIF: '+response[i].nif;
                                    html += '</div>';
                                html += '</div>';
                            html += '</div>';
                        }
                    html += '</div>';
                }else{
                    var html = '<h2 class="mb-5 mt-5 text-center text-warning">{{ ucfirst(trans('textos.sin_resultados')) }}</h2>';        
                }

                $('#seeCompanies').html(html);
            });
        }
    });
});
</script>
@endpush

@endsection