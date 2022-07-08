@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de contenidos --}}
		<a href="{{ route('contents.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_contenidos') !!}
		</a>

		{{-- Nuevo contenido --}}
		<a class="btn btn-info" href="{{ route('contents.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contenido_nuevo')) !!}</a>

		{{-- Galería de archivos --}}
		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-gallery">
			<i class="fa fa-image"></i> {!! ucfirst(trans('textos.galeria_archivos')) !!}
		</a>

		{{-- Eliminar contenido --}}
		@can('contents.destroy')
			<form method="POST" action="{{ route('contents.destroy', $content) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.contenido_eliminar')) }}" data-msg="{{ trans('textos.contenido_confirm_eliminar') }}"><i class="fa fa-trash"></i></button>
            </form>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('contents.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="id" value="{{ $content->id }}">

					<div class="form-group row">
						{{-- Código HTML --}}
						<label class="col-lg-2 col-form-label pt-3">{!! ucfirst(trans('textos.codigo_html')) !!}</label>	
						<div class="col-lg-4">
							<div class="alert alert-warning text-center copia" role="alert">
								@if($content->type == 1)
									@php
									echo "{!! App\Helpers\Helpers::info('".$content->code."') !!}";
									@endphp
								@else
									@php
									echo "{!! App\Helpers\Helpers::readContent('".$content->code."') !!}";
									@endphp
								@endif
							</div>
						</div>

						<div class="col-md-12 col-lg-6 pt-3">
							<small class="text-warning">
								{!! trans('textos.copia_codigo') !!}
							</small>
						</div>
					</div>

					<div class="form-group row">
						{{-- Nombre contenido --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contenido_nombre')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.contenido_nombre')) !!}" value="{{ $content->name }}" autocomplete="off" maxlength="150" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Fecha publicación --}}
						<label for="published_at" class="col-lg-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.fecha_publicacion')) !!}</label>
						<div class="col-lg-3 col-xl-2">
							@php
							$published_at = $content->published_at != ''? Carbon\Carbon::parse($content->published_at)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="published_at" id="published_at" value="{{ $published_at }}" :format="$languages[config('app.locale')][4]"/>
						</div>

						{{-- Fecha final de publicación --}}
						<label for="published_end" class="col-lg-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.fin_publicacion')) !!}</label>
						<div class="col-lg-3 col-xl-2">
							@php
							$published_end = $content->published_end != ''? Carbon\Carbon::parse($content->published_end)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="published_end" id="published_end" value="{{ $published_end }}" :format="$languages[config('app.locale')][4]"/>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-1 col-xl-2 col-form-label">{!! ucfirst(trans('textos.contenido_activo')) !!}</label>
						<div class="col-lg-1 col-xl-2">
							@php
							//$checked = $content->status == 1? true:false;
							@endphp
							{{-- <x-checkbox name="status" title="contenido_on_off" checked="{{ $checked }}" style="top: 5px;"/> --}}

							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.contenido_on_off') !!}">
						        <input type="checkbox" name="status" @if($content->status == 1) checked @endif>
						        <span class="chk" style="top: 5px;"></span>
						    </label>    
						</div>
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
						<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!} <br>
						</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" value="{{ $content->observations }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Links --}}
						<label for="links" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contenido_links')) !!}</label>

						@php
						$arrLinks = ($content->links)? unserialize($content->links):[];
						$links = implode(';',$arrLinks);
						@endphp

						<x-selectize name="links" class="col-lg-10" id="links" maxlength="500" value="{{ $links }}" warning="contenido_links_intro" style="position: relative; z-index: 0;"/>
					</div>

					@if($content->type == 2)
						{{-- Galería 
							08/07/22: se inactiva temporalmente pues entre en conflicto con el componente general de files-gallery.
							Buscar una opción complementaria para poder subir archivos propios del contenido editado.
						--}}
						{{-- <div class="form-group row">
							<label for="imgs" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.imagenes')) !!}</label>
							<div class="col-lg-10">
								@php
								$images = $content->images()->get();
								@endphp

								@if($images->count())
									<x-lightbox-gallery :images="$images"/>
								@endif

								<x-dropzone url="/admin/content-image/{{ $content->id }}/store"/>
							</div>
						</div> --}}
						
						<div class="form-group row mb-5">
							{{-- Videos --}}
							<label for="video" class="col-lg-2 col-form-label">Video</label>
							<div class="col-lg-10">
								<x-input-textarea name="video" value="{{ $content->video }}"/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Estilos del contenido --}}
							<label for="css" class="col-lg-10 offset-lg-2"><strong>{!! ucfirst(trans('textos.clases_css_explain')) !!}:</strong></label>
								
							@php
							$elements = [
								['trans' => trans('textos.titulo') , 'nombre'=>'css_titulo'],
								['trans' => trans('textos.destacado') , 'nombre'=>'css_destacado'],
								['trans' => trans('textos.contenido') , 'nombre'=>'css_contenido'],
								['trans' => trans('textos.links') , 'nombre'=>'css_links'],
								['trans' => trans('textos.imagenes') , 'nombre'=>'css_imagenes'],
								['trans' => 'video' , 'nombre'=>'css_video']
							];

							$classes = $content->classes? unserialize($content->classes):false;
							@endphp

							@foreach($elements as $elm)
								@php
								$value = $classes && isset($classes[$elm['nombre']])? $classes[$elm['nombre']]:'';
								@endphp

								<label for="{{ $elm['nombre'] }}" class="col-lg-2 col-form-label">{{ ucfirst($elm['trans']) }}</label>
								<div class="col-lg-4 mb-3">	
									<x-input type="text" name="{{ $elm['nombre'] }}" value="{{ $value }}" />
								</div>
							@endforeach
						</div>
					@endif
					<br>

					{{-- Tabs idiomas: --}}
					<x-tabs-languages :languages="$languages" />

					<div class="tab-content pt-5">
						@php
						$i = 0;

						$arrTitles = $content->title? unserialize($content->title):false;
						$arrExcerpts = $content->excerpt? unserialize($content->excerpt):false;
						$arrTags = $content->tags? unserialize($content->tags):false;
						$arrContents = $content->content? unserialize($content->content):false;
						@endphp

						@foreach($languages as $lang)	
							<div class="tab-pane fade show @if($i == 0) active @endif" role="tabpanel" id="tab-{{ $lang[0] }}">
								<div class="form-group row">
									{{-- Título --}}
									<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!} {{ $lang[3] }}</label>
									<div class="col-lg-10">
										@php
										$title = $arrTitles? $arrTitles[$lang[0]]:'';
										@endphp
										<x-input type="text" name="title_{{ $lang[0] }}" placeholder="{!! ucfirst(trans('textos.titulo')) !!}" value="{{ $title }}" autocomplete="off" maxlength="150"/>
									</div>
								</div>

								@if($content->type == 2)
									{{-- Tags --}}
									@php
									$tags = ($content->tags)? implode(';',$arrTags[$lang[0]]):'';
									@endphp
		
									<div class="form-group row">
										<label for="tags" class="col-lg-2 col-form-label">Tags</label>
										<x-selectize name="tags_{{ $lang[0] }}" class="col-lg-10" maxlength="500" value="{{ $tags }}" autocomplete="off" style="position: relative; z-index: 0;"/>
									</div>
								@endif

								<div class="form-group row">
									{{-- Destacado --}}
									<label for="excerpt" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.destacado')) !!} {{ $lang[3] }}</label>
									<div class="col-lg-10">
										@php
										$excerpt = $arrExcerpts? $arrExcerpts[$lang[0]]:'';
										@endphp
										<x-input-textarea name="excerpt_{{ $lang[0] }}" value="{{ $excerpt }}"/>
									</div>
								</div>

								@if($content->type == 2)
									{{-- Contenido --}}
									<div class="form-group row">
										<label for="content" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contenido')) !!} {{ $lang[3] }}</label>
										<div class="col-lg-10">
											@php
											$contenido = $arrContents? $arrContents[$lang[0]]:'';
											@endphp
											<x-input-textarea name="content_{{ $lang[0] }}" class="editor" value="{{ $contenido }}"/>
										</div>
									</div>
								@endif
							</div>

							@php
							$i++;
							@endphp
						@endforeach
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>	
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Galería de archivos --}}
@include('admin.files.partials.modal-gallery')

@endsection