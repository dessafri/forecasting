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
if(isset($_POST["submit_data"])){
  buatdata($_POST);
  $_POST = array();
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
    <title>Data Produksi</title>
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
            <div style="display: flex; justify-content: space-between">
                <h1 class="h1-brand">Data Produksi</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modaldata">Tambah Data</button>
            </div>
            <div class="tabel">
                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="table-data">
                        <tr>
                            <th>No</th>
                            <th>TAHUN</th>
                            <th>BULAN</th>
                            <th>PRODUKSI</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPeramalan = query("SELECT
                        id_mddata,
                          tahun,
                          bulan,
                          produksi
                        FROM
                          m_data");
                        $index = 1;
                        foreach($dataPeramalan as $dataPeramalan):
                            $bulan = $dataPeramalan['bulan'];
                                        $dateObj   = DateTime::createFromFormat('!m', $bulan);
                                        $monthName = $dateObj->format('F');
                        ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td><?= $dataPeramalan["tahun"]?></td>
                            <td><?= $monthName?></td>
                            <td><?= $dataPeramalan["produksi"]?></td>
                            <td>
                                <button class="btn btn-primary btn-sm"
                                    onclick="editdata(<?= $dataPeramalan['id_mddata']?>)">Edit</button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="hapusdata(<?= $dataPeramalan['id_mddata']?>)">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modaldata" tabindex="-1" aria-labelledby="modaldataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modaldataLabel">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="biodata" id="biodata">
                            <div class="form-group">
                                <label for="tahun">Tahun</label>
                                <input type="text" required name="tahun" class="form-control" id="tahun" />
                            </div>
                            <div class="form-group">
                                <label for="bulan">Bulan Dalam Angka</label>
                                <input type="text" required name="bulan" class="form-control" id="bulan" />
                            </div>
                            <div class="form-group">
                                <label for="produksi">Produksi</label>
                                <input type="text" required name="produksi" class="form-control" id="produksi" />
                            </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col col-12">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary" name="submit_data" id="simpan">Simpan</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="editModal"></div>
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

    <script>
    function editdata(id) {
        let iddata = id;
        let formData = new FormData();
        formData.append("id", iddata);
        fetch('dataperamalan.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            return response.json()
        }).then(res => {
            console.log(res);
            let modal = `
            <div class="modal fade" id="editmodaldata" tabindex="-1" aria-labelledby="modaldataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modaldataLabel">Edit Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="biodata" id="biodata">
                            <div class="form-group">
                                <label for="tahun">Tahun</label>
                                <input type="text" required name="tahun" value="${res[0].tahun}" class="form-control" id="edittahun" />
                                <input type="hidden" required name="tahun" value="${res[0].id_mddata}" class="form-control" id="editid" />
                            </div>
                            <div class="form-group">
                                <label for="bulan">Bulan Dalam Angka</label>
                                <input type="text" value="${res[0].bulan}" required name="bulan" class="form-control" id="editbulan" />
                            </div>
                            <div class="form-group">
                                <label for="produksi">Produksi</label>
                                <input type="text" value="${res[0].produksi}" required name="produksi" class="form-control" id="editproduksi" />
                            </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col col-12">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary" name="edit_data" id="editsimpan">Simpan</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
            `
            $("#editModal").html(modal);
            $("#editmodaldata").modal('show');
            $("#editsimpan").on('click', function() {
                let id = $("#editid").val();
                let tahun = $("#edittahun").val();
                let bulan = $("#editbulan").val();
                let produksi = $("#editproduksi").val();
                let formData = new FormData();
                formData.append("id", id);
                formData.append("tahun", tahun);
                formData.append("bulan", bulan);
                formData.append("produksi", produksi);
                fetch('updatedata.php', {
                    method: 'POST',
                    body: formData
                }).then(res => {
                    return res.json();
                }).then(response => {
                    alert("Update Data Berhasil")
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                })
            })
        })
    }

    function hapusdata(id) {
        if (confirm('Apakah Yakin Untuk Menghapus Data ?')) {
            let iddata = id;
            let formData = new FormData();
            formData.append("id", iddata)
            fetch('deletedata.php', {
                method: 'POST',
                body: formData
            }).then(res => {
                return res.json()
            }).then(res => {
                alert("Data Berhasil di Hapus")
                // window.location.reload();
                location.reload();
            })
        }
    }
    $(document).ready(function() {
        var table = $("#example").DataTable({
            lengthChange: true,
        });

        table
            .buttons()
            .container()
            .appendTo("#example_wrapper .col-md-6:eq(0)");
        $('.btn-delete-peserta').on("click", function() {
            let id = $(this).attr('data-id');
            Swal.fire({
                icon: "warning",
                position: "top",
                title: "Apakah anda yakin ?",
                text: "Data Peserta Akan Terhapus",
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true
            }).then((result => {
                if (result.isConfirmed) {
                    let formData = new FormData;
                    formData.append('id', id);
                    fetch("hapusPeserta.php", {
                        method: "POST",
                        body: formData
                    }).then(response => {
                        return response.json()
                    }).then(responseJson => {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Peserta Berhasil Dihapus',
                            icon: 'success',
                            position: "top",
                            showConfirmButton: false
                        })
                        setTimeout(() => {
                            window.location.reload(true);
                        }, 1000);
                    })
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Peserta Gagal Dihapus',
                        icon: 'error',
                        position: "top",
                        showConfirmButton: false
                    })
                    setTimeout(() => {
                        window.location.reload(true);
                    }, 1000);
                }
            }))
        })
    })
        
    </script>
</body>

</html>