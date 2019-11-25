<?php
require_once('Connections/Ventas.php');


if (isset($_GET['codigorc'])) {
  $codigorc = $_GET['codigorc'];
  //$codigotrans = $_GET['codigotrans'];
}

if (isset($_GET['codigotrans'])) {//bruto
  //$codigorc = $_GET['codigorc'];
  $codigotrans = $_GET['codigotrans'];
}

if (isset($_GET['codigonotad'])) {//bruto
  //$codigorc = $_GET['codigorc'];
  $codigonotad = $_GET['codigonotad'];
}

if (isset($_GET['codigonotac'])) {//bruto
  //$codigorc = $_GET['codigorc'];
  $codigonotac = $_GET['codigonotac'];
}

mysql_select_db($database_Ventas, $Ventas);

if(isset($codigorc)){
	$query_registrocompras = "select * from registro_compras rc inner join proveedor p on p.ruc=rc.rucproveedor inner join sucursal s on s.cod_sucursal=rc.codigosuc inner join acceso a on a.codacceso=rc.codacceso where rc.codigorc = $codigorc";
	$resultregistrocompra = mysql_query($query_registrocompras, $Ventas) or die("d21111" . mysql_error());
	$header = mysql_fetch_assoc($resultregistrocompra);

	//se recomiendo no usar el * mejor select d.codigorc etc no uses el * ok
	$query_detallecompras = "select * from detalle_compras d inner join producto p on p.codigoprod=d.codigoprod where codigocompras = $codigorc";
	$detalle = mysql_query($query_detallecompras, $Ventas) or die(mysql_error());
	$arraydetalle = array();
	while($row = mysql_fetch_assoc($detalle)){
	  array_push($arraydetalle, $row);
	}
	$resultado = array(
	  "header" => $header,
	  "detalle" => $arraydetalle

	);	;
	die(json_encode($resultado, 128));
}


if(isset($codigotrans)){
	$query_registrocompras = "select * from transporte_compra t inner join proveedor p on p.ruc=t.ructransporte where t.id_transporte = $codigotrans";
	$resultregistrocompra = mysql_query($query_registrocompras, $Ventas) or die("d21111" . mysql_error());
	$header = mysql_fetch_assoc($resultregistrocompra);

	$arraydetalle = array();

	$resultado = array(
	  "header" => $header

	);
die(json_encode($resultado, 128));
}

if(isset($codigonotad)){

	$query_registrocompras = "select * from notadebito_compra nd inner join proveedor p on p.ruc=nd.rucnd where nd.id_notadebito = $codigonotad";
	$resultregistrocompraa = mysql_query($query_registrocompras, $Ventas) or die("d21111" . mysql_error());
	$header = mysql_fetch_assoc($resultregistrocompraa);

	$arraydetalle = array();

	$resultado = array(
	  "header" => $header

	);
	die(json_encode($resultado, 128));
}
	



if(isset($codigonotac)){
	$query_registrocompras = "select * from notacredito_compra nc inner join proveedor p on p.ruc=nc.rucnotacredito where nc.id_notacredito = $codigonotac";
	$resultregistrocompraaa = mysql_query($query_registrocompras, $Ventas) or die("d21111" . mysql_error());
	$header = mysql_fetch_assoc($resultregistrocompraaa);

	$arraydetalle = array();

	$resultado = array(
	  "header" => $header

	);
	die(json_encode($resultado, 128));
}
	
