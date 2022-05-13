<div class="row">
    <div class="col-12">
        <div class="wrapper wrapper-content">   
            <ul class="notes">
                @foreach($notes as $note)
                    <li>
                        <div>
                            <small>
                                {{ $note->created_at->format($languages[config('app.locale')][4].' H:i:s'); }}
                            </small>

                            <h4>{{ $note->title }}</h4>
                            <p>{{ $note->note }}</p>

                            <a href="#" wire:click="delete({{ $note->id }})">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script>
window.onload = function(){
    Livewire.on('response', (qty) => {
        document.getElementById("notesQty").textContent = qty;
    });
}
</script>
