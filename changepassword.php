<?php
require_once('Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);

if (isset($_POST['passwordcurrent'])) {
	$passwordcurrent = $_POST['passwordcurrent'];
}
if (isset($_POST['passwordnew'])) {
	$passwordnew = $_POST['passwordnew'];
}
if (isset($_POST['codacceso'])) {
	$codacceso = $_POST['codacceso'];
}

$md5passwordcurrent = md5($passwordcurrent);
$md5passwordnew = md5($passwordnew);
$queryselectacceso = "select usuario from acceso where codacceso = $codacceso and clave = '$md5passwordcurrent'";

$resultselectacceso = mysql_query($queryselectacceso, $Ventas) or die(json_encode(array("success" => false, "msg" => json_encode(mysql_error()) . "  " . $querydetalle)));

$ismatch = mysql_num_rows($resultselectacceso) == 1;

if (!$ismatch) {
	die(json_encode(array("success" => false, "msg" => "La clave actual es incorrecto."), 128));	
}

$queryselectacceso = "update acceso set clave = '$md5passwordnew' where codacceso = $codacceso";

mysql_query($queryselectacceso, $Ventas) or die(json_encode(array("success" => false, "msg" => json_encode(mysql_error()) . "  " . $querydetalle)));

die(json_encode(array("success" => true, "msg" => ""), 128));
