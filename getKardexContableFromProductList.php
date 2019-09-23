<?php
require_once('Connections/Ventas.php');
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

if (isset($_GET['codigocompras'])) {
  $codigocompras = $_GET['codigocompras'];
}
$codsucursal = $_POST["codsucursal"];
$fecha_inicio = $_POST["fecha_inicio"];
$fecha_termino = $_POST["fecha_termino"];
$codproducto = $_POST["codproducto"];

mysql_select_db($database_Ventas, $Ventas);

$queryKardex = "select * from kardex_contable where codigoprod = $codproducto and sucursal = $codsucursal and fecha BETWEEN '$fecha_inicio' AND '$fecha_termino'";

$Factura = mysql_query($queryKardex, $Ventas) or die(mysql_error());
$result = array();
while($res = mysql_fetch_assoc($Factura)){
  
	array_push($result, $res);
}
die(json_encode($result, 128));

?>