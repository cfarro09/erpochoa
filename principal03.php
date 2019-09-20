<?php require_once('Connections/Ventas.php'); ?>
<?php
date_default_timezone_set('America/Lima');
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



mysql_select_db($database_Ventas, $Ventas);
$fechahoy=date("Y-m-d");
$query_ContadorVentas = "SELECT sum(total) AS ContadorVentas FROM ventas where fecha_emision='$fechahoy' and estadofact=1";
$ContadorVentas = mysql_query($query_ContadorVentas, $Ventas) or die(mysql_error());
$row_ContadorVentas = mysql_fetch_assoc($ContadorVentas);
$totalRows_ContadorVentas = mysql_num_rows($ContadorVentas);


mysql_select_db($database_Ventas, $Ventas);
$query_ContadorRecibos = "SELECT sum(total) AS ContadorRecibos FROM reciboventas where fecha_emision='$fechahoy' and estadofact=1";
$ContadorRecibos = mysql_query($query_ContadorRecibos, $Ventas) or die(mysql_error());
$row_ContadorRecibos = mysql_fetch_assoc($ContadorRecibos);
$totalRows_ContadorRecibos = mysql_num_rows($ContadorRecibos);


mysql_select_db($database_Ventas, $Ventas);
$query_ContadorAbonos = "SELECT sum(monto_rec) AS ContadorAbonos FROM abonos  where fecha_emision='$fechahoy' and estadoabono>=1";
$ContadorAbonos = mysql_query($query_ContadorAbonos, $Ventas) or die(mysql_error());
$row_ContadorAbonos = mysql_fetch_assoc($ContadorAbonos);
$totalRows_ContadorAbonos = mysql_num_rows($ContadorAbonos);

mysql_select_db($database_Ventas, $Ventas);
$query_ContadorCompras = "SELECT sum(montofact) AS ContadorCompras FROM compras  where fecha_emision='$fechahoy'";
$ContadorCompras = mysql_query($query_ContadorCompras, $Ventas) or die(mysql_error());
$row_ContadorCompras = mysql_fetch_assoc($ContadorCompras);
$totalRows_ContadorCompras = mysql_num_rows($ContadorCompras);
echo $fechahoy;


mysql_select_db($database_Ventas, $Ventas);
$query_ContadorCreditos = "SELECT sum(monto_rec) AS ContadorCreditos FROM credito  where fecha_emision='$fechahoy'";
$ContadorCreditos = mysql_query($query_ContadorCreditos, $Ventas) or die(mysql_error());
$row_ContadorCreditos = mysql_fetch_assoc($ContadorCreditos);
$totalRows_ContadorCreditos = mysql_num_rows($ContadorCreditos);


mysql_select_db($database_Ventas, $Ventas);
$query_ContadorCreditosC = "SELECT sum(montop) AS ContadorCreditosC FROM pagocredito  where fechap='$fechahoy'";
$ContadorCreditosC = mysql_query($query_ContadorCreditosC, $Ventas) or die(mysql_error());
$row_ContadorCreditosC = mysql_fetch_assoc($ContadorCreditosC);
$totalRows_ContadorCreditosC = mysql_num_rows($ContadorCreditosC);

mysql_select_db($database_Ventas, $Ventas);
$query_ContadorServiciosP = "SELECT sum(monto) AS ContadorServiciosP FROM serviciosapagar  where fregistro='$fechahoy'";
$ContadorServiciosP = mysql_query($query_ContadorServiciosP, $Ventas) or die(mysql_error());
$row_ContadorServiciosP = mysql_fetch_assoc($ContadorServiciosP);
$totalRows_ContadorServiciosP = mysql_num_rows($ContadorServiciosP);

mysql_select_db($database_Ventas, $Ventas);
$query_ContadorPersonal= "SELECT sum(sueldomensual) AS ContadorPersonal FROM sueldo_mensual  where fregistro='$fechahoy'";
$ContadorPersonal = mysql_query($query_ContadorPersonal, $Ventas) or die(mysql_error());
$row_ContadorPersonal = mysql_fetch_assoc($ContadorPersonal);
$totalRows_ContadorPersonal = mysql_num_rows($ContadorPersonal);



