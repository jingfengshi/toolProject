<div style='height:300px;width:600px;margin: auto'><canvas id="doughnut" style='height:200px;width:400px'></canvas></div>
<script>
    $(function () {
        var config = {
            type: 'bar',
            data: {
                datasets: [{
                    label: '新用户数',
                    data: [
                        @foreach($data as $key=>$value)
                        {{ $value }},
                        @endforeach
                    ],
                    backgroundColor: [
                        @foreach($data as $key=>$value)
                            'rgb(54, 162, 235)',
                        @endforeach
                    ]
                }],
                labels: [
                    @foreach($data as $key=>$value)
                    {{ $key }},
                    @endforeach
                ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        barPercentage: 1,
                        barThickness: 56,
                        maxBarThickness: 20,
                        minBarLength: 5,
                        gridLines: {
                            offsetGridLines: true
                        }
                    }]
                }
            }
        };
        var ctx = document.getElementById('doughnut').getContext('2d');
        new Chart(ctx, config);
    });
</script>
