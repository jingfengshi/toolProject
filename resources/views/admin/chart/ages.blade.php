<canvas id="doughnut" width="200" height="200"></canvas>
<script>
    $(function () {
        var config = {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [
                        {{ $ages['unknown'] }},
                        {{ $ages['under17'] }},
                        {{ $ages['age18_24'] }},
                        {{ $ages['age25_29'] }},
                        {{ $ages['age30_39'] }},
                        {{ $ages['age40_49'] }},
                        {{ $ages['over50'] }}
                    ],
                    backgroundColor: [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 205, 86)',
                        'rgb(0, 255, 255)',
                        'rgb(255, 153, 0)',
                        'rgb(32, 18, 77)',
                        'rgb(194, 123, 160)',
                    ]
                }],
                labels: [
                    '未知',
                    '17岁以下',
                    '18-24岁',
                    '25-29岁',
                    '30-39岁',
                    '40-49岁',
                    '50岁以上',
                ]
            },
            options: {
                maintainAspectRatio: false
            }
        };

        var ctx = document.getElementById('doughnut').getContext('2d');
        new Chart(ctx, config);
    });
</script>
