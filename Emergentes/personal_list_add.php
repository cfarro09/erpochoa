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

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="existe.php";
  $loginUsername = $_POST['cedula'];
  $LoginRS__query = sprintf("SELECT cedula FROM personal WHERE cedula=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_Ventas, $Ventas);
  $LoginRS=mysql_query($LoginRS__query, $Ventas) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Ingresar")) {
  $insertSQL = sprintf("INSERT INTO personal (cedula, nombre, paterno, materno, fecha_nac, direccion, direccionl, celular, codigoprofesion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['cedula'], "text"),
                       GetSQLValueString(strtoupper($_POST['nombre']), "text"),
                       GetSQLValueString(strtoupper($_POST['paterno']), "text"),
                       GetSQLValueString(strtoupper($_POST['materno']), "text"),
                       GetSQLValueString($_POST['fecha_nac'], "date"),
                       GetSQLValueString(strtoupper($_POST['direccion']), "text"),
					   GetSQLValueString(strtoupper($_POST['direccionl']), "text"),	
					   GetSQLValueString($_POST['celular'], "text"),
					   GetSQLValueString(strtoupper($_POST['codigoprofesion']), "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

  $insertGoTo = "personal_list_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_Ventas, $Ventas);
$query_Profesion = "SELECT * FROM profesion";
$Profesion = mysql_query($query_Profesion, $Ventas) or die(mysql_error());
$row_Profesion = mysql_fetch_assoc($Profesion);
$totalRows_Profesion = mysql_num_rows($Profesion);
 
$Icono="glyphicon glyphicon-user";
$Color="font-blue";
$Titulo="Agregar Personal";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form method="POST" action="<?php echo $editFormAction; ?>" name="Ingresar" id="Ingresar">

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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de DNI" placeholder="DNI" id="cedula" name="cedula" maxlength="8" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Apellido Paterno" placeholder="Apellido Paterno" id="paterno" name="paterno" maxlength="50" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Apellidos Materno" placeholder="Apellido Materno" id="materno" name="materno" maxlength="50" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Nombres" placeholder="Nombres" id="nombre" name="nombre" maxlength="100" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Fecha de Nacimiento" id="fecha_nac" name="fecha_nac" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" placeholder="Fecha Nacimiento"/>
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
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield6">
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Direcci&oacute;n" placeholder="Dirección" id="direccion" name="direccion" maxlength="80" />
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
<div class="input-group"><span id="sprytextfield16">
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Direcci&oacute;n Legal" placeholder="Dirección Legal" id="direccionl" name="direccionl" maxlength="80" />
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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de Celular" placeholder="Celular" id="celular" name="celular" maxlength="13" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-phone font-blue-soft"></i>
</span></div>
</div>
</div>
</td>
<td>
<div class="col-md-5">
<div class="form-group"><span id="spryselect1">
  <select name="codigoprofesion" id="codigoprofesion" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Profesi&oacute;n">
    <option value="0">--- Tipo de cargo ---</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Profesion['codigoprofesion']?>"><?php echo $row_Profesion['profesion']?></option><?php
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
                  
<?php include("Fragmentos/pie.php"); 
mysql_free_result($Profesion);
?>

<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"], minChars:8});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "none", {validateOn:["blur", "change"]});
	var sprytextfield16 = new Spry.Widget.ValidationTextField("sprytextfield16", "none", {validateOn:["blur", "change"]});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "none", {validateOn:["blur", "change"], minChars:12});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
</script>
