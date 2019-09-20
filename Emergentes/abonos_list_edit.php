<?php require_once('../Connections/Ventas.php'); 
date_default_timezone_set('America/Lima');
?>
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
?>
<script language="javascript">
	function calcularvuelto()
	{	
		var montototal = Ingresar.total.value;
		var montoabonado = Ingresar.totalabono.value;
		var totalsaldo = Ingresar.totalsaldo.value;
		var abonoact = Ingresar.montopagar.value;
		var saldoactual=parseFloat(montototal)-parseFloat(montoabonado)-parseFloat(abonoact);
	//	vuelto=(parseFloat(monto)-parseFloat(montocancelar)).toFixed(2);
//		montof=(parseFloat(monto)-parseFloat(vuelto)).toFixed(2);
	
		//document.getElementsByName("cambio")[0].value = vuelto;
	//	document.getElementsByName("montofact")[0].value = montof;
	document.getElementsByName("nuevosaldo")[0].value = saldoactual;

	/*
		if(vuelto>=0){
			alert("USTED TIENE QUE REALIZAR UNA COMPRA Y NO UN ABONO PORQUE LA CANTIDAD PAGADA ES IGUAL O SUPERIOR A SU COMPRA");
//			document.getElementsByName("cambio")[0].value = vuelto;
			//alert("CAMBIO O VUELTO "+vuelto);
		}
		else
		{	document.getElementsByName("cambio")[0].value = (vuelto*-1);
		
		    alert("SU SALDO PARA ENTREGAR SU PRODUCTO ES: "+vuelto*-1);
		}
*/	}
	function mensaje()
	{	
//		var monto = GuardarVenta.numerotc.value;
//		var montocancelar = GuardarVenta.montopagar1.value;
//		vuelto=(parseFloat(monto)-parseFloat(montocancelar)).toFixed(2);
		
		//document.getElementsByName("test1")[0].value = precioc;
	}
	
</script>

<?php 


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Ingresar")) {

if($_POST['nuevosaldo']>=0){
$fechahoy=date("Y-m-d");
$insertSQL2 = sprintf("insert into abonos values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                          GetSQLValueString(NULL, "text"),
						  GetSQLValueString($_POST['codigoventa'], "text"),
						  GetSQLValueString(NULL, "int"),
						  GetSQLValueString(NULL, "text"),
						  GetSQLValueString($_POST['codigocomprobante'], "text"),
						  GetSQLValueString('p_c', "text"),
						  GetSQLValueString($_POST['codigoclienten'], "int"),
						  GetSQLValueString(0, "double"),
						  GetSQLValueString($fechahoy, "date"),
						  GetSQLValueString('00:00:00', "date"),
						  GetSQLValueString($_POST['codacceso'], "int"),
						  GetSQLValueString($_POST['codigopersonal'], "int"),
						  GetSQLValueString($_POST['montopagar'], "double"),
		          GetSQLValueString($_POST['nuevosaldo'], "double"),
              GetSQLValueString(1, "int"),
              GetSQLValueString(0, "int"));


if ($_POST['nuevosaldo']<=0)
{
 $insertSQL = sprintf("insert into ventas values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString(NULL, "text"),
              GetSQLValueString($_POST['codigoventa'], "text"),
              GetSQLValueString($_POST['codigoventa'], "text"),
              GetSQLValueString('abo', "text"),
              GetSQLValueString(NULL, "int"),
              GetSQLValueString(NULL, "text"),
              GetSQLValueString(999, "text"),
              GetSQLValueString('p_c', "text"),
              GetSQLValueString($_POST['codigoclienten'], "int"),
              GetSQLValueString(NULL, "int"),
              GetSQLValueString($_POST['total']/1.12, "double"),
              GetSQLValueString($_POST['total']-($_POST['total']/1.12), "double"),
              GetSQLValueString($_POST['total'], "double"),
              GetSQLValueString($fechahoy, "date"),
              GetSQLValueString('00:00:00', "date"),
              GetSQLValueString($_POST['codacceso'], "int"),
              GetSQLValueString($_POST['codigopersonal'], "int"),
              GetSQLValueString(0, "float"),
              GetSQLValueString($_POST['total'], "double"),
              GetSQLValueString(2, "int"),
              GetSQLValueString($_POST['totalc'], "double"));

 $updateSQL = sprintf("UPDATE abonos SET estadoabono=3 WHERE codigoventa=%s and estadoabono=2",
                       GetSQLValueString(strtoupper($_POST['codigoventa']), "text"));

 mysql_select_db($database_Ventas, $Ventas);
  $Result3 = mysql_query($updateSQL, $Ventas) or die(mysql_error());


  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());


}

  mysql_select_db($database_Ventas, $Ventas);
  $Result2 = mysql_query($insertSQL2, $Ventas) or die(mysql_error());


  $updateGoTo = "abonos_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));


  }
else
  {
      echo '<script type="text/javascript">alert("MONTO CANCELADO ES MAYOR A LA DEUDA");</script>';

  }
}
$colname_Abono = "-1";
if (isset($_GET['codigoventa'])) {
  $colname_Abono = $_GET['codigoventa'];
}

mysql_select_db($database_Ventas, $Ventas);
$query_Abono = "select a.codigoabono, a.codigoventa, a.codigocomprobante, COUNT(a.codigoventa) as cantpagos, a.fecha_emision, a.total, sum(a.monto_rec) as monto_rec, CONCAT (c.paterno,' ', c.materno, ' ', c.nombre) as cliente,c.cedula as cedulac, CONCAT (p.paterno,' ', p.materno, ' ', p.nombre) as personal, c.codigoclienten, a.totalc from abonos a left join cnatural c on c.codigoclienten=a.codigoclienten left join personal p on p.codigopersonal=a.codigopersonal where a.codigoventa='$colname_Abono' GROUP BY a.codigoventa order by a.codigoabono";

