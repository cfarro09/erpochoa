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

if (isset($_GET['codigo'])) {
  $codigo = $_GET['codigo'];
}
mysql_select_db($database_Ventas, $Ventas);

$query_Factura_enc = "SELECT c.codigo_guia_sin_oc, a.usuario,p.ruc, s.cod_sucursal as sucursal,s.nombre_sucursal, c.codigoref2,c.estado, c.numero_guia, p.razonsocial, p.codigoproveedor as codigoproveedor, c.fecha FROM guia_sin_oc c inner join proveedor p on c.codigoproveedor=p.codigoproveedor  left join sucursal s on s.cod_sucursal = c.sucursal left join acceso a on a.codacceso = c.codacceso where c.codigo_guia_sin_oc = $codigo";


$Factura_enc = mysql_query($query_Factura_enc, $Ventas) or die(mysql_error());
$result_enc = array();
$row_encabezado = mysql_fetch_assoc($Factura_enc);

$query_detalle = "SELECT d.codigoprod, co.nombre_color AS color , IFNULL(p.minicodigo, '') minicodigo, p.nombre_producto, m.nombre as marca, d.cantidad, d.unidad_medida 
from detalle_guia_sin_oc d 
left join producto p on p.codigoprod = d.codigoprod 
left join  marca m on m.codigomarca = p.codigomarca  
INNER JOIN color co ON p.codigocolor = co.codigocolor 
where codigo_guia_sin_oc = $codigo";

//SELECT  ##consulta## WHERE c.codigo = %s", $codigo);
//SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, (a.cantidad*a.pcompra) AS total, a.cantidad, a.pcompra, a.concatenacion, a.codcomprobante, b.nombre_producto AS Producto, c.nombre AS Marca, d.nombre_presentacion AS Presentacion, e.nombre_color AS Color FROM detalle_compras_oc a INNER JOIN producto b ON a.codigoprod = b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN presentacion d ON b.codigopresent = d.codigopresent INNER JOIN color e ON b.codigocolor = e.codigocolor WHERE a.codigo = '$colname_Listado_Productos'  group by a.codigoprod

$Factura = mysql_query($query_detalle, $Ventas) or die(mysql_error());
$result = array();
while($res = mysql_fetch_assoc($Factura)){
	array_push($result, $res);
}
$res = array(
	"header" => $row_encabezado,
	"detalle" => $result
);
// $json  = json_encode(utf8ize($res));

// $error = json_last_error();

// var_dump($json, $error === JSON_ERROR_UTF8);

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

die(json_encode(utf8ize($res)));

?>