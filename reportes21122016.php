<?php require_once('Connections/Ventas.php'); 
//Enumerar filas de data tablas
 $NRegistros = 1;
?>
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
//Inicio Recibe Datos Maestro


if (isset($_REQUEST['TablaBuscar'])) {
  	$TablaBuscar = $_REQUEST['TablaBuscar'];
   	if ($TablaBuscar==1)
	{
		//Inicio Juego Registro Bienes
		$fecha1_Bienes = "-1";
		if (isset($_REQUEST['fecha1'])) {
  			$fecha1_Bienes = $_REQUEST['fecha1'];
		}	
		$fecha2_Bienes = "-1";
		if (isset($_REQUEST['fecha2'])) {
  			$fecha2_Bienes = $_REQUEST['fecha2'];
	}
	mysql_select_db($database_Ventas, $Ventas);
	$query_Bienes = sprintf("SELECT codigoinventario, codigo, nombre_bien, serie, fecha_adquisicion, numero_factura, fecha_incorporacion, precio_compra FROM inventario_bienes WHERE fecha_adquisicion BETWEEN %s AND %s ORDER BY fecha_adquisicion ASC", GetSQLValueString($fecha1_Bienes, "date"),GetSQLValueString($fecha2_Bienes, "date"));
	$Bienes = mysql_query($query_Bienes, $Ventas) or die(mysql_error());
	$row_Bienes = mysql_fetch_assoc($Bienes);
	$totalRows_Bienes = mysql_num_rows($Bienes);
	//Fin Juego Registro Bienes
	}
	if ($TablaBuscar==2)
	{
		//Inicio Juego Registro Clientes Juridicos
		$fecha1_ClientesJuridicos = "-1";
		if (isset($_REQUEST['fecha1'])) {
  			$fecha1_ClientesJuridicos = $_REQUEST['fecha1'];
	}
	$fecha2_ClientesJuridicos = "-2";
	if (isset($_REQUEST['fecha2'])) {
  		$fecha2_ClientesJuridicos = $_REQUEST['fecha2'];
	}
	mysql_select_db($database_Ventas, $Ventas);
	$query_ClientesJuridicos = sprintf("SELECT codigoclientej, ruc, razonsocial, contacto, email, fecha_registro FROM cjuridico WHERE fecha_registro BETWEEN %s AND %s", GetSQLValueString($fecha1_ClientesJuridicos, "date"),GetSQLValueString($fecha2_ClientesJuridicos, "date"));
$ClientesJuridicos = mysql_query($query_ClientesJuridicos, $Ventas) or die(mysql_error());
$row_ClientesJuridicos = mysql_fetch_assoc($ClientesJuridicos);
$totalRows_ClientesJuridicos = mysql_num_rows($ClientesJuridicos);
	}//Fin Juego Registro Clientes Juridicos
			
	if ($TablaBuscar==3)
	{
		//Inicio Juego Registro Clientes Naturales
		$fecha1_ClientesNaturales = "-1";
		if (isset($_REQUEST['fecha1'])) {
  			$fecha1_ClientesNaturales = $_REQUEST['fecha1'];
		}
		$fecha2_ClientesNaturales = "-1";
		if (isset($_REQUEST['fecha2'])) {
  			$fecha2_ClientesNaturales = $_REQUEST['fecha2'];
		}
		mysql_select_db($database_Ventas, $Ventas);
		$query_ClientesNaturales = sprintf("SELECT codigoclienten, cedula, CONCAT(paterno, ' ', materno, ' ', nombre) AS ClienteN, ciudad, email, fecha_registro FROM cnatural WHERE fecha_registro BETWEEN %s AND %s", GetSQLValueString($fecha1_ClientesNaturales, "date"),GetSQLValueString($fecha2_ClientesNaturales, "date"));
$ClientesNaturales = mysql_query($query_ClientesNaturales, $Ventas) or die(mysql_error());
$row_ClientesNaturales = mysql_fetch_assoc($ClientesNaturales);
$totalRows_ClientesNaturales = mysql_num_rows($ClientesNaturales);
	}
	if ($TablaBuscar==4)
	{
				//Inicio Juego Registro Clientes Naturales
		$fecha1_Compras = "-1";
		if (isset($_REQUEST['fecha1'])) {
  			$fecha1_Compras = $_REQUEST['fecha1'];
		}
		$fecha2_Compras = "-1";
		if (isset($_REQUEST['fecha2'])) {
  			$fecha2_Compras = $_REQUEST['fecha2'];
		}
		mysql_select_db($database_Ventas, $Ventas);
		$query_Compras = sprintf("SELECT numero, sum(preciototalc) as precio_compra, razonsocial, p.codigoproveedor as codigoproveedor, count(numero) as cant_item, sum(cantidad) as cant_articulo, fecha FROM historial_producto hp inner join proveedor p on hp.codigoproveedor=p.codigoproveedor where numero!='' and  fecha BETWEEN %s AND %s group by numero", GetSQLValueString($fecha1_Compras, "date"),GetSQLValueString($fecha2_Compras, "date"));
$Compras = mysql_query($query_Compras, $Ventas) or die(mysql_error());
$row_Compras = mysql_fetch_assoc($Compras);
$totalRows_Compras = mysql_num_rows($Compras);
	}
			//Fin Juego Registro compras
	if ($TablaBuscar==5)
	{
				//Inicio Juego Registro Productos Stock
		mysql_select_db($database_Ventas, $Ventas);
		$query_ProductoStock = sprintf("SELECT p.codigoprod, p.nombre_producto, m.nombre as marca, c.nombre_color, pr.nombre_presentacion, ps.precio_compra as pcompra, ps.precio_venta as pventa, ps.stock FROM `producto_stock` ps inner join producto p on p.codigoprod=ps.codigoprod inner join marca m on m.codigomarca=p.codigomarca inner join color c on c.codigocolor=p.codigocolor inner join presentacion pr on pr.codigopresent=p.codigopresent where p.obs<=99999 and ps.stock>0 group by p.codigoprod");
		$ProductoStock = mysql_query($query_ProductoStock, $Ventas) or die(mysql_error());
		$row_ProductoStock = mysql_fetch_assoc($ProductoStock);
		$totalRows_ProductoStock = mysql_num_rows($ProductoStock);
	}
			
	if ($TablaBuscar==7)
	{
		//Inicio Juego Registro Compras
		$fecha1_Ventas = "-1";
		if (isset($_REQUEST['fecha1'])) {
  			$fecha1_Ventas = $_REQUEST['fecha1'];
		}
		$fecha2_Ventas = "-1";
		if (isset($_REQUEST['fecha2'])) {
	 	 	$fecha2_Ventas = $_REQUEST['fecha2'];
		}
		mysql_select_db($database_Ventas, $Ventas);

		$query_Ventas = sprintf("SELECT v.codigoventas, v.codigo, v.tipocomprobante, v.fecha_emision, dv.codcomprobante, v.codigoventa, count(dv.codcomprobante) as cant_item, sum(dv.cantidad) as cant_articulo, v.total, v.igv, v.subtotal, CONCAT (cn.paterno,' ', cn.materno, ' ', cn.nombre) AS ClienteN, cj.razonsocial AS ClienteJ, CONCAT (p.paterno,' ', p.materno, ' ', p.nombre) AS Vendedor FROM detalle_ventas dv inner join ventas v on dv.codcomprobante=v.codigoventa left join cnatural cn on v.codigoclienten=cn.codigoclienten left join cjuridico cj on v.codigoclientej=cj.codigoclientej inner join personal p on p.codigopersonal=v.codigopersonal where dv.codcomprobante!='' and  fecha_emision BETWEEN %s AND %s group by dv.codcomprobante Order by v.codigoventas DESC", GetSQLValueString($fecha1_Ventas, "date"),GetSQLValueString($fecha2_Ventas, "date"));
$Ventas1 = mysql_query($query_Ventas, $Ventas) or die(mysql_error());
$row_Ventas = mysql_fetch_assoc($Ventas1);
$totalRows_Ventas = mysql_num_rows($Ventas1);
	}
			
			
}
//Fin Recibe Datos Maestro

