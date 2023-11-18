<?php $conn = mysqli_connect('localhost', 'root', '', 'forecasting');
error_reporting(E_ERROR);
if (!$conn) {
    mysqli_error($koneksi);
}

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}
function login($data)
{
    global $conn;
    $username = $data['username'];
    $password = $data['password'];

    $hasil = query(
        "SELECT * FROM m_user WHERE username = '$username' AND password = '$password' "
    );
    if ($hasil != null) {
        $_SESSION['id'] = '1';
        $_SESSION['role'] = $hasil[0]["role"];
        header('location: index.php');
        exit();
    } else {
        echo "
        <script src='//cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        
        alert('Username / Password Salah !!');
        </script>
        
        ";
    }
}
function logout()
{
    header('location: login.php');
    session_start();
    session_destroy();
    $_SESSION['id'] = '';
    $_SESSION['role'] = '';
}
function updatedata($data){
    global $conn;
    $id = $data["id"];
    $tahun = $data["tahun"];
    $bulan = $data["bulan"];
    $periode = $data["periode"];
    $produksi = $data["produksi"];

    $sqlUpdateData = "UPDATE m_data SET tahun= '$tahun' , bulan= '$bulan' , periode= '$periode' , produksi= '$produksi' WHERE id_mddata = '$id'";
    mysqli_query($conn,$sqlUpdateData);
}
function deletedata($data){
    global $conn;

    $id = $data['id'];
    mysqli_query($conn, "DELETE FROM m_data WHERE id_mddata='$id'");
}
function buatdata($data){
    global $conn;
    $tahun = $data["tahun"];
    $bulan = $data["bulan"];
    $produksi = $data["produksi"];

    if(is_numeric($tahun) && is_numeric($bulan) && is_numeric($produksi)){
        $result = mysqli_query($conn,"SELECT * FROM m_data WHERE bulan = '$bulan' AND tahun = '$tahun'");
        if(mysqli_num_rows($result) > 0){
            echo "<script>alert('Data Sudah Ada')</script>";
            return;
        }
        mysqli_query($conn, "INSERT INTO m_data (id_mddata, bulan, produksi, tahun ) VALUES (NULL, '$bulan', '$produksi', '$tahun')");
        echo "<script>alert('Tambah Data Berhasil')</script>";
    }else{
        echo "<script>alert('Harap Isi Dalam Format Angka')</script>";
    }

}

