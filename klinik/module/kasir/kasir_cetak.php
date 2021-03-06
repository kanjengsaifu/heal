<?php
     require_once("root.inc.php");
     require_once($ROOT."library/bitFunc.lib.php");
     require_once($ROOT."library/auth.cls.php");   
     require_once($ROOT."library/textEncrypt.cls.php");
     require_once($ROOT."library/datamodel.cls.php");
     require_once($ROOT."library/dateFunc.lib.php");
     require_once($ROOT."library/currFunc.lib.php");
     require_once($APLICATION_ROOT."library/view.cls.php");
     
     
     $view = new CView($_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);
 	$dtaccess = new DataAccess();                    
     $enc = new textEncrypt();
     $auth = new CAuth();
     $err_code = 0;
     $userData = $auth->GetUserData();


 	if(!$auth->IsAllowed("kasir",PRIV_CREATE)){
          die("access_denied");
          exit(1);
     } else if($auth->IsAllowed("kasir",PRIV_CREATE)===1){
          echo"<script>window.parent.document.location.href='".$APLICATION_ROOT."login.php?msg=Login First'</script>";
          exit(1);
     }

     $_x_mode = "New";
     $thisPage = "kasir_view.php";

  //ngambil data
        
	if($_GET["id_reg"]) {
	
		$sql = "select a.id_kwitansi , b.kwitansi_nomor from klinik.klinik_folio a
    join global.global_kwitansi b on b.kwitansi_id = a.id_kwitansi 
    where a.id_reg = ".QuoteValue(DPE_CHAR,$_GET["id_reg"]) ;
		$dataKWT = $dtaccess->Fetch($sql);


    $_POST["kwitansi_id"] = $dataKWT['id_kwitansi'];
		$_POST["id_reg"] = $_GET["id_reg"]; 
		$_POST["fol_jenis"] = $_GET["jenis"]; 
    $_POST["kwitansi_nomor"] = $dataKWT['kwitansi_nomor'];


	 


        if(!$_POST["kwitansi_id"]) 
        {
        
        
        $akhirKwit = $dtaccess->GetNewID("global_kwitansi","kwitansi_nomor",DB_SCHEMA_GLOBAL);
        
        if($akhirKwit==1){
        $awalKwit = 11000187;
        }
        $_POST["kwitansi_nomor"] = $awalKwit + $akhirKwit;  
		
        $dbTable = "global_kwitansi";
			
				$dbField[0] = "kwitansi_id";   // PK
				$dbField[1] = "kwitansi_nomor";
				$dbField[2] = "id_reg";
				
        if(!$_POST["kwitansi_id"]) $_POST["kwitansi_id"] = $dtaccess->GetNewID("global_kwitansi","kwitansi_id",DB_SCHEMA_GLOBAL);	  
				$dbValue[0] = QuoteValue(DPE_NUMERIC,$_POST["kwitansi_id"]);
				$dbValue[1] = QuoteValue(DPE_CHAR,$_POST["kwitansi_nomor"]);
				$dbValue[2] = QuoteValue(DPE_CHAR,$_POST["id_reg"]);

				$dbKey[0] = 0; // -- set key buat clause wherenya , valuenya = index array buat field / value
				$dtmodel = new DataModel($dbTable,$dbField,$dbValue,$dbKey,DB_SCHEMA_GLOBAL);
				
				$dtmodel->Insert() or die("insert error"); 
				
				unset($dtmodel);
				unset($dbField);
				unset($dbValue);
				unset($dbKey);    
        }
         
    $sql = "update klinik.klinik_folio set id_kwitansi = ".QuoteValue(DPE_NUMERIC,$_POST["kwitansi_id"])."  where fol_jenis = ".QuoteValue(DPE_CHAR,$_POST["fol_jenis"])." and id_reg = ".QuoteValue(DPE_CHAR,$_POST["id_reg"]);
		$dtaccess->Execute($sql); 
        	
		$sql = "select * from klinik.klinik_folio where fol_jenis = ".QuoteValue(DPE_CHAR,$_POST["fol_jenis"])." and id_reg = ".QuoteValue(DPE_CHAR,$_POST["id_reg"]) ;
		$dataFolio = $dtaccess->FetchAll($sql);


		$sql = "select b.cust_usr_nama,b.cust_usr_kode,b.cust_usr_jenis_kelamin ,b.cust_usr_alergi, ((current_date - cust_usr_tanggal_lahir)/365) as umur,  a.id_cust_usr
                    from klinik.klinik_registrasi a 
                    left join global.global_customer_user b on a.id_cust_usr = b.cust_usr_id 
                    left join global.global_kwitansi c on c.id_reg = a.reg_id
                    where a.reg_id = ".QuoteValue(DPE_CHAR,$_GET["id_reg"]);
   $dataPasien= $dtaccess->Fetch($sql);
          
		//$_POST["kwitansi_nomor"] = $kodeKwitansi;
		$_POST["id_cust_usr"] = $dataPasien["id_cust_usr"];
	}

?>

<html>
<head>

<title>Cetak Kartu Pasien</title>

<style type="text/css">
html{
  	width:100%;
  	height:100%;
  	margin:0;
  	padding:0;
}

body {
    font-family:      Arial, Verdana, Helvetica, sans-serif;
    margin: 0px;
  	font-size: 10px;
  	width:100%;
  	height:100%;
  	margin:0;
  	padding:0;
}

.tableisi {
	border-collapse:collapse;
    font-family:      Verdana, Arial, Helvetica, sans-serif;
    font-size:10px;
	border-top: black solid 1px; 
	border-bottom: black solid 1px;
}



