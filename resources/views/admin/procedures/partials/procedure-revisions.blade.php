<div class="row">
	{{-- Columna izquierda: revisiones --}}
	<div class="col-lg-3">
		<p><b>{{ trans('textos.revisiones') }}</b></p>

		@if($revisions->count())
			<ul>
				@foreach($revisions as $rev)
					<li class="rev @if($revision && $revision->id == $rev->id) bold @endif" data-rev="{{ $rev->id }}">
						<i class="fa fa-angle-right"></i>
						@if($rev->order == 0)
							{{ trans('textos.documento0') }}
						@else
							{{ ucfirst(trans('textos.revision')) }} {{ $rev->order }}
						@endif

						<span class="float-right">{{ $rev->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}</span>
					</li>
				@endforeach
			</ul>
		@else

		@endif
	</div>

	{{-- Columna derecha: contenido --}}
	<div class="col-lg-9">
		<form method="post" autocomplete="off" action="{{ route('procedure-revisions.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
			{{ csrf_field() }}
			<input type="hidden" name="procedure_id" value="{{ $procedure->id }}">
			<input type="hidden" name="revision_id" id="revisionId" value="@if($revision) {{ $revision->id }} @endif">

			<div class="row">
				<div class="col-12">
					{{-- Submit nueva versión --}}
					@if($revisions->count())
						<input type="submit" name="submit_new_version" class="btn btn-info" value="{{ trans('textos.revision_nueva_guardar') }}">
					@endif

					{{-- Submit --}}
					<input type="submit" name="submit_save" class="btn btn-info" value="{{ ucfirst(trans('textos.guardar')) }}">

					{{-- Imprimir --}}
					<a target="_blank" href="" class="btn btn-info close-animation" id="revisionPrint" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}" @if(!$revision) style="display: none;" @endif>
						<i class="fa fa-print"></i>
					</a>
				</div>
			</div>

			{{-- Contenido --}}
			@php
			$text = $revision? $revision->content:'';
			@endphp

			<x-input-textarea name="content" class="editor" id="revisionContent" value="{!! $text !!}" required/>
		</form>
	</div>
</div>