function buatHasil($inputPeriode){
    global $conn;
    hapusdma();
    $dataforecast = query('SELECT id_mddata, produksi FROM m_data');
    $indexMA = $inputPeriode -1;
    $indexDMA = $indexMA*2;
    $indexFT = $indexDMA+1;
    $skipArray = 0;
    $arrayCheck = $inputPeriode;
    $batasBawahIndexDMa = $indexMA;
    $MA2 = array();
    foreach($dataforecast as $index=>$data){
        if($index < $indexMA){
            array_push($MA2, array('id'=> $data['id_mddata'], 'value'=> 0) );
            // $skipArray+=1;
            // $arrayCheck +=1;
            // return;
        }else{
            $avg = [];
            $slice = array_slice($dataforecast,$skipArray,$arrayCheck);
            for($i=0;$i< $inputPeriode; $i++){
                array_push($avg, $slice[$i]['produksi']);
            }
            $result = array_sum($avg) / $inputPeriode;
            $skipArray+=1;
            $arrayCheck+=1;
            array_push($MA2, array('id'=> $data['id_mddata'], 'value'=> $result) );
        }
        
    }
    $sqlMa2 = "INSERT INTO td_dma (id_dma, id_data, ma2) VALUES";
    foreach($MA2 as $data){
        $sqlMa2 .= "(NULL,'" .
        $data['id'] .
        "','" .
        $data['value'] .
        "'),";
    }
    $sqlMa2 = rtrim($sqlMa2, ',');
    $dataDMA= query("SELECT * FROM td_dma");
    if(count($dataDMA) > 1){
    }else{
        mysqli_query($conn, $sqlMa2);
    }
    // DMA 2
    $dataMA2 = query('SELECT id_data, ma2 FROM td_dma');
    $DMA2 = array();
    foreach($dataMA2 as $index=> $data){
        if($index < $indexDMA){
            array_push($DMA2, array('id'=>$data['id_data'], 'value'=>0));
        }
        else{
            $avg = [];
            $batasAtasArray = $index;
            $slice = array_slice($dataMA2, $batasBawahIndexDMa, $batasAtasArray);
            for($i=0;$i< $inputPeriode; $i++){
                array_push($avg, $slice[$i]['ma2']);
            }
            $result = array_sum($avg) / $inputPeriode;
            // var_dump($slice);
            $batasBawahIndexDMa +=1;
            $batasAtasArray +=1;
            array_push($DMA2, array('id'=>$data['id_data'], 'value'=>$result));
        }
    }
    foreach($DMA2 as $d){
        $id= $d['id'];
        $val = $d['value'];
        $sqlDMA = "UPDATE td_dma SET dma2 = '$val' WHERE id_data = '$id'";
         mysqli_query($conn, $sqlDMA);
    }
    $dataA = query('SELECT id_data, ma2, dma2 FROM td_dma');
    $A = array();
    foreach($dataA as $index=> $data){
        if($index < $indexDMA){
            array_push($A, array('id'=>$data['id_data'], 'value'=>0));
        }
        else{
            $resultA = 2* $dataA[$index]['ma2'] - $data['dma2'];
            $resultB = (2/($inputPeriode-1))* ($dataA[$index]['ma2'] - $data['dma2']);
            array_push($A, array('id'=>$data['id_data'], 'value'=>$resultA, 'B'=>$resultB));
        }
    }
    foreach($A as $d){
        $id= $d['id'];
        $val = $d['value'];
        $valB = $d['B'];
        $sqlAB = "UPDATE td_dma SET a = '$val', b = '$valB' WHERE id_data = '$id'";
         mysqli_query($conn, $sqlAB);
    }
    $dataFT = query('SELECT id_data, a, b FROM td_dma');
    $FT = array();
    foreach($dataFT as $index=> $data){
        if($index < $indexFT){
            array_push($FT, array('id'=>$data['id_data'], 'value'=>0));
        }
        else{
            $result = ($dataFT[$index-1]['a']*1) + ($dataFT[$index-1]['b']*1);
            array_push($FT, array('id'=>$data['id_data'], 'value'=>$result));
        }
    }
    foreach($FT as $d){
        $id= $d['id'];
        $val = $d['value'];
        $sqlFT = "UPDATE td_dma SET ft = '$val' WHERE id_data = '$id'";
         mysqli_query($conn, $sqlFT);
    }
    //Perhitungan MAPE
    $dataErr = query('SELECT b.id_data, a.produksi, b.a, b.ft FROM m_data a JOIN td_dma b ON a.id_mddata = b.id_data');
    $ERR = array();
    foreach($dataErr as $index=> $data){
        if($index < $indexFT){
            array_push($ERR, array('id'=>$data['id_data'], 'value'=>0));
        }
        else{
            $result = $dataErr[$index]['produksi'] - $dataErr[$index]['ft'];
            $mape = $result/$dataErr[$index]['produksi']*100;
            $abs = abs($mape);
            array_push($ERR, array('id'=>$data['id_data'], 'value'=>$result, 'mape'=>round($abs,3)));
        }
    }
    foreach($ERR as $d){
        $id= $d['id'];
        $val = $d['value'];
        $mape = $d['mape'];
        $sqlErr = "UPDATE td_dma SET error = '$val', mape = '$mape' WHERE id_data = '$id'";
         mysqli_query($conn, $sqlErr);
    }

    $dataMape = query("SELECT COUNT(mape) AS banyak_mape, SUM(mape) as total_mape FROM td_dma WHERE mape > 0");
    $mapetotal = round($dataMape[0]['total_mape']/$dataMape[0]['banyak_mape'],2);
    mysqli_query($conn, "INSERT INTO mape_terbaik (id,periode,nilai) VALUES (NULL, $inputPeriode, $mapetotal )");


}
function hasilDekompose(){
    global $conn;
    global $result;
    hapusdekompose();
    $dataforecast = query('SELECT id_mddata, produksi FROM m_data');
    // dekompose
    $inputPeriode = 5;
    $arrayMADekompose = array();
    $batasBawahMaDekompose = 0;
    $batasAtasMaDekompose = 12;
    $totalStopdata = count($dataforecast) - $inputPeriode-1;
    foreach($dataforecast as $index=> $data){
        if($index < $inputPeriode){
            array_push($arrayMADekompose, array('id'=>$data['id_mddata'], 'value'=>0));
        }else{
            if($index < $totalStopdata){
                $total = [];
            $slice = array_slice($dataforecast, $batasBawahMaDekompose, $batasAtasMaDekompose);
            // var_dump($slice);
            for($i=$batasBawahMaDekompose; $i < $batasAtasMaDekompose; $i++){
            
                array_push($total, $dataforecast[$i]['produksi']);
            }
            
            // var_dump($total);
            if(count($total) == 12){
                // $result = array_sum($total)/12;
                $result = round(array_sum($total)/12,2);
                $batasBawahMaDekompose += 1;
                $batasAtasMaDekompose += 1;
                array_push($arrayMADekompose, array('id'=>$data['id_mddata'], 'value'=>$result));
            }
            }else{
                array_push($arrayMADekompose, array('id'=>$data['id_mddata'], 'value'=>0));
            
            }
            
        }
    }
    $sqlInsertDekompose = "INSERT INTO td_dekompose (id_dekompose, id_data, simple) VALUES";
    foreach($arrayMADekompose as $d){
        $sqlInsertDekompose .= "(NULL,'" .
        $d['id'] .
        "','" .
        $d['value'] .
        "'),";
    }
    $sqlInsertDekompose = rtrim($sqlInsertDekompose, ',');
    $dataDMA= query("SELECT * FROM td_dekompose");
    if(count($dataDMA) > 1){
    }else{
        mysqli_query($conn, $sqlInsertDekompose);
    }

    $arrayCentered = array();
    $sqlSimpleDekompose = query('SELECT id_data, simple FROM td_dekompose');
    foreach($sqlSimpleDekompose as $index=> $data){
        $xy = $dataforecast[$index]['produksi'] * $index;
        if($index < $inputPeriode+1){
            array_push($arrayCentered, array('id'=>$data['id_data'], 'value'=>0));
        }else{
            if($index < $totalStopdata){
                $total = [];
                array_push($total,$sqlSimpleDekompose[$index-1]['simple']);
                array_push($total,$sqlSimpleDekompose[$index]['simple']);
                $result = array_sum($total)/2;
                array_push($arrayCentered, array('id'=>$data['id_data'], 'value'=>$result));
            }else{
                array_push($arrayCentered, array('id'=>$data['id_data'], 'value'=>0));
            }
        }
    }
    foreach($arrayCentered as $d){
        $id= $d['id'];
        $val = $d['value'];
        $sqlCentered = "UPDATE td_dekompose SET centered = '$val' WHERE id_data = '$id'";
        mysqli_query($conn, $sqlCentered);
    }
    $arrayDetrend = array();
    $dataCentered = query("SELECT id_data,centered FROM td_dekompose");
    foreach($dataCentered as $index=>$data){
        if($index < $inputPeriode+1){
            array_push($arrayDetrend, array('id'=>$data['id_data'], 'value'=>0));
        }else{
            if($index < $totalStopdata){
                $result = $dataforecast[$index]['produksi'] - $dataCentered[$index]['centered'];
                array_push($arrayDetrend, array('id'=>$data['id_data'], 'value'=>$result));
            }else{
                array_push($arrayDetrend, array('id'=>$data['id_data'], 'value'=>0));
            }
        }
    }
    foreach($arrayDetrend as $d){
        $id= $d['id'];
        $val = $d['value'];
        $sqlDetrend = "UPDATE td_dekompose SET detrend = '$val' WHERE id_data = '$id'";
        mysqli_query($conn, $sqlDetrend);
    }
    $dataUnadjusted = query("SELECT 
    ROUND(AVG(td_dekompose.detrend)) AS average_detrend,
    m_data.bulan AS bulan, id_data
    FROM 
    td_dekompose
    JOIN 
    m_data ON td_dekompose.id_data = m_data.id_mddata
    WHERE 
    td_dekompose.detrend <> 0
    GROUP BY 
    m_data.bulan
    ORDER BY 
    bulan ASC");
    $total = [];
    foreach($dataUnadjusted as $data){
        array_push($total, $data['average_detrend']);
    }
    $average = round(array_sum($total) / count($total));
    $index = 0;
    foreach($dataforecast as $data){
        $result = $total[$index] - $average;
        if($index > 11){
            $index = 0;
        }else{
            $index +=1;
        }
        $id = $data['id_mddata'];
        $sqlSeasonal = "UPDATE td_dekompose SET seasonal = $result WHERE id_data = '$id'";
        mysqli_query($conn, $sqlSeasonal);
    }
    $dataseasonal = query("SELECT id_data,seasonal FROM td_dekompose");
    foreach($dataseasonal as $index=> $data){
        $result = $dataforecast[$index]['produksi'] - $data['seasonal'];
        $id = $data['id_data'];
        $sqldeseasoal = "UPDATE td_dekompose SET deseasonal = $result WHERE id_data = '$id'";
        mysqli_query($conn, $sqldeseasoal);
    }
    $dataNilai = query('SELECT COUNT(b.id_mddata) AS banyak_data, SUM(b.produksi) as produksi, SUM(a.x) as X, SUM(a.x2) AS X2, SUM(a.xy) AS XY FROM td_dekompose a JOIN m_data b ON b.id_mddata = a.id_data');
    $pangkat = pow($dataNilai[0]['X'],2);
    $nilaiA = round((($dataNilai[0]['produksi']*$dataNilai[0]['X2'])-($dataNilai[0]['X']*$dataNilai[0]['XY']))/(($dataNilai[0]['banyak_data']*$dataNilai[0]['X2'])-($pangkat)));
    $nilaiB = round((($dataNilai[0]['banyak_data']*$dataNilai[0]['XY'])-($dataNilai[0]['X']*$dataNilai[0]['produksi']))/(($dataNilai[0]['banyak_data']*$dataNilai[0]['X2'])-($pangkat)));
    $arraySt = array();
    foreach($dataforecast as $index=>$data){
        $st = $nilaiA+$nilaiB * $index;
        array_push($arraySt, array('id'=>$data['id_mddata'], 'value'=>$st));
    }
    foreach($arraySt as $d){
        $val = $d['value'];
        $id = $d['id'];
        $sqlST = "UPDATE td_dekompose SET st = '$val' WHERE id_data = '$id'";
        mysqli_query($conn, $sqlST);
    }
    $sqlTT = query('SELECT id_data, cma, st FROM td_dekompose');
    $arrayTT = array();
    foreach($sqlTT as $index=>$data){
        if($index < $inputPeriode+1){
            array_push($arrayTT, array('id'=>$data['id_data'], 'value'=>0));
        }else{
            $cma = $data['cma'];
            $st = $data['st'];
            $tt = $cma/$st;
            array_push($arrayTT, array('id'=>$data['id_data'], 'value'=>$tt));
        }
    }
    foreach($arrayTT as $data){
        $val = round($data['value'],2);
        $id = $data['id'];
        $sqlST = "UPDATE td_dekompose SET tt = '$val' WHERE id_data = '$id'";
        mysqli_query($conn, $sqlST);
    }

    $sqlTotalProduksiBulan = query("SELECT SUM(produksi) as produksi, m_data.bulan as bulan FROM m_data JOIN m_bulan ON m_data.bulan = m_bulan.id_bulan GROUP BY m_data.bulan ORDER BY bulan ASC");
    $sum = [];
    $resultRSI = array();
    foreach($sqlTotalProduksiBulan as $data){
        array_push($sum, $data['produksi']);
    }
    $jmlsumlagi = 0;
    foreach($sum as $data){
        $jmlsumlagi += $data;
    }
    $jmlsum = array_sum($sum);
    $index = 0;
    foreach($sum as $data){
        $jmlRasio = $data / $jmlsum;
        $si = round($jmlRasio,3)*12;
        array_push($resultRSI, array('id'=>$sqlTotalProduksiBulan[$index]['bulan'], 'rasio'=> round($jmlRasio,3), 'si'=> round($si,3)));
        $index++;
    }
    // die();
    foreach($resultRSI as $index=>$data){
        $arrayId = [];
        $id = $data['id'];
        $rasio = $data['rasio'];
        $si = $data['si'];
        $idData = query("SELECT m_data.id_mddata as id_data FROM m_data WHERE bulan = $id");
        foreach($idData as $id){
            $idData = $id['id_data'];
            $sqlct = "UPDATE td_dekompose SET ct = '$si' WHERE id_data = '$idData'";
            mysqli_query($conn, $sqlct);
        }
    }

    $sqlft = query('SELECT id_data, st, tt,ct FROM td_dekompose');
    $arrayft = array();
    $index = 0;
    foreach($sqlTT as $index=>$data){
        if($index < $inputPeriode+1){
            array_push($arrayft, array('id'=>$data['id_data'], 'value'=>0));
        }else{
            // $total = $data['st'] + $data['tt'] + $data['ct'];
            $ft = round($sqlft[$index]['st'] + $sqlft[$index]['tt'] + $sqlft[$index]['ct']) + 1;
            // var_dump($total);
            array_push($arrayft, array('id'=>$data['id_data'], 'value'=>$ft));
        }
        $index ++;
    }
    // var_dump($total);
    // die();
    foreach($arrayft as $data){
        $val = round($data['value'],2);
        $id = $data['id'];
        $sqlFT = "UPDATE td_dekompose SET ft = '$val' WHERE id_data = '$id'";
        mysqli_query($conn, $sqlFT);
    }
    //Perhitungan MAPE
    $dataErr = query('SELECT b.id_data, a.produksi, b.ft FROM m_data a JOIN td_dekompose b ON a.id_mddata = b.id_data');
    $ERR = array();
    foreach($dataErr as $index=> $data){
        if($index < $inputPeriode+1){
            array_push($ERR, array('id'=>$data['id_data'], 'value'=>0));
        }
        else{
            $result = $dataErr[$index]['produksi'] - $dataErr[$index]['ft'];
            $mape = $result/$dataErr[$index]['produksi']*100;
            $abs = abs($mape);
            array_push($ERR, array('id'=>$data['id_data'], 'value'=>$result, 'mape'=>round($abs,3)));
        }
    }
    foreach($ERR as $d){
        $id= $d['id'];
        $mape = round($d['mape'],2);
        $sqlErr = "UPDATE td_dekompose SET mape = '$mape' WHERE id_data = '$id'";
        mysqli_query($conn, $sqlErr);
    }
    $sqlMAPrediksi = query("SELECT SUM(tbl.produksi) / $inputPeriode as MA FROM (SELECT produksi FROM m_data ORDER BY m_data.id_mddata DESC LIMIT $inputPeriode) as tbl");
    $sqlCMa = query("SELECT td_dekompose.ma as MA, m_data.bulan FROM td_dekompose JOIN m_data ON td_dekompose.id_data = m_data.id_mddata ORDER BY td_dekompose.id_dekompose DESC LIMIT 1");
    $MaPrediksi = $sqlMAPrediksi[0]['MA'];
    $CmaPrediksi = ($MaPrediksi + $sqlCMa[0]['MA']) / 2;
    $x = $dataNilai[0]['banyak_data'];
    $x2 = pow($x,2);
    $ST = $nilaiA+$nilaiB*$x;
    $TT = round($CmaPrediksi / $ST,2);
    $bulan = 0;
    if($sqlCMa[0]['bulan'] == 12){
        $bulan += 1;
    }else{
        $bulan += $sqlCMa[0]['bulan'] +1;
    }
    $CT = query("SELECT td_dekompose.ct AS ct FROM m_data JOIN td_dekompose ON m_data.id_mddata = td_dekompose.id_data WHERE m_data.bulan = '$bulan' LIMIT 1");
    $ft = round($ST+$TT+$CT[0]['ct']+1);
    $_SESSION['ft'] = $ft;

    $dataMape = query("SELECT COUNT(mape) AS banyak_mape, SUM(mape) as total_mape FROM td_dekompose WHERE mape > 0");
    $mapetotal = round($dataMape[0]['total_mape']/$dataMape[0]['banyak_mape'],2);
    mysqli_query($conn, "INSERT INTO mape_terbaik (id,periode,nilai) VALUES (NULL, 'Dekomposisi', $mapetotal)");
}
function hapusdma(){
    global $conn;
    mysqli_query($conn, "DELETE FROM td_dma");
}
function hapusdekompose(){
    global $conn;
    mysqli_query($conn, "DELETE FROM td_dekompose");
}

?>