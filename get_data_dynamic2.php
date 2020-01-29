<?php
header('Content-Type: text/html; charset=utf-8');

require_once('Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);


//  table
$query = $_POST['query'];

mysql_query("SET NAMES 'utf8'", $Ventas);

$query_res = mysql_query($query, $Ventas) or die(mysql_error());
$res = array();
try{
    while($fila = mysql_fetch_object($query_res)) {
        
        array_push($res, $fila);
    }
    die(json_encode($res, 128));

}catch(Exception $e){
    var_dump($e->getMessage());
}


