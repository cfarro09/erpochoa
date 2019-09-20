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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Ingresar")) {
  $updateSQL = sprintf("UPDATE cargo_personal SET codcar=%s, fecha_actualizar=%s, observacion=%s WHERE codcarper=%s",
                       GetSQLValueString($_POST['codcar'], "int"),
                       GetSQLValueString($_POST['fecha_actualizar'], "date"),
                       GetSQLValueString(strtoupper($_POST['observacion']), "text"),
                       GetSQLValueString($_POST['codcarper'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "personal_cargo_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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



mysql_select_db($database_Ventas, $Ventas);
$query_Cargos = "SELECT * FROM cargos";
$Cargos = mysql_query($query_Cargos, $Ventas) or die(mysql_error());
$row_Cargos = mysql_fetch_assoc($Cargos);
$totalRows_Cargos = mysql_num_rows($Cargos);

$colname_Personal = "-1";
if (isset($_GET['codcarper'])) {
  $colname_Personal = $_GET['codcarper'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Personal = sprintf("SELECT a.codcarper, a.codigopersonal, a.codcar, a.fecha_actualizar, a.observacion, CONCAT(b.paterno,  ' ', b.materno, ' ', b.nombre) as Personal FROM cargo_personal a INNER JOIN personal b ON a.codigopersonal = b.codigopersonal WHERE codcarper = %s", GetSQLValueString($colname_Personal, "int"));
$Personal = mysql_query($query_Personal, $Ventas) or die(mysql_error());
$row_Personal = mysql_fetch_assoc($Personal);
$totalRows_Personal = mysql_num_rows($Personal);




$Icono="glyphicon glyphicon-signal";
$Color="font-blue";
$Titulo="Agregar Cargo";
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
<div class="col-md-5">
<div class="form-group"><span id="spryselect2">
  <select name="codcar" id="codcar" class="form-control tooltips"  data-placement="top" data-original-title="Seleccionar Cargo">
    <option value="0" <?php if (!(strcmp(0, $row_Personal['codcar']))) {echo "selected=\"selected\"";} ?>>--- Cargo ---</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Cargos['codcar']?>"<?php if (!(strcmp($row_Cargos['codcar'], $row_Personal['codcar']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Cargos['nombre_cargo']?></option>
    <?php
} while ($row_Cargos = mysql_fetch_assoc($Cargos));
  $rows = mysql_num_rows($Cargos);
  if($rows > 0) {
      mysql_data_seek($Cargos, 0);
	  $row_Cargos = mysql_fetch_assoc($Cargos);
  }
?>
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>



</td>

<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group input-medium date date-picker"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield3">
  <input name="fecha_actualizar" type="text" class="form-control tooltips"  data-placement="top" data-original-title="Editar Fecha de Ingreso o Cambio" id="fecha_actualizar" placeholder="Fecha de Ingreso o Cambio" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo $row_Personal['fecha_actualizar']; ?>"/>
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
<td colspan="2"> 
<div class="form-group"><span id="sprytextarea1">
  <textarea name="observacion" id="observacion" rows="3" class="form-control tooltips"  data-placement="top" data-original-title="Editar Descripci&oacute;n u Observaci&oacute;n" placeholder="Descripci&oacute;n u Observaci&oacute;n"><?php echo $row_Personal['observacion']; ?></textarea>
<span class="textareaRequiredMsg"></span></span></div>
</td>
</tr>

</tbody>
</table>
<input name="codcarper" type="hidden" value="<?php echo $row_Personal['codcarper']; ?>" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_update" value="Ingresar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Cargos);

mysql_free_result($Personal);
?>
<script type="text/javascript">


var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});

var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});

</script>
