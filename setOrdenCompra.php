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

if(false){
  $update1 = "update ordencompra_guia set estado = $header->estado where codigoguia = $header->codigoguia";
    $queryC = mysql_query($update1, $Ventas) or die(mysql_error());
  
  foreach($detalleArray as $detalle){
     $update2 = "update detalle_guia_oc set cant_recibida = $detalle->cantidad_recibida where codigo_guiaoc = $detalle->codigo_guiaoc";
    $queryDetalle = mysql_query($update2  , $Ventas) or die(mysql_error());

    $insertkardex = "insert into kardex_alm(codigoprod, codigoguia,numero, detalle, cantidad, saldo, codsucursal, tipo, tipodocumento, detalleaux) values ($detalle->codigoprod, $header->codigoguia, '$header->numeroguia', 'compras', $detalle->cantidad_kardex, IFNULL((select k1.saldo   from kardex_alm k1 where k1.codsucursal = $header->codsucursal and k1.codigoprod = $detalle->codigoprod order by k1.id_kardex_alm desc limit 1), 0) + $detalle->cantidad_kardex, $header->codsucursal, 'oc', '$header->tipodocalmacen', '$header->detalleaux')";
    $querykardex = mysql_query($insertkardex, $Ventas) or die(mysql_error());
  }

  die(json_encode(array("success" => true), 128));

}else{
   $insertCabecera = "insert into ordencompra_guia(codigoordcomp, codigoacceso, numeroguia, estado, observacion, tipodocalmacen) values ($header->codigoordcomp, $header->codigoacceso , '$header->numeroguia', $header->estado, '$header->observacion', '$header->tipodocalmacen')";

   $queryHeader = mysql_query($insertCabecera, $Ventas) or die(mysql_error());

   if ($header->estado == "3" || $header->estado == "2") {
    $query1 = "UPDATE ordencompra_guia set estado = $header->estado where codigoordcomp = $header->codigoordcomp";
    mysql_query($query1, $Ventas) or die(mysql_error());
   }

  $lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
  $lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];

  foreach($detalleArray as $detalle){
    $insertDetalle = "insert into detalle_guia_oc(codigo, codigoprod, cantidad, cant_recibida, codacceso) values ($lastId,$detalle->codigoprod,$detalle->cantidad,$detalle->cantidad_recibida, $header->codigoacceso)";
     $queryDetalle = mysql_query($insertDetalle, $Ventas) or die(mysql_error());

    $insertkardex = "
      insert into kardex_alm(codigoprod, codigoguia,numero, detalle, cantidad, saldo, codsucursal, tipo, tipodocumento, detalleaux) 
      values ($detalle->codigoprod, $lastId, '$header->numeroguia', 'compras', $detalle->cantidad_kardex, IFNULL((select k1.saldo  from kardex_alm k1 where k1.codsucursal = $header->codsucursal and k1.codigoprod = $detalle->codigoprod order by k1.id_kardex_alm desc limit 1), 0) + $detalle->cantidad_kardex, $header->codsucursal, 'oc', '$header->tipodocalmacen', '$header->detalleaux')";

    $querykardex = mysql_query($insertkardex, $Ventas) or die(mysql_error());
  }
  die(json_encode(array("success" => true), 128)); 
}
?>