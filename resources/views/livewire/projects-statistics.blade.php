<div class="modal-body">
    {{-- Proyectos por estado --}}
    <div class="chart">
        <canvas id="chartProjectsByStatus"></canvas>
    </div>

    <script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>
    <script>
    var num_projects = {{ json_encode($list) }};
    var barData = {
        labels: ["{{ ucfirst(trans('textos.inicio')) }}", "{{ ucfirst(trans('textos.en_curso')) }}", "{{ ucfirst(trans('textos.pendiente_aprobacion')) }}", "{{ ucfirst(trans('textos.finalizado')) }}"],
        datasets: [
            {
                label: "{{ ucfirst(trans('textos.proyectos_por_estado')) }}",
                backgroundColor: 'rgba(26,179,148,0.5)',
                pointBorderColor: "#fff",
                data: num_projects
            }
            // {
            //     label: "Data 2",
            //     backgroundColor: 'rgba(26,179,148,0.5)',
            //     borderColor: "rgba(26,179,148,0.7)",
            //     pointBackgroundColor: "rgba(26,179,148,1)",
            //     pointBorderColor: "#fff",
            //     data: [28, 48, 40, 19, 86, 27, 90]
            // }
        ]
    };

    var barOptions = {
        responsive: true
    };


    var ctx2 = document.getElementById("chartProjectsByStatus").getContext("2d");
    new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});
    </script>
</div>
