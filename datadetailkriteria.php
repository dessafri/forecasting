<?php
require './functions.php';

$idKriteria = $_POST["id"];

$data = query("SELECT * FROM detail_kriteria WHERE id_kriteria = '$idKriteria'");

echo json_encode($data);

?>