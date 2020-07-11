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

$query_Factura_enc = "SELECT c.codigoordcomp, c.direccion as direccionOrden, c.codigo, c.subtotal, s.nombre_sucursal,c.igv, c.montofact, c.fecha_emision, c.codigoproveedor, c.codigo, c.codigoref1, c.codigoref2, pe.nombre as nombrep, c.fecha_emision, pe.paterno as paternop, pe.materno as maternop, p.celular, p.ciudad, p.direccion, p.email, p.pais, p.paginaweb, p.telefono, p.ruc, p.razonsocial, a.usuario, c.sucursal FROM ordencompra c inner join acceso a on a.codacceso=c.codacceso inner join personal pe on pe.codigopersonal=c.codigopersonal inner join proveedor p on p.codigoproveedor=c.codigoproveedor left join sucursal s on s.cod_sucursal = c.sucursal  WHERE c.codigo = '$codigo'";


$Factura_enc = mysql_query($query_Factura_enc, $Ventas) or die(mysql_error());
$result_enc = array();
$row_encabezado = mysql_fetch_assoc($Factura_enc);


$query_Factura = "SELECT a.codigodetalleproducto, b.minicodigo, a.codigo, a.codigoprod, a.unidad_medida,(a.cantidad*a.pcompra) AS total, a.cantidad, a.pcompra, a.concatenacion, a.codcomprobante, b.nombre_producto AS Producto, c.nombre AS Marca, d.nombre_presentacion AS Presentacion, e.nombre_color AS Color FROM detalle_compras_oc a INNER JOIN producto b ON a.codigoprod = b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN presentacion d ON b.codigopresent = d.codigopresent INNER JOIN color e ON b.codigocolor = e.codigocolor WHERE a.codigo = '$codigo'  group by a.codigoprod";

//SELECT  ##consulta## WHERE c.codigo = %s", $codigo);
//SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, (a.cantidad*a.pcompra) AS total, a.cantidad, a.pcompra, a.concatenacion, a.codcomprobante, b.nombre_producto AS Producto, c.nombre AS Marca, d.nombre_presentacion AS Presentacion, e.nombre_color AS Color FROM detalle_compras_oc a INNER JOIN producto b ON a.codigoprod = b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN presentacion d ON b.codigopresent = d.codigopresent INNER JOIN color e ON b.codigocolor = e.codigocolor WHERE a.codigo = '$colname_Listado_Productos'  group by a.codigoprod

$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());
$result = array();
while($res = mysql_fetch_assoc($Factura)){
	array_push($result, $res);
}
$res = array(
	"header" => $row_encabezado,
	"detalle" => $result
);

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