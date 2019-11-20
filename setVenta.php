<?php
require_once('Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);

if (isset($_POST['json'])) {
	$json = $_POST['json'];
}
$queryheader = json_decode($json)->header;
$detalleArray = json_decode($json)->detalle;

mysql_query($queryheader, $Ventas) or die(mysql_error());
$lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
$lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];

foreach ($detalleArray as $querydetalle) {
	$querydetalle = str_replace("###ID###", $lastId, $querydetalle);
	mysql_query($querydetalle, $Ventas) or die(mysql_error());
}

die(json_encode(array("success" => true), 128));
