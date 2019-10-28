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
  $updateSQL = sprintf("UPDATE personal SET cedula=%s, nombre=%s, paterno=%s, materno=%s, fecha_nac=%s, direccion=%s, direccionl=%s, celular=%s, codigoprofesion=%s WHERE codigopersonal=%s",
                       GetSQLValueString($_POST['cedula'], "text"),
                       GetSQLValueString(strtoupper($_POST['nombre']), "text"),
                       GetSQLValueString(strtoupper($_POST['paterno']), "text"),
                       GetSQLValueString(strtoupper($_POST['materno']), "text"),
                       GetSQLValueString($_POST['fecha_nac'], "date"),
                       GetSQLValueString(strtoupper($_POST['direccion']), "text"),
					   GetSQLValueString(strtoupper($_POST['direccionl']), "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString($_POST['codigoprofesion'], "int"),
                       GetSQLValueString($_POST['codigopersonal'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "personal_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



mysql_select_db($database_Ventas, $Ventas);
$query_Profesion = "SELECT * FROM profesion";
$Profesion = mysql_query($query_Profesion, $Ventas) or die(mysql_error());
$row_Profesion = mysql_fetch_assoc($Profesion);
$totalRows_Profesion = mysql_num_rows($Profesion);

$colname_Edit = "-1";
if (isset($_GET['codigopersonal'])) {
  $colname_Edit = $_GET['codigopersonal'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Edit = sprintf("SELECT * FROM personal WHERE codigopersonal = %s", GetSQLValueString($colname_Edit, "int"));
$Edit = mysql_query($query_Edit, $Ventas) or die(mysql_error());
$row_Edit = mysql_fetch_assoc($Edit);
$totalRows_Edit = mysql_num_rows($Edit);
 
$Icono="glyphicon glyphicon-user";
$Color="font-blue";
$Titulo="Editar Personal";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form method="POST" action="<?php echo $editFormAction; ?>" name="Editar" id="Editar">

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
<input name="cedula" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de C&eacute;dula" id="cedula" placeholder="Cédula" value="<?php echo $row_Edit['cedula']; ?>" readonly="readonly" />
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
  <input name="paterno" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Apellido Paterno" id="paterno" value="<?php echo $row_Edit['paterno']; ?>" maxlength="50" placeholder="Apellido Paterno" />
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
  <input name="materno" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Apellido Materno" id="materno" value="<?php echo $row_Edit['materno']; ?>" maxlength="50" placeholder="Apellido Materno" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-user-female  font-blue-soft"></i>
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
<div class="input-group"><span id="sprytextfield4">
  <input name="nombre" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombres" id="nombre" value="<?php echo $row_Edit['nombre']; ?>" maxlength="100" placeholder="Nombres" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-users font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
	<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group input-medium date date-picker"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield5">
  <input name="fecha_nac" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Fecha de Nacimiento" id="fecha_nac" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo $row_Edit['fecha_nac']; ?>" placeholder="Fecha Nacimiento"/>
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn default" type="button">
<i class="fa fa-calendar font-blue-soft"></i>
</button>
</span>
</div>
<!-- /input-group -->
</div> 
</td>
<td>
</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield6">
  <input name="direccion" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Direcci&oacute;n" id="direccion" value="<?php echo $row_Edit['direccion']; ?>" maxlength="80" placeholder="Dirección" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="icon-pointer font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield6">
  <input name="direccionl" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Direcci&oacute;n Legal" id="direccionl" value="<?php echo $row_Edit['direccionl']; ?>" maxlength="80" placeholder="Dirección" />
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
<div class="col-md-4">
<div class="input-group"><span id="sprytextfield7">
<input name="celular" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar N&uacute;mero de Celular" id="celular" value="<?php echo $row_Edit['celular']; ?>" maxlength="13" placeholder="Celular" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-phone font-blue-soft"></i>
</span></div>
</div>
</div>
</td>
<td>
<div class="col-md-5">
<div class="form-group"><span id="spryselect1">
  <select name="codigoprofesion" id="codigoprofesion" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Profesi&oacute;n">
    <option value="0" <?php if (!(strcmp(0, $row_Edit['codigoprofesion']))) {echo "selected=\"selected\"";} ?>>--- Tipo de Cargo ---</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Profesion['codigoprofesion']?>"<?php if (!(strcmp($row_Profesion['codigoprofesion'], $row_Edit['codigoprofesion']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Profesion['profesion']?></option>
    <?php
} while ($row_Profesion = mysql_fetch_assoc($Profesion));
  $rows = mysql_num_rows($Profesion);
  if($rows > 0) {
      mysql_data_seek($Profesion, 0);
	  $row_Profesion = mysql_fetch_assoc($Profesion);
  }
?>
    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
</td>
<input name="codigopersonal" type="hidden" value="<?php echo $row_Edit['codigopersonal']; ?>" />
</tr>

</tbody>
</table>
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />
<input type="hidden" name="MM_update" value="Editar" />   

</form>
                  
<?php include("Fragmentos/pie.php"); 
mysql_free_result($Profesion);
?>
<?php
mysql_free_result($Edit);
?>

<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"], minChars:11});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "none", {validateOn:["blur", "change"]});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "phone_number", {validateOn:["blur", "change"], format:"phone_custom", pattern:"000 - 000 000"});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
</script>
