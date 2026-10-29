<div class="modal fade" id="modal-add-comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {!! ucfirst(trans('textos.nota_nueva')) !!}
                </h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('contact-comments.store') }}"  accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="contact_id" value="{{ $contact->id }}">

                    <div class="form-group row">
                        {{-- Categoría --}}
                        <div class="col-md-12">
                            <label for="category">{!! ucfirst(trans('textos.categoria')) !!}</label>
                        </div>
                        <div class="col-12">
                            <select name="category" class="form-control select2">
                                <option value="">{{ trans('textos.opcion_selec') }}</option>
                                @foreach($groupedCategories as $groupLabel => $options)
                                    <optgroup label="{{ $groupLabel }}">
                                        @foreach ($options as $value => $label)
                                            <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Tags --}}
                        <div class="col-md-12">
                            <label for="tags">Tags</label>
                        </div>
                        <div class="col-12">
                            <x-selectize name="tags" maxlength="500" autocomplete="off" style="position: relative; z-index: 0;"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Nota --}}
                        <div class="col-md-12">
                            <label for="comment">{!! trans('textos.nota') !!}*</label>
                        </div>
                        <div class="col-12">
                            <x-input-textarea name="comment" class="editor"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>
