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

$h = json_decode($json)->header;
$detalleArray = json_decode($json)->detalle;
$gastos = json_decode($json)->gastos;

mysql_select_db($database_Ventas, $Ventas);

if($h->codigocompras){
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
  $q_igv = "SELECT IFNULL(max(contamesincrement), 0) maxcodigo FROM registro_compras WHERE fecha BETWEEN '$h->firstday' and LAST_DAY('$h->firstday')";
  $result = mysql_query($q_igv, $Ventas) or die(mysql_error());
  $maxcodigo = (int) mysql_fetch_object($result)->maxcodigo + 1;

  $contamaxcodigo = $h->codigomesconta.$maxcodigo;
  $insertCabecera = "insert into registro_compras(tipomoneda, tipo_comprobante, rucproveedor, numerocomprobante, codacceso, subtotal, igv, total, estadofact, codigosuc, codigo_orden_compra, codigo_guia_sin_oc, fecha_registro, valorcambio, descuentocompras, codigoproveedor, codigomesconta, contamesincrement) values ('$h->tipomoneda', '$h->tipo_comprobante', '$h->ruc_proveedor', '$h->numerocomprobante', $h->codacceso, $h->subtotal, $h->igv, $h->total, $h->estadofact, $h->codigosuc, $h->codigo_orden_compra, $h->codigo_guia_sin_oc, '$h->fecha_registro', $h->valorcambio, $h->descuentocompras, $h->codigoproveedor, '$contamaxcodigo', $maxcodigo)";

  $queryHeader = mysql_query($insertCabecera, $Ventas) or die(mysql_error());

  $lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
  $lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];

  foreach($gastos as $gasto){
    $gastoquery = str_replace("##IDCOMPRAS##", $lastId, $gasto);
    mysql_query($gastoquery, $Ventas) or die(mysql_error());

  }
  $varlocambio = 1;
  if ($h->tipomoneda === 'dolares') {
    $varlocambio = $h->valorcambio;
  }
  
  foreach($detalleArray as $d){
    
    $insertDetalle = "insert into detalle_compras(codigoprod, cantidad, descxitem, vcu, vci, descmonto, vcf, igv, totalcompra, peso, preciotransporte, precioestibador, notadebito, precionotacredito, totalconadicionales, totalunidad, codigocompras) values ($d->codigoprod,$d->cantidad,$d->descuento,$d->vcu,$d->vci,$d->descmonto,$d->vcf,$d->igv,$d->totalcompra,$d->peso,$d->preciotransporte,$d->precioestibador,$d->notadebito,$d->precionotacredito,$d->totalconadicionales,$d->totalunidad, $lastId)";
    $queryDetalle = mysql_query($insertDetalle, $Ventas) or die("detalle : ".mysql_error());

    $lastIddetalle = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
    $lastIddetalle = (int) mysql_fetch_assoc($lastIddetalle)["LAST_INSERT_ID()"];

    $pventa = $d->descuento == 0 ? $d->vcu : $d->vcu * (100 - $d->descuento)/100;
    $pventa = $pventa * 1.18 * $varlocambio;
    
    $insertpv = "insert into precio_venta (codacceso, tipo_asignar_venta, codigodetalleproducto, codigoprod, vcf, totalunidad, porcpv1, precioventa1, porcpv2, precioventa2, porcpv3, precioventa3, codigocompras, initial)
    values ($h->codacceso, 1, $lastIddetalle, $d->codigoprod, $d->vcu, $d->vcu, 0, $pventa, 0, $pventa, 0, $pventa, $lastId, false)";
    mysql_query($insertpv, $Ventas) or die("pv $insertpv" . mysql_error());

    $validate = "select * from kardex_contable where codigoprod = $d->codigoprod and sucursal = $h->codigosuc order by id_kardex_contable desc limit 1";
    $res = mysql_query($validate, $Ventas) or die("kardex ".mysql_error());
    $row_Listado1 = mysql_fetch_assoc($res);

    if($row_Listado1)
    {
      $newcantidad = $row_Listado1['saldo'] + $d->cantidad;
      $xx = "insert into kardex_contable(codigoprod, fecha, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, preciototal, tipocomprobante, codigoproveedor) values ($d->codigoprod, '$h->fecha_registro', $lastId, '$h->numerocomprobante', 'Compras', $d->cantidad, $d->totalcompra, $newcantidad, $h->codigosuc, $d->totalcompra, '$h->tipo_comprobante', $h->codigoproveedor)";
    }
    else
    {
      $xx = "insert into kardex_contable(codigoprod, fecha, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, preciototal, tipocomprobante, codigoproveedor) values ($d->codigoprod, '$h->fecha_registro', $lastId, '$h->numerocomprobante', 'Compras', $d->cantidad, $d->totalunidad, $d->cantidad, $h->codigosuc, $d->totalcompra, '$h->tipo_comprobante', $h->codigoproveedor)";
    }
    mysql_query($xx, $Ventas) or die("ddd".mysql_error());
  }

  die(json_encode(array("success" => true), 128));
  
}
