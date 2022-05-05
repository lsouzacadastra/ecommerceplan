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

    <script type="text/javascript" >

        document.addEventListener("DOMContentLoaded", function(e) {
            Highcharts.chart('container', {
                title: {
                    text: ''
                },

                subtitle: {
                    text: ''
                },

                yAxis: {
                    title: {
                        text: 'Sessões'
                    }
                },

                xAxis: {
                    categories: ['', <?php echo $grafico_legenda; ?>]
                },

                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

                plotOptions: {
                    series: {
                        label: {
                            connectorAllowed: false
                        },
                        pointStart: 1
                    }
                },

                series: [{
                    name: 'Sessões',
                    data: [<?php echo $grafico_dados; ?>]
                }],

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                }
            });


            Highcharts.chart('grafico_midia_origem', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        ''
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                "series":<?php echo $grafico_midia_origem; ?>,
                
            });

            Highcharts.chart('grafico_trafego', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        ''
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                "series":<?php echo $grafico_trafego; ?>,
                
            });

            Highcharts.chart('container_trafego', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        ''
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                "series":<?php echo $grafico_referrer; ?>,
                
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

        <div class="row">

            <div class="col-2 card">
                <span>Pageview</span>
                <h1><?php echo $pageViews; ?></h1>
            </div>

            <div class="col-2 card">
                <span>Visitantes Unicos</span>
                <h1><?php echo $visitantes_unicos; ?></h1>
            </div>

            <div class="col-2 card">
                <span>Sessões</span>
                <h1><?php echo $visitas; ?></h1>
            </div>

            <div class="col-2 card">
                <span>Desktop</span>
                <h1><?php echo @$dispositivos['desktop']; ?></h1>
            </div>

            <div class="col-2 card" style="margin-right:0">

                <span>Mobile</span>
                <h1><?php echo @$dispositivos['mobile']; ?></h1>
            </div>

            <div class="col-2 card">
                <span>Origem Google</span>
                <h1><?php echo @$origem['google']; ?></h1>
            </div>

            <!--   
            <div class="col-2 card">
                <span>Origem Outros</span>
                <h1><?php echo @$origem['direto']; ?></h1>
            </div>
            -->
        </div>

        <br>
        <br>

        <div class="row first">
            <figure class="highcharts-figure">
                <div id="container"></div>
            </figure>
        </div>

        <div class="row first">

            <h2>Tráfego</h2>

            <table class="table">

                <thead>
                    <tr>
                        <th scope="col">Canal de Tráfego</th>
                        <th scope="col">Quantidade</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($trafego as $k => $v){ ?>

                    <tr>
                        <td><?php echo $k ?></td>
                        <td><?php echo $v ?></td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>

            <figure class="highcharts-figure">
                <div id="grafico_trafego"></div>
            </figure>


        </div>

        <div class="row first">

            <h2>Origem / Mídia</h2>

            <table class="table">

                <thead>
                    <tr>
                        <th scope="col">Origem / Mídia</th>
                        <th scope="col">Quantidade</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($midias_origem as $k => $v){ ?>

                    <tr>
                        <td><?php echo $k ?></td>
                        <td><?php echo $v ?></td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>

            <figure class="highcharts-figure">
                <div id="grafico_midia_origem"></div>
            </figure>


        </div>

 
        <div class="row first">

            <h2>Referências</h2>

            <table class="table">

                <thead>
                    <tr>
                        <th scope="col">Origem</th>
                        <th scope="col">Quantidade</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($referrer as $k => $v){ ?>

                    <tr>
                        <td><?php echo $k ?></td>
                        <td><?php echo $v ?></td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>

            <figure class="highcharts-figure">
                <div id="container_trafego"></div>
            </figure>
        </div>

        <div class="row first">

            <h2>Quais páginas seus usuários visitam?</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">URL</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($urls as $url => $registros){ ?>
                    <tr>
                        <td><a href="<?php echo $url ?>" target="_blank"><?php echo $url ?></a></td>
                        <td><?php echo $registros ?></td>
                    </tr>

                    <?php } ?>

                </tbody>
            </table>
        </div>

        <!--
        <div class="row first">

            <h2>Últimas Visistas</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Origem</th>
                        <th scope="col">URL</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach($lista_ultimos_acessos as $k => $registros){ ?>

                        <tr>
                            <td><?php echo @$registros['referrer'] ?></td>
                            <td><a href="<?php echo @$registros['url'] ?>" target="_blank"><?php echo @$registros['url'] ?></a></td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>
        </div>
        -->
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</body>
</html>