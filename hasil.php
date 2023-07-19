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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            <table class="table table-bordered mt-5">
                <?php
                        $datamapedma = query("SELECT COUNT(mape) AS banyak_mape, SUM(mape) as total_mape FROM td_dma WHERE mape > 0");
                        $mapetotalDma = round($datamapedma[0]['total_mape']/$datamapedma[0]['banyak_mape'],2);
                        $dataPrediksi = query("SELECT td_dma.id_dma, m_data.tahun, m_data.bulan, td_dma.a, td_dma.b FROM td_dma JOIN m_data ON m_data.id_mddata = td_dma.id_data ORDER BY id_dma DESC LIMIT 1");
                                        $tahun = 0;
                                        $bulan = $dataPrediksi[0]['bulan'];
                                        $dateObj   = DateTime::createFromFormat('!m', $bulan+1);$monthName = $dateObj->format('F');
                                        if($bulan == 12){
                                            $tahun += $dataPrediksi[0]['tahun'] + 1;
                                        }else{
                                        $tahun += $dataPrediksi[0]['tahun'];
                                        }
                                        $ftDma = ($dataPrediksi[0]['a']*1) + ($dataPrediksi[0]['b']*1);
                                        $dataMape = query("SELECT COUNT(mape) AS banyak_mape, SUM(mape) as total_mape FROM td_dekompose WHERE mape > 0");
                                        $mapetotal = round($dataMape[0]['total_mape']/$dataMape[0]['banyak_mape'],2);
                                        
                ?>
                <thead>
                    <tr>
                        <th scope="col">NO</th>
                        <th scope="col">BULAN</th>
                        <th scope="col">TAHUN</th>
                        <th scope="col">FT</th>
                        <th scope="col">MAPE TERBAIK</th>
                        <th scope="col">METODE</th>
                        <th scope="col">Periode</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2" class="text-center">1</td>
                        <td rowspan="2" class="text-center"><?= $monthName?></td>
                        <td rowspan="2" class="text-center"><?= $tahun?></td>
                        <td><?= $ftDma ?></td>
                        <td><?= $mapetotalDma?></td>
                        <td><a href="dma.php">DMA</a></td>
                        <td><?= $_SESSION['periode']?> Periode</td>
                    </tr>
                    <tr>
                        <td><?= $_SESSION['ft']?></td>
                        <td><?= $mapetotal?></td>
                        <td><a href="dekompose.php">DEKOMPOSE</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </section>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
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


</body>

</html>