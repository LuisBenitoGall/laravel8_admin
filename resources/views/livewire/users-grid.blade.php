<div class="row">
    @foreach($users as $user)
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-2" @if($loop->last) id="last-record" @endif>
            <div class="contact-box">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="text-center">
                            @if($user->Image)
                                <img alt="{{ $user->name }} {{ $user->surname }}" class="img-circle m-t-xs img-fluid" src="{{ asset('img/users/'.$user->Image) }}">
                            @else
                                <img class="img-circle m-t-xs img-fluid" src="{{ Avatar::create($user->name.' '.$user->surname)->toBase64() }}" />
                            @endif

                            {{-- Categoría --}}
                            @php
                            $arr = $user->categories? explode(',', $user->categories):false;
                            $categories = '';
                            if($arr){
                                foreach($arr as $cat){
                                    $category = App\Models\Category::select('name')->find($cat);   
                                    if($categories != ''){
                                        $categories .= ', ';
                                    } 
                                    $categories .= $category->name;
                                }
                            }
                            @endphp

                            <div class="m-t-xs font-bold">{!! $categories !!}</div>
                        </div>
                    </div>

                    <div class="col-sm-8">
                        <h3>
                            <a target="_blank" href="{{ route('users.show', $user) }}">{{ $user->name }} {{ $user->surname }}</a>
                        </h3>
                        
                        <p>
                            <i class="fa fa-envelope"></i>
                            <a target="_blank" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </p>

                        {{-- Teléfonos: --}}
                        @php
                        $phones = $user->phones;
                        $i = 1;
                        @endphp
                        
                        @if($phones->count() > 0)
                            @foreach($phones as $phone)
                                @if($i<=3)
                                    <p>
                                        <i class="fa fa-phone"></i>
                                        {{ ucfirst($phone->concept) }}
                                        <a target="_blank" href="tel:{{ $phone->phone_number }}">{{ $phone->phone_number }}</a>
                                    </p>
                                @endif

                                @php
                                $i++;
                                @endphp
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach    

    @if($loadRecords >= $totalRecords)
        <p class="col-12 mb-5 text-center text-warning"><b>{{ trans('textos.usuarios_no_mas') }}</b></p>
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
