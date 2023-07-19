<?php
require './functions.php';

deletedata($_POST);

echo json_encode($data);
?>