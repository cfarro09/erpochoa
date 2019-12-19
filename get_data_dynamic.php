<?php
require_once('Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);

//  table
$query = $_POST['query'];

$query_res = mysql_query($query, $Ventas) or die(mysql_error());
$res = array();

while($fila = mysql_fetch_array($query_res, MYSQL_NUM)) {
    array_push($res,$fila);
}

die(json_encode($res, 128));

