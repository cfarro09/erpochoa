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
  $updateSQL = sprintf("UPDATE cnatural SET cedula=%s, nombre=%s, paterno=%s, materno=%s, ciudad=%s, celular=%s, telefono=%s, direccion=%s, email=%s, sexo=%s, obs=%s WHERE codigoclienten=%s",
                       GetSQLValueString($_POST['cedula'], "text"),
                       GetSQLValueString(strtoupper($_POST['nombre']), "text"),
                       GetSQLValueString(strtoupper($_POST['paterno']), "text"),
                       GetSQLValueString(strtoupper($_POST['materno']), "text"),
                       GetSQLValueString(strtoupper($_POST['ciudad']), "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString(strtoupper($_POST['direccion']), "text"),
                       GetSQLValueString(strtolower($_POST['email']), "text"),
                       GetSQLValueString($_POST['sexo'], "text"),
					   GetSQLValueString($_POST['obs'], "text"),
                       GetSQLValueString($_POST['codigoclienten'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "cliente_natural_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Editar = "-1";
if (isset($_GET['codigoclienten'])) {
  $colname_Editar = $_GET['codigoclienten'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Editar = sprintf("SELECT * FROM cnatural WHERE codigoclienten = %s", GetSQLValueString($colname_Editar, "int"));
$Editar = mysql_query($query_Editar, $Ventas) or die(mysql_error());
$row_Editar = mysql_fetch_assoc($Editar);
$totalRows_Editar = mysql_num_rows($Editar);

$Icono="fa fa-leaf";
$Color="font-blue";
$Titulo="Agregar Cliente Natural";
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
<input name="cedula" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar DNI" id="cedula" value="<?php echo $row_Editar['cedula']; ?>"  readonly="readonly" placeholder="Cédula"/>
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
  <input name="paterno" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Apellido Paterno" id="paterno" value="<?php echo $row_Editar['paterno']; ?>" maxlength="50" placeholder="Apellido Paterno" />
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
<div class="input-group"><span id="sprytextfield3">
  <input name="materno" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Apellidos Materno" id="materno" value="<?php echo $row_Editar['materno']; ?>" maxlength="50" placeholder="Apellido Materno" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-user-female  font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>
<tr>
<td colspan="2"> 
<div class="form-group">
<div class="col-md-8">
<div class="input-group"><span id="sprytextfield4">
  <input name="nombre" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombres" id="nombre" value="<?php echo $row_Editar['nombre']; ?>" maxlength="100" placeholder="Nombres" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-users font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-6">
<div class="input-group"><span id="sprytextfield5">
  <input name="ciudad" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Ciudad" id="ciudad" value="<?php echo $row_Editar['ciudad']; ?>" maxlength="30" placeholder="Ciudad" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-pointer font-blue-soft"></i>
</span>
</div>
</div>
</div>
 
</td>
<td>
<div class="form-group">
<div class="col-md-8">
<div class="input-group"><span id="sprytextfield6">
  <input name="direccion" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Direcci&oacute;n" id="direccion" value="<?php echo $row_Editar['direccion']; ?>" maxlength="80" placeholder="Dirección" />
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
<div class="input-group"><span id="sprytextfield8">
<input name="email" type="text" class="form-control tooltips"  data-placement="top" data-original-title="Editar Correo Electr&oacute;nico" id="email" value="<?php echo $row_Editar['email']; ?>" maxlength="100" placeholder="Correo Electrónico" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-envelope   font-blue-soft"></i>
</span>
</div>
</div>
</div>

</td>
<td>
<div class="form-group form-md-radios">
<label>Sexo</label>
<div class="md-radio-inline">
<div class="md-radio tooltips"  data-placement="top" data-original-title="Masculino">
<input <?php if (!(strcmp($row_Editar['sexo'],"M"))) {echo "checked=\"checked\"";} ?> type="radio" id="radio14" name="sexo" value="M" class="md-radiobtn" checked="checked">
<label for="radio14">
<span></span>
<span class="check"></span>
<span class="box"></span> Masculino </label>
</div>


<div class="md-radio tooltips"  data-placement="top" data-original-title="Femenino">
<input <?php if (!(strcmp($row_Editar['sexo'],"F"))) {echo "checked=\"checked\"";} ?> type="radio" id="radio15" name="sexo" value="F" class="md-radiobtn">
<label for="radio15">
<span></span>
<span class="check"></span>
<span class="box"></span> Femenino </label>
</div>

</div>
</div>


</td>
</tr>

<tr>
<td> 
<div class="form-group">
<div class="col-md-4">
<div class="input-group"><span id="sprytextfield7">
<input name="celular" type="text" class="form-control tooltips"  data-placement="top" data-original-title="Editar N&uacute;mero de Celular" id="celular" value="<?php echo $row_Editar['celular']; ?>" maxlength="13" placeholder="Celular" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-phone font-blue-soft"></i>
</span></div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield9">
<input name="telefono" type="text" class="form-control tooltips"  data-placement="top" data-original-title="Editar N&uacute;mero Telef&oacute;nico" id="telefono" value="<?php echo $row_Editar['telefono']; ?>" maxlength="10" placeholder="Teléfono" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-phone-alt  font-blue-soft"></i>
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
<input name="codigoclienten" type="hidden" value="<?php echo $row_Editar['codigoclienten']; ?>" />
</tbody>
</table>
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_update" value="Editar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Editar);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"], minChars:11});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "none", {validateOn:["blur", "change"]});
//var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 000 0000"});
//var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "email", {validateOn:["blur", "change"]});
//var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 0000"});
</script>
