<div class="modal-body">
    {{-- Balance económico --}}
    <div class="chart">
        <canvas id="chartEconomicBalance"></canvas>
    </div>

    <script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>
    <script>
    var values = {{ json_encode($list) }};
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
        type: 'bar',
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

    $(function(){
        //Banner aviso balance económico:
        var ingresos = isNaN(values[0]) || values[0] === null? parseInt(0):values[0];
        var compras = isNaN(values[1]) || values[1] === null? parseInt(0):values[1];
        var gastos = isNaN(values[2]) || values[2] === null? parseInt(0):values[2];
        var gastos_ = parseFloat(compras) + parseFloat(gastos);

        //Margen de beneficio:
        var margin = (gastos_ * 100) / ingresos;

        //Aviso para beneficios inferiores al 10%:
        if(margin > 100){
            $('#economicBalanceWarning').addClass('alert-danger').text('{{ trans('textos.gastos_superan_ingresos_aviso') }}').fadeIn();

        }else if(margin > 90){
            $('#economicBalanceWarning').addClass('alert-warning').text('{{ trans('textos.beneficio_estrecho_aviso') }}').fadeIn();
        }
    });
    </script>
</div>