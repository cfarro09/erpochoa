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

if (isset($_POST['detalle'])) {
  $detalle = $_POST['detalle'];
}
if (isset($_POST['transporte'])) {
  $transporte = $_POST['transporte'];
}
$ventatotal = $_POST['ventatotal'];
$codigocompras = $_POST['codigocompras'];
$detalle = json_decode($detalle);
$transporte = json_decode($transporte);

mysql_select_db($database_Ventas, $Ventas);

if($transporte){
  $aux = "insert into transporte_compra (tipocomprobante,  numerocomprobante, empresatransporte, preciotransporte,  codigocompras) values ('$transporte->tipocomprobante', '$transporte->numerocomprobante', '$transporte->empresatransporte',$transporte->precio_transporte, $codigocompras)";
  $queryDetalle = mysql_query($aux, $Ventas) or die(mysql_error());
}
$insertCabecera = "update compras set totalv = $ventatotal where codigocompras = $codigocompras";
$queryDetalle = mysql_query($insertCabecera, $Ventas) or die(mysql_error());

foreach($detalle as $de){
  $insertCabecera = "update detalle_compras set pventa = $de->pventa where codigodetalleproducto = $de->codigodetalleproducto";
  $queryDetalle = mysql_query($insertCabecera, $Ventas) or die(mysql_error());
}

die(json_encode(array("success" => true), 128));




?>