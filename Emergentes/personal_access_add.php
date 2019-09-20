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

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="existe.php";
  $loginUsername = $_POST['usuario'];
  $LoginRS__query = sprintf("SELECT usuario FROM acceso WHERE usuario=%s", GetSQLValueString($loginUsername, "text"));
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
  
  $insertSQL = sprintf("INSERT INTO acceso (usuario, clave, nivel, codigopersonal, obs) VALUES (%s, md5(%s), %s, %s, %s)",
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString($_POST['clave'], "text"),
                       GetSQLValueString($_POST['nivel'], "text"),
                       GetSQLValueString($_POST['codigopersonal'], "int"),
                       GetSQLValueString(base64_encode($_POST['clave']), "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

  $insertGoTo = "personal_access_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Ingresar")) {
  $updateSQL = sprintf("UPDATE personal SET asignacion_acceso=%s WHERE codigopersonal=%s",
                       GetSQLValueString($_POST['asignacion_acceso'], "int"),
                       GetSQLValueString($_POST['codigopersonal'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
}

mysql_select_db($database_Ventas, $Ventas);
$query_Personal = "SELECT codigopersonal, CONCAT(paterno,  ' ', materno, ' ', nombre) as Personal FROM personal WHERE estado = '0' AND asignacion_acceso = '0' order by Personal";
$Personal = mysql_query($query_Personal, $Ventas) or die(mysql_error());
$row_Personal = mysql_fetch_assoc($Personal);
$totalRows_Personal = mysql_num_rows($Personal);




$Icono="glyphicon glyphicon-log-in";
$Color="font-blue";
$Titulo="Agregar Acceso";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form method="POST" action="<?php echo $editFormAction; ?>" name="Ingresar" id="Ingresar" >

<table class="table table-hover table-light">

<tbody>
<tr>
<td colspan="2">
  
  <table width="80%" border="0">
  <tr>
<td>

<div class="col-md-5">
<div class="form-group"><span id="spryselect2">
  <select name="codigopersonal" id="codigopersonal" class="form-control tooltips"  data-placement="top" data-original-title="Seleccionar Personal">
    <option value="0">--- Personal ---</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Personal['codigopersonal']?>"><?php echo $row_Personal['Personal']?>  </option>
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
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield2">
  <input name="usuario" type="text" class="form-control tooltips"  data-placement="top" data-original-title="Agregar Nombre Usuario" placeholder="Usuario" id="usuario" maxlength="12" value="" />
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
  <input  type="password" class="form-control tooltips"  data-placement="top" data-original-title="Agregar Clave de Usuario" placeholder="Clave" id="clave" name="clave" maxlength="32" value="" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="fa fa-user-secret  font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>


<tr>
<td> 
<div class="col-md-5">
<div class="form-group tooltips"  data-placement="top" data-original-title="Seleccionar Nivel"><span id="spryselect1">
  <select name="nivel" id="nivel" class="form-control ">
    <option value="0">--- Nivel ---</option>
    <option value="root"> Super Administrador</option>
    <option value="admin"> Administrador</option>
    <option value="user"> Usuario</option>
    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
</td>
<td>

</td>

</tr>

</tbody>
</table>
<input name="asignacion_acceso" type="hidden" value="1" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />
<input type="hidden" name="MM_update" value="Ingresar" />   

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
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
</script>