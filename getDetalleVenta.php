<?php
require_once('Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);
$detalle = array();

if (isset($_GET['id'])) {
  $id = $_GET['id'];
}
$querydetalle = "
  select dv.*, p.nombre_producto, prr.nombre_presentacion, m.nombre as marca from detalle_ventas dv
  inner join producto p on p.codigoprod = dv.codigoprod
  inner join marca m on m.codigomarca = p.codigomarca
  inner join presentacion prr on prr.codigopresent = p.codigopresent
  where codigoventa = $id
";

$resultquery = mysql_query($querydetalle, $Ventas) or die(mysql_error());
while($res = mysql_fetch_assoc($resultquery)){
  array_push($detalle, $res);
}

die(json_encode($detalle, 128));





?>