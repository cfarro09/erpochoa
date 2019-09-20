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
mysql_select_db($database_Ventas, $Ventas);

$query_Factura = "select d.codigodetalleproducto, d.cantidad, p.nombre_producto, m.nombre, d.pcompra, ps.precio_venta from detalle_compras d left join producto p on p.codigoprod = d.codigoprod left join marca m on m.codigomarca = p.codigomarca left join producto_stock ps on ps.codigoprod = d.codigoprod where d.codigocompras = $codigocompras";

$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());
$result = array();
while($res = mysql_fetch_assoc($Factura)){
  
	array_push($result, $res);
}
//$row_Factura = mysql_fetch_assoc($Factura);
//$totalRows_Factura = mysql_num_rows($Factura);

die(json_encode($result, 128));

?>