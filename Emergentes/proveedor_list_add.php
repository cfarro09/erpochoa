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
  $loginUsername = $_POST['ruc'];
  $LoginRS__query = sprintf("SELECT ruc FROM proveedor WHERE ruc=%s", GetSQLValueString($loginUsername, "text"));
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
  $insertSQL = sprintf("INSERT INTO proveedor (ruc, razonsocial, direccion, fax, contacto, email, pais, ciudad, telefono, celular, paginaweb) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString(strtoupper($_POST['razonsocial']), "text"),
                       GetSQLValueString(strtoupper($_POST['direccion']), "text"),
                       GetSQLValueString($_POST['fax'], "text"),
                       GetSQLValueString(strtoupper($_POST['contacto']), "text"),
                       GetSQLValueString(strtolower($_POST['email']), "text"),
					   GetSQLValueString(strtoupper($_POST['pais']), "text"),
					   GetSQLValueString(strtoupper($_POST['ciudad']), "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString(strtolower($_POST['paginaweb']), "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

  $insertGoTo = "proveedor_list_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$Icono="fa fa-magic";
$Color="font-blue";
$Titulo="Agregar Proveedor";
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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de R.U.C" placeholder="R.U.C" id="ruc" name="ruc" maxlength="11" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Raz&oacute;n Social" placeholder="Razón Social" id="razonsocial" name="razonsocial" maxlength="100" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Nombre Contacto" placeholder="Contacto" id="contacto" name="contacto" maxlength="50" />
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
    <option value="0">--- Pais ---</option>
    <option value="ar"> Argentina</option>
    <option value="bo"> Bolivia</option>
    <option value="br"> Brazil</option>
    <option value="ch"> Chile</option>
    <option value="co"> Colombia</option>
    <option value="ec"> Ecuador</option>
        <option value="pe" selected> Perú</option>
    <option value="ve"> Venezuela</option>
    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
  
  
  </td>
  <td>
  <div class="form-group">
<div class="col-md-6">
<div class="input-group"><span id="sprytextfield10">
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Ciudad" placeholder="Ciudad" id="ciudad" name="ciudad" maxlength="30" />
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
  <input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Direcci&oacute;n" placeholder="Dirección" id="direccion" name="direccion" maxlength="60" />
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
<input type="email" class="form-control tooltips" data-placement="top" data-original-title="Agregar Correo Electr&oacute;nico" placeholder="Correo Electrónico" id="email" name="email" maxlength="100" />
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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de Fax" placeholder="Fax" id="fax" name="fax" maxlength="10" />
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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero Telef&oacute;nico" placeholder="Teléfono" id="telefono" name="telefono" maxlength="10" />
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
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield8">
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de Celular" placeholder="Celular" id="celular" name="celular" maxlength="13" />
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
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar P&aacute;gina Web" placeholder="Página Web - http://www.demo.com" id="paginaweb" name="paginaweb" maxlength="100" />
<span class="textfieldRequiredMsg"></span><span class="textfieldInvalidFormatMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-globe font-blue-soft"></i>
</span>
</div>
</div>
</div>
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