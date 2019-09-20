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

$query_Factura_enc = "SELECT c.codigoordcomp, c.direccion as direccionOrden, g.estado as estadoguia, s.nombre_sucursal,g.codigoguia,g.numeroguia, g.observacion ,c.codigo, c.subtotal, c.igv, c.montofact, c.fecha_emision, c.codigoproveedor, c.codigo, c.codigoref1, c.codigoref2, pe.nombre as nombrep, c.fecha_emision, pe.paterno as paternop, pe.materno as maternop, p.celular, p.ciudad, p.direccion, p.email, p.pais, p.paginaweb, p.telefono, p.ruc, p.razonsocial, a.usuario, c.sucursal FROM ordencompra c inner join acceso a on a.codacceso=c.codacceso inner join personal pe on pe.codigopersonal=c.codigopersonal inner join proveedor p on p.codigoproveedor=c.codigoproveedor left join ordencompra_guia g on g.codigoordcomp = c.codigoordcomp left join sucursal s on s.cod_sucursal = c.sucursal WHERE c.codigo = '$codigo'";


$Factura_enc = mysql_query($query_Factura_enc, $Ventas) or die(mysql_error());
$result_enc = array();
$row_encabezado = mysql_fetch_assoc($Factura_enc);

$query_Factura = "SELECT dgoc.cant_recibida, dgoc.codigo_guiaoc as detalle_cod_oc_guia, m.nombre as marca, pr.nombre_producto, oc.codigoordcomp , doc.codigoprod, doc.cantidad, doc.pcompra, doc.igv, doc.totalcompras, doc.unidad_medida from detalle_compras_oc doc left join ordencompra oc on oc.codigo = doc.codigo left join ordencompra_guia ocg on ocg.codigoordcomp = oc.codigoordcomp left join detalle_guia_oc dgoc on dgoc.codigo = ocg.codigoguia and dgoc.codigoprod = doc.codigoprod left join producto pr on pr.codigoprod = doc.codigoprod left join marca m on m.codigomarca =  pr.codigomarca where doc.codigo = '$codigo'";

//ESE ES EL DETALLE Q MUESTRA, COMO LO UNES AL DETALLE DE LA GUIA

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
//$row_Factura = mysql_fetch_assoc($Factura);
//$totalRows_Factura = mysql_num_rows($Factura);

die(json_encode($res, 128));

?>