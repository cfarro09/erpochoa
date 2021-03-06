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
    $isproveedor  = 0; //is inventairo inicial
    if (strpos($header->codigoproveedor, 'inventario') || strpos($header->codigoproveedor, 'INVENTARIO') ) {
        $isproveedor = 1;
    }

  $insertCabecera = "insert into guia_sin_oc(codigoproveedor, codacceso, codigopersonal, sucursal, numero_guia, codigoref2, estado, tipodoc) values ($header->codigoproveedor, $header->codigoacceso , $header->codigopersonal, $header->codsucursal, '$header->numeroguia', '$header->codigoreferencia2', $header->estado, '$header->tipodoc')";
  $queryHeader = mysql_query($insertCabecera, $Ventas) or die(mysql_error());

  $lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
  $lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];

  foreach($detalleArray as $detalle){
    $insertDetalle = "insert into detalle_guia_sin_oc(codigo_guia_sin_oc, codigoprod, cantidad, unidad_medida, cantidad_aux) values ($lastId, $detalle->codigoprod,$detalle->cantidad, '$detalle->unidad_medida', $detalle->cantidad_aux)";
    $queryDetalle = mysql_query($insertDetalle, $Ventas) or die(mysql_error());
    

    $querylastsaldo = "select saldo from kardex_alm where codsucursal = $header->codsucursal and codigoprod = $detalle->codigoprod order by id_kardex_alm desc limit 1";
    $lastSaldo = mysql_query($querylastsaldo, $Ventas) or die(mysql_error());
    
    if($lastSaldo){
      $lastSaldo = (int) mysql_fetch_assoc($lastSaldo)["saldo"] + $detalle->cantidad;
    }else{
      $lastSaldo = $detalle->cantidad;
    }
    $insertkardex = "insert into kardex_alm(codigoprod, codigoguia,numero, detalle, cantidad, saldo, codsucursal, tipo, tipodocumento, isproveedor, detalleaux) values ($detalle->codigoprod, $lastId, '$header->numeroguia', 'compras', $detalle->cantidad, $lastSaldo, $header->codsucursal, 'soc', '$header->tipodoc', $isproveedor, '$header->desproveedor')";
    $querykardex = mysql_query($insertkardex, $Ventas) or die(mysql_error());

  }

  die(json_encode(array("success" => true), 128));
  
}



?>