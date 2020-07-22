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

if (isset($_GET['codigorc'])) {
  $codigorc = $_GET['codigorc'];
}
mysql_select_db($database_Ventas, $Ventas);

$query_Factura = "select pv.*, dc.cantidad, m.nombre as marca, p.nombre_producto from precio_venta pv left join detalle_compras dc on dc.codigodetalleproducto = pv.codigodetalleproducto left join producto p on pv.codigoprod = p.codigoprod left join marca m on m.codigomarca = p.codigomarca where pv.codigocompras = $codigorc " ;

$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());
$result = array();
while($res = mysql_fetch_assoc($Factura)){
	array_push($result, $res);
}
//$row_Factura = mysql_fetch_assoc($Factura);
//$totalRows_Factura = mysql_num_rows($Factura);
function utf8ize($d) {
  if (is_array($d)) {
      foreach ($d as $k => $v) {
          $d[$k] = utf8ize($v);
      }
  } else if (is_string ($d)) {
      return utf8_encode($d);
  }
  return $d;
}

die(json_encode(utf8ize($result)));

?>