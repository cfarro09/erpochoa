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

$query_Factura = "SELECT d.codigodetalleproducto, d.cantidad, d.codigoprod, p.nombre_producto, m.nombre, d.totalunidad, d.vcf,
IFNULL(pv.porcpv1, '') as porcpv1, IFNULL(pv.porcpv2, '') as porcpv2, IFNULL(pv.porcpv3, '') as porcpv3, IFNULL(pv.precioventa1, '') as precioventa1, IFNULL(pv.precioventa2, '') as precioventa2, IFNULL(pv.precioventa3, '') as precioventa3
from detalle_compras d 
left join producto p on p.codigoprod = d.codigoprod 
left join marca m on m.codigomarca = p.codigomarca 
left join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod =  d.codigoprod)
where d.codigocompras = $codigorc";

$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());
$result = array();
while($res = mysql_fetch_assoc($Factura)){
	array_push($result, $res);
}

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