<?php
session_start();
require './functions.php';
$role = $_SESSION["role"];
if ($_SESSION['id'] != '1') {
    header('location: login.php');
    exit();
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
            <div class="mb-4" style="display: flex; justify-content: space-between">
                <h1 class="h1-brand" style="font-size:22px;">METODE DOUBLE MOVING AVERAGE</h1>
            </div>
            <div class="metodeDMA" id="dma" style="margin-bottom: 100px;">
                <!-- <canvas id="myChart"></canvas> -->
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
                        $dataDMA = query("SELECT a.id_dma, b.id_mddata, b.tahun,b.bulan,b.periode, b.produksi, a.ma2, a.dma2, a.a, a.b, a.ft, a.error, a.mape FROM td_dma a JOIN m_data b ON a.id_data = b.id_mddata");
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
                            <td><?= $data["error"] ?></td>
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
        integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
    $(document).ready(function() {
        var table = $("#example").DataTable({
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
            .appendTo("#example_wrapper .col-md-6:eq(0)");
        var table = $("#tabel2").DataTable({
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
            .appendTo("#tabel2_wrapper .col-md-6:eq(0)");
        var table = $("#tabel3").DataTable({
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
            .appendTo("#tabel3_wrapper .col-md-6:eq(0)");
    });
    </script>
</body>

</html>