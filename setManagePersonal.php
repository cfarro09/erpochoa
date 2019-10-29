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

if (isset($_POST['json'])) {
  $json = $_POST['json'];
}

$header = json_decode($json);

mysql_select_db($database_Ventas, $Ventas);
  $update1 = "insert into personal_vacaciones (codigopersonal, fecha_inicio, fecha_fin, observacion, periodo, codacceso, tipo, estado) values ($header->codigopersonal, '$header->fecha_inicio', '$header->fecha_fin', '$header->observacion', $header->periodo, $header->codigoacceso, '$header->type_manage', 0)";
  $queryC = mysql_query($update1, $Ventas) or die(mysql_error());
  
  die(json_encode(array("success" => true), 128));

?>