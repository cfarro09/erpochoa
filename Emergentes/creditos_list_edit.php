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
?>
<script language="javascript">
	function calcularvuelto()
	{	
		var montototal = Ingresar.total.value;
		var montoabonado = Ingresar.totalabono.value;
		var totalsaldo = Ingresar.totalsaldo.value;
		var abonoact = Ingresar.montopagar.value;
		var saldoactual=parseFloat(totalsaldo)-parseFloat(abonoact);
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
 
$fechahoy=date("Y-m-d");

$insertSQL2 = sprintf("insert into pagocredito values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString(NULL, "text"),
              GetSQLValueString($_POST['ncuotapag']+1, "int"),
              GetSQLValueString($_POST['totalabono']+$_POST['montopagar'], "float"),
              GetSQLValueString($_POST['totalsaldo']-$_POST['montopagar'], "float"),
              GetSQLValueString($_POST['montopagar'], "float"),
              GetSQLValueString(0, "int"),
              GetSQLValueString($fechahoy, "text"),
              GetSQLValueString('00:00:00', "text"),
             
              GetSQLValueString($_POST['codigocredito'], "int"),
              GetSQLValueString($_POST['codigopersonal'], "text"));

if (($_POST['totalsaldo']-$_POST['montopagar'])<=0)
{
 $insertSQL = sprintf("insert into ventas values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString(NULL, "text"),
              GetSQLValueString($_POST['codigoventa'], "text"),
              GetSQLValueString($_POST['codigoventa'], "text"),
              GetSQLValueString('cre', "text"),
              GetSQLValueString(NULL, "int"),
              GetSQLValueString(NULL, "text"),
              GetSQLValueString(999, "text"),
              GetSQLValueString('p_c', "text"),
              GetSQLValueString($_POST['codigoclienten'], "int"),
              GetSQLValueString(NULL, "int"),
              GetSQLValueString($_POST['total']/1.14, "double"),
              GetSQLValueString($_POST['total']-($_POST['total']/1.14), "double"),
              GetSQLValueString($_POST['total'], "double"),
              GetSQLValueString($fechahoy, "date"),
              GetSQLValueString('00:00:00', "date"),
              GetSQLValueString($_POST['codacceso'], "int"),
              GetSQLValueString($_POST['codigopersonal'], "int"),
              GetSQLValueString(0, "float"),
              GetSQLValueString($_POST['total'], "double"),
              GetSQLValueString(1, "int"),
              GetSQLValueString($_POST['totalc'], "double"));

$updateSQL = sprintf("UPDATE credito SET estadocredito=2 WHERE codigocredito=%s and estadocredito=1",
                       GetSQLValueString(strtoupper($_POST['codigocredito']), "text"));

 mysql_select_db($database_Ventas, $Ventas);
  $Result3 = mysql_query($updateSQL, $Ventas) or die(mysql_error());


  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());


}

  mysql_select_db($database_Ventas, $Ventas);
  $Result2 = mysql_query($insertSQL2, $Ventas) or die(mysql_error());


  $updateGoTo = "creditos_list_edit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
$colname_Credito = "-1";
if (isset($_GET['codigocredito'])) {
  $colname_Credito = $_GET['codigocredito'];
}

mysql_select_db($database_Ventas, $Ventas);
$query_Credito = "select cr.codigocredito, cr.codigoventa, cr.codigocomprobante, cr.cantcuota as totalcuotas, cr.fecha_emision, cr.total, sum(cr.monto_rec) as monto_rec, CONCAT (c.paterno,' ', c.materno, ' ', c.nombre) as cliente,c.cedula as cedulac, CONCAT (p.paterno,' ', p.materno, ' ', p.nombre) as personal, c.codigoclienten, cr.metodopago, cr.tea, cr.totalcredito, cr.cuotames, a.fecha_venc, max(pg.ncuotapag) as ncuotapag, min(pg.saldo) as saldo, max(pg.totalpagocr) as totalpagocr, cr.totalc from credito cr left join cnatural c on c.codigoclienten=cr.codigoclienten left join personal p on p.codigopersonal=cr.codigopersonal left join cronogramacredito a on a.codigocredito=cr.codigocredito left join pagocredito pg on pg.codigocredito=cr.codigocredito where cr.codigocredito='$colname_Credito' GROUP BY cr.codigoventa order by cr.codigocredito";

$Credito = mysql_query($query_Credito, $Ventas) or die(mysql_error());
$row_Credito = mysql_fetch_assoc($Credito);
$totalRows_Credito = mysql_num_rows($Credito);
if ($row_Credito['montop']==NULL)
    $montop=0;
  else
   $montop=$row_Credito['montop'];   

if ($row_Credito['ncuotapag']==NULL)
    $ncuotapag=0;
 else  
    $ncuotapag=$row_Credito['ncuotapag'];

