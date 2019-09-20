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
  $insertSQL = sprintf("INSERT INTO servicios (nombre, tipo, monto, observacion) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString(strtoupper($_POST['nombre']), "text"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['monto'], "double"),
                       GetSQLValueString(strtoupper($_POST['observacion']), "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());
  
  
  if($_POST['tipo']==1){
    
		$rs = mysql_query("SELECT MAX(codigosv) AS id FROM servicios");
		if ($row = mysql_fetch_row($rs)) {
			$id = $row[0];
		}

  		
    $insertSQL5 = sprintf("INSERT INTO producto (nombre_producto, codigomarca, codigocat, codigosubcat, codigocolor, codigopresent,obs) VALUES (%s, %s, %s, %s, %s, %s, %s)",
					   GetSQLValueString(strtoupper($_POST['nombre']), "text"),
                      GetSQLValueString(1, "int"),
                       GetSQLValueString(1, "int"),
                       GetSQLValueString(1, "int"),
                       GetSQLValueString(1, "int"),
                       GetSQLValueString(1, "int"),
					   GetSQLValueString($id, "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result5 = mysql_query($insertSQL5, $Ventas) or die(mysql_error());


		$rs1 = mysql_query("SELECT MAX(codigoprod) AS id FROM producto");
		if ($row1 = mysql_fetch_row($rs1)) {
			$id1 = $row1[0];
		}
  $monto=$_POST['monto'];
    $insertSQL2 = sprintf("INSERT INTO historial_producto(codigoprod, precio_compra, precio_venta, detalle_producto, cantidad, codigoproveedor) VALUES ($id1, 2, $monto, '.', 100000, 0)");

  mysql_select_db($database_Ventas, $Ventas);
  $Result2 = mysql_query($insertSQL2, $Ventas) or die(mysql_error());
 
 //tercer insert
  $insertSQL3 = sprintf("INSERT INTO producto_stock(codigoprod, stock,precio_compra,precio_venta) VALUES ($id1, 100000,1,$monto)");

  mysql_select_db($database_Ventas, $Ventas);
  $Result3 = mysql_query($insertSQL3, $Ventas) or die(mysql_error());

  
  }
  

  
  
  

  $insertGoTo = "service_list_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

 
$Icono="glyphicon glyphicon-list-alt";
$Color="font-blue";
$Titulo="Agregar Servicios";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>

<script type="text/javascript"> 
function habilitar(obj) { 
  var hab; 
  frm=obj.form; 
  num=obj.selectedIndex; 
  if (num==1) hab=true; 
  else if (num==2) hab=false; 
  Ingresar.monto.disabled=hab; 
 
} 
</script> 

<form method="POST" action="<?php echo $editFormAction; ?>" name="Ingresar" id="Ingresar">

<table class="table table-hover table-light">

<tbody>
<tr>
  <td colspan="2"> 
  <div class="form-group">
  <div class="col-md-10">
  <div class="input-group"><span id="sprytextfield1">
    <input type="text" class="form-control" placeholder="Nombre del Servicio" id="nombre" name="nombre" maxlength="200" />
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
    <option>--- Tipo de Servicio ---</option>
    <option value="0">A Pagar</option>
    <option value="1">A Ofrecer</option>
    
  </select>
  <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
  
  </td>
  <td>
  <div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield2">
<input type="text" class="form-control" placeholder="Monto del Servicio" id="monto" name="monto" maxlength="5" />
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
  <textarea name="observacion" id="observacion" rows="3" class="form-control" placeholder="Descripción u Observación para el Servicio"></textarea>
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
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar" />   

</form>
 <?php include("Fragmentos/pie.php"); 

?>                 

<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"], counterId:"countsprytextarea1", counterType:"chars_count"});
</script>
