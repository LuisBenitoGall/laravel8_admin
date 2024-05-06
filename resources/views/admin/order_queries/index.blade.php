@extends('admin.partials.layout')

@section('content')

<div class="row">
    <div class="col-12">                        
        <div class="card mb-3">
            <div class="card-body">
        		<div class="row">
					<div class="col-lg-12 mb-3">
						<h3><u>{{ trans('textos.consultas_relacion') }}</u></h3>	
					</div>

	            	@foreach($queries as $q)
	            		<div class="col-4 col-lg-3">
							<div class="card bg-success mb-3 p-3">
								<a href="{{ route('order-queries.'.$q[1]) }}" class="text-white">{{ trans('textos.'.$q[0]) }}</a>
							</div>
						</div>	
	            	@endforeach
	            </div>
            </div>
        </div>
    </div>
</div>

@endsection