<?php require_once('../Connections/Ventas.php'); ?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Editar")) {
  $updateSQL = sprintf("UPDATE cjuridico SET ruc=%s, razonsocial=%s, fax=%s, telefono=%s, celular=%s, contacto=%s, email=%s, obs=%s WHERE codigoclientej=%s",
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString(strtoupper($_POST['razonsocial']), "text"),
                       GetSQLValueString($_POST['fax'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString(strtoupper($_POST['contacto']), "text"),
                       GetSQLValueString(strtolower($_POST['email']), "text"),
					   GetSQLValueString($_POST['obs'], "text"),
                       GetSQLValueString($_POST['codigoclientej'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "cliente_juridico_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Editar = "-1";
if (isset($_GET['codigoclientej'])) {
  $colname_Editar = $_GET['codigoclientej'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Editar = sprintf("SELECT * FROM cjuridico WHERE codigoclientej = %s", GetSQLValueString($colname_Editar, "int"));
$Editar = mysql_query($query_Editar, $Ventas) or die(mysql_error());
$row_Editar = mysql_fetch_assoc($Editar);
$totalRows_Editar = mysql_num_rows($Editar);


$Icono="fa fa-black-tie";
$Color="font-blue";
$Titulo="Editar Cliente Jurídico";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form action="<?php echo $editFormAction; ?>" method="POST" name="Editar" id="Editar">

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
<input name="ruc" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de RUC" id="ruc" value="<?php echo $row_Editar['ruc']; ?>"  readonly="readonly" placeholder="R.U.C" />
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
  <input name="razonsocial" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombre de Raz&oacute;n Social" id="razonsocial" value="<?php echo $row_Editar['razonsocial']; ?>" maxlength="100" placeholder="Razón Social" />
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
  <input name="contacto" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombre de Contacto" id="contacto" value="<?php echo $row_Editar['contacto']; ?>" maxlength="50" placeholder="Contacto" />
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
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield6">
<input name="fax" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de Fax" id="fax" value="<?php echo $row_Editar['fax']; ?>" maxlength="10" placeholder="Fax" />
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
<input name="telefono" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero Telef&oacute;nico" id="telefono" value="<?php echo $row_Editar['telefono']; ?>" maxlength="10" placeholder="Teléfono" />
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
<input name="celular" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de Celular" id="celular" value="<?php echo $row_Editar['celular']; ?>" maxlength="14" placeholder="Celular" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-phone font-blue-soft"></i>
</span></div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield5">
<input name="email" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Correo Electr&oacute;nico" id="email" value="<?php echo $row_Editar['email']; ?>" maxlength="100" placeholder="Correo Electrónico" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-envelope   font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>
<tr>

  <td colspan="2"><div class="form-group">
    <div class="col-md-10">
      <div class="input-group"><span id="sprytextfield3">
       <textarea name="obs" id="obs" cols="80" placeholder="Observacion"><?php echo $row_Editar['obs']; ?></textarea>
        <span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon"> <i class="glyphicon glyphicon-envelope font-blue-soft"></i> </span> </div>
    </div>
  </div></td>
</tr>

<tr>
<td> 






</td>
<td>
<input name="codigoclientej" type="hidden" value="<?php echo $row_Editar['codigoclientej']; ?>" />
</td>

</tr>

</tbody>
</table>

<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<?php include("Fragmentos/pie.php"); ?>
<input type="hidden" name="MM_update" value="Editar" />
</form>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"], minChars:13});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "email", {validateOn:["blur", "change"]});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 0000"});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 0000"});
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 000 0000"});


</script>
<?php
mysql_free_result($Editar);
?>
