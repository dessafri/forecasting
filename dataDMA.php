<?php
require './functions.php';

$data = query("SELECT a.id_dma, b.id_mddata, b.tahun,c.nama_bulan, b.produksi, a.ma2, a.dma2, a.a, a.b, a.ft, a.error, a.mape FROM td_dma a JOIN m_data b ON a.id_data = b.id_mddata JOIN m_bulan c ON b.bulan = c.id_bulan");

echo json_encode($data);
?>