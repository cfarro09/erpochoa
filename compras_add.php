<?php $total=0; $totalv=0; $numeroc=NULL; $validarc=1; $validarv=1;$totalv1=0;?>
<?php require_once('Connections/Ventas.php'); ?>
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
    case "float":
      $theValue = ($theValue != "") ? floatval($theValue) : "NULL";
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

if ((isset($_POST["MM_eliminar"])) && ($_POST["MM_eliminar"] == "EliminarProducto")) {	
  $deleteSQL = sprintf("DELETE FROM detalle_compras WHERE  codigodetalleproducto=%s",
                       GetSQLValueString($_POST['codigodetalleproducto'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($deleteSQL, $Ventas) or die(mysql_error());
}



//eliminar venta
if ((isset($_POST["MM_EliminarVenta"])) && ($_POST["MM_EliminarVenta"] == "EliminarVenta")) {	
  $deleteSQL = sprintf("DELETE FROM detalle_compras WHERE codigo=%s",
                       GetSQLValueString($_POST['codigo'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($deleteSQL, $Ventas) or die(mysql_error());
}

//grabar factura venta
if ((isset($_POST["MM_GuardarVenta"])) && ($_POST["MM_GuardarVenta"] == "GuardarVenta")) {	
  
if($_POST['codigoproveedor']==NULL || $_POST['numcomprobante']==NULL || $_POST['codigosuc']==NULL || $_POST['validarc']<=0 || $_POST['validarv']<=0){
    ?>    <script type="text/javascript">
           alert("INGRESE CODIGO DE PROVEEDOR, NUMERO DE FACTURA Y SUCURSAL, O PRECIO DE COMPRA O VENTA ESTA EN CERO");
       </script> 
    <?php 
    }
else {

     $codigocomp=$_POST['comprobante'].$_POST['codigo'];
	$updateSQL = sprintf("UPDATE detalle_compras SET codcomprobante=%s where codigo=%s",
                          GetSQLValueString($codigocomp, "text"),
						  GetSQLValueString($_POST['codigo'], "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  
 $insertSQL = sprintf("insert into compras values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString(NULL, "text"),
						  GetSQLValueString($_POST['codigo'], "text"),
						  GetSQLValueString($codigocomp, "text"),
						  GetSQLValueString($_POST['comprobante'], "text"),
						  GetSQLValueString($_POST['codigobanco'], "int"),
						  GetSQLValueString($_POST['numerotarjeta'], "text"),
						  GetSQLValueString($_POST['codigo'], "text"),
						  GetSQLValueString($_POST['tipopago'], "text"),
						  GetSQLValueString($_POST['codigoproveedor'], "int"),
						  GetSQLValueString($_POST['numcomprobante'], "int"),
						  GetSQLValueString($_POST['fecha_emision'], "date"),
						  GetSQLValueString($_POST['hora_emision'], "date"),
						  GetSQLValueString($_POST['codacceso'], "int"),
						  GetSQLValueString($_POST['codigopersonal'], "int"),
              GetSQLValueString(round($_POST['montopagar']/1.12,2), "double"),
              GetSQLValueString(round($_POST['montopagar']-$_POST['montopagar']/1.12,2), "double"),
						  GetSQLValueString($_POST['montopagar'], "double"),
              GetSQLValueString(1, "int"),
              GetSQLValueString($_POST['totalv1'], "double"),
              GetSQLValueString($_POST['codigosuc'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());
  
$updateSQL = sprintf("UPDATE producto_stock INNER JOIN detalle_compras ON producto_stock.codigoprod = detalle_compras.codigoprod SET producto_stock.stock = producto_stock.stock+detalle_compras.cantidad, producto_stock.precio_compra = detalle_compras.totalcompras, producto_stock.precio_venta = detalle_compras.pventa where detalle_compras.codcomprobante=%s",
                          GetSQLValueString($codigocomp, "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
?><script type="text/javascript">
window.location="product_list.php";
</script>

<?php } 
}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "IngresarProducto")) {


mysql_select_db($database_Ventas, $Ventas);
$codigoproducto10=$_POST['codigoprod'];
$query_Productos = "SELECT codigoprod, precio_venta, round(precio_compra/1.12,2) as precio_compra FROM producto_stock WHERE codigoprod = $codigoproducto10 ORDER BY codigoprod desc";
$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
$row_Productos = mysql_fetch_assoc($Productos);
$totalRows_Productos = mysql_num_rows($Productos);

	$concatenacion=$_POST['CodigoProducto'].$_POST['codigoprod'];
	 
	//mysql_select_db($database_Ventas, $Ventas);
$query_Contador_Clientes = "SELECT count(*) AS Contador FROM detalle_compras where concatenacion='$concatenacion'";
$Contador_Clientes = mysql_query($query_Contador_Clientes, $Ventas) or die(mysql_error());
$row_Contador_Clientes = mysql_fetch_assoc($Contador_Clientes);
$totalRows_Contador_Clientes = mysql_num_rows($Contador_Clientes);
	if($row_Contador_Clientes['Contador']==0)
	{
	
	$concatenacion=$_POST['CodigoProducto'].$_POST['codigoprod'];
	 $pventa=$row_Productos['precio_venta'];
	 
  $insertSQL = sprintf("INSERT INTO detalle_compras (codigo, codigoprod, concatenacion, pventa, pcompra, igv, totalcompras) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['CodigoProducto'], "text"),
                       GetSQLValueString($_POST['codigoprod'], "int"),
					   GetSQLValueString($concatenacion,"text"),
             GetSQLValueString($row_Productos['precio_venta'], "double"),
             GetSQLValueString($row_Productos['precio_compra'], "double"),
           GetSQLValueString(round($row_Productos['precio_compra']*0.18,2), "double"),
         GetSQLValueString(round($row_Productos['precio_compra']+$row_Productos['precio_compra']*0.18,2), "double"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());
	}
	else
	{
		echo "<script language='JavaScript'>alert('Grabacion Correcta');</script>";  
	}

  $insertGoTo = "compras_add.php?codigo=" . $_GET['codigo'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

//actualiza stock
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Cantidad")) {
  $updateSQL = sprintf("UPDATE detalle_compras SET cantidad=%s WHERE codigodetalleproducto=%s",
                       GetSQLValueString($_POST['cantidad'], "int"),
                       GetSQLValueString($_POST['codigodetalleproducto'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
}
//actualiza precio
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Precio_compra")) {
	
  $updateSQL = sprintf("UPDATE detalle_compras SET pcompra=%s, igv=%s, totalcompras=%s WHERE codigodetalleproducto=%s",
                       GetSQLValueString($_POST['pcompra'], "double"),
                       GetSQLValueString(round($_POST['pcompra']*0.18,2), "double"),
                       GetSQLValueString(($_POST['pcompra']*0.18)+$_POST['pcompra'], "double"),
                       GetSQLValueString($_POST['codigodetalleproducto'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Precio_venta")) {
  
  $updateSQL = sprintf("UPDATE detalle_compras SET pventa=%s WHERE codigodetalleproducto=%s",
                       GetSQLValueString($_POST['pventa'], "double"),
                       GetSQLValueString($_POST['codigodetalleproducto'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
}
mysql_select_db($database_Ventas, $Ventas);
$query_Productos = "SELECT * FROM vt_producto_compra";

$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
$row_Productos = mysql_fetch_assoc($Productos);
$totalRows_Productos = mysql_num_rows($Productos);

$colname_Detalle_Compras = "-1";
if (isset($_GET['codigo'])) {
  $colname_Detalle_Compras = $_GET['codigo'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Detalle_Compras = sprintf("SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, a.cantidad,b.nombre_producto AS Producto,c.nombre AS Marca, ps.precio_venta, e.nombre_color, sum(a.cantidad*ps.precio_compra) as Importe,ps.stock, a.pcompra, a.pventa FROM detalle_compras a  INNER JOIN producto b ON a.codigoprod =b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN color e ON b.codigocolor = e.codigocolor INNER JOIN producto_stock ps ON ps.codigoprod=b.codigoprod WHERE a.codigo = %s GROUP BY a.codigoprod desc ", GetSQLValueString($colname_Detalle_Compras, "text"));
$Detalle_Compras = mysql_query($query_Detalle_Compras, $Ventas) or die(mysql_error());
$row_Detalle_Compras = mysql_fetch_assoc($Detalle_Compras);
$totalRows_Detalle_Compras = mysql_num_rows($Detalle_Compras);
$validastock=$row_Detalle_Compras['cantidad'];

mysql_select_db($database_Ventas, $Ventas);
$query_Clientes = "SELECT codigoproveedor as codigoclienten, CONCAT(razonsocial, ' ', ruc) as ClienteNatural FROM proveedor  WHERE estado = 0 order by razonsocial";
$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
$row_Clientes = mysql_fetch_assoc($Clientes);
$totalRows_Clientes = mysql_num_rows($Clientes);

mysql_select_db($database_Ventas, $Ventas);
$query_Bancos = "SELECT * FROM banco WHERE estado = 0";
$Bancos = mysql_query($query_Bancos, $Ventas) or die(mysql_error());
$row_Bancos = mysql_fetch_assoc($Bancos);
$totalRows_Bancos = mysql_num_rows($Bancos);

mysql_select_db($database_Ventas, $Ventas);
$query_Sucursal = "SELECT * FROM sucursal";
$Sucursal = mysql_query($query_Sucursal, $Ventas) or die(mysql_error());
$row_Sucursal = mysql_fetch_assoc($Sucursal);
$totalRows_Sucursal = mysql_num_rows($Sucursal);

//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-shopping-cart";
$Color="font-blue";
$Titulo="Generar Compras";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="disabled";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
?>


<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<table width="100%" border="0">
<tr>
<td width="65%"  valign="top">
<table width="100%" border="0">
<tr>
<td>




<form action="<?php echo $editFormAction; ?>" method="POST" name="IngresarProducto">
<div class="form-group has-success">
<div class="input-group select2-bootstrap-append">
<span id="spryselect1">

<select id="single-append-text" class="form-control select2-allow-clear" name="codigoprod">
  <option value="" <?php if (!(strcmp("", "compras_add.php"))) {echo "selected=\"selected\"";} ?>></option>
  <?php
do {  
?>
  <option value="<?php echo $row_Productos['codigoprod']?>"<?php if (!(strcmp($row_Productos['codigoprod'], "compras_add.php"))) {echo "selected=\"selected\"";} ?>><?php echo $row_Productos['nombre_producto']?> - <?php echo $row_Productos['Marca']; ?> - <?php echo $row_Productos['nombre_color']; ?> - <?php echo "$/.". $row_Productos['precio_venta']; ?> - <?php echo $row_Productos['minicodigo']; ?> - (<?php echo "Stock ".$row_Productos['stock']; ?>)</option>
  <?php
} while ($row_Productos = mysql_fetch_assoc($Productos));
  $rows = mysql_num_rows($Productos);
  if($rows > 0) {
      mysql_data_seek($Productos, 0);
	  $row_Productos = mysql_fetch_assoc($Productos);
  }
?>
</select>
<span class="selectRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn btn-default" type="submit">
<span class="glyphicon glyphicon-shopping-cart "></span>
</button>
<input name="CodigoProducto" id="CodigoProducto" type="hidden" value="<?php echo $_GET['codigo']; ?>" />
<input type="hidden" name="MM_insert" value="IngresarProducto" />
</form>







<?php 
 //Enumerar filas de data tablas
 $i = 1;?>
</span>
</div>
</div>
</td>
</tr>
<tr>
<td valign="top"><?php if ($totalRows_Detalle_Compras == 0) { // Show if recordset empty ?>
    <div class="alert alert-danger"> <strong>AUN NO SE HA INGRESADO NINGUN PRODUCTO A LA VENTA...!</strong> </div>
    <?php } // Show if recordset empty ?>
  <?php if ($totalRows_Detalle_Compras > 0) { // Show if recordset not empty ?>
    <table class="table" border="0" >
      <thead>
        <tr>
          <th width="5%"> # </th>
          <th width="7%"> Cant </th>
          <th width="30%"> Producto </th>
          <th width="10%"> Marca </th>
          <th width="10%" >Precio Compra S/IGV</th>
          <th width="10%" >Precio Venta C/IGV</th>
          <th width="10%"> Importe </th>
          <th width="5%"> </th>
        </tr>
      </thead>
      <tbody>
 <tbody>
        <?php do { ?>
          <tr>
            <td valign="middle"><?php echo $i; ?></td>
            <td valign="top">
            <form action="<?php echo $editFormAction; ?>" method="POST" name="Cantidad" id="Cantidad">
                <input name="cantidad" type="text" class="form-control tooltips input-sm"  id="nrecibo" value="<?php echo $row_Detalle_Compras['cantidad']; ?>"  maxlength="3" data-placement="top" data-original-title="<?php echo $row_Detalle_Compras['stock']; ?>"/>
                <input name="codigodetalleproducto" type="hidden" id="codigodetalleproducto" value="<?php echo $row_Detalle_Compras['codigodetalleproducto']; ?>" />
                <input type="hidden" name="MM_update" value="Cantidad" />
              
            </form></td>
            <td><?php echo $row_Detalle_Compras['Producto']; ?></td>
            <td><?php echo $row_Detalle_Compras['Marca']; ?></td>
            

            <td>   
              <form action="<?php echo $editFormAction; ?>" method="POST" name="Precio_compra" id="Precio_compra">
            
        
            <input name="pcompra" type="text" class="form-control tooltips input-sm"  id="pcompra" value="<?php echo $row_Detalle_Compras['pcompra']; ?>"  maxlength="6" data-placement="top" data-original-title="<?php echo $row_Detalle_Compras['pcompra'].' - '.round($row_Detalle_Compras['pcompra']*0.18+$row_Detalle_Compras['pcompra'],2); ?>"/>
            
            <input name="codigodetalleproducto" type="hidden" id="codigodetalleproducto" value="<?php echo $row_Detalle_Compras['codigodetalleproducto']; ?>" />
                <input type="hidden" name="MM_update" value="Precio_compra" />
              
              
            </form>
          </td>

            <td align="center">
            <form action="<?php echo $editFormAction; ?>" method="POST" name="Precio_venta" id="Precio_venta">
            
				
            <input name="pventa" type="text" class="form-control tooltips input-sm"  id="pventa" value="<?php echo $row_Detalle_Compras['pventa']; ?>"  maxlength="6" data-placement="top" data-original-title="<?php echo $row_Detalle_Compras['precio_venta']; ?>"/>
            
            <input name="codigodetalleproducto" type="hidden" id="codigodetalleproducto" value="<?php echo $row_Detalle_Compras['codigodetalleproducto']; ?>" />
                <input type="hidden" name="MM_update" value="Precio_venta" />
              
              
            </form>
            </td>

              
            <td align="center">
            
            <?php 
			$importe=$row_Detalle_Compras['pcompra']*$row_Detalle_Compras['cantidad'];
			$total=$total+$importe;
      $validarc=$validarc*$importe;  //validarv=  

      $importec=$row_Detalle_Compras['pventa']*$row_Detalle_Compras['cantidad'];
      $totalv1=$totalv1+$importec;
      $validarv=$validarv*$importec;
				
      echo $importe;
    
			//echo $row_Detalle_Compras['Importe']; ?>
  

               </td>
            <td><?php //echo $EliminarProducto; ?>
            <form action="#" method="POST" name="EliminarProducto">
            <button type="submit" class="btn red-thunderbird btn-sm tooltips" data-placement="top" data-original-title="Eliminar Producto"><i class="glyphicon glyphicon-trash"></i></button>
            <input name="codigodetalleproducto" type="hidden" value="<?php echo $row_Detalle_Compras['codigodetalleproducto']; ?>" />
            <input type="hidden" name="MM_eliminar" value="EliminarProducto" />
            </form>
            
            
            
            
                       </td>
          </tr>
          <?php $i++;} while ($row_Detalle_Compras = mysql_fetch_assoc($Detalle_Compras)); ?>
      </tbody>
    </table>
    <?php } // Show if recordset not empty 
	?>
</td>
</tr>
</table>



  
<?php if ($totalRows_Detalle_Compras > 0) { // Show if recordset not empty ?>
  <table width="80%" border="0" align="center">
    <tr>
      <td align="center" valign="top">
      
      <?php //echo $EliminarVenta; ?>
      <form action="#" method="POST" name="EliminarVenta" id="EliminarVenta">
<button type="submit" class="btn red-thunderbird btn-lg tooltips " data-placement="top" data-original-title="Eliminar Venta">Eliminar Venta</button>
<input name="codigo" type="hidden" value="<?php echo $_GET['codigo']; ?>" />
<input type="hidden" name="MM_EliminarVenta" value="EliminarVenta" />
</form>
      
    
      </td>
 
      <td align="center" valign="top">
      <form action="#" method="POST" name="GuardarVenta" id="GuardarVenta">
      <button type="submit" class="btn blue-chambray btn-lg tooltips" data-placement="top" data-original-title="Guardar Venta"><i class="glyphicon glyphicon-shopping-cart"></i>Generar Compras</button>
      <input name="codigo" type="hidden" value="<?php echo $_GET['codigo']; ?>" />
<input type="hidden" name="MM_GuardarVenta" value="GuardarVenta" />
  <input name="validarc" type="hidden" id="validarc" value="<?php echo $validarc; ?>" />
    <input name="validarv" type="hidden" id="validarv" value="<?php echo $validarv; ?>" />
     <input name="totalv1" type="hidden" id="totalv1" value="<?php echo $totalv1; ?>" />
<input name="CodigoProducto" id="CodigoProducto" type="hidden" value="<?php echo $_GET['codigo']; ?>" />
<input name="codacceso" type="hidden" value="<?php echo $_SESSION['kt_login_id']; ?>" />
<input name="codigopersonal" type="hidden" value="<?php echo $_SESSION['kt_codigopersonal']; ?>" />

     
      </td>
 
      </tr>
  </table>
  <?php echo $row_Detalle_Compras['nombre_color']; ?>
  <?php } // Show if recordset not empty ?>
</td>
<td width="35%" valign="top">


<div class="row">
<div class="col-md-12">
<!-- BEGIN Portlet PORTLET-->
<div class="portlet box blue-chambray text-center">
<div class="portlet-title text-center ">
<div class="caption text-center">
<h1 >$ <?php 
$total=round($total*0.18+$total,2);
echo  $total;
$subtotal=$total/1.18;
$iva=$total-$subtotal;

?></h1></div>
<input type="hidden" name="montopagar" id="montopagar" value="<?php echo($total); ?>" />
</div>
<div class="portlet-body">

<table width="100%" border="0" >
<tr>
<td>
<div class="col-md-13">
<div class="form-group"><span id="spryselect2">
<select name="comprobante" id="comprobante" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Comprobante" required OnChange="TClienteOnChange(this)">
  <option value="fac" selected="selected">Factura</option>
  <option value="bol">Nota de Venta</option>
  <option value="tic" disabled="disabled">Recibo</option>
  <option value="otr" disabled="disabled">Otro</option>
  
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
</td>
<script type="text/javascript">
function pagoOnChange(sel) {
      if (sel.value=="tcr"){
           divC = document.getElementById("banco");
           divC.style.display = "";
 
           divT = document.getElementById("nTargeta");
           divT.style.display = "";
 
      }else{
 
           divC = document.getElementById("banco");
           divC.style.display="none";
 
           divT = document.getElementById("nTargeta");
           divT.style.display = "none";
      }
}
</script>
<td>
<div class="col-md-13">
<div class="form-group"><span id="spryselect3">
<select name="tipopago" id="tipopago" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Comprobante" required onChange="pagoOnChange(this)">
    <option value="p_c" selected="selected">Pago Contado</option>
    <option value="tcr">Pago Tarjeta credito</option>
  <option value="otr">Otros</option>
  
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
</td>
</tr>


<tr>
<td valign="top">
<div class="col-md-13" id="banco" style=" display:none">
<div class="form-group" ><span id="spryselect4">
<select name="codigobanco" id="codigobanco" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Banco">
  <option value="">- Banco -</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Bancos['codigobanco']?>"><?php echo $row_Bancos['nombre_banco']?></option>
  <?php
} while ($row_Bancos = mysql_fetch_assoc($Bancos));
  $rows = mysql_num_rows($Bancos);
  if($rows > 0) {
      mysql_data_seek($Bancos, 0);
	  $row_Bancos = mysql_fetch_assoc($Bancos);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
</td>
<td valign="top">
<div id="nTargeta" style=" display:none">
<span id="sprytextfield1">

  <input  name="numerotarjeta" type="text" class="form-control tooltips "  id="numerotarjeta" value=""  maxlength="50" data-placement="top" data-original-title="N&uacute;mero Tarjeta"   />
  <span class="textfieldRequiredMsg"></span></span>
  </div>
  </td>
</tr>



<br>


<tr>
<td>
<span id="sprytextfield4">
  <input name="numcomprobante" type="text" class="form-control tooltips "  id="numcomprobante" value=""  maxlength="16" data-placement="top" data-original-title="Numero Comprobante"/>
  <span class="textfieldRequiredMsg"></span></span>
</td>


<td><span id="sprytextfield5">
       <div class="form-group" id="sucursal" ><span id="spryselect5">
  <select name="codigosuc" class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar sucursal" >
    <optgroup label="Sucursal"> </optgroup>
    <option value=""></option>
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
  <span class="selectRequiredMsg"></span></span></div>
    


 </td>
</tr>

<tr>
  <td colspan="2"><table width="100%" border="0">
    <tr>
      <td width="95%" valign="top">
      <div class="form-group" id="ClienteN" ><span id="spryselect5">
  <select name="codigoproveedor" class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar Comprobante" >
    <optgroup label="Proveedores"> </optgroup>
    <option value=""></option>
    <?php
do {  
?>
    <option value="<?php echo $row_Clientes['codigoclienten']?>"><?php echo $row_Clientes['ClienteNatural']?></option>
    <?php
} while ($row_Clientes = mysql_fetch_assoc($Clientes));
  $rows = mysql_num_rows($Clientes);
  if($rows > 0) {
      mysql_data_seek($Clientes, 0);
	  $row_Clientes = mysql_fetch_assoc($Clientes);
  }
?>

  
  </select>
  <span class="selectRequiredMsg"></span></span></div>
      
      </td>
      <td width="5%" valign="top">
      <div class="col-md-2">
<div class="input-group">
  <div class="input-group-btn">
<button type="button" class="btn blue dropdown-toggle" data-toggle="dropdown"><i class="icon-users"></i>
<i class="fa fa-angle-down"></i>
</button>
<ul class="dropdown-menu pull-right">
<li class="divider"> </li>
<li>
<a href="javascript:;" onclick="abre_ventana('Emergentes/proveedor_list_add.php',900,700)"><i class="glyphicon glyphicon-floppy-disk "></i> Agregar Proveedor </a>
</li>


<li class="divider"> </li>
</ul>
</div>
<!-- /btn-group -->
</div>
<!-- /input-group -->
</div>
      
      </td>
    </tr>
  </table></td>
  </tr>
<br>

<tr>

<td colspan="2">
<br>


<div class="form-group" id="ClienteJ" style="display:none"><span id="spryselect6">
  </select>
  <span class="selectRequiredMsg"></span></span></div>


</td>

</tr>
<tr>
<td>

<?php $time = time();?>
<span id="sprytextfield4">
<input value="<?php echo date("Y-m-d");?>" type="text" name ="fecha_emision" id ="fecha_comprobante" class="form-control form-control-inline input-medium date-picker tooltips"  value="fecha_emision"  data-date-format="yyyy-mm-dd" data-placement="top" data-original-title="Fecha Emisi&oacute;n" value="<?php echo date("d-m-Y");?>" required="required"/>
  
  <span class="textfieldRequiredMsg"></span></span>
</td>
<td><span id="sprytextfield5">
<input type="text" name="hora_emision" id="hora_emision" class="form-control timepicker timepicker-24 tooltips" data-placement="top" data-original-title="Hora Emisi&oacute;n" required="required">

  <span class="textfieldRequiredMsg"></span></span></td>
</tr>

</table>

<table width="100%" border="0">
<tr>
<td colspan="2">
<hr />
</td>
</tr>
<tr>
<td valign="top" align="right">
<h5 class="font-red-mint"><strong>SUB TOTAL</strong></h5>
</td>
<td valign="top" align="right">
<h5 class="font-red-mint"><strong>$ <?php echo number_format($subtotal,2); ?>
<input type="text" name="subtotal10" id="subtotal10" align="center"  readonly="readonly" value="<?php echo number_format($subtotal,2);?>"  size="8" hidden="true"  /></strong></h5></td>
</tr>
<tr>
<td valign="top" align="right">
<h5 class="font-red-mint"><strong>IGV 18%</strong></h5>
</td>
<td valign="top"align="right"><h5 class="font-red-mint"><strong>$ <?php echo number_format($iva,2);?> 
<input type="text" name="igv" id="igv" align="center"  readonly="readonly" value="<?php echo number_format($iva,2);?>"  size="8" hidden="true"  /></strong></h5></td>
</tr>
<tr>
<td valign="top" align="right">
<h5 class="font-red-mint"><strong>TOTAL </strong></h5></td>
<td valign="top" align="right">
<h5 class="font-red-mint"><strong>$ <?php echo number_format($total,2);?>
<input type="text" name="total10" id="total10" align="center"  readonly="readonly" value="<?php echo number_format($total,2);?>"  size="8" hidden="true"  />
<input type="text" name="totalv" id="totalv" align="center"  readonly="readonly" value="<?php echo number_format($totalv,2);?>"  size="8" hidden="true"  />
</h5></td>
</tr>
<tr>
<td colspan="2">
<!--<button type="submit" class="btn blue-chambray btn-block btn-lg tooltips" data-placement="top" data-original-title="Calcular"><i class="fa fa-calculator"></i>Calcular11</button>
</td>--> 
</tr>
</table>

</form>
</div>
</div>
<!-- END Portlet PORTLET-->
</div>
</div>
</form>
</td>
</tr>
</table>


<script type="text/javascript">
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", {invalidValue:"0", validateOn:["blur", "change"]});
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
</script>  
<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
mysql_free_result($Clientes);
mysql_free_result($Detalle_Compras);
?>