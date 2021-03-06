<?php
     require_once("root.inc.php");
     require_once($ROOT."library/auth.cls.php");
     require_once($ROOT."library/textEncrypt.cls.php");
     require_once($ROOT."library/datamodel.cls.php");
     require_once($ROOT."library/dateFunc.lib.php");  
     require_once($ROOT."library/currFunc.lib.php");
     require_once($APLICATION_ROOT."library/view.cls.php");
     
     $dtaccess = new DataAccess();
     $enc = new textEncrypt();
     $auth = new CAuth();
     $delimiter = ";";
     $startLine = 1;
     $skr = date('Y-m-d H:i:s');
     $view = new CView($_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);
     
     if($_POST["csvFile"]) $csvFile = $_POST["csvFile"];
     else $csvFile = $APLICATION_ROOT."temp/";

     if(isset($_POST["btnNext"])){
          if($_FILES["csv_file"]["tmp_name"]){
               $err = false;
          } else {
               $err=true;
          }
         
          if(!$err){
               if (is_uploaded_file($_FILES["csv_file"]["tmp_name"])) {
                    $csvFile .= $_FILES["csv_file"]["name"];
                    chmod($_FILES["csv_file"]["tmp_name"],1777);
                    copy($_FILES["csv_file"]["tmp_name"], $csvFile);
               }
          }
                   
          if ((!$myFile = @fopen(stripslashes($csvFile), "r")) || $err==true) {
               $err = true;
          } else {             
               while ($data = fgetcsv($myFile, 200000, $delimiter)) {
                    //echo $mk[$struk[trim($data[0])]][trim($data[2])][trim($data[1])]."--".$struk[trim($data[0])]."--".$data[2]."--".$data[1]."<br>";
                    $dbTable = "klinik.klinik_biaya_baru";
               
                    //$dbField[0] = "biaya_id";   // PK
                    $dbField[0] = "biaya_kode";
                    $dbField[1] = "biaya_nama";
                    $dbField[2] = "biaya_beli";
                    $dbField[3] = "biaya_jual";
                       
                    //if(!$prosedurId) $prosedurId = $dtaccess->GetTransID();   
                    //$dbValue[0] = QuoteValue(DPE_CHAR,$prosedurId);
                    $dbValue[0] = QuoteValue(DPE_CHAR,$data[0]);
                    $dbValue[1] = QuoteValue(DPE_CHAR,str_replace("'","\"",$data[1]));
                    $dbValue[2] = QuoteValue(DPE_NUMERIC,$data[2]);
                    $dbValue[3] = QuoteValue(DPE_NUMERIC,$data[3]);
                       
                    $dbKey[0] = 0; // -- set key buat clause wherenya , valuenya = index array buat field / value
                    $dtmodel = new DataModel($dbTable,$dbField,$dbValue,$dbKey);
                    $dtmodel->Insert() or die("insert  error");	
                    
                    unset($dtmodel); unset($dbValue); unset($dbKey); unset($jam);unset($waktu);
                    
                    $count++;         
                    
                    //unset($prosedurId);
               }
          }
     }
     
     echo "<br>".$count."--".$startLine;
     
?>

<?php echo $view->RenderBody("inosoft.css",true); ?>

<body>
<form name="frmEdit" method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>" enctype="multipart/form-data">

<table width="60%" border="0" cellpadding="1" cellspacing="1">
    <tr>
        <td align="left" colspan=4 class="tblHeader">Import Data prosedur</td>
    </tr>
    <tr>
        <td width="25%" align="left" class="tblMainCol"><strong>CSV File<?php if($err){?> <font color="red">(*)</font><?php } ?></strong></td>
        <td width="75%" class="tblCol" colspan="3"><input type="file" name="csv_file" size=25 class="inputField"></td>
    </tr>
    <tr>
        <td colspan="4" align="center" class="tblCol">
           <input type="submit" name="btnNext" value="Proses" class="inputField" OnClick="document.frmEdit.btnNext.value = 'Please Wait'">
        </td>
    </tr>
</table>
</form>
<?php if($_POST["btnNext"] && !$err) {?>
    <font color="green">Proses Import Data prosedur Sudah Selesai</font>
<?php }?>
<?php if($err){?><label><font color="red">Pilih File</font></label><?php } ?>
</body>
</html>
<?
    $dtaccess->Close();
?>