<div class="modal fade modal-sort-blocks" id="modal-sort-blocks" tabindex="-1" role="dialog" aria-labelledby="sortBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="sortBlock">{!! ucfirst(trans('textos.bloques_ordenar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{!! trans('textos.bloques_ordenar_texto') !!}</p>

            	<form method="post" autocomplete="off" action="{{ route('budget-form-items.sort-blocks') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="form-group row">
                        {{-- Bloques --}}
                        <div class="col-lg-12">
                            <ul class="custom-list" id="sort-blocks">
                                @foreach($blocks as $blk)
                                    <li>
                                        <input type="hidden" name="id[]" value="{{ $blk->id }}">
                                        {{ $blk->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <x-btn-submit name="submit_sort_blocks" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>