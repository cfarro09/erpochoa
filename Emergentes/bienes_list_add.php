<?php require_once('../Connections/Ventas.php'); ?>
<?php
//MX Widgets3 include
require_once('../includes/wdg/WDG.php');

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
  $insertSQL = sprintf("INSERT INTO inventario_bienes (codigo, nombre_bien, serie, descripcion_bien, fecha_adquisicion, numero_factura, fecha_incorporacion, precio_compra, codigomarca, codigocat, codigosubcat, codigocolor, codigopresent) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString(strtoupper($_POST['nombre_bien']), "text"),
                       GetSQLValueString(strtoupper($_POST['serie']), "text"),
                       GetSQLValueString(strtoupper($_POST['descripcion_bien']), "text"),
                       GetSQLValueString($_POST['fecha_adquisicion'], "date"),
                       GetSQLValueString($_POST['numero_factura'], "text"),
                       GetSQLValueString($_POST['fecha_incorporacion'], "date"),
                       GetSQLValueString($_POST['precio_compra'], "double"),
                       GetSQLValueString($_POST['codigomarca'], "int"),
                       GetSQLValueString($_POST['codigocat'], "int"),
                       GetSQLValueString($_POST['codigosubcat'], "int"),
                       GetSQLValueString($_POST['codigocolor'], "int"),
                       GetSQLValueString($_POST['codigopresent'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

  $insertGoTo = "bienes_list_add.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Ingresar")) {
  $insertSQL = sprintf("INSERT INTO historial_mov_invbien (codigo, codigotipomov, fecha_mov, codacceso, codigooficina, codigopersonal) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString($_POST['codigotipomov'], "int"),
                       GetSQLValueString($_POST['fecha_mov'], "date"),
                       GetSQLValueString($_POST['codacceso'], "int"),
                       GetSQLValueString($_POST['codigooficina'], "int"),
                       GetSQLValueString($_POST['codigopersonal'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());
}

mysql_select_db($database_Ventas, $Ventas);
$query_Colores = "SELECT * FROM color WHERE estado = 0";
$Colores = mysql_query($query_Colores, $Ventas) or die(mysql_error());
$row_Colores = mysql_fetch_assoc($Colores);
$totalRows_Colores = mysql_num_rows($Colores);

mysql_select_db($database_Ventas, $Ventas);
$query_Marca = "SELECT * FROM marca WHERE estado = 0";
$Marca = mysql_query($query_Marca, $Ventas) or die(mysql_error());
$row_Marca = mysql_fetch_assoc($Marca);
$totalRows_Marca = mysql_num_rows($Marca);

mysql_select_db($database_Ventas, $Ventas);
$query_Presentacion = "SELECT * FROM presentacion WHERE estado = 0";
$Presentacion = mysql_query($query_Presentacion, $Ventas) or die(mysql_error());
$row_Presentacion = mysql_fetch_assoc($Presentacion);
$totalRows_Presentacion = mysql_num_rows($Presentacion);

mysql_select_db($database_Ventas, $Ventas);
$query_Categoria = "SELECT * FROM categoria WHERE estado = 0";
$Categoria = mysql_query($query_Categoria, $Ventas) or die(mysql_error());
$row_Categoria = mysql_fetch_assoc($Categoria);
$totalRows_Categoria = mysql_num_rows($Categoria);

mysql_select_db($database_Ventas, $Ventas);
$query_SubCategoria = "SELECT * FROM subcategoria WHERE estado = 0";
$SubCategoria = mysql_query($query_SubCategoria, $Ventas) or die(mysql_error());
$row_SubCategoria = mysql_fetch_assoc($SubCategoria);
$totalRows_SubCategoria = mysql_num_rows($SubCategoria);

mysql_select_db($database_Ventas, $Ventas);
$query_TipoMovimiento = "SELECT * FROM tipomoviemiento WHERE estado = 0";
$TipoMovimiento = mysql_query($query_TipoMovimiento, $Ventas) or die(mysql_error());
$row_TipoMovimiento = mysql_fetch_assoc($TipoMovimiento);
$totalRows_TipoMovimiento = mysql_num_rows($TipoMovimiento);

mysql_select_db($database_Ventas, $Ventas);
$query_Personal = "SELECT codigopersonal, CONCAT(paterno,  ' ', materno, ' ', nombre) as Personal FROM personal WHERE estado = '0'";
$Personal = mysql_query($query_Personal, $Ventas) or die(mysql_error());
$row_Personal = mysql_fetch_assoc($Personal);
$totalRows_Personal = mysql_num_rows($Personal);

mysql_select_db($database_Ventas, $Ventas);
$query_Oficina = "SELECT * FROM oficina WHERE estado = 0";
$Oficina = mysql_query($query_Oficina, $Ventas) or die(mysql_error());
$row_Oficina = mysql_fetch_assoc($Oficina);
$totalRows_Oficina = mysql_num_rows($Oficina);

$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Agregar Bien";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
include("Fragmentos/cod_gen.php");
?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script><html xmlns:wdg="http://ns.adobe.com/addt">
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/common/js/sigslot_core.js"></script>
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/wdg/classes/MXWidgets.js"></script>
<script type="text/javascript" src="../includes/wdg/classes/MXWidgets.js.php"></script>
<script type="text/javascript" src="../includes/wdg/classes/JSRecordset.js"></script>
<script type="text/javascript" src="../includes/wdg/classes/DependentDropdown.js"></script>
<?php
//begin JSRecordset
$jsObject_SubCategoria = new WDG_JsRecordset("SubCategoria");
echo $jsObject_SubCategoria->getOutput();
//end JSRecordset
?>
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<head></head>
<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">
<input name="codigo" type="hidden" id="codigo" value="<?php echo $CodGen?>">
 
<div class="tabbable-custom nav-justified">
<ul class="nav nav-tabs nav-justified">
<li class="active">
<a href="#tab_1_1_1" data-toggle="tab"> Detalle del Bien </a>
</li>
<li>
<a href="#tab_1_1_2" data-toggle="tab"> Movimientos - Asignaciones </a>
</li>

</ul>
<div class="tab-content">
<div class="tab-pane active" id="tab_1_1_1">
<table width=100% border="0" class="table table-hover table-light">

<tr>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield1">
<input name="nombre_bien" type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Nombre de Bien" id="nombre_bien" placeholder="Nombre de Bien" maxlength="200" />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> <i class="fa fa-building-o font-blue-soft"></i> </span> </div>
</div>
</div>

</td>
<td>
<div class="form-group">
<div class="col-md-7">
<div class="input-group"><span id="sprytextfield2">
<input name="serie" type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Serie de Bien" id="serie" placeholder="Serie de Bien" maxlength="20"  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> <i class="glyphicon glyphicon-barcode  font-blue-soft"></i> </span> </div>
</div>
</div>
</td>
</tr>
<tr>
<td>
<div class="col-md-8">
<div class="form-group"><span id="spryselect1">
<select name="codigocat" id="codigocat" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Categor&iacute;a">
  <option value="">-- Categor&iacute;a --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Categoria['codigocat']?>"><?php echo $row_Categoria['nombre']?></option>
  <?php
} while ($row_Categoria = mysql_fetch_assoc($Categoria));
  $rows = mysql_num_rows($Categoria);
  if($rows > 0) {
      mysql_data_seek($Categoria, 0);
	  $row_Categoria = mysql_fetch_assoc($Categoria);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
</td>
<td>
<div class="col-md-8">
<div class="form-group"><span id="spryselect2">
<select name="codigosubcat" class="form-control tooltips" id="codigosubcat" wdg:subtype="DependentDropdown" data-placement="top" data-original-title="Seleccionar Sub Categor&iacute;a" wdg:type="widget" wdg:recordset="SubCategoria" wdg:displayfield="nombre" wdg:valuefield="codigosubcat" wdg:fkey="codigocat" wdg:triggerobject="codigocat">
<option value="">-- Sub Categor&iacute;as --</option>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
</td>
</tr>
<tr>
<td colspan="2">
<table width="100%" border="0">
<tr>
<td width=33%>
<div class="col-md-10">
<div class="form-group"><span id="spryselect3">
<select name="codigomarca" id="codigomarca" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Marca">
  <option value="">-- Marca --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Marca['codigomarca']?>"><?php echo $row_Marca['nombre']?></option>
  <?php
} while ($row_Marca = mysql_fetch_assoc($Marca));
  $rows = mysql_num_rows($Marca);
  if($rows > 0) {
      mysql_data_seek($Marca, 0);
	  $row_Marca = mysql_fetch_assoc($Marca);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>


</td>
<td width=33%>
<div class="col-md-10">
<div class="form-group"><span id="spryselect4">
<select name="codigopresent" id="codigopresent" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Presentaci&oacute;n">
  <option value="">-- Presentaci&oacute;n --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Presentacion['codigopresent']?>"><?php echo $row_Presentacion['nombre_presentacion']?></option>
  <?php
} while ($row_Presentacion = mysql_fetch_assoc($Presentacion));
  $rows = mysql_num_rows($Presentacion);
  if($rows > 0) {
      mysql_data_seek($Presentacion, 0);
	  $row_Presentacion = mysql_fetch_assoc($Presentacion);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>


</td>
<td width=33%>
<div class="col-md-10">
<div class="form-group"><span id="spryselect5">
<select name="codigocolor" id="codigocolor" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Color">
  <option value="">-- Color --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Colores['codigocolor']?>"><?php echo $row_Colores['nombre_color']?></option>
  <?php
} while ($row_Colores = mysql_fetch_assoc($Colores));
  $rows = mysql_num_rows($Colores);
  if($rows > 0) {
      mysql_data_seek($Colores, 0);
	  $row_Colores = mysql_fetch_assoc($Colores);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>

</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield5">
<input name="numero_factura" type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar N&uacute;mero de Factura" id="nrecibo" placeholder="N&uacute;mero de Factura" maxlength="20"  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> 
<i class="fa fa-slack  font-blue-soft"></i> </span> </div>
</div>
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-5">
<div class="input-group"><span id="sprytextfield6">
<input name="precio_compra" type="text" class="form-control tooltips" data-placement="top" data-original-title="Agregar Precio de Compra" id="precio_compra" placeholder="Precio Compra" maxlength="6"  />
<span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> 
<i class="fa fa-dollar font-blue-soft"></i> </span> </div>
</div>
</div>
</td>
</tr>
<tr>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group input-medium date date-picker tooltips" data-placement="top" data-original-title="Agregar Fecha de Adquisici&oacute;n del Bien"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield3">
<input type="text" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" placeholder="Fecha de Adquisici&oacute;n"/>
<span class="textfieldRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn default" type="button">
<i class="fa fa-calendar font-blue-soft"></i>
</button>
</span>
</div>
</div> 
</div>
</td>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group input-medium date date-picker tooltips" data-placement="top" data-original-title="Agregar Fecha de Incorporaci&oacute;n del Bien"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield4">
<input type="text" class="form-control" id="fecha_incorporacion" name="fecha_incorporacion" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" placeholder="Fecha de Emisión"/>
<span class="textfieldRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn default" type="button">
<i class="fa fa-calendar font-blue-soft"></i>
</button>
</span>
</div>
</div> 
</div>
</td>
</tr>

<tr>
<td colspan="2">
<span id="sprytextarea1">
<textarea name="descripcion_bien" id="descripcion_bien" rows="2" class="form-control tooltips" data-placement="top" data-original-title="Agregar Descripci&oacute;n y/o Caracter&iacute;stica del Bien" placeholder="Descripci&oacute;n y/o caracter&iacute;stica del Bien"></textarea>
<span class="textareaRequiredMsg"></span></span>
</td>
</tr>




</table>

</div>
<div class="tab-pane disabled" id="tab_1_1_2">
<table width="100%" border="0" class="table table-hover table-light">
<tr>
<td>
<div class="col-md-10">
<div class="form-group"><span id="spryselect6">
<select name="codigotipomov" id="codigotipomov" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Tipo de Movimiento">
  <option value="">-- Tipo Movimiento --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_TipoMovimiento['codigotipomov']?>"><?php echo $row_TipoMovimiento['nombre_tipomov']?></option>
  <?php
} while ($row_TipoMovimiento = mysql_fetch_assoc($TipoMovimiento));
  $rows = mysql_num_rows($TipoMovimiento);
  if($rows > 0) {
      mysql_data_seek($TipoMovimiento, 0);
	  $row_TipoMovimiento = mysql_fetch_assoc($TipoMovimiento);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div></td>
<td>
<div class="form-group">
<div class="col-md-10">
<div class="input-group input-medium date date-picker tooltips" data-placement="top" data-original-title="Agregar Fecha de Movimiento del Bien"  data-date-format="yyyy-mm-dd" data-date-viewmode="years"><span id="sprytextfield7">
<input type="text" class="form-control" id="fecha_mov" name="fecha_mov" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" placeholder="Fecha de Movimiento"/>
<span class="textfieldRequiredMsg"></span></span><span class="input-group-btn">
<button class="btn default" type="button">
<i class="fa fa-calendar font-blue-soft"></i></button>
</span></div>
</div> 
</div></td>
</tr>
<tr>
<td colspan="2">
<div class="col-md-10">
<div class="form-group"><span id="spryselect7">
<select name="codigopersonal" id="codigopersonal" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Personal para Asignaci&oacute;n del Bien">
  <option value="">-- Personal Asignaci&oacute;n de Bien --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Personal['codigopersonal']?>"><?php echo $row_Personal['Personal']?></option>
  <?php
} while ($row_Personal = mysql_fetch_assoc($Personal));
  $rows = mysql_num_rows($Personal);
  if($rows > 0) {
      mysql_data_seek($Personal, 0);
	  $row_Personal = mysql_fetch_assoc($Personal);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div></td>
</tr>
<tr>
<td colspan="2">
<div class="col-md-10">
<div class="form-group"><span id="spryselect8">
<select name="codigooficina" id="codigooficina" class="form-control tooltips" data-placement="top" data-original-title="Seleccionar Oficina para Asignaci&oacute;n del Bien">
  <option value="">-- Oficina Asignaci&oacute;n de Bien --</option>
  <?php
do {  
?>
  <option value="<?php echo $row_Oficina['codigooficina']?>"><?php echo $row_Oficina['nombre_oficina']?></option>
  <?php
} while ($row_Oficina = mysql_fetch_assoc($Oficina));
  $rows = mysql_num_rows($Oficina);
  if($rows > 0) {
      mysql_data_seek($Oficina, 0);
	  $row_Oficina = mysql_fetch_assoc($Oficina);
  }
?>
</select>
<span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div></td>
</tr>
</table>
<input name="codacceso" type="hidden" id="codacceso" value="<?php echo $_SESSION['kt_login_id']; ?>" />
</div>
</div>
</div>

<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
<input type="hidden" name="MM_insert" value="Ingresar">
</form>

<?php include("Fragmentos/pie.php"); 

?>
</body>
<?php
mysql_free_result($Colores);

mysql_free_result($Marca);

mysql_free_result($Presentacion);

mysql_free_result($Categoria);

mysql_free_result($SubCategoria);

mysql_free_result($TipoMovimiento);

mysql_free_result($Personal);

mysql_free_result($Oficina);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {validateOn:["blur", "change"]});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur", "change"]});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "none", {validateOn:["blur", "change"]});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect7 = new Spry.Widget.ValidationSelect("spryselect7", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect8 = new Spry.Widget.ValidationSelect("spryselect8", {invalidValue:"0", validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});
</script>
