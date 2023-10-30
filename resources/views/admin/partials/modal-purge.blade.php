<div class="modal fade" id="modal-purge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <img src="{{ asset('img/purge/purge.jpg') }}" alt="La Purga 2023" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var start = new Date("2023-10-31T13:50:00");
    var end = new Date("2023-10-31T13:51:00");

    function purgeModal() {
        var ahora = new Date();

        if (ahora >= start && ahora <= end) {
            $('#modal-purge').modal('show');
        }
    }

    // Comprueba cada minuto si ha llegado el momento de mostrar el modal.
    setInterval(purgeModal, 10000); // 60000 ms = 1 minuto    
});
</script>
@endpush