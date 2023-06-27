<?php
require './functions.php';

$idKriteria = $_POST['id'];

$data = query("SELECT * FROM kriteria INNER JOIN detail_kriteria ON kriteria.id_kriteria = detail_kriteria.id_kriteria WHERE kriteria.id_kriteria = '$idKriteria'");

echo json_encode($data);
?>