$Abono = mysql_query($query_Abono, $Ventas) or die(mysql_error());
$row_Abono = mysql_fetch_assoc($Abono);
$totalRows_Abono = mysql_num_rows($Abono);

mysql_select_db($database_Ventas, $Ventas);
$query_SubCategorias = "SELECT * FROM subcategoria WHERE estado = 0";
$SubCategorias = mysql_query($query_SubCategorias, $Ventas) or die(mysql_error());
$row_SubCategorias = mysql_fetch_assoc($SubCategorias);
$totalRows_SubCategorias = mysql_num_rows($SubCategorias);

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
$query_Colores = "SELECT * FROM color WHERE estado = 0";
$Colores = mysql_query($query_Colores, $Ventas) or die(mysql_error());
$row_Colores = mysql_fetch_assoc($Colores);
$totalRows_Colores = mysql_num_rows($Colores);


mysql_select_db($database_Ventas, $Ventas);
$query_Producto = sprintf("SELECT * FROM producto WHERE codigoprod = %s", GetSQLValueString($colname_Producto, "int"));
$Producto = mysql_query($query_Producto, $Ventas) or die(mysql_error());
$row_Producto = mysql_fetch_assoc($Producto);
$totalRows_Producto = mysql_num_rows($Producto);

$Icono="fa fa-cubes";
$Color="font-blue";
$Titulo="Pago de Abono - ".$row_Producto['codigoprod'];
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
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
$jsObject_SubCategorias = new WDG_JsRecordset("SubCategorias");
echo $jsObject_SubCategorias->getOutput();
//end JSRecordset
?>
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<div class="form-group">
  <div class="col-md-10">
  <div class="input-group">Nombre del Cliente<span id="sprytextfield1">
  <input name="nombre_producto" type="text" class="form-control tooltips" data-placement="top" data-original-title="Nombre del Cliente" id="nombre_producto" placeholder="Nombre del Cliente" value="<?php echo $row_Abono['cliente']."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp - &nbsp&nbsp&nbsp Cedula ".$row_Abono['cedulac']; ?>" maxlength="200" readonly />
  <span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> <i class="fa fa-cubes font-blue-soft"></i> </span> </div>
  </div>
  </div>
<table width=100% border="0" class="table table-hover table-light">
 
  <tr>
    <td>
    <div class="col-md-8">
<div class="form-group">Total Venta<span id="spryselect1">
<input type="text" name="total" id="total" class="form-control tooltips" data-placement="top" data-original-title="Total de Venta" readonly value="<?php echo $row_Abono['total']; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
    <td>
    <div class="col-md-8">
<div class="form-group">Total Abonado<span id="spryselect1">
<input type="text" name="totalabono" id="totalabono" class="form-control tooltips" data-placement="top" data-original-title="Total de Venta" readonly value="<?php echo $row_Abono['monto_rec']; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
        <td>
    <div class="col-md-8">
<div class="form-group">Total de Saldo<span id="spryselect1">
<input type="text" name="totalsaldo" id="totalsaldo" class="form-control tooltips" data-placement="top" readonly data-original-title="Total de Saldo" value="<?php echo $row_Abono['total']-$row_Abono['monto_rec']; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
  </tr>
  
  
  
  
 <tr>
    <td>
    <div class="col-md-8">
<div class="form-group">Proximo Abono<span id="spryselect1">
<input type="text" name="montopagar" id="montopagar" class="form-control tooltips" data-placement="top" data-original-title="Monto a Pagar" onblur="calcularvuelto()" onblur="mensaje()" value="">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
    <td>
    <div class="col-md-8">
<div class="form-group">Nuevo Saldo<span id="spryselect1">
<input type="text" name="nuevosaldo" id="nuevosaldo" class="form-control tooltips" data-placement="top" data-original-title="Nuevo Saldo" value="">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
     
    
  </tr>
  
    
  <input name="codigoventa" type="hidden" id="codigoventa" value="<?php echo $row_Abono['codigoventa']; ?>">  
  <input name="codigocomprobante" type="hidden" id="codigocomprobante" value="<?php echo $row_Abono['codigocomprobante']; ?>">
  <input name="totalc" type="hidden" id="totalc" value="<?php echo $row_Abono['totalc']; ?>"> 
   <input name="codigoclienten" type="hidden" id="codigoclienten" value="<?php echo $row_Abono['codigoclienten']; ?>"> 
   <input name="codacceso" type="hidden" value="<?php echo $_SESSION['kt_login_id']; ?>" />
<input name="codigopersonal" type="hidden" value="<?php echo $_SESSION['kt_codigopersonal']; ?>" />
</table>

  <?php 
//------------- Inicio Botones------------
include("Botones/BotonesActualizar.php"); 
//------------- Fin Botones------------
?>
  <input type="hidden" name="MM_update" value="Ingresar">
</form>
                  
<?php include("Fragmentos/pie.php"); 

?>
<?php
mysql_free_result($Categorias);

mysql_free_result($SubCategorias);

mysql_free_result($Marca);

mysql_free_result($Presentacion);

mysql_free_result($Colores);

mysql_free_result($Producto);
?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {validateOn:["blur", "change"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"0", validateOn:["blur", "change"]});
var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"0", validateOn:["blur", "change"]});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1", {validateOn:["blur", "change"]});
</script>
