<?php require_once('../Connections/Ventas.php'); ?>
<?php
date_default_timezone_set('America/Lima');

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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Ingresar")) {
  $fechahoy=date("Y-m-d");
  $updateSQL = sprintf("UPDATE sueldo_mensual SET sueldomensual=%s, fecha_pago=%s, observacion=%s, fregistro=%s WHERE codigosueldo=%s",
                       GetSQLValueString($_POST['sueldomensual'], "double"),
                       GetSQLValueString($_POST['fecha_pago'], "date"),
                       GetSQLValueString($_POST['observacion'], "text"),
                       GetSQLValueString($fechahoy, "date"),
                       GetSQLValueString($_POST['codigosueldo'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "personal_money_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


$colname_Personal = "-1";
if (isset($_GET['codigosueldo'])) {
  $colname_Personal = $_GET['codigosueldo'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Personal = sprintf("SELECT a.codigosueldo, a.codigopersonal, a.sueldomensual, a.fecha_pago, CONCAT(b.paterno,  ' ', b.materno, ' ', b.nombre) as Personal, a.observacion FROM sueldo_mensual a INNER JOIN personal b ON a.codigopersonal = b.codigopersonal WHERE codigosueldo = %s", GetSQLValueString($colname_Personal, "int"));
$Personal = mysql_query($query_Personal, $Ventas) or die(mysql_error());
$row_Personal = mysql_fetch_assoc($Personal);
$totalRows_Personal = mysql_num_rows($Personal);

$Icono="fa fa-money";
$Color="font-blue";
$Titulo="Editar Pago Mensual";
include("Fragmentos/cabecera.php");

 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar" >

<table class="table table-hover table-light">

<tbody>
<tr>
<td colspan="2">
  
  <table width="100%" border="0">
  <tr>
<td align="center">

<h4>
<?php echo $row_Personal['Personal']; ?>
</h4>

</td>
</tr>
</table>

</td>
</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-3">
<div class="input-group"><span id="sprytextfield2">
<input name="sueldomensual" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Sueldo Mensual" id="sueldomensual" placeholder="Sueldo Mensual" value="<?php echo $row_Personal['sueldomensual']; ?>" maxlength="4" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="fa fa-dollar font-blue-soft"></i>
</span>
</div>
</div>
</div>



</td>

<td>
<div class="form-group">
<div class="col-md-10">

<div class="input-group input-medium date date-picker"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield3">
  <input name="fecha_pago" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Fecha de Pago" id="fecha_pago" placeholder="Fecha de Pago" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo $row_Personal['fecha_pago']; ?>"/>
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn default" type="button">
<i class="fa fa-calendar font-blue-soft"></i>
</button>
</span>
</div>

</div> 
</td>



</tr>

 <tr>
            <td colspan="3"><span id="sprytextarea1">
              <textarea name="observacion" id="observacion" rows="3" class="form-control tooltips" data-placement="top" data-original-title="Descripci&oacute;n del pago" placeholder=" descripci&oacute;n del pago"><?php echo $row_Personal['observacion']; ?></textarea>
              <span class="textareaRequiredMsg"></span></span></td>
          </tr>




<input name="codigosueldo" type="hidden" value="<?php echo $row_Personal['codigosueldo']; ?>" />
</tbody>
</table>

<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<br /><br /><br /><br />
<input type="hidden" name="MM_update" value="Ingresar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Personal);
?>
<script type="text/javascript">

var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});



</script>
