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

mysql_select_db($database_Ventas, $Ventas);
$query_Servicios = "SELECT * FROM servicios WHERE tipo = 1";
$Servicios = mysql_query($query_Servicios, $Ventas) or die(mysql_error());
$row_Servicios = mysql_fetch_assoc($Servicios);
$totalRows_Servicios = mysql_num_rows($Servicios);

$Icono="fa fa-gears";
$Color="font-blue";
$Titulo="Servicios";
include("Fragmentos/cabecera.php");
include("../Fragmentos/abrirpopupcentro.php");

 ?>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<form method="POST" name="Ingresar" id="Ingresar" >

<table class="table table-hover table-light">

<tbody>

<tr>
  <td colspan="2">
<input type="hidden"  id="codigoclienten" name="codigoclienten" />
<input type="hidden" id="codigoclientej" name="codigoclientej" />
<div class="col-md-10">
<div class="input-group"><span id="sprytextfield1">
  <input type="text" class="form-control" placeholder="Seleccionar Persona Natural o Jurídica" onkeypress="return cancelWrite(event)" onkeydown="return cancelWrite(event)" name="persona" id="persona" />
  <span class="textfieldRequiredMsg"></span></span>
  <div class="input-group-btn">
<button type="button" class="btn blue dropdown-toggle" data-toggle="dropdown"><i class="icon-users"></i>
<i class="fa fa-angle-down"></i>
</button>
<ul class="dropdown-menu pull-right">
<li class="divider"> </li>
<li>
<a href="javascript:;" onclick="abre_ventana('Rejillas/cliente_jur.php',900,550)"><i class="glyphicon glyphicon-search"></i> Buscar Persona Jurídico </a>
</li>
<li>
<a href="javascript:;" onclick="abre_ventana('cliente_juridico_list_add.php',700,500)"><i class="glyphicon glyphicon-floppy-disk "></i> Agregar Persona Jurídico </a>
</li>
<li class="divider"> </li>
<li>
<a href="javascript:;" onclick="abre_ventana('Rejillas/cliente_nat.php',900,600)"><i class="glyphicon glyphicon-search"></i> Buscar Persona Natural </a>
</li>

<li>
<a href="javascript:;" onclick="abre_ventana('cliente_natural_list_add.php',700,600)"><i class="glyphicon glyphicon-floppy-disk "></i> Agregar Persona Natural </a>
</li>
<li class="divider"> </li>
</ul>
</div>
<!-- /btn-group -->
</div>
<!-- /input-group -->
</div>
                                            
  </td>
</tr>
<tr>
  <td colspan="2">
    <div class="col-md-10">
      <div class="form-group"><span id="spryselect1">
        <select name="codigosv" id="codigosv" class="form-control ">
          <option value="0">--- Servicio a Ofrecer ---</option>
          <?php
do {  
?>
          <option value="<?php echo $row_Servicios['codigosv']?>"><?php echo $row_Servicios['nombre']?></option>
          <?php
} while ($row_Servicios = mysql_fetch_assoc($Servicios));
  $rows = mysql_num_rows($Servicios);
  if($rows > 0) {
      mysql_data_seek($Servicios, 0);
	  $row_Servicios = mysql_fetch_assoc($Servicios);
  }
?>
          </select>
        <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div> </div>
    </td>
</tr>




<tr>
<td>
<div class="form-group col-md-6">
<div class="input-group input-group-lg">
<input type="text" class="form-control" id="fingreso" name="fingreso" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo date('Y-m-d');?>"/>
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
<input type="text" class="form-control" id="hingreso" name="hingreso" onKeyPress="return cancelWrite(event)" onKeyDown="return cancelWrite(event)" value="<?php echo date('h:i:s');?>"/>
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
  <textarea name="observacion" id="observacion" rows="3" class="form-control" placeholder="Descripción" ></textarea>
  <span id="countsprytextarea1">&nbsp;</span><span class="textareaRequiredMsg"></span></span></td>
  </tr>

</tbody>
</table>
<input name="usuario_ingreso" type="hidden" value="<?php echo $_SESSION['kt_login_id']; ?>" />
<input name="personal_ingreso" type="hidden" value="<?php echo $_SESSION['kt_codigopersonal']; ?>" />
<?php 
//------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Servicios);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});


var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});

var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"], counterId:"countsprytextarea1", counterType:"chars_count"});


</script>
