<?php
require './functions.php';

$idPeserta = $_POST['id'];

$data = query("SELECT peserta.nik, peserta.nama, peserta.jenis_kelamin, peserta.tanggal_lahir, peserta.alamat, peserta.rt, peserta.rw, jawaban.id_kriteria,jawaban.jawaban_peserta, kriteria.nama_kriteria, detail_kriteria.keterangan as pilihan_peserta FROM peserta INNER JOIN jawaban ON peserta.nik = jawaban.nik LEFT JOIN kriteria ON jawaban.id_kriteria = kriteria.id_kriteria LEFT JOIN detail_kriteria ON detail_kriteria.nilai = jawaban.jawaban_peserta WHERE peserta.nik = '$idPeserta' AND detail_kriteria.id_kriteria = kriteria.id_kriteria");

echo json_encode($data);
?>