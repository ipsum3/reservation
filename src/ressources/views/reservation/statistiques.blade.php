@extends('IpsumAdmin::layouts.app')
@section('title', 'Statistiques')

@section('content')

    <h1 class="main-title">Statistiques</h1>
        <div class="row">

        <div id="stats-chart">
            <div class="row">
                <div class="col-md-3">
                    <div class="box">
                        <div class="box-body">
                            <div class="stat-description">
                                Réservation{{ $stats['hier'] > 1 ? 's' : '' }} hier
                            </div>
                            <div class="stat-number lead">
                                <strong>{{ $stats['hier'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box">
                        <div class="box-body">
                            <div class="stat-description">
                                Réservation{{ $stats['jour'] > 1 ? 's' : '' }} aujourd'hui
                            </div>
                            <div class="stat-number lead">
                                <strong>{{ $stats['jour'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box">
                        <div class="box-body">
                            <div class="stat-description">
                                Locations en cours
                            </div>
                            <div class="stat-number lead">
                                <strong>{{ $stats['en_cours'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box">
                        <div class="box-body">
                            <div class="stat-description">
                                Locations à venir
                            </div>
                            <div class="stat-number lead">
                                <strong>{{ $stats['a_venir'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="col-md-12">
                        <div class="box">
                            <div class="btn-toolbar ml-sm-2" style="">

                                {{ Aire::open()->class('form-inline')->route('admin.reservation.statistiques') }}

                                <label class="sr-only" for="type_id">Type de date </label>
                                <select id="type_date" name="type_date" class="form-control mb-2 mr-sm-2" style="max-width: 300px;">
                                    <option value="created_at" {{ request()->get('type_date') === "created_at"  ? 'selected' : '' }}>Date de création</option>
                                    <option value="debut_at" {{ request()->get('type_date') === "debut_at"  ? 'selected' : '' }}>Date de départ</option>
                                </select>

                                <input type="text" name="periode" id="date_debut" value="{{ request()->get('periode') }}" class="form-control mb-2 mr-sm-2 datepicker-range" placeholder="Date"/>

                                <button type="submit" class="btn btn-secondary mb-2">Rechercher</button>

                                {{ Aire::close() }}

                            </div>
                        </div>
                    </div>
                <div class="col-md-9">
                    <div class="box">
                        <div class="box-header">
                            <h2 class="box-title">Volume de transactions par mois</h2>
                        </div>
                        <div class="box-body">
                            <div style="width: 80%;height: 542px;  margin: auto;">
                                <canvas id="myLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">
                                    <div class="stat-description">
                                        CA réservations
                                    </div>
                                    <div class="stat-number lead" style="width: 100%;">
                                        <strong>@prix($stats['montant']) &nbsp;€</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h2 class="box-title">Taux de rotation du parc</h2>
                                </div>
                                <div class="box-body">
                                    <div style="height: 150px;">
                                        <canvas id="tauxRotationChart" class="mt-4"  style="width: 222px;height: 108px;display: block;margin: 0 auto;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h2 class="box-title">Taux d'annulation</h2>
                                </div>
                                <div class="box-body">
                                    <div style="height: 150px;">
                                        <canvas id="tauxRotationChart2" class="mt-4"  style="width: 222px;height: 108px;display: block;margin: 0 auto;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header">
                            <h2 class="box-title">Nombre de réservation par état </h2>
                        </div>
                        <div class="box-body">
                            <div style="width: 80%; margin: auto;">
                                <canvas id="myDoughnutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header">
                            <h2 class="box-title">Top 5 des lieux de réservation </h2>
                        </div>
                        <div class="box-body">
                            <div style="width: 80%; margin: auto;">
                                <canvas id="myDoughnutChart3"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header">
                            <h2 class="box-title">Nombre de réservation par source </h2>
                        </div>
                        <div class="box-body">
                            <div style="width: 80%; margin: auto;">
                                <canvas id="myDoughnutChart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                            <div style="width: 80%; margin: auto; height: 800px;">
                                <canvas id="myBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gauge.js/1.2.1/gauge.min.js" integrity="sha512-CvDF0JVxliK2VV8gGA7qEEyRPcORRA2miPvpDhXvlfw0TpbGAmoQHMmEP2eziwKLsNz8PaoNfs4yjnlcpn4E3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        @php
            $colors = [
                [255, 99, 132, 0.6],
                [54, 162, 235, 0.6],
                [255, 206, 86, 0.6],
                [75, 192, 192, 0.6],
                [153, 102, 255, 0.6],
            ];

            for ($i = 0; $i < 20; $i++) {
                $red = rand(0, 255);
                $green = rand(0, 255);
                $blue = rand(0, 255);

                $color = array($red, $green, $blue);
                $colors[] = $color;
            }
        @endphp
        <script>
            //reservation par état
            var data = @json($stats['reservationsParEtat']);

            var labels = data.map(item => '' + item.label);
            var values = data.map(item => item.count);

            var ctx = document.getElementById('myDoughnutChart').getContext('2d');
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            @foreach($colors as $color)
                                'rgba({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }}, 0.6)',
                            @endforeach
                        ],
                        borderColor: [
                            @foreach($colors as $color)
                                'rgba({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }}, 1)',
                            @endforeach
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            //reservation par lieu
            var data = @json($stats['reservationsParLieu']);

            var labels = data.map(item => '' + item.label);
            var values = data.map(item => item.count);

            var ctx = document.getElementById('myDoughnutChart3').getContext('2d');
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            @foreach($colors as $color)
                                'rgba({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }}, 0.6)',
                            @endforeach
                        ],
                        borderColor: [
                            @foreach($colors as $color)
                                'rgba({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }}, 1)',
                            @endforeach
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // reservation par source
            var data = @json($stats['reservationsParSource']);

            var labels = data.map(item => item.label);
            var values = data.map(item => item.count);

            var ctx = document.getElementById('myDoughnutChart2').getContext('2d');
            var myDoughnutChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            @foreach($colors as $color)
                                'rgba({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }}, 0.6)',
                            @endforeach
                        ],
                        borderColor: [
                            @foreach($colors as $color)
                                'rgba({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }}, 1)',
                            @endforeach
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            // end
            //reservation par categorie
            var data = @json($stats['reservationsParCategorie']);

            data.sort(function (a, b) {
                return a.label.localeCompare(b.label);
            });

            var labels = data.map(item => item.label);
            var values = data.map(item => item.count);
            var average_costs = data.map(item => item.average_cost);
            var average_costs_by_day = data.map(item => item.average_costs_by_day);

            var ctx = document.getElementById('myBarChart').getContext('2d');
            var myBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        /*{
                            label: 'Réservations par catégorie',
                            data: values,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Panier moyen par catégorie',
                            data: average_costs,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },*/
                        {
                            label: 'Coût moyen d\'une réservation par jour par catégorie',
                            data: average_costs_by_day,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },

                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            //end
            //stats by month line
            var moisLabels = @json($stats['moisLabels']);
            var reservationCountData = @json($stats['reservationCountData']);
            var montantTotalData = @json($stats['montantTotalData']);

            var reservationCountMax = Math.max(...reservationCountData);
            var montantTotalMax = Math.max(...montantTotalData);

            // Créer le graphique en courbes avec Chart.js
            var ctx = document.getElementById('myLineChart').getContext('2d');
            var myLineChart =  new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: moisLabels,
                    datasets: [{
                        label: 'Nombre de réservations',
                        data: reservationCountData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: reservationCountMax
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            suggestedMax: montantTotalMax
                        }
                    }
                }
            });

            myLineChart.data.datasets.push({
                type: 'line',
                label: 'Montant total',
                data: montantTotalData,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                yAxisID: 'y1'
            });

            myLineChart.update();

            // ==============================================================
            // Guage 2
            // ==============================================================


            var opts = {
                staticLabels: {
                    font: "22px sans-serif",  // Specifies font
                    labels: [100, 130, 150, 220.1, 260, 300],  // Print labels at these values
                    color: "#000000",  // Optional: Label text color
                    fractionDigits: 0  // Optional: Numerical precision. 0=round off.
                },

                angle: 0.35, // The span of the gauge arc
                lineWidth: 0.1, // The line thickness
                radiusScale: 1, // Relative radius
                pointer: {
                    length: 0.6, // // Relative to gauge radius
                    strokeWidth: 0.115, // The thickness
                    color: '#2e2f39' // Fill color
                },
                limitMax: false, // If false, max value increases automatically if value > maxValue
                limitMin: false, // If true, the min value of the gauge will be fixed
                colorStart: '#55c3f1', // Colors
                colorStop: '#55c3f1', // just experiment with them
                strokeColor: '#e4e4ee', // to see which ones work best for you
                generateGradient: true,
                highDpiSupport: true, // High resolution support
                // renderTicks is Optional
                renderTicks: {
                    divisions: 5,
                    divWidth: 1.1,
                    divLength: 0.7,
                    divColor: '#333333',
                    subDivisions: 3,
                    subLength: 0.5,
                    subWidth: 0.6,
                    subColor: '#666666'
                }

            };
            var target = document.getElementById('tauxRotationChart'); // your canvas element
            var gauge = new Donut(target).setOptions(opts); // create sexy gauge!
            gauge.maxValue = 100; // set max gauge value
            gauge.setMinValue(0); // Prefer setter over gauge.minValue = 0
            gauge.animationSpeed = 32; // set animation speed (32 is default value)
            gauge.set({{ $stats['taux_rotation'] }}); // set actual value

            var percentageLabel = document.createElement('div');
            percentageLabel.innerHTML = {{ (int)$stats['taux_rotation'] }} + '%';
            percentageLabel.style.textAlign = 'center';
            percentageLabel.style.fontSize = "x-large";
            percentageLabel.style.position = "absolute";
            percentageLabel.style.top = "50%";
            percentageLabel.style.left = "50%";
            percentageLabel.style.transform = "translate(-50%, -50%)";
            target.parentElement.appendChild(percentageLabel);

            // taux annulation

            var target2 = document.getElementById('tauxRotationChart2'); // your canvas element
            var gauge2 = new Donut(target2).setOptions(opts); // create sexy gauge!
            gauge2.maxValue = 100; // set max gauge value
            gauge2.setMinValue(0); // Prefer setter over gauge.minValue = 0
            gauge2.animationSpeed = 32; // set animation speed (32 is default value)
            gauge2.set({{ $stats['annulationRate'] }}); // set actual value

            var percentageLabel2 = document.createElement('div');
            percentageLabel2.innerHTML = {{ (int)$stats['annulationRate'] }} + '%';
            percentageLabel2.style.textAlign = 'center';
            percentageLabel2.style.fontSize = "x-large";
            percentageLabel2.style.position = "absolute";
            percentageLabel2.style.top = "50%";
            percentageLabel2.style.left = "50%";
            percentageLabel2.style.transform = "translate(-50%, -50%)";
            target2.parentElement.appendChild(percentageLabel2);

        </script>



@endsection