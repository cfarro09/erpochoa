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
if (isset($_GET['estado'])) {
  $estado = $_GET['estado'];
}
mysql_select_db($database_Ventas, $Ventas);

$query_Factura = "UPDATE ordencompra SET estado = $estado WHERE codigo='$codigo'";

//SELECT  ##consulta## WHERE c.codigo = %s", $codigo);
//SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, (a.cantidad*a.pcompra) AS total, a.cantidad, a.pcompra, a.concatenacion, a.codcomprobante, b.nombre_producto AS Producto, c.nombre AS Marca, d.nombre_presentacion AS Presentacion, e.nombre_color AS Color FROM detalle_compras_oc a INNER JOIN producto b ON a.codigoprod = b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN presentacion d ON b.codigopresent = d.codigopresent INNER JOIN color e ON b.codigocolor = e.codigocolor WHERE a.codigo = '$colname_Listado_Productos'  group by a.codigoprod

$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());

//$row_Factura = mysql_fetch_assoc($Factura);
//$totalRows_Factura = mysql_num_rows($Factura);

die(json_encode(array("success"	), 128));

?>