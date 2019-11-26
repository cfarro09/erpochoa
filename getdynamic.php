<?php
require_once('Connections/Ventas.php');

$queryheader = "";
$querydetail = "";
if (isset($_POST['queryheader'])) {
	$queryheader = $_POST['queryheader'];
	//$codigotrans = $_GET['codigotrans'];
}
if (isset($_POST['querydetail'])) {
	$querydetail = $_POST['querydetail'];
	//$codigotrans = $_GET['codigotrans'];
}
mysql_select_db($database_Ventas, $Ventas);
if($queryheader != ""){
	$resultqueryheader = mysql_query($queryheader, $Ventas) or die("d21111" . mysql_error());
	$header = mysql_fetch_assoc($resultqueryheader);	
}

if($querydetail != ""){
	$resultqueryheader = mysql_query($queryheader, $Ventas) or die("d21111" . mysql_error());
	$header = mysql_fetch_assoc($resultqueryheader);	
}

$query_registrocompras = "select * from registro_compras rc inner join proveedor p on p.ruc=rc.rucproveedor inner join sucursal s on s.cod_sucursal=rc.codigosuc inner join acceso a on a.codacceso=rc.codacceso where rc.codigorc = $codigorc";
$resultregistrocompra = mysql_query($query_registrocompras, $Ventas) or die("d21111" . mysql_error());
$header = mysql_fetch_assoc($resultregistrocompra);

//se recomiendo no usar el * mejor select d.codigorc etc no uses el * ok
$query_detallecompras = "select * from detalle_compras d inner join producto p on p.codigoprod=d.codigoprod where codigocompras = $codigorc";
$detalle = mysql_query($query_detallecompras, $Ventas) or die(mysql_error());
$arraydetalle = array();
while ($row = mysql_fetch_assoc($detalle)) {
	array_push($arraydetalle, $row);
}
$resultado = array(
	"header" => $header,
	"detalle" => $arraydetalle

);;
die(json_encode($resultado, 128));
