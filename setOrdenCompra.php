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

if (isset($_POST['json'])) {
  $json = $_POST['json'];
}

$header = json_decode($json)->header;
$detalleArray = json_decode($json)->detalle;

mysql_select_db($database_Ventas, $Ventas);

if($header->codigoguia){
  $update1 = "update ordencompra_guia set estado = $header->estado where codigoguia = $header->codigoguia";
    $queryC = mysql_query($update1, $Ventas) or die(mysql_error());
  
  foreach($detalleArray as $detalle){
     $update2 = "update detalle_guia_oc set cant_recibida = $detalle->cantidad_recibida where codigo_guiaoc = $detalle->codigo_guiaoc";
    $queryDetalle = mysql_query($update2  , $Ventas) or die(mysql_error());

    $querylastsaldo = "select saldo from kardex_alm where codsucursal = $header->codsucursal and codigoprod = $detalle->codigoprod order by fecha desc limit 1";
    $lastSaldo = mysql_query($querylastsaldo, $Ventas) or die(mysql_error());
    if($lastSaldo){
      $lastSaldo = (int) mysql_fetch_assoc($lastSaldo)["saldo"] + $detalle->cantidad_kardex;
    }else{
      $lastSaldo = $detalle->cantidad_kardex;
    }

    $insertkardex = "insert into kardex_alm(codigoprod, codigoguia,numero, detalle, cantidad, saldo, codsucursal, tipo) values ($detalle->codigoprod, $detalle->codigo_guiaoc, '$header->numeroguia', 'compras', $detalle->cantidad_kardex, $lastSaldo, $header->codsucursal, 'oc')";
    $querykardex = mysql_query($insertkardex, $Ventas) or die(mysql_error());
  }

  die(json_encode(array("success" => true), 128));

}else{
   $insertCabecera = "insert into ordencompra_guia(codigoordcomp, codigoacceso, numeroguia, estado, observacion) values ($header->codigoordcomp, $header->codigoacceso , '$header->numeroguia', $header->estado, '$header->observacion')";
   $queryHeader = mysql_query($insertCabecera, $Ventas) or die(mysql_error());

  $lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
  $lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];

  foreach($detalleArray as $detalle){
    $insertDetalle = "insert into detalle_guia_oc(codigo, codigoprod, cantidad, cant_recibida, codacceso) values ($lastId,$detalle->codigoprod,$detalle->cantidad,$detalle->cantidad_recibida, $header->codigoacceso)";
     $queryDetalle = mysql_query($insertDetalle, $Ventas) or die(mysql_error());

    $querylastsaldo = "select saldo from kardex_alm where codsucursal = $header->codsucursal and codigoprod = $detalle->codigoprod order by fecha desc limit 1";
    $lastSaldo = mysql_query($querylastsaldo, $Ventas) or die(mysql_error());
    if($lastSaldo){
      $lastSaldo = (int) mysql_fetch_assoc($lastSaldo)["saldo"] + $detalle->cantidad_kardex;
    }else{
      $lastSaldo = $detalle->cantidad_kardex;
    }

    $insertkardex = "insert into kardex_alm(codigoprod, codigoguia,numero, detalle, cantidad, saldo, codsucursal, tipo) values ($detalle->codigoprod, $detalle->codigo_guiaoc, '$header->numeroguia', 'compras', $detalle->cantidad_kardex, $lastSaldo, $header->codsucursal, 'oc')";
    $querykardex = mysql_query($insertkardex, $Ventas) or die(mysql_error());


  }

  die(json_encode(array("success" => true), 128));
  
}



?>