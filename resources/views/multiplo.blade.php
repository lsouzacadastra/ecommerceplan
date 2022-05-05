<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{  url('') }}/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <title>Painel</title>

    <style>
        #grafico {
            height: 850px;
        }

        .highcharts-yaxis-labels{
            display: none;
        }

        .red{
            color: red;
        }

        .green{
            color: green;
        }
    </style>

    <script type="text/javascript" >

        document.addEventListener("DOMContentLoaded", function(e) {     
            
            Highcharts.chart('grafico', {
                legend: {
                    align: 'left',
                    verticalAlign: 'top',
                    layout: 'vertical',
                    x: 0,
                    y: 20, 
                    itemStyle: {
                        
                        fontSize: '13pt'
                    }
                },

                chart: {
                    type: 'line',
                    zoomType: 'xy',
                    panning: true,
                    panKey: 'shift'
                },

                plotOptions: {
                    area: {
                        stacking: 'percent',
                        lineColor: '#ffffff',
                        lineWidth: 1,
                        marker: {
                            lineWidth: 1,
                            lineColor: '#ffffff'
                        }
                    }
                },
                title: {
                    text: 'ANÁLISE PERDA DE RECEITA'
                },

                subtitle: {
                    text: ''
                },

                yAxis: {
                    title: {
                        text: ''
                    }
                },

                xAxis: {
                    categories: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4', 'Semana 5', 'Semana 6', 'Semana 7', 'Semana 8'],
                    crosshair: {
                        width: 1,
                        color: 'gray',
                        dashStyle: 'shortdot'
                    }
                },

                // legend: {
                //     layout: 'vertical',
                //     align: 'right',
                //     verticalAlign: 'middle'
                // },

                series: <?php echo $grafico; ?>,
                
                tooltip: {
                    pointFormat: '<b></b><br>{point.custom.extraInformation}',
                    formatter: function() {
                        console.log(this);
                         return '' + this.x + '<br/> <span style="font-size: 11pt;"><b>' + this.series.name + '</b> </span> <br/>' + this.point.custom.extraInformation;
                     }
                },

                // tooltip: {
                //     formatter: function() {
                //         return '' + this.x + '<br/> <b>' + this.series.name + '</b>';
                //     }
                // }

                // responsive: {
                //     rules: [{
                //         condition: {
                //             maxWidth: 500
                //         },
                //         chartOptions: {
                //             legend: {
                //                 layout: 'horizontal',
                //                 align: 'center',
                //                 verticalAlign: 'bottom'
                //             }
                //         }
                //     }]
                // }

            });
        });

       
    </script>

</head>

<body>

<nav>
    <div class="container g-0">

        <div class="row">
            <div class="col-2">
                <h2>SOLUTIONS</h2>
            </div>
        </div>
    </div>
</nav>

<div class="content">
    
    <div class="container">

        <div class="row first">

            <h2>Chefstock</h2>

            <figure class="highcharts-figure">
                <div id="grafico"></div>
            </figure>

        </div>
    </div>

    <br>
    <br>
</div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</body>
</html>