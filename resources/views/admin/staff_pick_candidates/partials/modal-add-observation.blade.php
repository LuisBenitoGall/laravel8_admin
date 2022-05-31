<div class="modal fade modal-add-observation" id="modal-add-observation" tabindex="-1" role="dialog" aria-labelledby="addObservation" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addObservation">{!! ucfirst(trans('textos.comentario_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('staff-pick-observations.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">

					<div class="form-group row mt-1">
                        {{-- Fecha entrevista --}}
                        <label for="meeting_date" class="col-lg-2 col-form-label">{{ trans('textos.fecha_entrevista') }}</label>
                        <div class="col-lg-4">
                            @php
                            $today = Carbon\Carbon::now()->format($languages[session()->get('locale')][4]);
                            @endphp

                            <x-date-picker name="meeting_date" :format="$languages[config('app.locale')][4]" value="{{ $today }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
						{{-- Comentario --}}
						<label for="observations" class="col-lg-12">{!! ucfirst(trans('textos.comentario')) !!}</label>
						<div class="col-lg-12">
							<x-input-textarea name="observations" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>