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
$header = null;
$arraydetalle = array();

mysql_select_db($database_Ventas, $Ventas);
if($queryheader != ""){
	$resultqueryheader = mysql_query($queryheader, $Ventas) or die("d21111" . mysql_error());
	$header = mysql_fetch_assoc($resultqueryheader);	
}

if($querydetail != ""){
	$resultquerydetail = mysql_query($querydetail, $Ventas) or die("d21111" . mysql_error());
	$detail = mysql_fetch_assoc($resultquerydetail);
	while ($row = mysql_fetch_assoc($detail)) {
		array_push($arraydetalle, $row);
	}
}
$resultado = array(
	"header" => $header,
	"detalle" => $arraydetalle

);
die(json_encode($resultado, 128));
