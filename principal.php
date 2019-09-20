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
$Titulo="Sedes Sucursales";
$Titulo="Sedes Sucursales";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$EstadoBotonAgregar="disabled";
//$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/cod_gen.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menusuc.php");





?>

<!-- BEGIN DASHBOARD STATS 1-->
<div class="row">
  <img src="assets/images/inca-kola-logo.jpg" width="200px" alt="">
</div>

<br>

<div class="row">
  
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