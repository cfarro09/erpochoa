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
$gastos = json_decode($json)->gastos;

mysql_select_db($database_Ventas, $Ventas);

if($header->codigocompras){
  // $update1 = "update ordencompra_guia set numeroguia = $header->numeroguia, estado = $header->estado, observacion = '$header->observacion' where codigoguia = $header->codigoguia";
  //   $queryC = mysql_query($update1, $Ventas) or die(mysql_error());
  
  // foreach($detalleArray as $detalle){
  //   $update2 = "update detalle_guia_oc set cant_recibida = $detalle->cantidad_recibida where codigo_guiaoc = $detalle->codigo_guiaoc";
  //   $queryDetalle = mysql_query($update2  , $Ventas) or die(mysql_error());

  //   $insertkardex = "insert into kardex_alm(codigoprod, codigoguia,numero, detalle, cantidad, saldo) values ($detalle->codigoprod, $detalle->codigo_guiaoc, $header->numeroguia, 'compras', $detalle->cantidad_recibida, $detalle->saldo)";
  //   $querykardex = mysql_query($insertkardex, $Ventas) or die(mysql_error());
  // }

  // die(json_encode(array("success" => true), 128));

}else{
  $insertCabecera = "insert into compras(tipomoneda, tipo_comprobante, codigoproveedor, numerofactura, codacceso, codigopersonal, subtotal, igv, total, estadofact, totalv, codigosuc, codigo_orden_compra, codigo_guia_sin_oc, tipocambio, descuento) values ('$header->moneda', '$header->tipocomprobante', $header->codigoproveedor, '$header->nrocomprobante', $header->codacceso, $header->codigopersonal, $header->subtotal, $header->igv, $header->total, $header->estadofact, $header->totalv, $header->codsucursal, $header->codigo_orden_compra, $header->codigo_guia_sin_oc, $header->tipocambio, $header->descuento)";
  $queryHeader = mysql_query($insertCabecera, $Ventas) or die(mysql_error());

  $lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
  $lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];

  foreach($gastos as $gasto){
    $gastoquery = str_replace("##IDCOMPRAS##", $lastId, $gasto);
    mysql_query($gastoquery, $Ventas) or die(mysql_error());

  }

  foreach($detalleArray as $detalle){
    $insertDetalle = "insert into detalle_compras(codigoprod, cantidad, pventa, pcompra, igv, totalcompras, codigocompras, pcompradolar) values ($detalle->codigoprod, $detalle->cantidad, $detalle->pventa, $detalle->pcompra, $detalle->igv, $detalle->totalcompras, $lastId, $detalle->preciodolar)";
    $queryDetalle = mysql_query($insertDetalle, $Ventas) or die(mysql_error());

    $validate = "select * from kardex_contable where codigoprod = $detalle->codigoprod and sucursal = $header->codsucursal order by id_kardex_contable desc limit 1";
    $res = mysql_query($validate, $Ventas) or die(mysql_error());
    $row_Listado1 = mysql_fetch_assoc($res);

    if($row_Listado1){
      $newcantidad = $row_Listado1['cantidad'] + $detalle->cantidad;
      $xx = "insert into kardex_contable(codigoprod, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, tipocomprobante) values ($detalle->codigoprod, $lastId, '$header->nrocomprobante', 'Compras', $detalle->cantidad, $detalle->totalcompras, $newcantidad, $header->codsucursal, '$header->tipocomprobante')";
    }else{
      $xx = "insert into kardex_contable(codigoprod, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, tipocomprobante) values ($detalle->codigoprod, $lastId, '$header->nrocomprobante', 'Compras', $detalle->cantidad, $detalle->totalcompras, $detalle->cantidad, $header->codsucursal, '$header->tipocomprobante')";
    }
    $queryDetalle = mysql_query($xx, $Ventas) or die(mysql_error());
  }

  die(json_encode(array("success" => true), 128));
  
}



?>