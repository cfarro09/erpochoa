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
  $insertSQL = sprintf("INSERT INTO proveedor_cuentas (codigoproveedor, codigobanco, titular, numero_cuenta, tipo_cuenta, estado_cuenta) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['codigoproveedor'], "int"),
                       GetSQLValueString($_POST['codigobanco'], "int"),
                       GetSQLValueString(strtoupper($_POST['titular']), "text"),
                       GetSQLValueString($_POST['numero_cuenta'], "text"),
                       GetSQLValueString($_POST['tipo_cuenta'], "text"),
                       GetSQLValueString($_POST['estado_cuenta'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

  $insertGoTo = "proveedor_cuentas_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_Proveedor = "-1";
if (isset($_GET['codigoproveedor'])) {
  $colname_Proveedor = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Proveedor = sprintf("SELECT * FROM proveedor WHERE codigoproveedor = %s", GetSQLValueString($colname_Proveedor, "int"));
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
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<h3 class="font-red-thunderbird text-center">PROVEEDOR</h3>
<h4 class="font-red-thunderbird text-center"><?php echo $row_Proveedor['ruc']; ?> - <?php echo $row_Proveedor['razonsocial']; ?></h4>

<table class="table table-hover table-light">

<tbody>
<tr>
<td>
<div class="col-md-5">
<div class="form-group"><span id="spryselect1">
  <select name="codigobanco" id="codigobanco" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Banco">
    <option value="0">--- Banco ---</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Banco['codigobanco']?>"><?php echo $row_Banco['nombre_banco']?></option>
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Titular de la Cuenta" placeholder="Titular Cuenta" id="titular" name="titular" maxlength="100" />
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
    <option value="0">--- Tipo de Cuenta ---</option>
    <option value="ah"> Ahorro</option>
    <option value="co"> Corriente</option>
    <option value="ch"> Cheque</option>
    <option value="cd"> Certificado de Dep贸sito</option>

    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
  
  
  </td>
  <td>
  <div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield2">
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de Cuenta" placeholder="N煤mero Cuenta" id="numero_cuenta" name="numero_cuenta" maxlength="30" />
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
<input type="radio" id="radio14" name="estado_cuenta" value="0" class="md-radiobtn" checked="checked">
<label for="radio14">
<span></span>
<span class="check"></span>
<span class="box"></span> Activa </label>
</div>


<div class="md-radio tooltips" data-placement="top" data-original-title="Cuenta No Activa">
<input type="radio" id="radio15" name="estado_cuenta" value="1" class="md-radiobtn">
<label for="radio15">
<span></span>
<span class="check"></span>
<span class="box"></span> No Activa </label>
</div>

</div>
</div>


<input name="codigoproveedor" type="hidden" value="<?php echo $row_Proveedor['codigoproveedor']; ?>" />
</td>
<td>

</td>

</tr>




</tbody>
</table>
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />
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