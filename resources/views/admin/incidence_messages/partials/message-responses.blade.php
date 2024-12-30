<div class="row">
    <div class="col-12 mb-4">
        {!! $message->message !!}
    </div>

	@if($messages->count())
        <div class="col-12">
            <h4>{{ ucfirst(trans('textos.respuestas')) }}</h4>
        </div>

        @php
        function displayMessages($messages, $languages, $level = 0){
            foreach($messages as $msg){
                // Indentar según el nivel:
                $margin_indent = $level * 15;
                @endphp

                <div class="col-12">
                    <div class="panel panel-default" style="margin-left: {{ $margin_indent }}px;">
                        <div class="panel-heading d-flex justify-content-between align-items-center">
                            <span>
                                <b># {{ $msg->author->name }} {{ $msg->author->surname }}</b>, {{ $msg->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
                            </span>

                            <a href="{{ route('incidence-messages.show', $msg->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.leer')) }}"><i class="fa fa-eye"></i></a>
                        </div>

                        <div class="panel-body">
                            {!! $msg->message !!}
                        </div>
                    </div>
                </div>

                @php
                // Llamar recursivamente a las respuestas:
                if ($msg->children->isNotEmpty()) {
                    displayMessages($msg->children, $languages, $level + 1);
                }
            }
        }
        @endphp

        {!! displayMessages($messages, $languages, $level = 0) !!}

    @else
        <div class="col-12 p-5 text-center">
            <h3 class="text-warning">{{ trans('textos.mensaje_sin_respuestas') }}</h3>
        </div>    
    @endif
</div>

