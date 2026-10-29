<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblContactComments">
			<thead>
				<tr>
					<th class="text-center wdt50">{{ ucfirst(trans('textos.fecha')) }}</th>
					<th class="wdt100">{{ ucfirst(trans('textos.autor')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.nota')) }}</th>
					<th class="wdt100">{{ ucfirst(trans('textos.categoria')) }}</th>
					<th class="wdt50">Tags</th>
					<th class="text-center wdt100">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($comments as $comment)
					<tr>
						{{-- Fecha --}}
						<td class="text-right">
							{{ $comment->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}	
						</td>
						{{-- Autor --}}
						<td>
							{{ $comment->name }} {{ ucwords($comment->surname) }}	
						</td>
						{{-- Nota --}}
						<td class="contact-comment">
							{!! $comment->comment !!}	
						</td>
						{{-- Categoria --}}
						<td class="contact-category" data-category="{{ $comment->category }}">
							@if($comment->category)
								{!! $flatCategories[$comment->category] !!}
							@endif
						</td>
						{{-- Tags --}}
						@if($comment->tags)
							@php
							$tags = explode(',', $comment->tags);
							@endphp
						@else
							@php
							$tags = [];
							@endphp
						@endif
						<td class="contact-tags" data-tags='@json($tags)'>
							@if(!empty($tags))
								@foreach($tags as $tag)
									<span class="badge badge-primary">{{ $tag }}</span>
								@endforeach
							@endif
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Editar --}}
							<a href="#" class="btn btn-xs btn-info btn-module btnEditComment" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-toggle="modal" data-target="#modal-edit-comment" data-id="{{ $comment->id }}"><i class="fa fa-edit"></i></a>

							{{-- Eliminar --}}
							<form method="POST" action="{{ route('contact-comments.destroy', $comment->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
								<input name="_method" type="hidden" value="DELETE">
								{{ csrf_field() }}
								<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.nota_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
							</form>	
						</td>
					</tr>
				@endforeach	
			</tbody>
		</table>
	</div>
</div>



	



	
