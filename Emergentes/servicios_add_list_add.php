<?php require_once('../Connections/Ventas.php'); ?>
<?php
//MX Widgets3 include


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
  $insertSQL = sprintf("INSERT INTO servicios_add (nombre_servicio, estado, descripcion) VALUES (%s, %s, %s)",
                       GetSQLValueString(strtoupper($_POST['nombre_servicio']), "text"),
                        GetSQLValueString(0, "text"),
					   GetSQLValueString($_POST['observacion'], "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());
  
  
 // $insertSQL2 = sprintf("INSERT INTO historial_producto(codigoprod, precio_compra, precio_venta, detalle_producto, cantidad, codigoproveedor,codigosuc) VALUES ($id, 0, 0, '.', 0, $id1,0)");

 // mysql_select_db($database_Ventas, $Ventas);
//  $Result2 = mysql_query($insertSQL2, $Ventas) or die(mysql_error());
 
 //tercer insert
   	
  //$insertSQL3 = sprintf("INSERT INTO producto_stock(codigoprod, stock, productosucursal) VALUES ($id, 0,1)");
  //mysql_select_db($database_Ventas, $Ventas);
  //$Result3 = mysql_query($insertSQL3, $Ventas) or die(mysql_error());


  
//$insertSQL5 = sprintf("INSERT INTO producto_stock(codigoprod, stock, productosucursal) VALUES ($id, 0,3)");
 // mysql_select_db($database_Ventas, $Ventas);
 // $Result5 = mysql_query($insertSQL5, $Ventas) or die(mysql_error());
  
  $insertGoTo = "servicios_add_list_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


$Icono="fa fa-cubes";
$Color="font-blue";
$Titulo="Agregar Insumo";
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
<input name="nombre_servicio" type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Nombre Servicio" id="nombre_servicio" placeholder="Nombre Servicio" maxlength="200"  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> <i class="fa fa-cubes font-blue-soft"></i> </span> </div>
</div>
</div>
<table width=100% border="0" class="table table-hover table-light">
          <tr>
            <td colspan="3"><span id="sprytextarea1">
              <textarea name="observacion" id="observacion" rows="3" class="form-control tooltips" data-placement="top" data-original-title="Descripci&oacute;n del pago" placeholder=" descripci&oacute;n del pago"></textarea>
              <span class="textareaRequiredMsg"></span></span></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
    </tr>

</tr>
    
    
</table>

  <?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
  <input type="hidden" name="MM_insert" value="Ingresar">
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
//var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});
</script>