?>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<?php

//Titulo e icono de la pagina
$Icono="fa fa-file-pdf-o";
$Color="font-blue";
$Titulo="Reportes";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$EstadoBotonAgregar="disabled";
//$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/cod_gen.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");

?> 


<form name="buscar" id="buscar" action="reportes.php" method="POST">      
<table width="100%" border="0" class="table table-bordered table-hover">
  <tbody>
    <tr class="active" >
      <td width="25%" align="center">
      <div class="col-md-12">
        <span id="spryselect1">
  <select name="TablaBuscar" id="TablaBuscar" class="form-control tooltips " data-placement="top" data-original-title="Seleccionar Tabla">
    <option value="0">-- Buscar en... --</option>
    <optgroup label="Reportes Simples">
    <option value="1">Inventario - Bienes</option>
    <option value="2">Clientes Juridicos</option>
    <option value="3">Clientes Naturales</option>
    
    </optgroup>
    <optgroup label="Reportes Complejos - Kardex">
    <option value="4">Compras</option>
    <option value="5">Productos Stock</option>
    <option value="6">Servicios Ofrecidos</option>
    <option value="7">Ventas</option>
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
      </td>
      <td width="50%" align="center">

<div class="col-md-10">
<div class="input-group input-large date-picker input-daterange" data-date="2010-01-01" data-date-format="yyyy-mm-dd">
<span id="sprytextfield1">
  <input type="text" class="form-control  tooltips" name="fecha1" data-placement="top" data-original-title="Agregar Fecha Inicio" />
  <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon"> Hasta </span><span id="sprytextfield2">
  <input type="text" class="form-control tooltips" name="fecha2" data-placement="top" data-original-title="Agregar Fecha Fin" />
  <span class="textfieldRequiredMsg"></span></span></div>