$Icono="fa fa-cubes";
$Color="font-blue";
$Titulo="Pago de Credito ";
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

<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<form action="<?php echo $editFormAction; ?>" method="POST" name="Ingresar" id="Ingresar">

<div class="form-group">
  <div class="col-md-10">
  <div class="input-group">Nombre del Cliente<span id="sprytextfield1">
  <input name="nombre_producto" type="text" class="form-control tooltips" data-placement="top" data-original-title="Nombre del Cliente" id="nombre_producto" placeholder="Nombre del Cliente" value="<?php echo $row_Credito['cliente']."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp - &nbsp&nbsp&nbsp Cedula ".$row_Credito['cedulac']; ?>" maxlength="200" readonly />
  <span class="textfieldRequiredMsg"></span><span class="textfieldMinCharsMsg"></span></span><span class="input-group-addon"> <i class="fa fa-cubes font-blue-soft"></i> </span> </div>
  </div>
  </div>
<table width=100% border="0" class="table table-hover table-light">
 
  <tr>
    <td>
    <div class="col-md-8">
<div class="form-group">Total Credito<span id="spryselect1">
<input type="text" name="total" id="total" class="form-control tooltips" data-placement="top" data-original-title="Total de Credito" readonly value="<?php echo $row_Credito['totalcredito']; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
    <td>
    <div class="col-md-8">
<div class="form-group">Total Abonado<span id="spryselect1">
<input type="text" name="totalabono" id="totalabono" class="form-control tooltips" data-placement="top" data-original-title="Total de Venta" readonly value="<?php echo round($row_Credito['totalpagocr'],2); ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
        <td>
    <div class="col-md-8">
<div class="form-group">Total de Saldo<span id="spryselect1">
<input type="text" name="totalsaldo" id="totalsaldo" class="form-control tooltips" data-placement="top" readonly data-original-title="Total de Saldo" value="<?php if ($ncuotapag==0) echo $row_Credito['totalcredito']-round($row_Credito['saldo'],2); else echo round($row_Credito['saldo'],2)?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
  </tr>
  
    <tr>
    <td>
    <div class="col-md-8">
<div class="form-group">Cantidad Cuotas<span id="spryselect1">
<input type="text" name="totalcuotas" id="totalcuotas" class="form-control tooltips" data-placement="top" data-original-title="Cantidad de Pagos" readonly value="<?php echo $row_Credito['totalcuotas']; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
    <td>
    <div class="col-md-8">
<div class="form-group">Cuotas Pagadas<span id="spryselect1">
<input type="text" name="ncuotapag" id="ncuotapag" class="form-control tooltips" data-placement="top" data-original-title="Total de Venta" readonly value="<?php echo $ncuotapag; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
        <td>
    <div class="col-md-8">
<div class="form-group">Cuota Credito<span id="spryselect1">
<input type="text" name="cuotames" id="cuotames" class="form-control tooltips" data-placement="top" readonly data-original-title="Cuota de Credito" value="<?php echo $row_Credito['cuotames']; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
  </tr>
  
  
 <tr>
    <td>
    <div class="col-md-8">
<div class="form-group">Monto a Pagar<span id="spryselect1">
<input type="text" name="montopagar" id="montopagar" class="form-control tooltips" data-placement="top" data-original-title="Monto a Pagar" onblur="calcularvuelto()" onblur="mensaje()" value="">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
    <td>
    <div class="col-md-8">
<div class="form-group">Saldo Total<span id="spryselect1">
<input type="text" name="nuevosaldo" id="nuevosaldo" class="form-control tooltips" data-placement="top" data-original-title="Nuevo Saldo" value="">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
       <td>
    <div class="col-md-8">
<div class="form-group">Fecha de Vencimiento<span id="spryselect1">
<input type="text" name="fecha_venc" id="fecha_venc" class="form-control tooltips" data-placement="top" readonly data-original-title="Total de Saldo" value="<?php echo $row_Credito['fecha_venc']; ?>">
 <span class="selectInvalidMsg"></span><span class="selectRequiredMsg"></span></span></div>
</div>
    </td>
    
  </tr>
  
    
  <input name="codigocredito" type="hidden" id="codigocredito" value="<?php echo  $colname_Credito; ?>">  
  <input name="codigocomprobante" type="hidden" id="codigocomprobante" value="<?php echo $row_Abono['codigocomprobante']; ?>"> 
   <input name="codigoclienten" type="hidden" id="codigoclienten" value="<?php echo $row_Credito['codigoclienten']; ?>"> 
   <input name="codigoventa" type="hidden" id="codigoventa" value="<?php echo $row_Credito['codigoventa']; ?>"> 
   <input name="totalc" type="hidden" id="totalc" value="<?php echo $row_Credito['totalc']; ?>"> 
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
