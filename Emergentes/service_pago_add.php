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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Ingresar")) {
  $insertSQL = sprintf("INSERT INTO serviciosapagar (codigosv, codacceso, codigopersonal, nrecibo, monto, femision, fpago, observacion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['codigosv'], "int"),
                       GetSQLValueString($_POST['codacceso'], "int"),
                       GetSQLValueString($_POST['codigopersonal'], "int"),
                       GetSQLValueString($_POST['nrecibo'], "text"),
                       GetSQLValueString($_POST['monto'], "int"),
                       GetSQLValueString($_POST['femision'], "date"),
                       GetSQLValueString($_POST['fpago'], "date"),
					   GetSQLValueString(strtoupper($_POST['observacion']), "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());
}

mysql_select_db($database_Ventas, $Ventas);
$query_Servicios = "SELECT * FROM servicios WHERE tipo = 0";
$Servicios = mysql_query($query_Servicios, $Ventas) or die(mysql_error());
$row_Servicios = mysql_fetch_assoc($Servicios);
$totalRows_Servicios = mysql_num_rows($Servicios);

$Icono="fa fa-dollar";
$Color="font-blue";
$Titulo="Pago de Servicios";
include("Fragmentos/cabecera.php");

 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar" >

<table class="table table-hover table-light">

<tbody>
<tr>
<td colspan="2">
<div class="col-md-5">
<div class="form-group"><span id="spryselect1">
<select name="codigosv" id="codigosv" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Servicio a Pagar">
  <option value="0">--- Servicio a Pagar ---</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Servicios['codigosv']?>"><?php echo $row_Servicios['nombre']?></option>
  <?php
} while ($row_Servicios = mysql_fetch_assoc($Servicios));
  $rows = mysql_num_rows($Servicios);
  if($rows > 0) {
      mysql_data_seek($Servicios, 0);
	  $row_Servicios = mysql_fetch_assoc($Servicios);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div> 
  

</td>
</tr>

<tr>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group input-medium date date-picker tooltips" data-placement="top" data-original-title="Agregar Fecha de Emisi&oacute;n del Recibo"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield5">
  <input type="text" class="form-control" id="femision" name="femision" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" placeholder="Fecha de Emisión"/>
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Fecha de Pago del Recibo" id="fpago" name="fpago" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" placeholder="Fecha de Pago"/>
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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de Recibo" placeholder="Número de Recibo" id="nrecibo" name="nrecibo" maxlength="20" />
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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Monto a Pagar" placeholder="Monto a Pagar" id="monto" name="monto" maxlength="5" />
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
  <textarea name="observacion" id="observacion" rows="3" class="form-control tooltips" data-placement="top" data-original-title="Agregar Descripci&oacute;n u Observaci&oacute;n" placeholder="Descripción u Obervación"></textarea>
<span class="textareaRequiredMsg"></span></span></td>
  </tr>

</tbody>
</table>
<input name="codacceso" type="hidden" value="<?php echo $_SESSION['kt_login_id']; ?>" />
<input name="codigopersonal" type="hidden" value="<?php echo $_SESSION['kt_codigopersonal']; ?>" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Servicios);
?>
<script type="text/javascript">
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});


</script>
