<?php
require './functions.php';

$id = $_POST['id'];

$data = query("SELECT * FROM m_data WHERE id_mddata = '$id'");

echo json_encode($data);
?>