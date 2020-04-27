<?php require_once('../Connections/Ventas.php'); ?>
<?php
//MX Widgets3 include
require_once('../includes/wdg/WDG.php');

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
  $updateSQL = sprintf("UPDATE servicios_add SET nombre_servicio=%s, descripcion=%s WHERE codigoserv=%s",
                       GetSQLValueString(strtoupper($_POST['nombre_servicio']), "text"),
                       GetSQLValueString($_POST['detalle_producto'], "text"),
					   GetSQLValueString($_POST['codigoserv'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "servicios_add_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


$colname_Producto = "-1";
if (isset($_GET['codigoserv'])) {
  $colname_Producto = $_GET['codigoserv'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Producto = "SELECT * FROM servicios_add WHERE codigoserv = '$colname_Producto'";
$Producto = mysql_query($query_Producto, $Ventas) or die(mysql_error());
$row_Producto = mysql_fetch_assoc($Producto);
$totalRows_Producto = mysql_num_rows($Producto);

$Icono="fa fa-cubes";
$Color="font-blue";
$Titulo="Editar Servicio - ".$row_Producto['codigoserv'];
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script><html xmlns:wdg="http://ns.adobe.com/addt">
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/common/js/sigslot_core.js"></script>
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/wdg/classes/MXWidgets.js"></script>
<script type="text/javascript" src="../includes/wdg/classes/MXWidgets.js.php"></script>
<script type="text/javascript" src="../includes/wdg/classes/JSRecordset.js"></script>
<script type="text/javascript" src="../includes/wdg/classes/DependentDropdown.js"></script>

<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<div class="form-group">
  <div class="col-md-10">
  <div class="input-group"><span id="sprytextfield1">
  <input name="nombre_servicio" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombre Servicio" id="nombre_servicio" placeholder="Nombre Servicio" value="<?php echo $row_Producto['nombre_servicio']; ?>" maxlength="400"  />
  <span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> <i class="fa fa-cubes font-blue-soft"></i> </span> </div>
  </div>
  </div>

  <table width=100% border="0" class="table table-hover table-light>
  
    <tr>
            <td colspan="3"><span id="sprytextarea1">
              <textarea name="detalle_producto" id="detalle_producto" rows="3" class="form-control tooltips" data-placement="top" data-original-title="Descripci&oacute;n y/o Caracter&iacute;stica del Producto" placeholder=" descripci&oacute;n y/o caracter&iacute;stica del producto"><?php echo $row_Producto['descripcion'];?></textarea>
              <span class="textareaRequiredMsg"></span></span></td>
          </tr>
  <input name="codigoserv" type="hidden" id="codigoserv" value="<?php echo $row_Producto['codigoserv']; ?>">  
</table>

  <?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
  <input type="hidden" name="MM_update" value="Ingresar">
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>

<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"0", validateOn:["blur", "change"]});
</script>
