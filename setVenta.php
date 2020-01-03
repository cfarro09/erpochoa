<?php
require_once('Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);
if (isset($_POST['json'])) {
	$json = $_POST['json'];
}
$lastId = "";
$queryheader = json_decode($json)->header;
$detalleArray = json_decode($json)->detalle;

if($queryheader){
	mysql_query($queryheader, $Ventas) or die(json_encode(array("success" => false, "msg" => json_encode(mysql_error()) . "  " . $queryheader)));
	$lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(json_encode(array("success" => false, "msg" => json_encode(mysql_error()) . "  ". $queryheader)));
	$lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];
}

foreach ($detalleArray as $querydetalle) {
	$querydetalle = str_replace("###ID###", $lastId, $querydetalle);
	mysql_query($querydetalle, $Ventas) or die(json_encode(array("success" => false, "msg" => json_encode(mysql_error()) . "  " . $querydetalle)));
}

die(json_encode(array("success" => true), 128));
