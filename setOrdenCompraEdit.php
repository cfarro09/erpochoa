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
  // codigodetalle
}else{
  $insertCabecera = "
  update ordencompra set
  codigoproveedor = $header->codigoproveedor,
 fecha_emision = '$header->fecha_emision' ,
 hora_emision = '$header->hora_emision' ,
 codigopersonal = $header->codigopersonal ,
 subtotal = $header->subtotal ,
 igv = $header->igv ,
 montofact = $header->montofact ,
 estadofact = $header->estadofact ,
 sucursal = $header->sucursal ,
 codigoref1 = '$header->codigoref1' ,
 codigoref2 = '$header->codigoref2' ,
 estado = $header->estado ,
 direccion = '$header->direccion'
 where codigo = '$header->codigo'";

 $queryHeader = mysql_query($insertCabecera, $Ventas) or die(mysql_error());

 $queryDetalle = mysql_query("delete from detalle_compras_oc where codigo = '$header->codigo'", $Ventas) or die(mysql_error());
 
  foreach($detalleArray as $detalle){
      $insertDetalle = "insert into detalle_compras_oc(codigo, codigoprod, cantidad, concatenacion, pcompra, igv, totalcompras, unidad_medida) values ('$header->codigo', $detalle->codigoprod, $detalle->cantidad, '$detalle->concatenacion', $detalle->pcompra, $detalle->igv, $detalle->totalcompras, '$detalle->unidad_medida')";

      $queryDetalle = mysql_query($insertDetalle, $Ventas) or die(mysql_error());
    
    


  }

  die(json_encode(array("success" => true), 128));
  
}
