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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Editar")) {
  $updateSQL = sprintf("UPDATE proveedor_cuentas SET codigobanco=%s, titular=%s, numero_cuenta=%s, tipo_cuenta=%s, estado_cuenta=%s WHERE codprovcue=%s",
                       GetSQLValueString($_POST['codigobanco'], "text"),
                       GetSQLValueString(strtoupper($_POST['titular']), "text"),
                       GetSQLValueString($_POST['numero_cuenta'], "text"),
                       GetSQLValueString($_POST['tipo_cuenta'], "text"),
                       GetSQLValueString($_POST['estado_cuenta'], "int"),
                       GetSQLValueString($_POST['codprovcue'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "proveedor_cuentas_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

$colname_Proveedor = "-1";
if (isset($_GET['codprovcue'])) {
  $colname_Proveedor = $_GET['codprovcue'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Proveedor = sprintf("SELECT a.codprovcue, a.codigobanco, a.titular, a.numero_cuenta, a.tipo_cuenta, a.estado_cuenta, b.ruc, b.razonsocial FROM proveedor_cuentas a INNER JOIN proveedor b on a.codigoproveedor = b. codigoproveedor WHERE a.codprovcue = %s", GetSQLValueString($colname_Proveedor, "int"));
$Proveedor = mysql_query($query_Proveedor, $Ventas) or die(mysql_error());
$row_Proveedor = mysql_fetch_assoc($Proveedor);
$totalRows_Proveedor = mysql_num_rows($Proveedor);

mysql_select_db($database_Ventas, $Ventas);
$query_Banco = "SELECT * FROM banco WHERE estado = 0";
$Banco = mysql_query($query_Banco, $Ventas) or die(mysql_error());
$row_Banco = mysql_fetch_assoc($Banco);
$totalRows_Banco = mysql_num_rows($Banco);

$Icono="glyphicon glyphicon-credit-card";
$Color="font-blue";
$Titulo="Agregar Cuenta";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form action="<?php echo $editFormAction; ?>" method="POST" name="Editar" id="Editar">


<h3 class="font-red-thunderbird text-center">PROVEEDOR</h3>
<h4 class="font-red-thunderbird text-center"><?php echo $row_Proveedor['ruc']; ?> - <?php echo $row_Proveedor['razonsocial']; ?></h4>

<table class="table table-hover table-light">

<tbody>

<tr>
<td>
<div class="col-md-5">
<div class="form-group"><span id="spryselect1">
  <select name="codigobanco" id="codigobanco" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Banco">
    <option value="0" <?php if (!(strcmp(0, $row_Proveedor['codigobanco']))) {echo "selected=\"selected\"";} ?>>--- Banco ---</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Banco['codigobanco']?>"<?php if (!(strcmp($row_Banco['codigobanco'], $row_Proveedor['codigobanco']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Banco['nombre_banco']?></option>
    <?php
} while ($row_Banco = mysql_fetch_assoc($Banco));
  $rows = mysql_num_rows($Banco);
  if($rows > 0) {
      mysql_data_seek($Banco, 0);
	  $row_Banco = mysql_fetch_assoc($Banco);
  }
?>
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
</td>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield1">
  <input name="titular" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Titular de la Cuenta" id="titular" value="<?php echo $row_Proveedor['titular']; ?>" maxlength="100" placeholder="Titular Cuenta" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-user  font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>
<tr>
  <td>
  <div class="col-md-5">
<div class="form-group"><span id="spryselect2">
  <select name="tipo_cuenta" id="tipo_cuenta" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Tipo de Cuenta">
    <option value="0" <?php if (!(strcmp(0, $row_Proveedor['tipo_cuenta']))) {echo "selected=\"selected\"";} ?>>--- Tipo de Cuenta ---</option>
    <option value="ah" <?php if (!(strcmp("ah", $row_Proveedor['tipo_cuenta']))) {echo "selected=\"selected\"";} ?>> Ahorro</option>
    <option value="co" <?php if (!(strcmp("co", $row_Proveedor['tipo_cuenta']))) {echo "selected=\"selected\"";} ?>> Corriente</option>
    <option value="ch" <?php if (!(strcmp("ch", $row_Proveedor['tipo_cuenta']))) {echo "selected=\"selected\"";} ?>> Cheque</option>
    <option value="cd" <?php if (!(strcmp("cd", $row_Proveedor['tipo_cuenta']))) {echo "selected=\"selected\"";} ?>> Certificado de Depósito</option>

    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
  
  
  </td>
  <td>
  <div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield2">
<input name="numero_cuenta" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de Cuenta" id="numero_cuenta" value="<?php echo $row_Proveedor['numero_cuenta']; ?>" maxlength="100" placeholder="Número Cuenta" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-credit-card  font-blue-soft"></i>
</span>
</div>
</div>
</div>
  
  </td>
</tr>
<tr>
<td>
<div class="form-group form-md-radios">
<label>Estado Cuenta</label>
<div class="md-radio-inline">
<div class="md-radio tooltips" data-placement="top" data-original-title="Cuenta Activa">
<input <?php if (!(strcmp($row_Proveedor['estado_cuenta'],"0"))) {echo "checked=\"checked\"";} ?> type="radio" id="radio14" name="estado_cuenta" value="0" class="md-radiobtn" checked="checked">
<label for="radio14">
<span></span>
<span class="check"></span>
<span class="box"></span> Activa </label>
</div>


<div class="md-radio tooltips" data-placement="top" data-original-title="Cuenta No Activa">
<input <?php if (!(strcmp($row_Proveedor['estado_cuenta'],"1"))) {echo "checked=\"checked\"";} ?> type="radio" id="radio15" name="estado_cuenta" value="1" class="md-radiobtn">
<label for="radio15">
<span></span>
<span class="check"></span>
<span class="box"></span> No Activa </label>
</div>

</div>
</div>



</td>
<td>

</td>

</tr>


<input name="codprovcue" type="hidden" value="<?php echo $row_Proveedor['codprovcue']; ?>" />

</tbody>
</table>
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_update" value="Editar" />
</form>
<?php include("Fragmentos/pie.php"); ?>
<?php
mysql_free_result($Proveedor);

mysql_free_result($Banco);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});

</script>