mysql_select_db($database_Ventas, $Ventas);
$query_ContadorServicios = "SELECT count(codigosv) AS ContadorServicios FROM servicios";
$ContadorServicios = mysql_query($query_ContadorServicios, $Ventas) or die(mysql_error());
$row_ContadorServicios = mysql_fetch_assoc($ContadorServicios);
$totalRows_ContadorServicios = mysql_num_rows($ContadorServicios);





 
//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-home";
$Color="font-blue";
$Titulo="Pagina Principal";
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

<!-- BEGIN DASHBOARD STATS 1-->
<div class="row">
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat blue">
<div class="visual">
<i class="fa fa-users"></i>
</div>
<div class="details">
<div class="number">
<span data-counter="counterup" data-value="<?php if ($row_ContadorCreditos['ContadorCreditos']==0) echo 0; else
echo $row_ContadorCreditos['ContadorCreditos']; ?>"</span>
</div>
<div class="desc"> GENERAR CREDITOS</div>
</div>
<a class="more" href="creditos_add.php?codigo=<?php echo $_GET['codigo']; ?>"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat red">
<div class="visual">
<i class="fa fa-magic"></i>
</div>


<div class="details">
<div class="number">
    
<span data-counter="counterup" data-value="<?php if ($row_ContadorAbonos['ContadorAbonos']==0) echo 0; else
echo $row_ContadorAbonos['ContadorAbonos']; ?>"></span>
</div>
<div class="desc"> GENERAR ABONOS </div>
</div>
<a class="more" href="abonos_add.php?codigo=<?php echo $_GET['codigo']; ?>"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat purple">
<div class="visual">
<i class="fa fa-balance-scale"></i>
</div>


<div class="details">
<div class="number">
    
<span data-counter="counterup" data-value="<?php if ($row_ContadorVentas['ContadorVentas']==0) echo 0; else
echo $row_ContadorVentas['ContadorVentas']; ?>"></span>
</div>
<div class="desc"> VENTAS </div>
</div>
<a class="more" href="ventas_add.php?codigo=<?php echo $_GET['codigo']; ?>"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat purple">
<div class="visual">
<i class="fa fa-balance-scale"></i>
</div>
<div class="details">
<div class="number">
    
<span data-counter="counterup" data-value="<?php if ($row_ContadorCreditosC['ContadorCreditosC']==0) echo 0; else
echo $row_ContadorCreditosC['ContadorCreditosC']; ?>"></span>
</div>
<div class="desc"> PAGO DE CREDITOS </div>
</div>
<a class="more" href="creditos_list.php"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
</div>

<br>

<div class="row">
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat blue">
<div class="visual">
<i class="fa fa-users"></i>
</div>
<div class="details">
<div class="number">
<span data-counter="counterup" data-value="<?php if ($row_ContadorCompras['ContadorCompras']==0) echo 0; else
echo $row_ContadorCompras['ContadorCompras']; ?>"></span>
</div>
<div class="desc"> COMPRAS</div>
</div>
<a class="more" href="compras_add.php?codigo=<?php echo $_GET['codigo']; ?>"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat red">
<div class="visual">
<i class="fa fa-magic"></i>
</div>


<div class="details">
<div class="number">
    
<span data-counter="counterup" data-value="<?php if ($row_ContadorServiciosP['ContadorServiciosP']==0) echo 0; else
echo $row_ContadorServiciosP['ContadorServiciosP']; ?>"></span>
</div>
<div class="desc"> PAGO DE SERVICIOS </div>
</div>
<a class="more" href="service_pago.php"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat purple">
<div class="visual">
<i class="fa fa-balance-scale"></i>
</div>
<div class="details">
<div class="number">
    
<span data-counter="counterup" data-value="<?php if ($row_ContadorPersonal['ContadorPersonal']==0) echo 0; else
echo $row_ContadorPersonal['ContadorPersonal']; ?>"></span>
</div>
<div class="desc"> PAGOS DE PERSONAL </div>
</div>
<a class="more" href="personal_money.php"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat purple">
<div class="visual">
<i class="fa fa-balance-scale"></i>
</div>
<div class="details">
<div class="number">
    