</div>





 
</td>
      

      
      <td width="25%" align="center" valign="top">
      
      <button type="submit" class="btn blue btn-lg">
	  <i class="fa fa-search"></i> Buscar</button>
      
      </td>
    </tr>
  </tbody>
</table>
</form>
<hr>
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

<BR><BR><BR>
<div class="tools"> </div>
<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Bienes----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_Bienes > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
        <th width="15%"> C&oacute;digo</th>
        <th width="20%"> Nombre Bien</th>
        <th width="15%"> Serie</th>
        <th width="15%"> F. Adqui.</th>
        <th width="15%"> Factura</th>
        <th width="15%"> Pre. Adqui.</th>
      </tr>
    </thead>
      <?php do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td><?php echo $row_Bienes['codigo']; ?></td>
          <td><?php echo $row_Bienes['nombre_bien']; ?></td>
          <td><?php echo $row_Bienes['serie']; ?></td>
          <td><?php echo $row_Bienes['fecha_adquisicion']; ?></td>
          <td><?php echo $row_Bienes['numero_factura']; ?></td>
          <td><?php echo $row_Bienes['precio_compra']; ?></td>
          
        </tr>
                <?php $NRegistros++; } while ($row_Bienes = mysql_fetch_assoc($Bienes)); ?>
        <tr>
          <td>T</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td align="right"><strong>TOTAL:</strong>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        
   
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Bienes-------------------------->
<!----------------------------------------------------------------------------------->  


<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Cliente Juridicos----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_ClientesJuridicos > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
        <th width="5%"> C&oacute;digo</th>
        <th width="15%"> R.U.C</th>
        <th width="25%"> Raz&oacute;n Social</th>
        <th width="20%"> Contacto</th>
        <th width="20%"> Email</th>
        <th width="10%"> F. Registro</th>
      </tr>
    </thead>
      <?php do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td><?php echo $row_ClientesJuridicos['codigoclientej']; ?></td>
          <td><?php echo $row_ClientesJuridicos['ruc']; ?></td>
          <td><?php echo $row_ClientesJuridicos['razonsocial']; ?></td>
          <td><?php echo $row_ClientesJuridicos['contacto']; ?></td>
          <td><?php echo $row_ClientesJuridicos['email']; ?></td>
          <td><?php echo $row_ClientesJuridicos['fecha_registro']; ?></td>
          
        </tr>
      <?php $NRegistros++; } while ($row_ClientesJuridicos  = mysql_fetch_assoc($ClientesJuridicos)); ?>
    
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Cliente Juridicos-------------------------->
<!----------------------------------------------------------------------------------->  
 
