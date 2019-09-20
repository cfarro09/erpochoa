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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Actualizar")) {
  $updateSQL = sprintf("UPDATE servicios SET nombre=%s, tipo=%s, monto=%s, observacion=%s WHERE codigosv=%s",
                       GetSQLValueString(strtoupper($_POST['nombre']), "text"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['monto'], "int"),
                       GetSQLValueString(strtoupper($_POST['observacion']), "text"),
                       GetSQLValueString($_POST['codigosv'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "service_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Actualiza = "-1";
if (isset($_GET['codigosv'])) {
  $colname_Actualiza = $_GET['codigosv'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Actualiza = sprintf("SELECT * FROM servicios WHERE codigosv = %s", GetSQLValueString($colname_Actualiza, "int"));
$Actualiza = mysql_query($query_Actualiza, $Ventas) or die(mysql_error());
$row_Actualiza = mysql_fetch_assoc($Actualiza);
$totalRows_Actualiza = mysql_num_rows($Actualiza);

 
$Icono="glyphicon glyphicon-list-alt";
$Color="font-blue";
$Titulo="Editar Servicios";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>

<script type="text/javascript"> 
function habilitar(obj) { 
  var hab; 
  frm=obj.form; 
  num=obj.selectedIndex; 
  if (num==1) hab=true; 
  else if (num==2) hab=false; 
  Actualizar.monto.disabled=hab; 
 
} 
</script> 

<form method="POST" action="<?php echo $editFormAction; ?>" name="Actualizar" id="Actualizar">

<table class="table table-hover table-light">

<tbody>
<tr>
  <td colspan="2"> 
  <div class="form-group">
  <div class="col-md-10">
  <div class="input-group"><span id="sprytextfield1">
    <input name="nombre" type="text" class="form-control" id="nombre" placeholder="Nombre del Servicio" value="<?php echo $row_Actualiza['nombre']; ?>" maxlength="200" />
    <span class="textfieldRequiredMsg"></span></span><span class="input-group-addon">
  <i class="fa fa-balance-scale font-blue-soft"></i>
  </span>
  </div>
  </div>
  </div>
  </td>
</tr>
<tr>
  <td>
  <div class="col-md-5">
<div class="form-group"><span id="spryselect1">
  <select name="tipo" id="tipo" class="form-control " onChange="habilitar(this)">
    <option value="" <?php if (!(strcmp("", $row_Actualiza['tipo']))) {echo "selected=\"selected\"";} ?>>--- Tipo de Servicio ---</option>
    <option value="0" <?php if (!(strcmp(0, $row_Actualiza['tipo']))) {echo "selected=\"selected\"";} ?>>A Pagar</option>
    <option value="1" <?php if (!(strcmp(1, $row_Actualiza['tipo']))) {echo "selected=\"selected\"";} ?>>A Ofrecer</option>
    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
  
  </td>
  <td>
  <div class="form-group">

<div class="col-md-5">
<div class="input-group"><span id="sprytextfield2">
<input name="monto" type="text"  class="form-control" id="monto" placeholder="Monto del Servicio" value="<?php echo $row_Actualiza['monto']; ?>" maxlength="5"/>
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon">
<i class="fa fa-dollar font-blue-soft"></i>
</span>
</div>
</div>
</div>
  
  </td>
</tr>
<tr>
<td colspan="2"><span id="sprytextarea1">
  <textarea name="observacion" id="observacion" rows="3" class="form-control" placeholder="Descripción u Observación para el Servicio"><?php echo $row_Actualiza['observacion']; ?></textarea>
  <span class="textareaRequiredMsg"></span></span></td>
</tr>
<tr>
<td> 

</td>
<td>

</td>

</tr>

</tbody>
</table>
<input name="codigosv" type="hidden" value="<?php echo $row_Actualiza['codigosv']; ?>" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />
<input type="hidden" name="MM_update" value="Actualizar" />   

</form>
 <?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Actualiza);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"], counterId:"countsprytextarea1", counterType:"chars_count"});
</script>
