@props(['alert' => false, 'msg' => false])

<div>
    @if(session('msg') || $msg)
        <div class="alert alert-success" role="alert">
            {{ session('msg') }}
            {{ $msg }}
        </div>
    @endif
    @if(session('alert') || $alert)
        <div class="alert alert-danger" role="alert">
            {{ session('alert') }}
            {{ $alert }}
        </div>
    @endif
</div>