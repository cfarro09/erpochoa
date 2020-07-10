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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Actualizar")) {
  $updateSQL = sprintf("UPDATE acceso SET usuario=%s, clave=md5(%s) WHERE codacceso=%s",
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString($_POST['clave'], "text"),
                       GetSQLValueString($_POST['codacceso'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "personal_access_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Personal = "-1";
if (isset($_GET['codacceso'])) {
  $colname_Personal = $_GET['codacceso'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Personal = sprintf("SELECT a.codacceso, a.usuario, CONCAT(b.paterno,  ' ', b.materno, ' ', b.nombre) as Personal, a.clave, a.nivel FROM acceso a INNER JOIN personal b ON a.codigopersonal = b.codigopersonal WHERE codacceso = %s", GetSQLValueString($colname_Personal, "int"));
$Personal = mysql_query($query_Personal, $Ventas) or die(mysql_error());
$row_Personal = mysql_fetch_assoc($Personal);
$totalRows_Personal = mysql_num_rows($Personal);




$Icono="glyphicon glyphicon-log-in";
$Color="font-blue";
$Titulo="Editar Acceso";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<form action="<?php echo $editFormAction; ?>" method="POST" name="Actualizar" id="Actualizar" >

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
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield2">
  <input name="usuario" type="text" class="form-control tooltips"  data-placement="top" data-original-title="Editar Nombre de Usuario" id="usuario" placeholder="Usuario"  value="<?php echo $row_Personal['usuario']; ?>" maxlength="20" readonly="readonly"/>
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
  <input name="clave"  type="password" class="form-control tooltips"  data-placement="top" data-original-title="Editar Clave de Usuario" id="clave" placeholder="Clave" value="<?php echo $row_Personal['clave']; ?>" maxlength="32" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
<i class="fa fa-user-secret  font-blue-soft"></i>
</span>
</div>
</div>
</div>
</td>

</tr>




</tbody>
</table>

<input name="codacceso" type="hidden" id="codacceso" value="<?php echo $row_Personal['codacceso']; ?>" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_update" value="Actualizar" />
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

</script>
