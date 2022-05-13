<div class="ibox">
    <div class="ibox-title">
        <span class="label label-primary pull-right">{!! $project->status? App\Helpers\helpers::projectStatus($project->status):'' !!}</span>
        <h5>
            <a href="{{ route('projects.edit', $project) }}">{{ $project->name }}</a>
        </h5>
    </div>

    <div class="ibox-content">
        @if($project->Image)
            <div class="team-members text-center">
                <a href="{{ route('projects.edit', $project) }}">
                    <img alt="{{ $project->name }}" class="img-fluid" src="{{ asset('img/projects/'.$project->Image) }}">
                </a>
                {{-- <a href="#"><img alt="member" class="img-circle" src="img/a2.jpg"></a>
                <a href="#"><img alt="member" class="img-circle" src="img/a3.jpg"></a>
                <a href="#"><img alt="member" class="img-circle" src="img/a5.jpg"></a>
                <a href="#"><img alt="member" class="img-circle" src="img/a6.jpg"></a> --}}
            </div>
        @endif

        {{-- <h4>Info about Design Team</h4> --}}
        <p>
            @php
            $text = $project->description? App\Helpers\helpers::truncate($project->description):'';
            @endphp

            {!! $text !!}
        </p>
        <div>
            <span>{{ ucfirst(trans('textos.progreso')) }}:</span>
            <div class="stat-percent">{{ $project->completed }}%</div>
            <div class="progress progress-mini">
                <div style="width: {{ $project->completed }}%;" class="progress-bar"></div>
            </div>
        </div>
        <div class="row  m-t-sm">
            <div class="col-sm-4">
                <div class="font-bold">{{ strtoupper(trans('textos.previstas')) }}</div>
                @if($project->hours_forecast > 0)
                    <div class="text-right">
                        {{ $project->hours_forecast }}h.
                    </div>
                @else
                    <div class="text-center">
                        <i class="fa fa-exclamation-circle text-warning" rel="tooltip" title="{{ ucfirst(trans('textos.no_informado')) }}"></i>
                    </div>
                @endif
            </div>
            <div class="col-sm-4">
                <div class="font-bold">{{ strtoupper(trans('textos.trabajadas')) }}</div>
                @if($project->hours_worked > 0)
                    <div class="text-right">
                        {{ $project->hours_worked }}h.
                    </div>
                @else
                    <div class="text-center">
                        <i class="fa fa-exclamation-circle text-warning" rel="tooltip" title="{{ ucfirst(trans('textos.no_informado')) }}"></i>
                    </div>
                @endif
            </div>
            <div class="col-sm-4 text-right">
                <div class="font-bold">{{ strtoupper(trans('textos.entrega')) }}</div>
                
                @php
                $estimated_delivery_date = $project->estimated_delivery_date != ''? \Carbon\Carbon::parse($project->estimated_delivery_date)->format($languages[session()->get('locale')][4]):false;
                @endphp

                @if($estimated_delivery_date)
                    <div class="text-right">
                        {{ $estimated_delivery_date }}
                    </div>
                @else
                    <div class="text-center">
                        <i class="fa fa-exclamation-circle text-warning" rel="tooltip" title="{{ ucfirst(trans('textos.no_informado')) }}"></i>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>