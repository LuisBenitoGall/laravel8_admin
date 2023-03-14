<div class="row">
    @foreach($products as $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2" @if($loop->last) id="last-record" @endif>
            @include('admin.products.partials.product-card', ['product' => $product, 'currency' => $currency, 'order_' => $order_])
        </div>
    @endforeach

    @if($loadRecords >= $totalRecords)
        <p class="col-12 mb-5 text-center text-warning"><b>{{ trans('textos.productos_no_mas') }}</b></p>
    @endif


    <input type="hidden" id="last-record" value="">

    <script>
    const lastRecord = document.getElementById('last-record');
    const options = {
        root: null,
        threshold: 1,
        rootMargin: '0px'
    }   
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                @this.loadMore();
            }
        });
    });

    observer.observe(lastRecord);
    </script>
</div>
