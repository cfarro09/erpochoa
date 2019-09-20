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

if (isset($_POST['frase'])) {
  $frase = $_POST['frase'];
}
if (isset($_POST['type'])) {
  $type = $_POST['type'];
}
if (isset($_POST['id'])) {
  $id = $_POST['id'];
}
mysql_select_db($database_Ventas, $Ventas);
$queryUpdate = "update frases set selected = 0";
$sucursal = mysql_query($queryUpdate, $Ventas) or die(mysql_error());
if($type == "add"){
  $query_setSucursal = "insert into frases (frase, selected) values ('$frase', 1)";
}else{
  $query_setSucursal = "update frases set selected = 1 where id = $id";
}
  $sucursal = mysql_query($query_setSucursal, $Ventas) or die(mysql_error());

die(json_encode(array('success' => true), 128));

?>