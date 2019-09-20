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
  $updateSQL = sprintf("UPDATE tipomoviemiento SET nombre_tipomov=%s WHERE codigotipomov=%s",
                       GetSQLValueString(strtoupper($_POST['nombre_tipomov']), "text"),
                       GetSQLValueString($_POST['codigotipomov'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "tipo_movimiento_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Editar = "-1";
if (isset($_GET['codigotipomov'])) {
  $colname_Editar = $_GET['codigotipomov'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Editar = sprintf("SELECT * FROM tipomoviemiento WHERE codigotipomov = %s", GetSQLValueString($colname_Editar, "int"));
$Editar = mysql_query($query_Editar, $Ventas) or die(mysql_error());
$row_Editar = mysql_fetch_assoc($Editar);
$totalRows_Editar = mysql_num_rows($Editar);

$Icono="glyphicon glyphicon-retweet";
$Color="font-blue";
$Titulo="Editar Tipo Movimiento";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<table class="table table-hover table-light">

<tbody>
<tr>
<td>
  
  <div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield1">
<input name="nombre_tipomov" type="text" class="form-control tooltips"  data-placement="top" data-original-title="Editar Nombre de Color" id="nombre_tipomov" placeholder="Tipo de Movimiento" value="<?php echo $row_Editar['nombre_tipomov']; ?>" maxlength="100"  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="glyphicon glyphicon-tint font-blue-soft"></i>
</span>
</div>
</div>
</div>
<input name="codigotipomov" type="hidden" id="codigotipomov" value="<?php echo $row_Editar['codigotipomov']; ?>" />
</td>
</tr>

</tbody>
</table>
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_update" value="Ingresar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Editar);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
</script>
