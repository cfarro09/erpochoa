<?php require_once('../Connections/Ventas.php'); ?>
<script language="javascript">
	function unafuncion6()
	{
		var pcsiniva = Ingresar.precio_compra.value;
		
		iva=(parseFloat(pcsiniva)*$IGV1).toFixed(2);
		precioc=(parseFloat(pcsiniva)+parseFloat(iva)).toFixed(2);
	
		document.getElementsByName("test")[0].value = iva;
		document.getElementsByName("test1")[0].value = precioc;
	}
	
</script>
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
$ivaarticulo=NULL;
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Ingresar")) {
	$fecha_actual = date("Y-m-d");
	$codigonum=$_POST['codigoproveedor'].'-'.$_POST['comprobante'].'-'.$_POST['numero'];
  $updateSQL = sprintf("INSERT INTO historial_producto (codigoprod, precio_compra, precio_venta, cantidad, codigoproveedor, detalle_producto,fecha,comprobante,numero,preciototalc, ivaart_ind, pcomprasiniva, codigosuc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString(strtoupper($_POST['codigoprod']), "int"),
                       GetSQLValueString($_POST['test1'], "float"),
                       GetSQLValueString($_POST['precio_venta'], "float"),
                       GetSQLValueString($_POST['cantidad'], "int"),
                       GetSQLValueString($_POST['codigoproveedor'], "int"),
                       GetSQLValueString($_POST['detalle_producto'], "text"),
					   GetSQLValueString($fecha_actual, "text"),
					   GetSQLValueString($_POST['comprobante'], "text"),
					   GetSQLValueString($codigonum, "text"),
					   GetSQLValueString($_POST['test1']*$_POST['cantidad'], "float"),
					   GetSQLValueString($_POST['test'], "float"),
					   GetSQLValueString($_POST['precio_compra'], "float"),
					   GetSQLValueString($_POST['codigosuc'], "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

//captura el ultimo elemento de la bd
$colname_Producto = $_GET['codigoprod'];
$precio_compra=number_format(($_POST['precio_compra']+($_POST['precio_compra']*$IGV1)),2);
$precio_venta=$_POST['precio_venta'];
$codigosucursal=$_POST['codigosuc'];
	if($codigosucursal==1){
			$query_Stock = mysql_query("select stock from producto_stock where codigoprod=$colname_Producto");

			$id=0;
			if ($row = mysql_fetch_row($query_Stock)) {
				$id = trim($row[0]);
			}
			$cant=$_POST['cantidad'];
			$id=$id+$cant;
	

			$updateSQL2 = sprintf("update producto_stock set stock=$id, precio_compra=$precio_compra, precio_venta=$precio_venta where codigoprod=$colname_Producto");
	}
	
	
	
	
	
	
	if($codigosucursal==2){
			$query_Stock = mysql_query("select stock2 from producto_stock where codigoprod=$colname_Producto");

			$id=0;
			if ($row = mysql_fetch_row($query_Stock)) {
				$id = trim($row[0]);
			}
			$cant=$_POST['cantidad'];
			$id=$id+$cant;
	//$detalle='compra1';
//$updateSQL2 = sprintf("INSERT INTO producto_stock (codigoprod, detalle, stock) VALUES ($colname_Producto, 'compra', $id)");


			$updateSQL2 = sprintf("update producto_stock set stock2=$id, precio_compra=$precio_compra, precio_venta=$precio_venta where codigoprod=$colname_Producto");
	}





	if($codigosucursal==3){
			$query_Stock = mysql_query("select stock3 from producto_stock where codigoprod=$colname_Producto");

			$id=0;
			if ($row = mysql_fetch_row($query_Stock)) {
				$id = trim($row[0]);
			}
			$cant=$_POST['cantidad'];
			$id=$id+$cant;
	//$detalle='compra1';
//$updateSQL2 = sprintf("INSERT INTO producto_stock (codigoprod, detalle, stock) VALUES ($colname_Producto, 'compra', $id)");


			$updateSQL2 = sprintf("update producto_stock set stock3=$id, precio_compra=$precio_compra, precio_venta=$precio_venta where codigoprod=$colname_Producto");
	}
	

  mysql_select_db($database_Ventas, $Ventas);



$Result2 = mysql_query($updateSQL2, $Ventas) or die(mysql_error());


  $updateGoTo = "product_list_cantidad.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}







mysql_select_db($database_Ventas, $Ventas);
$query_Categorias = "SELECT * FROM categoria WHERE estado = 0";
$Categorias = mysql_query($query_Categorias, $Ventas) or die(mysql_error());
$row_Categorias = mysql_fetch_assoc($Categorias);
$totalRows_Categorias = mysql_num_rows($Categorias);

mysql_select_db($database_Ventas, $Ventas);
$query_SubCategorias = "SELECT * FROM subcategoria WHERE estado = 0";
$SubCategorias = mysql_query($query_SubCategorias, $Ventas) or die(mysql_error());
$row_SubCategorias = mysql_fetch_assoc($SubCategorias);
$totalRows_SubCategorias = mysql_num_rows($SubCategorias);

mysql_select_db($database_Ventas, $Ventas);
$query_Proveedor = "SELECT * FROM proveedor WHERE estado = 0";
$Proveedor = mysql_query($query_Proveedor, $Ventas) or die(mysql_error());
$row_Proveedor = mysql_fetch_assoc($Proveedor);
$totalRows_Proveedor = mysql_num_rows($Proveedor);






mysql_select_db($database_Ventas, $Ventas);
$query_Sucursal = "SELECT * FROM sucursal";
$Sucursal = mysql_query($query_Sucursal, $Ventas) or die(mysql_error());
$row_Sucursal = mysql_fetch_assoc($Sucursal);
$totalRows_Sucursal = mysql_num_rows($Sucursal);





mysql_select_db($database_Ventas, $Ventas);
$query_Marca = "SELECT * FROM marca WHERE estado = 0";
$Marca = mysql_query($query_Marca, $Ventas) or die(mysql_error());
$row_Marca = mysql_fetch_assoc($Marca);
$totalRows_Marca = mysql_num_rows($Marca);

mysql_select_db($database_Ventas, $Ventas);
$query_Presentacion = "SELECT * FROM presentacion WHERE estado = 0";
$Presentacion = mysql_query($query_Presentacion, $Ventas) or die(mysql_error());
$row_Presentacion = mysql_fetch_assoc($Presentacion);
$totalRows_Presentacion = mysql_num_rows($Presentacion);

mysql_select_db($database_Ventas, $Ventas);
$query_Colores = "SELECT * FROM color WHERE estado = 0";
$Colores = mysql_query($query_Colores, $Ventas) or die(mysql_error());
$row_Colores = mysql_fetch_assoc($Colores);
$totalRows_Colores = mysql_num_rows($Colores);

$colname_Producto = "-1";
if (isset($_GET['codigoprod'])) {
  $colname_Producto = $_GET['codigoprod'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Producto = sprintf("SELECT * FROM producto WHERE codigoprod = %s", GetSQLValueString($colname_Producto, "int"));
$Producto = mysql_query($query_Producto, $Ventas) or die(mysql_error());
$row_Producto = mysql_fetch_assoc($Producto);
$totalRows_Producto = mysql_num_rows($Producto);

$Icono="fa fa-cubes";
$Color="font-blue";
$Titulo="Editar Producto - ".$row_Producto['codigoprod'];
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script><html xmlns:wdg="http://ns.adobe.com/addt">
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>


<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<div class="form-group">
  <div class="col-md-10">
  <div class="input-group"><span id="sprytextfield1">
  <input name="nombre_producto" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Nombre Producto" id="nombre_producto" placeholder="Nombre Producto" value="<?php echo $row_Producto['nombre_producto']; ?>" maxlength="200" readonly  />
  <span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> <i class="fa fa-cubes font-blue-soft"></i> </span> </div>
  </div>
  </div>
<table width=100% border="0" class="table table-hover table-light">
    <tr>
    <td>
    <div class="form-group">
<div class="col-md-4">
<div class="input-group"><span id="sprytextfield2">
<input name="precio_compra" id="precio_compra" type="text" class="form-control tooltips" data-placement="top" data-original-title="Precio de Compra sin IVA"  placeholder="Precio Compra" value="" maxlength="8" onKeyDown="unafuncion6()" onBlur="unafuncion6()" /><span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> 
<i class="fa fa-dollar font-blue-soft"></i> </span> </div>
</div>
</div>
</td>
    <td>
    <div class="form-group">
<div class="col-md-4">
<div class="input-group"><span id="sprytextfield3">
<input name="precio_venta" id="precio_venta" type="text" class="form-control tooltips" data-placement="top" data-original-title="Editar Precio de Venta"  placeholder="Precio Venta" value="" maxlength="8"  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> 
<i class="fa fa-dollar font-blue-soft"></i> </span> </div>
</div>
</div>
    </td>
  </tr>
  






    <tr>
    <td>
 
    
    
    
    
    
    <div class="form-group">
<div class="col-md-4">
<div class="input-group"><span id="sprytextfield2">
<input name="test" id="test" type="text" class="form-control tooltips" data-placement="top" data-original-title="Iva"  placeholder="Iva compra" value="" maxlength="8" readonly /><span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> 
<i class="fa fa-dollar font-blue-soft"></i> </span> </div>
</div>
</div>
</td>
    <td>
    <div class="form-group">
<div class="col-md-4">
<div class="input-group"><span id="sprytextfield3">
<input name="test1" id="test1" type="text" class="form-control tooltips" data-placement="top" data-original-title="Precio de Compra Unidad Incluye IVA"  placeholder="Precio Compra incluye IVA" value="" readonly  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> 
<i class="fa fa-dollar font-blue-soft"></i> </span> </div>
</div>
</div>
    </td>
  </tr>










  <tr>
    <td>
    
<div class="form-group">
<input name="cantidad" id="cantidad" type="text" class="form-control tooltips" data-placement="top" data-original-title="insertar cantidad de Compra"  placeholder="Cantidad Producto" value="" maxlength="5"  /><span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    <td>
    <div class="col-md-8">
<div class="form-group"><span id="spryselect1">
<select name="codigosuc" id="codigosuc" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Proveedor">
  <option value="">-- Sucursal --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Sucursal['codigosuc']?>"><?php echo $row_Sucursal['nombresuc']?></option>
  <?php
} while ($row_Sucursal = mysql_fetch_assoc($Sucursal));
  $rows = mysql_num_rows($Sucursal);
  if($rows > 0) {
      mysql_data_seek($Sucursal, 0);
	  $row_Sucursal = mysql_fetch_assoc($Sucursal);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
  </tr>
  
  
  <table width=100% border="0" class="table table-hover table-light">
  <tr>
  <td><div class="col-md-8">
<div class="form-group"><span id="spryselect1">
<select name="codigoproveedor" id="codigoproveedor" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Proveedor">
  <option value="">-- Proveedor --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Proveedor['codigoproveedor']?>"><?php echo $row_Proveedor['razonsocial']?></option>
  <?php
} while ($row_Proveedor = mysql_fetch_assoc($Proveedor));
  $rows = mysql_num_rows($Proveedor);
  if($rows > 0) {
      mysql_data_seek($Proveedor, 0);
	  $row_Proveedor = mysql_fetch_assoc($Proveedor);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
</td>
  <td>
    <div class="col-md-8">
<div class="form-group"><span id="spryselect2">
<select name="comprobante" id="comprobante" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Comprobante">
  <option value="0">-- Comprobante</option>
    <option value="factura">Factura</option>
  <option value="boleta">Boleta</option>
  <option value="ticket">Ticket</option>
  <option value="otros">Otros</option>
  
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    <td>
    <div class="col-md-8">
<div class="form-group"><span id="sprytextfield5">
<input name="numero" id="numero" type="text" class="form-control tooltips" data-placement="top" data-original-title="insertar n&uacute;mero de Factura"  placeholder="Numero Factura" value="" maxlength="20"  /><span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
  </tr>
  </table>
  
  <tr>
    <td colspan="2"><table width="100%" border="0">
      <tr>
        <td width=33%>
        <div class="col-md-10">
<div class="form-group"><span id="spryselect3">

<select name="codigomarca" id="codigomarca" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Marca" disabled>
  <option value="" <?php if (!(strcmp("", $row_Producto['codigomarca']))) {echo "selected=\"selected\"";} ?>>-- Marca --</option>
  <?php
do {  
?>
<option value="<?php echo $row_Marca['codigomarca']?>"<?php if (!(strcmp($row_Marca['codigomarca'], $row_Producto['codigomarca']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Marca['nombre']?></option>
  <?php
} while ($row_Marca = mysql_fetch_assoc($Marca));
  $rows = mysql_num_rows($Marca);
  if($rows > 0) {
      mysql_data_seek($Marca, 0);
	  $row_Marca = mysql_fetch_assoc($Marca);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
        
        
        </td>
        <td width=33%>
        <div class="col-md-10">
<div class="form-group"><span id="spryselect4">
<select name="codigopresent" id="codigopresent" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Presentaci&oacute;n" disabled>
  <option value="" <?php if (!(strcmp("", $row_Producto['codigopresent']))) {echo "selected=\"selected\"";} ?>>-- Presentaci&oacute;n --</option>
  <?php
do {  
?>
<option value="<?php echo $row_Presentacion['codigopresent']?>"<?php if (!(strcmp($row_Presentacion['codigopresent'], $row_Producto['codigopresent']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Presentacion['nombre_presentacion']?></option>
  <?php
} while ($row_Presentacion = mysql_fetch_assoc($Presentacion));
  $rows = mysql_num_rows($Presentacion);
  if($rows > 0) {
      mysql_data_seek($Presentacion, 0);
	  $row_Presentacion = mysql_fetch_assoc($Presentacion);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
        
        
        </td>
        <td width=33%>
        <div class="col-md-10">
<div class="form-group"><span id="spryselect5">
<select name="codigocolor" id="codigocolor" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Color" disabled>
  <option value="" <?php if (!(strcmp("", $row_Producto['codigocolor']))) {echo "selected=\"selected\"";} ?>>-- Color --</option>
  <?php
do {  
?>
<option value="<?php echo $row_Colores['codigocolor']?>"<?php if (!(strcmp($row_Colores['codigocolor'], $row_Producto['codigocolor']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Colores['nombre_color']?></option>
  <?php
} while ($row_Colores = mysql_fetch_assoc($Colores));
  $rows = mysql_num_rows($Colores);
  if($rows > 0) {
      mysql_data_seek($Colores, 0);
	  $row_Colores = mysql_fetch_assoc($Colores);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
        
        </td>
      </tr>
    </table>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <span id="sprytextarea1">
  <textarea name="detalle_producto" id="detalle_producto" rows="3" class="form-control tooltips" data-placement="top" data-original-title="Editar Otra Descripci&oacute;n y/o Caracter&iacute;stica del Producto" placeholder="Otra descripci&oacute;n y/o caracter&iacute;stica del producto"></textarea>
  <span class="textareaRequiredMsg"></span></span>
    
    </td>
  </tr>
    
  <input name="codigoprod" type="hidden" id="codigoprod" value="<?php echo $row_Producto['codigoprod']; ?>">  
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
<?php
mysql_free_result($Categorias);

mysql_free_result($SubCategorias);

mysql_free_result($Marca);

mysql_free_result($Presentacion);

mysql_free_result($Colores);

mysql_free_result($Producto);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});
</script>
