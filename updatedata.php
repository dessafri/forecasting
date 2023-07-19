<?php
require './functions.php';

updatedata($_POST);

echo json_encode($data);
?>