<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Cliente Naturales----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_ClientesNaturales > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
        <th width="5%"> C&oacute;digo</th>
        <th width="15%"> C&eacute;dula</th>
        <th width="30%"> Cliente</th>
        <th width="10%"> Ciudad</th>
        <th width="20%"> Email</th>
        <th width="10%"> F. Registro</th>
      </tr>
    </thead>
      <?php do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td><?php echo $row_ClientesNaturales['codigoclienten']; ?></td>
          <td><?php echo $row_ClientesNaturales['cedula']; ?></td>
          <td><?php echo $row_ClientesNaturales['ClienteN']; ?></td>
          <td><?php echo $row_ClientesNaturales['ciudad']; ?></td>
          <td><?php echo $row_ClientesNaturales['email']; ?></td>
          <td><?php echo $row_ClientesNaturales['fecha_registro']; ?></td>
          
        </tr>
      <?php $NRegistros++; } while ($row_ClientesNaturales  = mysql_fetch_assoc($ClientesNaturales)); ?>
    
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Cliente Naturales-------------------------->
<!----------------------------------------------------------------------------------->   






<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Compras----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_Compras > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
        <th width="5%"> Numero Comp</th>
        <th width="15%"> Precio Compra</th>
        <th width="30%"> Razon Social</th>
        <th width="10%"> Cantidad Item</th>
        <th width="20%"> Cantidad Articulos</th>
        <th width="10%"> Fecha Compra</th>
      </tr>
    </thead>
      <?php do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td align="center"><?php echo $row_Compras['numero']; ?></td>
          <td align="center"><?php echo number_format($row_Compras['precio_compra'],2); ?></td>
          <td><?php echo $row_Compras['razonsocial']; ?></td>
          <td align="center"><?php echo $row_Compras['cant_item']; ?></td>
          <td align="center"><?php echo $row_Compras['cant_articulo']; ?></td>
          <td><?php echo $row_Compras['fecha']; ?></td>
          
        </tr>
      <?php $NRegistros++; } while ($row_Compras= mysql_fetch_assoc($Compras)); ?>
     
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Compras-------------------------->
<!----------------------------------------------------------------------------------->   





<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Compras----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_ProductoStock > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
        <th width="5%"> Codigo Prod</th>
        <th width="15%"> Nombre Producto</th>
        <th width="30%"> Marca</th>
        <th width="10%"> Color</th>
        <th width="20%"> Presentacion</th>
        <th width="10%"> P. Compra</th>
        <th width="10%"> P. Venta</th>
        <th width="10%"> Stock</th>
      </tr>
    </thead>
      <?php do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td align="center"><?php echo $row_ProductoStock['codigoprod']; ?></td>
          <td align="center"><?php echo $row_ProductoStock['nombre_producto']; ?></td>
          <td><?php echo $row_ProductoStock['marca']; ?></td>
          <td align="center"><?php echo $row_ProductoStock['nombre_color']; ?></td>
          <td align="center"><?php echo $row_ProductoStock['nombre_presentacion']; ?></td>
          <td align="center"><?php echo $row_ProductoStock['pcompra']; ?></td>
          <td align="center"><?php echo $row_ProductoStock['pventa']; ?></td>
          <td><?php echo $row_ProductoStock['stock']; ?></td>
          
        </tr>
      <?php $NRegistros++; } while ($row_ProductoStock= mysql_fetch_assoc($ProductoStock)); ?>
    
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Compras-------------------------->
<!----------------------------------------------------------------------------------->   


<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Ventas----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_Ventas > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="3%"> N&deg; </th>
        <th width="7%"> Comprobante</th>
        <th width="10%"> Precio venta</th>
        <th width="25%"> Cliente Nat - Jur</th>
        <th width="5%"> Cant Item</th>
        <th width="5%"> Cant Articulos</th>
        <th width="10%"> Fecha venta</th>
        <th width="12%"> Vendedor</th>
      </tr>
    </thead>
      <?php do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td align="center"><?php echo $row_Ventas['codcomprobante']; ?></td>
          <td align="center"><?php echo number_format($row_Ventas['total'],2); ?></td>
          <td> <?php echo $row_Ventas['ClienteN'];?><?php echo $row_Ventas['ClienteJ']; ?></td>
          <td align="center"><?php echo $row_Ventas['cant_item']; ?></td>
          <td align="center"><?php echo $row_Ventas['cant_articulo']; ?></td>
          <td><?php echo $row_Ventas['fecha_emision']; ?></td>
          <td><?php echo $row_Ventas['Vendedor']; ?></td>
          
        </tr>
      <?php $NRegistros++; } while ($row_Ventas= mysql_fetch_assoc($Ventas1)); ?>
    
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Ventas-------------------------->
<!----------------------------------------------------------------------------------->   

  
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});

</script>     
                               
<?php 
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>


<?php
mysql_free_result($Bienes);

mysql_free_result($ClientesNaturales);
?>
