<?php
require './functions.php';
session_start();
if ($_SESSION == '') {
    if ($_SESSION['id'] == '1') {
        header('location: index.php');
        exit();
    }
} else {
    $_SESSION['id'] = '0';
}
if (isset($_POST['submit_login'])) {
    login($_POST);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style/scss/bootstrap.css" />
    <title>Login</title>
</head>

<body>
    <section class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light"
                style="margin: 0; padding-right: 0; padding-left: 0">
                <a class="navbar-brand brand col-12" href="login.php">SISTEM PERBANDINGAN METODE</a>
            </nav>
        </div>
    </section>
    <section class="content">
        <div class="container">
            <h1 class="text-title text-center text-uppercase">
                SISTEM Perbandingan metode double moving average dan dekomposisi aditif untuk peramalan jumlah produksi
                aspal
            </h1>
            <section class="login d-flex align-items-center justify-content-center">
                <!-- <img class="mx-auto d-block" src="image/bg.jpg" alt="image-bg" /> -->

                <div class="card">
                    <div class="container">
                        <h2 class="text-center">Silahkan Login Terlebih Dahulu</h2>
                        <form method="POST">
                            <div class="form-group">
                                <label for="inputUsername">Username</label>
                                <input type="text" class="form-control" name="username" id="inputUsername"
                                    aria-describedby="emailHelp" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" class="form-control" name="password"
                                    id="exampleInputPassword1" />
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" name="submit_login" class="btn btn-primary text-center">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </section>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
</body>

</html>