<nav class="navbar navbar-expand-lg navbar-light bg-light" style="margin: 0; padding-right: 0; padding-left: 0 ">
    <a class="navbar-brand brand col-2" href="index.php">SISTEM PERBANDINGAN</a>
    <div class="collapse navbar-collapse d-flex justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link font-navbar" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-navbar" href="peramalan.php">Data Peramalan</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle font-navbar" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Perhitungan
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="dma.php">Double Moving Average</a>
                    <a class="dropdown-item" href="dekompose.php">Dekomposisi Aditif</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link font-navbar" href="hasil.php">Hasil</a>
            </li>

        </ul>
    </div>
    <form method="POST" class="form">
        <button class="btn btn-danger" name="submit_logout">Logout</button>
    </form>
</nav>