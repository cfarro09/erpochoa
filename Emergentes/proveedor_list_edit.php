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
  $updateSQL = sprintf("UPDATE proveedor SET ruc=%s, razonsocial=%s, pais=%s, ciudad=%s, direccion=%s, fax=%s, contacto=%s, email=%s, telefono=%s, celular=%s, paginaweb=%s WHERE codigoproveedor=%s",
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString(strtoupper($_POST['razonsocial']), "text"),
					   GetSQLValueString(strtolower($_POST['pais']), "text"),
					   GetSQLValueString(strtoupper($_POST['ciudad']), "text"),
                       GetSQLValueString(strtoupper($_POST['direccion']), "text"),
                       GetSQLValueString($_POST['fax'], "text"),
                       GetSQLValueString(strtoupper($_POST['contacto']), "text"),
                       GetSQLValueString(strtolower($_POST['email']), "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString(strtolower($_POST['paginaweb']), "text"),
                       GetSQLValueString($_POST['codigoproveedor'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "proveedor_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Editar = "-1";
if (isset($_GET['codigoproveedor'])) {
  $colname_Editar = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Editar = sprintf("SELECT * FROM proveedor WHERE codigoproveedor = %s", GetSQLValueString($colname_Editar, "int"));
$Editar = mysql_query($query_Editar, $Ventas) or die(mysql_error());
$row_Editar = mysql_fetch_assoc($Editar);
$totalRows_Editar = mysql_num_rows($Editar);

$Icono="fa fa-magic";
$Color="font-blue";
$Titulo="Editar Proveedor";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<table class="table table-hover table-light">

<tbody>
<tr>
<td colspan="2">
  
  <table width="40%" border="0">
  <tr>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield1">
<input name="ruc" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de R.U.C" id="ruc" placeholder="R.U.C" value="<?php echo $row_Editar['ruc']; ?>" readonly="readonly" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="icon-credit-card  font-blue-soft"></i>
</span>
</div>
</div>
</div>

</td>
</tr>
</table>

</td>
</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield2">
  <input name="razonsocial" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Raz&oacute;n Social" id="razonsocial" placeholder="Razón Social" value="<?php echo $row_Editar['razonsocial']; ?>" maxlength="100" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-user r font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield4">
  <input name="contacto" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombre de Contacto" id="contacto" placeholder="Contacto" value="<?php echo $row_Editar['contacto']; ?>" maxlength="50" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-user r font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>
<tr>
  <td>
  <div class="col-md-5">
<div class="form-group"><span id="spryselect1">
  <select name="pais" id="pais" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Pais">
    <option value="0" <?php if (!(strcmp(0, $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>>--- Pais ---</option>
    <option value="ar" <?php if (!(strcmp("ar", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Argentina</option>
    <option value="bo" <?php if (!(strcmp("bo", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Bolivia</option>
    <option value="br" <?php if (!(strcmp("br", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Brazil</option>
    <option value="ch" <?php if (!(strcmp("ch", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Chile</option>
    <option value="co" <?php if (!(strcmp("co", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Colombia</option>
    <option value="ec" <?php if (!(strcmp("ec", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Ecuador</option>
    <option value="pe" <?php if (!(strcmp("pe", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Perú</option>
    <option value="ve" <?php if (!(strcmp("ve", $row_Editar['pais']))) {echo "selected=\"selected\"";} ?>> Venezuela</option>
    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
  
  
  </td>
  <td>
  <div class="form-group">
<div class="col-md-6">
<div class="input-group"><span id="sprytextfield10">
  <input name="ciudad" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Ciudad" id="ciudad" placeholder="Ciudad" value="<?php echo $row_Editar['ciudad']; ?>" maxlength="30" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-pointer font-blue-soft"></i>
</span>
</div>
</div>
</div>
  
  </td>
</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield3">
  <input name="direccion" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Direcci&oacute;n" id="direccion" placeholder="Dirección" value="<?php echo $row_Editar['direccion']; ?>" maxlength="60" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-pointer  font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield5">
<input name="email" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Correo Electr&oacute;nico Social" id="email" placeholder="Correo Electrónico" value="<?php echo $row_Editar['email']; ?>" maxlength="100" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-envelope   font-blue-soft"></i>
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
<div class="input-group"><span id="sprytextfield6">
<input name="fax" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de FAX" id="fax" placeholder="Fax" value="<?php echo $row_Editar['fax']; ?>" maxlength="10" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-earphone font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield7">
<input name="telefono" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero Telef&oacute;nico " id="telefono" placeholder="Teléfono" value="<?php echo $row_Editar['telefono']; ?>" maxlength="10" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-phone-alt  font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>
<tr>
<td> 
<div class="form-group">
<div class="col-md-4">
<div class="input-group"><span id="sprytextfield8">
<input name="celular" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de Celular" id="celular" placeholder="Celular" value="<?php echo $row_Editar['celular']; ?>" maxlength="14" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-phone font-blue-soft"></i>
</span></div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield9">
<input name="paginaweb" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar P&aacute;gina Web" id="paginaweb" placeholder="Página Web - http://www.demo.com" value="<?php echo $row_Editar['paginaweb']; ?>" maxlength="100" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-globe font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>
<input name="codigoproveedor" type="hidden" value="<?php echo $row_Editar['codigoproveedor']; ?>" />
</tbody>
</table>
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />
<input type="hidden" name="MM_update" value="Ingresar" />   

</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"], minChars:11});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
//var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
//var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "email", {validateOn:["blur", "change"]});
//var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 0000"});
//var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 0000"});
//var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 000 0000"});
//var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "url", {validateOn:["blur", "change"]});
var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
</script>

<?php
mysql_free_result($Editar);
?>
