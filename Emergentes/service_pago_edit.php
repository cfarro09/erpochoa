<?php require_once('../Connections/Ventas.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Actualizar")) {
  $updateSQL = sprintf("UPDATE serviciosapagar SET codacceso=%s, codigopersonal=%s, nrecibo=%s, monto=%s, femision=%s, fpago=%s, observacion=%s WHERE codigosap=%s",
                       GetSQLValueString($_POST['codacceso'], "int"),
                       GetSQLValueString($_POST['codigopersonal'], "int"),
                       GetSQLValueString($_POST['nrecibo'], "text"),
                       GetSQLValueString($_POST['monto'], "int"),
                       GetSQLValueString($_POST['femision'], "date"),
                       GetSQLValueString($_POST['fpago'], "date"),
                       GetSQLValueString(strtoupper($_POST['observacion']), "text"),
                       GetSQLValueString($_POST['codigosap'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "service_pago_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Actualizar = "-1";
if (isset($_GET['codigosap'])) {
  $colname_Actualizar = $_GET['codigosap'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Actualizar = sprintf("SELECT a.codigosap, a.nrecibo, a.monto, a.femision, a.fpago,a.observacion,b.nombre FROM serviciosapagar a INNER JOIN servicios b ON a.codigosv = b.codigosv WHERE a.codigosap = %s", GetSQLValueString($colname_Actualizar, "int"));
$Actualizar = mysql_query($query_Actualizar, $Ventas) or die(mysql_error());
$row_Actualizar = mysql_fetch_assoc($Actualizar);
$totalRows_Actualizar = mysql_num_rows($Actualizar);

$Icono="fa fa-dollar";
$Color="font-blue";
$Titulo="Editar Pago de Servicios";
include("Fragmentos/cabecera.php");

 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Actualizar" id="Actualizar" >

<table class="table table-hover table-light">

<tbody>
<tr>
<td colspan="2">
<h4><?php echo $row_Actualizar['nombre']; ?></h4> 
  

</td>
</tr>

<tr>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group input-medium date date-picker"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield5">
  <input name="femision" type="text" class="form-control" id="femision" placeholder="Fecha de Emisión" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo $row_Actualizar['femision']; ?>"/>
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn default" type="button">
<i class="fa fa-calendar font-blue-soft"></i>
</button>
</span>
</div>

</div> 
</div>



</td>

<td>
 <div class="form-group">
<div class="col-md-10">
<div class="input-group input-medium date date-picker"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield6">
  <input name="fpago" type="text" class="form-control" id="fpago" placeholder="Fecha de Cancelación" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo $row_Actualizar['fpago']; ?>"/>
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn default" type="button">
<i class="fa fa-calendar font-blue-soft"></i>
</button>
</span>
</div>

</div> 
</div>
</td>



</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield2">
<input name="nrecibo" type="text" class="form-control" id="nrecibo" placeholder="Número de Recibo" value="<?php echo $row_Actualizar['nrecibo']; ?>" maxlength="20" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="fa fa-barcode font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield4">
<input name="monto" type="text" class="form-control" id="monto" placeholder="Monto a Pagar" value="<?php echo $row_Actualizar['monto']; ?>" maxlength="5" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="fa fa-dollar font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
</tr>
<tr>
  <td colspan="2"><span id="sprytextarea1">
  <textarea name="observacion" id="observacion" rows="3" class="form-control" placeholder="Descripción"><?php echo $row_Actualizar['observacion']; ?></textarea>
  <span id="countsprytextarea1">&nbsp;</span><span class="textareaRequiredMsg"></span></span></td>
  </tr>

</tbody>
</table>
<input name="codigosap" type="hidden" id="codigosap" value="<?php echo $row_Actualizar['codigosap']; ?>" />
<input name="codacceso" type="hidden" value="<?php echo $_SESSION['kt_login_id']; ?>" />
<input name="codigopersonal" type="hidden" value="<?php echo $_SESSION['kt_codigopersonal']; ?>" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_update" value="Actualizar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Actualizar);
?>
<script type="text/javascript">
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "none", {validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"], counterId:"countsprytextarea1", counterType:"chars_count"});



</script>
