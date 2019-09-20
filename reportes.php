<?php require_once('Connections/Ventas.php'); 
//Enumerar filas de data tablas
 $NRegistros = 1; $totaliva=0;
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
	elseif ($TablaBuscar==2)
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
			
			elseif ($TablaBuscar==3)
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
			elseif ($TablaBuscar==4)
			{
				//Inicio Juego Registro de Compras
$fecha1_Compras = "-1";
if (isset($_REQUEST['fecha1'])) {
  $fecha1_Compras = $_REQUEST['fecha1'];
}
$fecha2_Compras = "-1";
if (isset($_REQUEST['fecha2'])) {
  $fecha2_Compras = $_REQUEST['fecha2'];
}
mysql_select_db($database_Ventas, $Ventas);


$query_Compras = sprintf("SELECT numerofactura as numero, hp.montofact, razonsocial, p.codigoproveedor as codigoproveedor, fecha_emision as fecha, hp.igv, hp.subtotal, hp.totalv FROM compras hp inner join proveedor p on hp.codigoproveedor=p.codigoproveedor where fecha_emision BETWEEN %s AND %s group by numerofactura order by fecha_emision", GetSQLValueString($fecha1_Compras, "date"),GetSQLValueString($fecha2_Compras, "date"));
$Compras = mysql_query($query_Compras, $Ventas) or die(mysql_error());
$row_Compras = mysql_fetch_assoc($Compras);
$totalRows_Compras = mysql_num_rows($Compras);
			}
			//Fin Juego Registro compras
			elseif ($TablaBuscar==5)
			{
				//Inicio Juego Registro Productos Stock
mysql_select_db($database_Ventas, $Ventas);


$query_ProductoStock = sprintf("SELECT p.codigoprod, p.nombre_producto, m.nombre as marca, c.nombre_color, pr.nombre_presentacion, ps.precio_compra as pcompra, ps.precio_venta as pventa, ps.stock FROM `producto_stock` ps inner join producto p on p.codigoprod=ps.codigoprod inner join marca m on m.codigomarca=p.codigomarca inner join color c on c.codigocolor=p.codigocolor inner join presentacion pr on pr.codigopresent=p.codigopresent where p.obs=0 and ps.stock>0 group by p.codigoprod");
$ProductoStock = mysql_query($query_ProductoStock, $Ventas) or die(mysql_error());
$row_ProductoStock = mysql_fetch_assoc($ProductoStock);
$totalRows_ProductoStock = mysql_num_rows($ProductoStock);
			}
			
		elseif ($TablaBuscar==6)
      {
        //Inicio Juego Registro Productos Stock
        $fecha1_Compras = "-1";
if (isset($_REQUEST['fecha1'])) {
  $fecha1_Compras = $_REQUEST['fecha1'];
}
$fecha2_Compras = "-1";
if (isset($_REQUEST['fecha2'])) {
  $fecha2_Compras = $_REQUEST['fecha2'];
}


mysql_select_db($database_Ventas, $Ventas);


$query_Servicios = sprintf("SELECT s.codigosv, s.nombre, sum(p.monto) as precio, p.fpago FROM servicios s inner join serviciosapagar p on p.codigosv=s.codigosv where p.monto>0 and p.fpago BETWEEN %s AND %s group by s.codigosv order by s.nombre", GetSQLValueString($fecha1_Compras, "date"),GetSQLValueString($fecha2_Compras, "date"));
$Servicios = mysql_query($query_Servicios, $Ventas) or die(mysql_error());
$row_Servicios = mysql_fetch_assoc($Servicios);
$totalRows_Servicios = mysql_num_rows($Servicios);
      }	
			
			
			
			
			
			
			elseif ($TablaBuscar==7)
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
			
			

      elseif ($TablaBuscar==8)
      {
        //Inicio Juego Registro Compras

      $fecha1_Comprobantes = "-1";
if (isset($_REQUEST['fecha1'])) {
  $fecha1_Comprobantes = $_REQUEST['fecha1'];
}
$fecha2_Comprobantes = "-1";
if (isset($_REQUEST['fecha2'])) {
  $fecha2_Comprobantes = $_REQUEST['fecha2'];
}


mysql_select_db($database_Ventas, $Ventas);
$query_Comprobantes= sprintf("SELECT codigoventas, total, totalc, fecha_emision, codigoventa, tipocomprobante FROM `ventas` WHERE fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_Comprobantes, "date"),GetSQLValueString($fecha2_Comprobantes, "date"));

//$query_Comprobantes= sprintf("SELECT count(*) as totalcomprobante, sum(total) as totalventa, sum(totalc) as totalcompra FROM `ventas` WHERE fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_Comprobantes, "date"),GetSQLValueString($fecha2_Comprobantes, "date"));
$Comprobantes = mysql_query($query_Comprobantes, $Ventas) or die(mysql_error());
$row_Comprobantes = mysql_fetch_assoc($Comprobantes);
$totalRows_Comprobantes = mysql_num_rows($Comprobantes);
      } 
      
 elseif ($TablaBuscar==9)
      {
        //Inicio Juego Registro Compras

$totalRows_BalanceFechas=1;
$fecha1_BalanceFechas = "-1";
$fecha2_BalanceFechas = "-1";
if (isset($_REQUEST['fecha1'])) {
  $fecha1_BalanceFechas = $_REQUEST['fecha1'];
}
$fecha2_Comprobantes = "-1";
if (isset($_REQUEST['fecha2'])) {
  $fecha2_BalanceFechas = $_REQUEST['fecha2'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_TotalFacturasV= sprintf("SELECT sum(total) as total FROM `ventas` WHERE estadofact>=1 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$TotalFacturasV = mysql_query($query_TotalFacturasV, $Ventas) or die(mysql_error());
$row_TotalFacturasV = mysql_fetch_assoc($TotalFacturasV);

$query_TotalFacturasC= sprintf("SELECT sum(montofact) as total FROM `compras` WHERE estadofact>=1 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$TotalFacturasC = mysql_query($query_TotalFacturasC, $Ventas) or die(mysql_error());
$row_TotalFacturasC = mysql_fetch_assoc($TotalFacturasC);


$query_TotalRecibosV= sprintf("SELECT sum(montofact) as total FROM `reciboventas` WHERE estadofact>=1 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$TotalRecibosV = mysql_query($query_TotalRecibosV, $Ventas) or die(mysql_error());
$row_TotalRecibosV = mysql_fetch_assoc($TotalRecibosV);

$query_SueldoMensual= sprintf("SELECT sum(sueldomensual) as total FROM `sueldo_mensual` WHERE estado=0 and fregistro BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$SueldoMensual = mysql_query($query_SueldoMensual, $Ventas) or die(mysql_error());
$row_SueldoMensual = mysql_fetch_assoc($SueldoMensual);

$query_AbonosRecibidos= sprintf("SELECT sum(monto_rec) as montorecibido FROM `abonos` WHERE estadoabono>=1 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$AbonosRecibidos = mysql_query($query_AbonosRecibidos, $Ventas) or die(mysql_error());
$row_AbonosRecibidos = mysql_fetch_assoc($AbonosRecibidos);

$query_AbonosFacturados= sprintf("SELECT sum(total) as montofacturado FROM `abonos` WHERE estadoabono>=1 and saldo=0 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$AbonosFacturados = mysql_query($query_AbonosFacturados, $Ventas) or die(mysql_error());
$row_AbonosFacturados = mysql_fetch_assoc($AbonosFacturados);


$query_ProductosxEntregar= sprintf("SELECT sum(totalc) as total FROM `abonos` WHERE estadoabono=2 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$ProductosxEntregar = mysql_query($query_ProductosxEntregar, $Ventas) or die(mysql_error());
$row_ProductosxEntregar = mysql_fetch_assoc($ProductosxEntregar);

$query_InicialCredito= sprintf("SELECT sum(monto_rec) as total FROM `credito` WHERE estadocredito=1 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$InicialCredito = mysql_query($query_InicialCredito, $Ventas) or die(mysql_error());
$row_InicialCredito = mysql_fetch_assoc($InicialCredito);


$query_ProductosCreditos= sprintf("SELECT sum(totalc) as total FROM `credito` WHERE estadocredito=1 and fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$ProductosCreditos = mysql_query($query_ProductosCreditos, $Ventas) or die(mysql_error());
$row_ProductosCreditos = mysql_fetch_assoc($ProductosCreditos);

$query_CuotasCredito= sprintf("SELECT sum(montop) as total FROM `pagocredito` WHERE fechap BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$CuotasCredito = mysql_query($query_CuotasCredito, $Ventas) or die(mysql_error());
$row_CuotasCredito = mysql_fetch_assoc($CuotasCredito);

$query_PagoServicios= sprintf("SELECT sum(monto) as total FROM `serviciosapagar` WHERE fregistro BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$PagoServicios = mysql_query($query_PagoServicios, $Ventas) or die(mysql_error());
$row_PagoServicios = mysql_fetch_assoc($PagoServicios);

$query_CreditoSaldo= sprintf("SELECT sum(saldofinal) as total FROM `credito` WHERE fecha_emision BETWEEN %s AND %s", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$CreditoSaldo = mysql_query($query_CreditoSaldo, $Ventas) or die(mysql_error());
$row_CreditoSaldo = mysql_fetch_assoc($CreditoSaldo);

$query_AbonoSaldo= sprintf("SELECT sum(saldo) as total FROM `abonosaldo`", GetSQLValueString($fecha1_BalanceFechas, "date"));
$AbonoSaldo = mysql_query($query_AbonoSaldo, $Ventas) or die(mysql_error());
$row_AbonoSaldo = mysql_fetch_assoc($AbonoSaldo);

$query_ProductoStock= sprintf("SELECT sum(`stock`*`precio_compra`) as totalc, sum(`stock`*`precio_venta`) as totalv, count(*) as totalitem, sum(stock) as totalstock FROM `producto_stock` WHERE `stock`>0 and stock<2000");
$ProductoStock = mysql_query($query_ProductoStock, $Ventas) or die(mysql_error());
$row_ProductoStock = mysql_fetch_assoc($ProductoStock);

$query_Clientes= sprintf("SELECT count(*) as total FROM `cnatural` WHERE `estado`=0");
$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
$row_Clientes = mysql_fetch_assoc($Clientes);

$query_Proveedor= sprintf("SELECT count(*) as total FROM `proveedor` WHERE `estado`=0");
$Proveedor = mysql_query($query_Proveedor, $Ventas) or die(mysql_error());
$row_Proveedor = mysql_fetch_assoc($Proveedor);

$query_Personal= sprintf("SELECT count(*) as total FROM `personal`");
$Personal = mysql_query($query_Personal, $Ventas) or die(mysql_error());
$row_Personal = mysql_fetch_assoc($Personal);

      } 
      elseif ($TablaBuscar==10)
      {
        //Inicio Juego Registro Compras
$totalRows_BalanceVendedor=1;
$fecha1_BalanceFechas = "-1";
$fecha2_BalanceFechas = "-1";
if (isset($_REQUEST['fecha1'])) {
  $fecha1_BalanceFechas = $_REQUEST['fecha1'];
}
$fecha2_Comprobantes = "-1";
if (isset($_REQUEST['fecha2'])) {
  $fecha2_BalanceFechas = $_REQUEST['fecha2'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_VendedorVentas= sprintf("SELECT p.cedula, p.nombre, p.paterno, sum(v.total) as totalv, sum(v.totalc) as totalc, count(v.codigopersonal) as cantidadventas FROM personal p inner join ventas v on v.codigopersonal=p.codigopersonal WHERE fecha_emision BETWEEN %s AND %s GROUP BY p.codigopersonal", GetSQLValueString($fecha1_BalanceFechas, "date"),GetSQLValueString($fecha2_BalanceFechas, "date"));
$VendedorVentas = mysql_query($query_VendedorVentas, $Ventas) or die(mysql_error());
$row_VendedorVentas = mysql_fetch_assoc($VendedorVentas);


      
      
      
      
           
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
    <option value="8">Ganancias en Comprobantes</option>
    <option value="9">Balance General</option>
    <option value="10">Ventas por Vendedor</option>
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
<!-- ---------------------------------------------------------------------------------------------------------- -->

<BR><BR><BR>
<div class="tools"> </div>
<!--------------------------------------------------------------------------------- -->
<!--------------------------------Inicio Tabla Bienes----------------------->
<!--------------------------------------------------------------------------------- -->
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
    	<th colspan="6">Total de Registros</th>
        <th><?php echo $NRegistros; ?></th>
    </tr>

        
   
    
  </table>
  <?php } // Show if recordset not empty ?>
<!--------------------------------------------------------------------------------- -->
<!--------------------------------Fin Tabla Bienes-------------------------->
<!--------------------------------------------------------------------------------- -->  


<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Cliente Juridicos----------------------->
<!--------------------------------------------------------------------------------- -->
<?php if ($totalRows_ClientesJuridicos > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="7%"> N&deg; </th>
        <th width="5%"> C&oacute;digo</th>
        <th width="13%"> R.U.C</th>
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
    <tr>
    	<th colspan="6">Total de Registros</th>
        <th><?php echo $NRegistros; ?></th>
    </tr>
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Cliente Juridicos-------------------------->
<!----------------------------------------------------------------------------------->  
 
<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Cliente Naturales----------------------->
<!--------------------------------------------------------------------------------- -->
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
    <tr>
    	<th colspan="6">Total de Registros</th>
        <th><?php $NRegistros=$NRegistros-1; echo $NRegistros; ?></th>
    </tr>
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Cliente Naturales-------------------------->
<!--------------------------------------------------------------------------------- -->   






<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Compras----------------------->
<!--------------------------------------------------------------------------------- -->
<?php if ($totalRows_Compras > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
        <th width="12%"> Numero Comprobante</th>
        <th width="12%"> EMPRESA</th>
        <th width="12%"> Precio Compra</th>
        <th width="28%"> IVA</th>
        <th width="10%"> Total Compra</th>
        <th width="10%"> Fecha Compra</th>
        <th width="10%"> Precio Venta Aprox</th>
        <th width="10%"> Ganancia</th>
      </tr>
    </thead>
      <?php $totalfact=0; $totalart=0; $totalventa=0; $totalganancia=0;
	  	do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td align="center"><?php echo $row_Compras['numero']; ?></td>
          <td><?php echo $row_Compras['razonsocial']; ?></td>
          <td align="right"><?php $totalfact+=$row_Compras['subtotal'];
		  echo number_format($row_Compras['subtotal'],2); ?></td>
          <td><?php $totaliva+=$row_Compras['igv'];

          echo number_format($row_Compras['igv'],2); ?></td>
           <td><?php echo $row_Compras['montofact']; ?></td>
          <td><?php echo $row_Compras['fecha']; ?></td>
          <td align="center"><?php 
          $totalventa+=$row_Compras['totalv'];
          echo $row_Compras['totalv']; ?></td>
          <td align="center"><?php $ganancia=round((($row_Compras['totalv']-$row_Compras['montofact'])*100)/$row_Compras['montofact'],2);
          echo $ganancia.'%'; 
          $totalganancia+=$ganancia;
          ?></td>
          
          
        </tr>
      <?php $NRegistros++; } while ($row_Compras= mysql_fetch_assoc($Compras)); ?>
     <tr>
    	<th colspan="3">Total</th>
        <td align="right"><?php echo number_format($totalfact,2); ?></td>
    	  <td align="center"><?php echo $totaliva; ?></td>
         <td align="center"><?php echo round($totaliva+$totalfact,2); ?></td>
         <td align="center">-</td>
         <td align="center"><?php echo $totalventa; ?></td>
         <td align="center"><?php echo round($totalganancia/($NRegistros-1),2).'%'; ?></td>
    </tr>
    
  </table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Compras-------------------------->
<!--------------------------------------------------------------------------------- -->   





<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Stock----------------------->
<!--------------------------------------------------------------------------------- -->
<?php if ($totalRows_ProductoStock > 0) 
	{ // Show if recordset not empty ?>
  		<table class="table table-striped table-bordered table-hover" id="sample_1">
    		<thead>
      			<tr>
        			<th width="5%"> N</th>
        			<th width="5%"> Cod_P</th>
        			<th width="35%"> Nombre Producto</th>
        			<th width="10%"> Marca</th>
        			<th width="10%"> Color</th>
        			<th width="10%"> Present.</th>
        			<th width="10%"> P.Compra</th>
        			<th width="10%"> P.Venta</th>
        			<th width="5%"> Stock</th>
<th width="10%"> Ganancia</th>
      			</tr>
    		</thead>
      		<?php $totalpv=0; $totalpc=0; $totalart=0; do { ?>
        		<tr>
          
          			<td><?php echo $NRegistros; ?></td>
          			<td align="center"><?php echo substr($row_ProductoStock['codigoprod'],-4); ?></td>
          			<td align="center"><?php echo $row_ProductoStock['nombre_producto']; ?></td>
          			<td><?php echo $row_ProductoStock['marca']; ?></td>
          			<td align="center"><?php echo $row_ProductoStock['nombre_color']; ?></td>
          			<td align="center"><?php echo $row_ProductoStock['nombre_presentacion']; ?></td>
          			<td align="center"><?php $totalpc+=($row_ProductoStock['pcompra']*$row_ProductoStock['stock']); echo $row_ProductoStock['pcompra']; ?></td>
          			<td align="center"><?php $totalpv+=($row_ProductoStock['pventa']*$row_ProductoStock['stock']); echo $row_ProductoStock['pventa']; ?></td>
          			<td align="center"><?php $totalart+=$row_ProductoStock['stock'];echo $row_ProductoStock['stock']; ?></td>
<td align="center"><?php $ganancia=(($row_ProductoStock['pventa']-$row_ProductoStock['pcompra'])/$row_ProductoStock['pcompra'])*100;echo number_format(($ganancia),2).'%'; ?></td>
          
        		</tr>
      <?php $NRegistros++; } while ($row_ProductoStock= mysql_fetch_assoc($ProductoStock)); ?>
 
  		</table>
      	<strong> Total Articulos Stock &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $totalart;?></strong><br />
        <strong> Total Precio Compra &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo number_format($totalpc,2); ?></strong><br />
        <strong> Total Precio de Ventas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format($totalpv,2);?></strong><br />	
        <strong> Total Ganancia Aproximada &nbsp;&nbsp;&nbsp;  <?php echo number_format($totalpv-$totalpc,2); ?></strong>
        
     
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Stock-------------------------->
<!----------------------------------------------------------------------------------->   

<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Servicios----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_Servicios > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="3%"> N&deg; </th>
        <th width="7%"> Codigo Serv</th>
        <th width="10%"> Nombre</th>
        <th width="25%"> Precio</th>
      </tr>
    </thead>
      <?php $totalservicios=0; do { $totalservicios=$totalservicios+$row_Servicios['precio'];?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td align="center"><?php echo $row_Servicios['codigosv']; ?></td>
          <td align="center"><?php echo $row_Servicios['nombre']; ?></td>
          <td> <?php echo $row_Servicios['precio'];?>
        </tr>
      <?php $NRegistros++; } while ($row_Servicios= mysql_fetch_assoc($Servicios)); ?>

    
  </table>
      <strong> Total de Servicios &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $totalservicios; ?></strong>
    
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Servicios-------------------------->
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
      <?php $totalventas=0; do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td align="center"><?php echo $row_Ventas['codcomprobante']; ?></td>
          <td align="center"><?php $totalventas+=$row_Ventas['total']; echo number_format($row_Ventas['total'],2); ?></td>
          <td> <?php echo $row_Ventas['ClienteN'];?><?php echo $row_Ventas['ClienteJ']; ?></td>
          <td align="center"><?php echo $row_Ventas['cant_item']; ?></td>
          <td align="center"><?php echo $row_Ventas['cant_articulo']; ?></td>
          <td><?php echo $row_Ventas['fecha_emision']; ?></td>
          <td><?php echo $row_Ventas['Vendedor']; ?></td>
          
        </tr>
      <?php $NRegistros++; } while ($row_Ventas= mysql_fetch_assoc($Ventas1)); ?>

    
  </table>
      <strong> Total de Ventas &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $totalventas; ?></strong>
    
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Ventas-------------------------->
<!----------------------------------------------------------------------------------->   







<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Comprobantes----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_Comprobantes > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="3%"> NUMERO</th>
        <th width="10%"> FECHA</th>

        <th width="10%"> TIPO</th>
        <th width="5%"> COMPROBANTE</th>
        <th width="7%"> TOTAL VENTA</th>
        <th width="10%"> TOTAL COMPRA</th>
        <th width="25%"> GANANCIA</th>
        <th width="25%"> % GANANCIA</th>
      </tr>
      </thead>
      <?php $totalv=0; 
        $totalc=0;
      do { 
$ganancia=number_format($row_Comprobantes['total'],2)-number_format($row_Comprobantes['totalc'],2);
      ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td><?php echo $row_Comprobantes['fecha_emision']; ?></td>
          <td align="center"><?php 
         if($row_Comprobantes['tipocomprobante']=='fac')
              $tipo='FACTURA';
          if($row_Comprobantes['tipocomprobante']=='cre')
              $tipo='CREDITO';
          if($row_Comprobantes['tipocomprobante']=='abo')
              $tipo='ABONO'; 
          echo $tipo; ?></td>
          <td align="center"><?php echo 'fact'.substr($row_Comprobantes['codigoventas'],-4); ?></td>
          <td align="center"><?php echo number_format($row_Comprobantes['total'],2); ?></td>
          <td> <?php echo $row_Comprobantes['totalc'];?></td>
          <td align="center"><?php echo $ganancia; ?></td>

          <td align="center"><?php echo round($ganancia*100/$row_Comprobantes['totalc'],2).'%'; ?></td>
        </tr>
      <?php $NRegistros++; 
      $totalv+=$row_Comprobantes['total'];
      $totalc+=$row_Comprobantes['totalc'];;
      } while ($row_Comprobantes= mysql_fetch_assoc($Comprobantes)); ?>

    
  </table>
      <strong> Total de Ventas  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $totalv; ?></strong><br>
      <strong> Total de Compras &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $totalc; ?></strong><br>
      <strong> Total de Ganancias &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $totalv-$totalc; ?></strong><br>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Comprobantes-------------------------->
<!----------------------------------------------------------------------------------->  

<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Balance Fechas----------------------->
<!-----------------------------------------------------------------------------------!>
<?php if ($totalRows_BalanceFechas > 0) { // Show if recordset not empty ?>
<table width="727" class="table table-striped table-bordered table-hover" id="sample_1">
   <tr>
    <th colspan="5"><center>REPORTE DE FECHA<br> DESDE <?php echo ($fecha1_BalanceFechas); ?> HASTA 
    <?php echo ($fecha2_BalanceFechas); ?><center></th>
  </tr>
  <tr>
    <td colspan="2">INGRESOS</td>
    <td width="34">&nbsp;</td>
    <td colspan="2">EGRESOS</td>
  </tr>
  <tr>
    <td width="218">DETALLE</td>
    <td width="91">TOTAL</td>
    <td>&nbsp;</td>
    <td width="218">DETALLE</td>
    <td width="132">TOTAL</td>
  </tr>
  <tr>
    <td>Factura de Ventas</td>
    <td><?php echo $row_TotalFacturasV['total'];?></td>
    <td>&nbsp;</td>
    <td>Facturas de Compras</td>
    <td><?php echo $row_TotalFacturasC['total'];?></td>
  </tr>
  <tr>
    <td>Recibo de Ventas</td>
    <td><?php echo number_format($row_TotalRecibosV['total'], 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Pagos de Personal</td>
    <td><?php echo number_format($row_SueldoMensual['total'], 2, '.', '');?></td>
  </tr>
  <tr>
    <td>Abonos</td>
    <td><?php echo number_format($row_AbonosRecibidos['montorecibido']-$row_AbonosFacturados['montofacturado'], 2, '.', '');?></td>
    <td></td>
    <td>Productos x entregar - abonos</td>
    <td><?php echo number_format($row_ProductosxEntregar['total'], 2, '.', ''); ?></td>
  </tr>
  <tr>
    <td>Inicial de Creditos</td>
    <td><?php echo number_format($row_InicialCredito['total'], 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Productos Entregados a Creditos</td>
    <td><?php echo number_format($row_ProductosCreditos['total'], 2, '.', '');?></td>
  </tr>
  <tr>
    <td>Cuotas de Creditos Pagadas</td>
    <td><?php echo number_format($row_CuotasCredito['total'], 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Pago de Servicios</td>
    <td><?php echo number_format($row_PagoServicios['total'], 2, '.', '');?></td>
  </tr>
  <tr>
    <td>Total de Ingresos</td>
    <td><?php 
        $ingresoBalance=$row_TotalFacturasV['total']+$row_TotalRecibosV['total']+round($row_AbonosRecibidos['montorecibido']-$row_AbonosFacturados['montofacturado'],2)+$row_InicialCredito['total']+$row_CuotasCredito['total'];
        $egresoBalance=$row_TotalFacturasC['total']+$row_SueldoMensual['total']+$row_ProductosxEntregar['total']+$row_ProductosCreditos['total']+$row_PagoServicios['total'];
    $ingresoBalance=number_format($ingresoBalance, 2, '.', '');
    echo $ingresoBalance;?> </td>
    <td>&nbsp;</td>
    <td>Total de Egresos</td>
    <td><?php echo number_format($egresoBalance, 2, '.', '');?></td>
  </tr>
  <tr>
    <td>UTILIDAD</td>
    <td><?php
    $utilidad=number_format($ingresoBalance-$egresoBalance, 2, '.', '');
    if($utilidad>=0)
        echo ($utilidad);
    else
        echo($utilidad);
    ?> </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
      
      <tr>
    <th colspan=2><center>Resumen General</center></th>
    
    <td>&nbsp;</td>
    <th colspan=2><center>Datos Relevantes</center></th>
  </tr>
      <tr>
    <td>Ingresos</td>
    <td><?php echo number_format($ingresoBalance, 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Total Clientes</td>
    <td><?php echo $row_Clientes['total'];?></td>
  </tr>
      <tr>
    <td height="23">Cuotas de creditos por cobrar</td>
    <td><?php echo number_format($row_CreditoSaldo['total'], 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Total Proveedores</td>
    <td><?php echo $row_Proveedor['total'];?></td>
  </tr>
   <tr>
    <td height="23">Saldo de Abonos</td>
    <td><?php echo number_format($row_AbonoSaldo['total'], 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Empleados Activos</td>
    <td><?php echo $row_Personal['total'];?></td>
  </tr>
  <tr>
    <td>Total Ingresos + Total a Cobrar</td>
    <td><?php echo number_format($ingresoBalance+$row_CreditoSaldo['total']+$row_AbonoSaldo['total'], 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Tipos de Productos</td>
    <td><?php echo $row_ProductoStock['totalitem'];?></td>
  </tr>
  <tr>
    <td>Egresos</td>
    <td><?php echo number_format($egresoBalance, 2, '.', '');?></td>
    <td>&nbsp;</td>
    <td>Cantidad de Productos en existencia</td>
    <td><?php echo $row_ProductoStock['totalstock'];?></td>
  </tr>
        <tr>
    <td>Productos en Stock (Precio de Compra)</td>
    <td><?php echo $row_ProductoStock['totalc'];?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
        <tr>
    <td>Productos en Stock (Precio de Venta)</td>
    <td><?php echo $row_ProductoStock['totalv'];?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>


        <tr>
    <td>Utilidad General (Ingresos + Total Stock)</td>
    <td><?php echo round($row_ProductoStock['totalv']+$ingresoBalance+$row_CreditoSaldo['total']+$row_AbonoSaldo['total'],2);?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
        
</table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Balance Fechas-------------------------->
<!----------------------------------------------------------------------------------->  


<!----------------------------------------------------------------------------------->
<!--------------------------------Inicio Tabla Balance----------------------->
<!----------------------------------------------------------------------------------->
<?php if ($totalRows_BalanceVendedor > 0) { // Show if recordset not empty ?>
<table width="727" class="table table-striped table-bordered table-hover" id="sample_1">
  <tr>
    <th colspan="5"><center>REPORTE DE FECHA<br> DESDE <?php echo ($fecha1_BalanceFechas); ?> HASTA 
    <?php echo ($fecha2_BalanceFechas); ?><center></th>
  </tr><tr>
    <td>N.</td>
    <td>CEDULA</td>
    <td>NOMBRE</td>
    <td>CANTIDAD VENTAS</td>
    <td>TOTAL VENTAS</td>
    <td>UTILIDAD</td>
  </tr>
      <?php $totalventas=0; do { ?>
        <tr>
          
          <td><?php echo $NRegistros; ?></td>
          <td align="center"><?php echo $row_VendedorVentas['cedula']; ?></td>
          <td align="center"><?php echo $row_VendedorVentas['nombre'].' '.$row_VendedorVentas['paterno']; ?></td>
          <td><?php echo $row_VendedorVentas['cantidadventas']; ?></td>
          <td><?php echo $row_VendedorVentas['totalv']; ?></td>
          <td><?php echo $row_VendedorVentas['totalv']-$row_VendedorVentas['totalc']; ?></td>
             </tr>
      <?php $NRegistros++; 
      } while ($row_VendedorVentas= mysql_fetch_assoc($VendedorVentas)); ?>

        
</table>
  <?php } // Show if recordset not empty ?>
<!----------------------------------------------------------------------------------->
<!--------------------------------Fin Tabla Balance-------------------------->
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
