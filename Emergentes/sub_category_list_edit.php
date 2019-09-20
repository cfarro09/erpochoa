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
  $updateSQL = sprintf("UPDATE subcategoria SET codigocat=%s, nombre=%s WHERE codigosubcat=%s",
                       GetSQLValueString($_POST['codigocat'], "int"),
                       GetSQLValueString(strtoupper($_POST['nombre']), "text"),
                       GetSQLValueString($_POST['codigosubcat'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "sub_category_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_Ventas, $Ventas);
$query_Categoria = "SELECT * FROM categoria WHERE estado = 0";
$Categoria = mysql_query($query_Categoria, $Ventas) or die(mysql_error());
$row_Categoria = mysql_fetch_assoc($Categoria);
$totalRows_Categoria = mysql_num_rows($Categoria);

$colname_Editar = "-1";
if (isset($_GET['codigosubcat'])) {
  $colname_Editar = $_GET['codigosubcat'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Editar = sprintf("SELECT * FROM subcategoria WHERE codigosubcat = %s", GetSQLValueString($colname_Editar, "int"));
$Editar = mysql_query($query_Editar, $Ventas) or die(mysql_error());
$row_Editar = mysql_fetch_assoc($Editar);
$totalRows_Editar = mysql_num_rows($Editar);

$Icono="fa fa-th-list";
$Color="font-blue";
$Titulo="Editar Sub-Categoria";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<table class="table table-hover table-light" width="100%">

<tbody>
<tr>
<td>
  
  <div class="form-group">
  <div class="col-md-10">
  <div class="input-group"><span id="sprytextfield1">
  <input name="nombre" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombre de Sub Categor&iacute;a" id="nombre" placeholder="Sub Categoria" value="<?php echo $row_Editar['nombre']; ?>" maxlength="200"  />
  <span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
  <i class="fa fa-th-list  font-blue-soft"></i>
  </span>
  </div>
  </div>
  </div>
  
</td>
</tr>
<tr>
  <td>
  <div class="col-md-10">
<div class="form-group"><span id="spryselect1">
  <select name="codigocat" id="codigocat" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Categor&iacute;a">
    <option value="" <?php if (!(strcmp("", $row_Editar['codigocat']))) {echo "selected=\"selected\"";} ?>>-- Categoria --</option>
    <?php
do {  
?>
    <option value="<?php echo $row_Categoria['codigocat']?>"<?php if (!(strcmp($row_Categoria['codigocat'], $row_Editar['codigocat']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Categoria['nombre']?></option>
    <?php
} while ($row_Categoria = mysql_fetch_assoc($Categoria));
  $rows = mysql_num_rows($Categoria);
  if($rows > 0) {
      mysql_data_seek($Categoria, 0);
	  $row_Categoria = mysql_fetch_assoc($Categoria);
  }
?>
    
    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
  
  </td>
  </tr>

</tbody>
</table>
<input name="codigosubcat" type="hidden" id="codigosubcat" value="<?php echo $row_Editar['codigosubcat']; ?>" />
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
mysql_free_result($Categoria);

mysql_free_result($Editar);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
</script>
