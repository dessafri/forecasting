<?php
session_start();
require './functions.php';
$role = $_SESSION["role"];
if ($_SESSION['id'] != '1') {
    header('location: login.php');
    exit();
}

$periode = $_SESSION["periode"];

if(isset($_POST["submitperiode"])){
    $periode = $_POST["periode"];
    $_SESSION["periode"] = $periode;
    buatHasil($periode);
}

if(isset($_POST["submit_logout"])){
  logout($_POST);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style/scss/bootstrap.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css" />
    <style>
        .swal2-popup {
            font-size: 12px !important;
            font-family: Georgia, serif;
        }

        h2 {
            margin-top: 30px;
            margin-bottom: 30px;
            font-size: 18px;
        }
    </style>
    <title>DOUBLE MOVING AVERAGE</title>
</head>

<body>
    <section class="header">
        <div class="container">
            <?php 
            include('navbar.php')
            ?>
        </div>
    </section>
    <section class="content">
        <div class="container">
            <div class="d-flex justify-content-between" id="info">
                <p style="font-size: 16px;">Periode Pengukuran : <span class="font-weight-bold"><?=$periode?> Periode
                    </span></p>
                <button class="btn btn-primary" onclick="gantiPeriode()">Ganti Periode Pengukuran</button>
            </div>
            <div class="form-input d-none" id="form">
                <form method="post">
                    <label for="exampleInputEmail1" class="text-center">Masukkan periode awal untuk di
                        Hitung</label>
                    <div class="row">
                    <div class="select col-md-6">
                        <div class="form-group text-center d-block">
                            <select name="periode" class="form-control text-center">
                                <option value="1">Pilih Periode</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <div class="button col-md-6">
                        <button type="submit" name="submitperiode" class="btn btn-primary">Submit</button>
                    </div>
                    </div>
                </form>
            </div>
            <div class="metodeDMA d-none" id="dma" style="margin-bottom: 100px;">
                <div class="mb-4" style="display: flex; justify-content: space-between">
                    <h1 class="h1-brand" style="font-size:22px;">METODE DOUBLE MOVING AVERAGE</h1>
                </div>
                <canvas id="myChart"></canvas>
                <table id="tabel4" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>TAHUN</th>
                            <th>BULAN</th>
                            <th>PRODUKSI</th>
                            <th>S't</th>
                            <th>S"t</th>
                            <th>at</th>
                            <th>bt</th>
                            <th>Ft</th>
                            <th>MAPE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataDMA = query("SELECT a.id_dma, b.id_mddata, b.tahun,b.bulan, b.produksi, a.ma2, a.dma2, a.a, a.b, a.ft, a.error, a.mape FROM td_dma a JOIN m_data b ON a.id_data = b.id_mddata");
                        $index = 1;
                        foreach($dataDMA as $data):
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $data["tahun"]?></td>
                            <td><?= $data["bulan"] ?></td>
                            <td><?= $data["produksi"] ?></td>
                            <td><?= $data["ma2"]?></td>
                            <td><?= $data["dma2"] ?></td>
                            <td><?= $data["a"] ?></td>
                            <td><?= $data["b"]?></td>
                            <td><?= $data["ft"] ?></td>
                            <td><?= $data["mape"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="totalMape mt-4">
                    <?php
                        $dataMape = query("SELECT COUNT(mape) AS banyak_mape, SUM(mape) as total_mape FROM td_dma WHERE mape > 0");
                        $mapetotal = round($dataMape[0]['total_mape']/$dataMape[0]['banyak_mape'],2);
                        ?>
                    <div class="row">
                        <div class="col-4 offset-8">
                            <p>MAPE (Mean Absolute Percentage Error) : <span
                                    class="d-inline-block font-weight-bold"><?= $mapetotal?> %</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <span>Hasil Prediksi</span>
                        <table class="table table-striped table-bordered mt-3" style="width: 100%">
                            <thead>
                                <tr>
                                    <td>Tahun Berikutnya</td>
                                    <td>Bulan</td>
                                    <td>Ft</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                        $dataPrediksi = query("SELECT td_dma.id_dma, m_data.tahun, m_data.bulan, td_dma.a, td_dma.b FROM td_dma JOIN m_data ON m_data.id_mddata = td_dma.id_data ORDER BY id_dma DESC LIMIT 1");
                                        $tahun = 0;
                                        $bulan = $dataPrediksi[0]['bulan'];
                                        $dateObj   = DateTime::createFromFormat('!m', $bulan+1);$monthName = $dateObj->format('F');
                                        if($bulan == 12){
                                            $tahun += $dataPrediksi[0]['tahun'] + 1;
                                        }else{
                                        $tahun += $dataPrediksi[0]['tahun'];
                                        }
                                        $ft = ($dataPrediksi[0]['a']*1) + ($dataPrediksi[0]['b']*1);
                                        ?>
                                    <td><?= $tahun ?></td>
                                    <td><?= $monthName ?></td>
                                    <td><?= $ft?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"
        integrity="sha512-6PM0qYu5KExuNcKt5bURAoTPVyMExQN2bvLyzuBfqkTSSnYZKG3hkwUV0nsagZKk2OB4L7E3q1uQKHNHd4stIQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <script>
        let periodePengukuran = '<?php echo $periode;?>';
        if (periodePengukuran > 0) {
            $('#dma').removeClass('d-none');
            $('#dekompose').removeClass('d-none');
        } else {
            $('#form').removeClass('d-none');
            $('#info').addClass('d-none');
        }

        function gantiPeriode() {
            $('#dma').addClass('d-none');
            $('#dekompose').addClass('d-none');
            $('#form').removeClass('d-none');
            $('#info').removeClass('d-flex');
            $('#info').addClass('d-none');

        }
    </script>
    <script>
        $(document).ready(function () {
            const ctx = document.getElementById('myChart');
            fetch('dataDMA.php')
                .then(res => res.json())
                .then(response => {
                    const prod = [];
                    const ft = [];
                    const bulan = [];
                    response.forEach(item => {
                        prod.push(item.produksi);
                        ft.push(item.ft);
                        bulan.push(item.nama_bulan);
                    });
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: bulan,
                            datasets: [{
                                    label: 'Produksi',
                                    data: prod,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'cyan',
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'FT',
                                    data: ft,
                                    borderColor: 'rgb(75, 150, 1)',
                                    backgroundColor: 'green',
                                    yAxisID: 'y1'
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },

                            stacked: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Hasil Forecasting Metode DMA'
                                },
                                datalabels: {
                                    display: true,
                                    formatter: function (value, context) {
                                        // Check if value is a number
                                        if (typeof value === 'number') {
                                            return value.toFixed(
                                                2
                                            ); // Format as a number with two decimal places
                                        } else {
                                            return value; // If it's not a number, return it as is
                                        }
                                    },
                                    align: 'top',
                                    anchor: 'end',
                                    color: 'black',
                                },
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: false
                                    }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',

                                    // grid line settings
                                    grid: {
                                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                                    },
                                },
                            }
                        },
                        plugins: [ChartDataLabels],
                    });
                })
        });
    </script>
</body>

</html>