<?php
header('Content-Type: text/html; charset=utf-8');

require_once('Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);


//  table
$query = $_POST['query'];

mysql_query("SET NAMES 'utf8'", $Ventas);

mysql_query($query, $Ventas) or die(mysql_error());
try{
    $res = array(
        "succes" => true
    );
}catch(Exception $e){
    $res = array(
        "succes" => true,
        "msg" => $e->getMessage()
    );
}
die(json_encode($res, 128));


