<div class="modal-body">
    {{-- Balance económico --}}
    <div class="chart">
        <canvas id="chartEconomicBalance"></canvas>
    </div>

    <script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>
    <script>
    var values = {{ json_encode($list) }};
    console.log(values);
    var barData = {
        labels: ["{{ ucfirst(trans('textos.ingresos')) }}", "{{ ucfirst(trans('textos.pagos')) }}"],
        datasets: [
            {
                label: "{{ ucfirst(trans('textos.ventas')) }}",
                backgroundColor: color_chart_success,
                pointBorderColor: "#fff",
                data: [values[0], 0]
            },
            {
                label: "{{ ucfirst(trans('textos.compras')) }}",
                backgroundColor: color_chart_danger,
                pointBorderColor: "#fff",
                data: [0, values[1]]
            },
            {
                label: "{{ ucfirst(trans('textos.gastos')) }}",
                backgroundColor: color_chart_warning,
                pointBorderColor: "#fff",
                data: [0, values[2]]
            }
        ]
    };

    var barOptions = {
        responsive: true,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
            }
        },
        maintainAspectRatio: false,
        legend: {position: 'bottom'}
    };

    var ctx = document.getElementById("chartEconomicBalance").getContext("2d");
    new Chart(ctx, {
        type: 'bar', 
        data: barData, 
        options:barOptions
    });
    </script>
</div>