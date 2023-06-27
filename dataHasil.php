<?php 
require './functions.php';

$data = query("SELECT peserta.nama, peserta.nik, perangkingan_alternatif.nilai_perankingan_alternatif FROM perangkingan_alternatif LEFT JOIN peserta ON peserta.nik = perangkingan_alternatif.nik ORDER BY perangkingan_alternatif.nilai_perankingan_alternatif DESC");

echo json_encode($data);
?>