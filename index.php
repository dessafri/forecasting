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
    <title>Dashboard</title>
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
            <h1 class="text-title text-center text-uppercase">
                SISTEM Perbandingan metode double moving average dan dekomposisi aditif untuk peramalan jumlah produksi
                aspal
            </h1>
            <img class="mx-auto d-block" src="image/bg-1.jpg" alt="image-bg">
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
</body>

</html>