<div class="row">
    @foreach($projects as $project)
        <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-2" @if($loop->last) id="last-record" @endif>  
            @include('admin.projects.partials.project-card', ['project' => $project, 'languages' => $languages])
        </div>
    @endforeach 

    @if($loadRecords >= $totalRecords)
        <p class="col-12 mb-5 text-center text-warning"><b>{{ trans('textos.proyectos_no_mas') }}</b></p>
    @endif

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