.tablenota {
    font-family:      Verdana, Arial, Helvetica, sans-serif;
    font-size:        10px;
	border: solid black 1px; 
	border-collapse:collapse;
}

.tablenota .judul  {
	border: solid black 1px; 

}

.tablenota .isi {
	border-right: solid black 1px;
}

.ttd {
	height:50px;
}

.judul {

     font-size:      10px;
	font-weight: bolder;
}


</style>



<script>
//$(document).ready( function() {
//	window.print();
//});
      
</script>

</head>

<body>

<div style="width:14cm;margin:80px 0 0 25px;position:absolute;height:15cm;" align="center" style="margin-left:70px;">
<table align="center" style="width:21cm;"  border="0" cellpadding="4" cellspacing="1">
	<tr>
		<td align="center" style="font-size:17px">
		PEMERINTAH PROPINSI JAWA TIMUR<br />
		DINAS KESEHATAN PROPINSI JAWA TIMUR<br /><br />
		<big><b>BKMM CeHC SURABAYA</b></big><br />
		PUSAT PELAYANAN MATA MASYARAKAT<br /><br />
		<small>Jl. Gayung Kebonsari Timur No. 49 Surabaya<br />
    tlp. (031) 8283508-10 E-mail bkmm@diknesjatim.go.id
    </small>
    </td>
	</tr>
</table> 



<BR><BR><BR>

<table border="0" align="center" cellpadding="4" style="width:21cm;" cellspacing="1">
	<tr>
		<td align="center" style="font-size:14px"><STRONG>BUKTI PEMBAYARAN</STRONG></td>
	</tr>
</table> 
<br>
<table border="0" align="center" style="width:21cm;border:1px solid black;border-collapse:collapse;font-size:12px;" >
	<tr>
		<td style="border-bottom:1px solid black;border-right:1px solid black;" width= "40%" align="center"><b>NAMA PASIEN</b></td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;" width= "30%" align="center"><b>NO REGISTER</b></td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;" width= "40%" align="center"><b>NOMOR KWITANSI</b></td>
	</tr>	
	<tr height="25">
		<td style="border-bottom:1px solid black;border-right:1px solid black;" align="center"><label><?php echo $dataPasien["cust_usr_nama"]; ?></label></td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;"  align="center"><label><?php echo $dataPasien["cust_usr_kode"]; ?></label></td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;"  align="center"><label><?php echo $_POST["kwitansi_nomor"]; ?></label></td>
	</tr>	
</table>

<BR>
<BR>

	
<table style="width:21cm;"   align="center"  class="tablenota">
	<tr height="30">
		<td style="border-bottom:1px solid black;border-right:1px solid black;" width="5%" align="center"><STRONG>NO</STRONG></td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;" width="10%" align="center"><STRONG>KODE</STRONG></td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;" width="40%" align="center"><STRONG>JENIS PELAYANAN</STRONG></td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;" width="1%" align="center"><STRONG>VOL</STRONG></td>
		<td style="border-bottom:1px solid black;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;" width="20%" align="center"><STRONG>SUBTOTAL</STRONG></td>
	</tr>
	
	<?php for($i=0,$n=count($dataFolio);$i<$n;$i++) { $total+=$dataFolio[$i]["fol_nominal"];?>
		<tr height="25">
			<td align="right" class="isi"><?php echo ($i+1); ?></td>
			<td align="right" class="isi">&nbsp;</td>
			<td align="left" class="isi"><?php echo $dataFolio[$i]["fol_nama"];?></td>
			<td align="right" class="isi">1</td>
			<td align="right" class="isi"><?php echo currency_format($dataFolio[$i]["fol_nominal"]);?></td>
		</tr>
	<?php } ?>
	<?php if($n<10) { for($i=0;$i<(10-$n);$i++) { ?>
		<tr height="10">
			<td align="right" class="isi">&nbsp;</td>
			<td align="right" class="isi">&nbsp;</td>
			<td align="left" class="isi">&nbsp;</td>
			<td align="right" class="isi">&nbsp;</td>
			<td align="right" class="isi">&nbsp;</td>
		</tr>
	<?php }} ?>
	<tr height="25">
		<td style="border-bottom:1px solid black;border-top:1px solid black;" align="left"colspan=4><strong>TOTAL</strong></td>
		<td style="border-bottom:1px solid black;border-top:1px solid black;" align="right"><?php echo currency_format($total);?></td>
	</tr>
</table>

<BR>
<BR>

<table  border="0" align="center" style="width:21cm;border:1px solid black;border-collapse:collapse;font-size:12px;" >
	<tr height="25">
		<td  style="border-bottom:1px solid black;border-right:1px solid black;"  width= "25%" align="center">KASIR</td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;"  width= "25%" align="center">TANGGAL</td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;"  width= "25%" align="center">TANDA TANGAN</td>
		<td style="border-bottom:1px solid black;border-right:1px solid black;"  width= "25%" align="center">CONFIRMED</td>
	</tr>	
	<tr height="25">
		<td align="left"  style="border-right:1px solid black;" ><?php echo $userData['name']; ?></td>
		<td  style="border-right:1px solid black;" align="center" valign="center"><b><?php $tgl = getdateToday();
                           echo format_date_long($tgl);  ?></b></td>
		<td style="border-right:1px solid black;" align="left">&nbsp;</td>
		<td style="border-right:1px solid black;" align="left">&nbsp;</td>
	</tr>	
</table>
</div>

</body>
</html>
