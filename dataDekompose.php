<?php
require './functions.php';

$data = query("SELECT a.id_dekompose, b.id_mddata, b.tahun,c.nama_bulan,b.periode, b.produksi, a.ma, a.cma, a.x, a.x2, a.xy, a.st, a.tt, a.ct, a.ft, a.mape FROM td_dekompose a JOIN m_data b ON a.id_data = b.id_mddata JOIN m_bulan c ON b.bulan = c.id_bulan");

echo json_encode($data);
?>