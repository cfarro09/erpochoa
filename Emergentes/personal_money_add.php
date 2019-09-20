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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Ingresar")) {
  $fechahoy=date("Y-m-d");
  $insertSQL = sprintf("INSERT INTO sueldo_mensual (codigopersonal, sueldomensual, fecha_pago, observacion, fregistro) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['codigopersonal'], "int"),
                       GetSQLValueString($_POST['sueldomensual'], "double"),
                       GetSQLValueString($_POST['fecha_pago'], "date"),
                       GetSQLValueString($_POST['observacion'], "text"),
                       GetSQLValueString($fechahoy, "date"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

  $insertGoTo = "personal_money_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_Ventas, $Ventas);
$query_Personal = "SELECT codigopersonal, CONCAT(paterno,  ' ', materno, ' ', nombre) as Personal FROM personal WHERE estado = '0'";
$Personal = mysql_query($query_Personal, $Ventas) or die(mysql_error());
$row_Personal = mysql_fetch_assoc($Personal);
$totalRows_Personal = mysql_num_rows($Personal);

$Icono="fa fa-money";
$Color="font-blue";
$Titulo="Agregar Pago Mensual";
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
<td>

<div class="col-md-5">
<div class="form-group"><span id="spryselect1">
  <select name="codigopersonal" id="codigopersonal" class="form-control tooltips" data-placement="top" data-original-title="Agregar Personal">
    <option value="0">--- Personal ---</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Personal['codigopersonal']?>"><?php echo $row_Personal['Personal']?> </option>
    <?php
} while ($row_Personal = mysql_fetch_assoc($Personal));
  $rows = mysql_num_rows($Personal);
  if($rows > 0) {
      mysql_data_seek($Personal, 0);
	  $row_Personal = mysql_fetch_assoc($Personal);
  }
?>
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>

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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Sueldo Mensual" placeholder="Sueldo Mensual" id="sueldomensual" name="sueldomensual" maxlength="4" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Fecha de Pago" id="fecha_pago" name="fecha_pago" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" placeholder="Fecha de Pago"/>
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
              <textarea name="observacion" id="observacion" rows="3" class="form-control tooltips" data-placement="top" data-original-title="Descripci&oacute;n del pago" placeholder=" descripci&oacute;n del pago"></textarea>
              <span class="textareaRequiredMsg"></span></span></td>
          </tr>




</tbody>
</table>

<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
<br /><br /><br /><br />
<input type="hidden" name="MM_insert" value="Ingresar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Personal);
?>
<script type="text/javascript">

var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});


</script>
