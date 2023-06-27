<?php
session_start();
require './functions.php';
$role = $_SESSION["role"];
$banyakPeriode = query("SELECT count(id_dekompose) as banyak FROM `td_dekompose` WHERE ma = 0");
if($banyakPeriode[0]['banyak'] > 0){
    $_SESSION['periode'] = $banyakPeriode[0]['banyak'];
}else{
    $_SESSION['periode'] = 0;
}
if ($_SESSION['id'] != '1') {
    header('location: login.php');
    exit();
}

if(isset($_POST["submit_logout"])){
    logout($_POST);
}
$periode = $_SESSION["periode"];

if(isset($_POST["submitperiode"])){
    $periode = $_POST["periode"];
    $_SESSION["periode"] = $periode;
    buatHasil($periode);
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
    <title>Hasil</title>
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
                    <label for="exampleInputEmail1" class="text-center">Masukkan periode awal untuk di Hitung</label>
                    <div class="form-group text-center d-block">
                        <input type="text" name="periode" class="form-control text-center">
                    </div>
                    <div class="button text-center">
                        <button type="submit" name="submitperiode" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
            <div class="metodeDMA d-none" id="dma" style="margin-bottom: 100px;">
                <h2>Metode DMA</h2>
                <canvas id="myChart"></canvas>
                <table id="tabel4" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>TAHUN</th>
                            <th>BULAN</th>
                            <th>PRODUKSI</th>
                            <th>MA2(S')</th>
                            <th>DMA2(S")</th>
                            <th>a</th>
                            <th>b</th>
                            <th>Ft</th>
                            <th>Error (At-Ft)</th>
                            <th>MAPE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataDMA = query("SELECT a.id_dma, b.id_mddata, b.tahun,c.nama_bulan,b.periode, b.produksi, a.ma2, a.dma2, a.a, a.b, a.ft, a.error, a.mape FROM td_dma a JOIN m_data b ON a.id_data = b.id_mddata JOIN m_bulan c ON b.bulan = c.id_bulan");
                        $index = 1;
                        $dataMape = query("SELECT COUNT(mape) AS banyak_mape, SUM(mape) as total_mape FROM td_dma WHERE mape > 0");
                        $mapetotal = round($dataMape[0]['total_mape']/$dataMape[0]['banyak_mape'],2);
                        foreach($dataDMA as $data):
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $data["tahun"]?></td>
                            <td><?= $data["nama_bulan"] ?></td>
                            <td><?= $data["produksi"] ?></td>
                            <td><?= $data["ma2"]?></td>
                            <td><?= $data["dma2"] ?></td>
                            <td><?= $data["a"] ?></td>
                            <td><?= $data["b"]?></td>
                            <td><?= $data["ft"] ?></td>
                            <td><?= $data["error"] ?></td>
                            <td><?= $data["mape"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- <tr>
                            <td colspan="11"><?= $mapetotal ?></td>
                        </tr> -->
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
            <div class="metodeDekompose d-none" id="dekompose" style="margin-bottom: 100px;">
                <h2>Metode DEKOMPOSE</h2>

                <canvas id="myChartDekompose"></canvas>
                <table id="tabel5" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>TAHUN</th>
                            <th>BULAN</th>
                            <th>PRODUKSI</th>
                            <th>MA</th>
                            <th>CMA</th>
                            <th>X</th>
                            <th>X2</th>
                            <th>XY</th>
                            <th>ST</th>
                            <th>TT</th>
                            <th>CT</th>
                            <th>FT</th>
                            <th>MAPE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataDekompose = query("SELECT a.id_dekompose, b.id_mddata, b.tahun,c.nama_bulan,b.periode, b.produksi, a.ma, a.cma, a.x, a.x2, a.xy, a.st, a.tt, a.ct, a.ft, a.mape FROM td_dekompose a JOIN m_data b ON a.id_data = b.id_mddata JOIN m_bulan c ON b.bulan = c.id_bulan");
                        $index = 1;
                        foreach($dataDekompose as $data):
                        ?>
                        <tr>
                            <td><?= $index++?></td>
                            <td><?= $data["tahun"]?></td>
                            <td><?= $data["nama_bulan"] ?></td>
                            <td><?= $data["produksi"] ?></td>
                            <td><?= $data["ma"]?></td>
                            <td><?= $data["cma"] ?></td>
                            <td><?= $data["x"] ?></td>
                            <td><?= $data["x2"]?></td>
                            <td><?= $data["xy"] ?></td>
                            <td><?= $data["st"] ?></td>
                            <td><?= $data["tt"] ?></td>
                            <td><?= $data["ct"] ?></td>
                            <td><?= $data["ft"] ?></td>
                            <td><?= $data["mape"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="totalMape mt-4">
                    <?php
                        $dataMape = query("SELECT COUNT(mape) AS banyak_mape, SUM(mape) as total_mape FROM td_dekompose WHERE mape > 0");
                        $mapetotal = round($dataMape[0]['total_mape']/$dataMape[0]['banyak_mape'],2);
                        ?>
                    <div class="row">
                        <div class="col-5 offset-7 d-flex justify-content-end">
                            <p class="text-end">MAPE (Mean Absolute Percentage Error) : <span
                                    class=" font-weight-bold"><?= $mapetotal?>
                                    %</span></p>
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
                                    <td><?= $_SESSION['ft']?></td>
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
        integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    // console.log(periode)
    var table = $("#tabel4").DataTable({
        lengthChange: true,
        buttons: [{
                extend: "excel",
                text: "Export Excel",
                className: "btn-success",
            },
            {
                extend: "spacer",
                style: "bar",
            },
            {
                extend: "pdf",
                text: "Export PDF",
                className: "btn-danger"
            },
            {
                extend: "spacer",
                style: "bar",
            },
            {
                extend: "colvis",
                text: "SORTIR"
            },
        ],
    });
    table
        .buttons()
        .container()
        .appendTo("#tabel4_wrapper .col-md-6:eq(0)");
    var table = $("#tabel5").DataTable({
        lengthChange: true,
        buttons: [{
                extend: "excel",
                text: "Export Excel",
                className: "btn-success",
            },
            {
                extend: "spacer",
                style: "bar",
            },
            {
                extend: "pdf",
                text: "Export PDF",
                className: "btn-danger"
            },
            {
                extend: "spacer",
                style: "bar",
            },
            {
                extend: "colvis",
                text: "SORTIR"
            },
        ],
    });
    table
        .buttons()
        .container()
        .appendTo("#tabel5_wrapper .col-md-6:eq(0)");

    const ctx = document.getElementById('myChart');
    const ctx2 = document.getElementById('myChartDekompose');
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
                        }
                    },
                    scales: {
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
                }
            });
        })
    fetch('dataDekompose.php')
        .then(res => res.json())
        .then(response => {
            console.log(response)
            const prod = [];
            const ft = [];
            const bulan = [];
            response.forEach(item => {
                prod.push(item.produksi);
                ft.push(item.ft);
                bulan.push(item.nama_bulan);
            });
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: bulan,
                    datasets: [{
                            label: 'Produksi',
                            data: prod,
                            borderColor: 'rgb(100, 10, 192)',
                            backgroundColor: 'purple',
                            yAxisID: 'y'
                        },
                        {
                            label: 'FT',
                            data: ft,
                            borderColor: 'rgb(255, 150, 1)',
                            backgroundColor: 'orange',
                            yAxisID: 'y1'
                        },
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        // mode: 'index',
                        intersect: false,
                        axis: 'x'
                    },
                    // stacked: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Hasil Forecasting Metode DMA'
                        }
                    },
                    scales: {
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
                }
            });
        })
    </script>
</body>

</html>