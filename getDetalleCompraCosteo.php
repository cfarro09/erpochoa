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

if (isset($_GET['type'])) {
  $type = $_GET['type'];
}
if (isset($_GET['codigo'])) {
  $codigo = $_GET['codigo'];
}
mysql_select_db($database_Ventas, $Ventas);
if(isset($type)){
  if($type == "ordencompra"){
    $firstheader = "SELECT c.codigoordcomp, c.direccion as direccionOrden, g.estado as estadoguia, s.nombre_sucursal,g.codigoguia,g.numeroguia as numero_guia, g.observacion ,c.codigo, c.subtotal, c.igv, c.montofact, c.fecha_emision, c.codigoproveedor, c.codigo, c.codigoref1, c.codigoref2, pe.nombre as nombrep, c.fecha_emision, pe.paterno as paternop, pe.materno as maternop, p.celular, p.ciudad, p.direccion, p.email, p.pais, p.paginaweb, p.telefono, p.ruc, p.razonsocial, a.usuario, c.sucursal FROM ordencompra c inner join acceso a on a.codacceso=c.codacceso inner join personal pe on pe.codigopersonal=c.codigopersonal inner join proveedor p on p.codigoproveedor=c.codigoproveedor left join ordencompra_guia g on g.codigoordcomp = c.codigoordcomp left join sucursal s on s.cod_sucursal = c.sucursal WHERE c.codigo = '$codigo'";
  }else{
    $firstheader = "SELECT c.codigo_guia_sin_oc, a.usuario,p.ruc, s.cod_sucursal as sucursal,s.nombre_sucursal, c.codigoref2,c.estado, c.numero_guia, p.razonsocial, p.codigoproveedor as codigoproveedor, c.fecha FROM guia_sin_oc c inner join proveedor p on c.codigoproveedor=p.codigoproveedor  left join sucursal s on s.cod_sucursal = c.sucursal left join acceso a on a.codacceso = c.codacceso where c.codigo_guia_sin_oc = $codigo";

  }
  $firstheader1 = mysql_query($firstheader, $Ventas) or die("d21111" . mysql_error());
  $headerx = mysql_fetch_assoc($firstheader1);
}else{
  $headerx = array();
}




$query_Factura_enc = "select * from  registro_compras where codigorc = $codigorc";

$Factura_enc = mysql_query($query_Factura_enc, $Ventas) or die("22222" . mysql_error());
$row_encabezado = mysql_fetch_assoc($Factura_enc);



$result_enc = array();

$query_detalle = "select m.nombre as marca, p.nombre_producto, m.nombre, d.* from detalle_compras d left join producto p on p.codigoprod = d.codigoprod left join marca m on m.codigomarca = p.codigomarca where d.codigocompras = $codigorc";

$detalle = mysql_query($query_detalle, $Ventas) or die(mysql_error());
$result = array();
while($res = mysql_fetch_assoc($detalle)){
	array_push($result, $res);
}
$res = array(
	"header" => $row_encabezado,
  "headerx" => $headerx,
  "detalle" => $result
);

die(json_encode($res, 128));

?>