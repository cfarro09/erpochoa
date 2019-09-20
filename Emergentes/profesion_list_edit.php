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
  $updateSQL = sprintf("UPDATE profesion SET profesion=%s WHERE codigoprofesion=%s",
                       GetSQLValueString(strtoupper($_POST['profesion']), "text"),
                       GetSQLValueString($_POST['codigoprofesion'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "profesion_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Agregar = "-1";
if (isset($_GET['codigoprofesion'])) {
  $colname_Agregar = $_GET['codigoprofesion'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Agregar = sprintf("SELECT * FROM profesion WHERE codigoprofesion = %s", GetSQLValueString($colname_Agregar, "int"));
$Agregar = mysql_query($query_Agregar, $Ventas) or die(mysql_error());
$row_Agregar = mysql_fetch_assoc($Agregar);
$totalRows_Agregar = mysql_num_rows($Agregar);

$Icono="fa fa-graduation-cap";
$Color="font-blue";
$Titulo="Editar Profes&iacute;on";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<table class="table table-hover table-light">

<tbody>
<tr>
<td colspan="2">
  
  <div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield1">
<input name="profesion" type="text" class="form-control tooltips" id="profesion" placeholder="Profes&iacute;on" value="<?php echo $row_Agregar['profesion']; ?>" maxlength="100" data-placement="top" data-original-title="Agregar Nombre de Profesi&oacute;n"  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="fa fa-graduation-cap font-blue-soft"></i>
</span>
</div>
</div>
</div>

</td>
</tr>

</tbody>
</table>
<input name="codigoprofesion" type="hidden" id="codigoprofesion" value="<?php echo $row_Agregar['codigoprofesion']; ?>" />

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
mysql_free_result($Agregar);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
</script>