<span data-counter="counterup" data-value="<?php if ($row_ContadorRecibos['ContadorRecibos']==0) echo 0; else
echo $row_ContadorRecibos['ContadorRecibos']; ?>"></span>
</div>
<div class="desc"> RECIBO DE VENTAS</div>
</div>
<a class="more" href="recibos_add.php?codigo=<?php echo $_GET['codigo']; ?>"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
</div>

<br>

<div class="tiles text-center">
<a href="product_list.php">
<div class="tile bg-green">
<div class="tile-body">
<i class="fa fa-cubes"></i>
</div>
<div class="tile-object">
<div class="name "> Productos </div>
<div class="number"> </div>
</div>
</div>
</a>


<a href="bienes_list.php">
<div class="tile bg-blue-steel">
<div class="tile-body">
<i class="fa fa-building-o"></i>
</div>
<div class="tile-object">
<div class="name"> Bienes </div>
<div class="number">  </div>
</div>
</div>
</a>



<a href="personal_list.php">
<div class="tile bg-green-jungle">
<div class="tile-body">
<i class="glyphicon glyphicon-user"></i>
</div>
<div class="tile-object">
<div class="name"> Personal </div>
<div class="number"> </div>
</div>
</div>
</a>


<a href="reportes.php">
<div class="tile bg-yellow-gold">
<div class="tile-body">
<i class="fa fa-pie-chart"></i>
</div>
<div class="tile-object">
<div class="name"> Reportes </div>
<div class="number"> </div>
</div>
</div>
</a>
<a href="proforma_add.php?codigo=<?php echo $_GET['codigo']; ?>">
<div class="tile bg-blue-ebonyclay">
<div class="tile-body">
<i class="fa fa-sliders"></i>
</div>
<div class="tile-object">
<div class="name"> Proformas </div>
<div class="number"> </div>
</div>
</div>
</a>

<a href="category_list.php">
<div class="tile bg-purple-studio">
<div class="tile-body">
<i class="fa fa-sitemap"></i>
</div>
<div class="tile-object">
<div class="name"> Clasificaci&oacute;n </div>
<div class="number">  </div>
</div>
</div>
</a> 




<a href="ventas_list.php">
<div class="tile bg-blue-steel">
<div class="tile-body">
<i class="fa fa-building-o"></i>
</div>
<div class="tile-object">
<div class="name"> Listado de Ventas </div>
<div class="number">  </div>
</div>
</div>
</a>








<a href="proveedor_list.php">
<div class="tile bg-blue-ebonyclay">
<div class="tile-body">
<i class="fa fa-sliders"></i>
</div>
<div class="tile-object">
<div class="name"> Proveedores </div>
<div class="number"> </div>
</div>
</div>
</a>




<a href="cliente_natural_list.php">
<div class="tile bg-green-jungle">
<div class="tile-body">
<i class="glyphicon glyphicon-user"></i>
</div>
<div class="tile-object">
<div class="name"> Clientes </div>
<div class="number"> </div>
</div>
</div>
</a>

<a href="abonos_list.php">
<div class="tile bg-blue-ebonyclay">
<div class="tile-body">
<i class="fa fa-sliders"></i>
</div>
<div class="tile-object">
<div class="name"> Listado de Abonos </div>
<div class="number"> </div>
</div>
</div>
</a>


<a href="service.php">
<div class="tile bg-green-jungle">
<div class="tile-body">
<i class="glyphicon glyphicon-user"></i>
</div>
<div class="tile-object">
<div class="name"> Servicios al Clientes </div>
<div class="number"> </div>
</div>
</div>
</a>


<a href="category_list.php">
<div class="tile bg-yellow-soft">
<div class="tile-body">
<i class="fa fa-cogs"></i>
</div>
<div class="tile-object">
<div class="name"> Manteniminetos </div>
<div class="number">  </div>
</div>
</div>
</a>  




<a href="">
<div class="tile bg-red-thunderbird">
<div class="tile-body">
<i class="fa fa-balance-scale"></i>
</div>
<div class="tile-object">
<div class="name"> Configuraciones </div>
<div class="number">  </div>
</div>
</div>
</a> 
</div>



      
                               
<?php 
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>
<?php
mysql_free_result($Contador_Clientes);

mysql_free_result($ContadorProveedor);

mysql_free_result($ContadorVentas);
?>
