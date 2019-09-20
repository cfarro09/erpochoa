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
mysql_select_db($database_Ventas, $Ventas);

if (isset($_POST['json'])) {
  $json = $_POST['json'];
}
$data = json_decode($json);

$accesos = json_encode($data->accesos);
if($data->cod_acceso_seguridad){
  $data->cod_acceso_seguridad = (int) $data->cod_acceso_seguridad;
  $insertCabecera = "update acceso_seguridad set acceso = '$accesos', cod_sucursal = $data->sucursal where cod_acceso_seguridad = $data->cod_acceso_seguridad";
}else{
  $insertCabecera = "insert into acceso_seguridad (acceso, cod_sucursal, personal) values ('$accesos', $data->sucursal, $data->personal)";
}
$queryHeader = mysql_query($insertCabecera, $Ventas) or die(mysql_error());


die(json_encode(array("success" => true), 128));





?>