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


if (isset($_POST['exearray'])) {
  $exearray = $_POST['exearray'];
}
$exearray = json_decode($exearray);
foreach($exearray as $de){
  mysql_query($de, $Ventas) or die(mysql_error());
}

if (isset($_POST['querys'])) {
  $querys = $_POST['querys'];
  $querys = json_decode($querys);

  $lastId = mysql_query("SELECT LAST_INSERT_ID()", $Ventas) or die(mysql_error());
  $lastId = (int) mysql_fetch_assoc($lastId)["LAST_INSERT_ID()"];

  foreach ($querys as $query) {
    $query = str_replace("##ID##", $lastId, $query);
    mysql_query($query, $Ventas) or die(mysql_error());
  }
}

die(json_encode(array("success" => true), 128));




?>