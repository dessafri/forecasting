<?php
require './functions.php';

$data = query("SELECT a.id_dekompose, b.id_mddata, b.tahun,c.nama_bulan, b.produksi, a.simple, a.centered, a.detrend, a.seasonal, a.deseasonal, a.trend, a.forecast, a.error, a.error1, a.error2, a.errorat FROM td_dekompose a JOIN m_data b ON a.id_data = b.id_mddata JOIN m_bulan c ON b.bulan = c.id_bulan");

echo json_encode($data);
?>