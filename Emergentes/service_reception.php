<?php require_once('../Connections/Ventas.php'); ?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Ingresar")) {
  $insertSQL = sprintf("INSERT INTO hist_serv_enproceso (codigosv, codigosao, usuario_enproceso, personal_enproceso, fecha_enproceso, hora_enproceso, observacion_enproceso) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['codigosv'], "int"),
                       GetSQLValueString($_POST['codigosao'], "int"),
                       GetSQLValueString($_POST['usuario_enproceso'], "int"),
                       GetSQLValueString($_POST['personal_enproceso'], "int"),
                       GetSQLValueString($_POST['fecha_enproceso'], "date"),
                       GetSQLValueString($_POST['hora_enproceso'], "date"),
                       GetSQLValueString(strtoupper($_POST['observacion_enproceso']), "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

  $insertGoTo = "service_reception.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Ingresar")) {
  $updateSQL = sprintf("UPDATE serviciosaofrecer SET estado_servicio=%s WHERE codigosao=%s",
                       GetSQLValueString($_POST['estado_servicio'], "text"),
                       GetSQLValueString($_POST['codigosao'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
}

$colname_Servicio_Recepcion = "-1";
if (isset($_GET['codigosao'])) {
  $colname_Servicio_Recepcion = $_GET['codigosao'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Servicio_Recepcion = sprintf("SELECT codigosao, codigosv, estado_servicio FROM serviciosaofrecer WHERE codigosao = %s", GetSQLValueString($colname_Servicio_Recepcion, "int"));
$Servicio_Recepcion = mysql_query($query_Servicio_Recepcion, $Ventas) or die(mysql_error());
$row_Servicio_Recepcion = mysql_fetch_assoc($Servicio_Recepcion);
$totalRows_Servicio_Recepcion = mysql_num_rows($Servicio_Recepcion);

$Icono="fa fa-gears";
$Color="font-blue";
$Titulo="Servicio Recepción";
include("Fragmentos/cabecera.php");
include("../Fragmentos/abrirpopupcentro.php");

 ?>

<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<title></title>
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar" >

<table class="table table-hover table-light">

<tbody>



<tr>
<td>
<div class="form-group col-md-6">
<div class="input-group input-group-lg">
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Fecha de Inicio de Atenci&oacute;n" id="fecha_enproceso" name="fecha_enproceso" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo date('Y-m-d');?>"/>
<span class="input-group-btn">
<button class="btn green-jungle" type="button"><i class="fa fa-calendar"></i></button>
</span>
</div>
<!-- /input-group -->
</div>
</td>
<td>
<div class="form-group col-md-6">
<div class="input-group input-group-lg">
<input type="text" class="form-control tooltips" data-placement="top" data-original-title="Hora de Inicio de Atenci&oacute;n" id="hora_enproceso" name="hora_enproceso" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo date('h:i:s');?>"/>
<span class="input-group-btn">
<button class="btn green" type="button"><i class="fa fa-clock-o"></i></button>
</span>
</div>
<!-- /input-group -->
</div>

</td>
</tr>
<tr>
  <td colspan="2"><span id="sprytextarea1">
  <textarea name="observacion_enproceso" id="observacion_enproceso" rows="3" class="form-control tooltips" data-placement="top" data-original-title="Agregar Descripci&oacute;n u Observaci&oacute;n" placeholder="Descripción u Observación"></textarea>
<span class="textareaRequiredMsg"></span></span>
  
 </td>
  </tr>

</tbody>
</table>
<input name="codigosv" type="hidden" id="codigosv" value="<?php echo $row_Servicio_Recepcion['codigosv']; ?>" />
<input name="codigosao" type="hidden" id="codigosao" value="<?php echo $row_Servicio_Recepcion['codigosao']; ?>" />
<input name="estado_servicio" type="hidden" id="estado_servicio" value="P" />
<input name="usuario_enproceso" type="hidden" value="<?php echo $_SESSION['kt_login_id']; ?>" />
<input name="personal_enproceso" type="hidden" value="<?php echo $_SESSION['kt_codigopersonal']; ?>" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />
<input type="hidden" name="MM_update" value="Ingresar" />
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Servicio_Recepcion);
?>
<script>
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});


</script>
