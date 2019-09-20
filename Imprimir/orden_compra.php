<?php
require('../fpdf/fpdf.php');
require_once('../Connections/Ventas.php');
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
$colname_Factura = "-1";
 $colname_Listado_Productos ="-1";

if (isset($_GET['codigocompras'])) {
  $colname_Factura = $_GET['codigocompras'];
  $colname_Listado_Productos = $_GET['codigocompras'];
}
mysql_select_db($database_Ventas, $Ventas);

$query_Factura = sprintf("SELECT c.codigo, c.subtotal, c.igv, c.montofact, c.fecha_emision, c.codigoproveedor, c.codigo, c.codigoref1, c.codigoref2, pe.nombre as nombrep, c.fecha_emision, pe.paterno as paternop, pe.materno as maternop, p.celular, p.ciudad, p.direccion, p.email, p.pais, p.paginaweb, p.telefono, p.ruc, p.razonsocial FROM ordencompra c inner join acceso a on a.codacceso=c.codacceso inner join personal pe on pe.codigopersonal=c.codigopersonal inner join proveedor p on p.codigoproveedor=c.codigoproveedor WHERE c.codigo = %s", GetSQLValueString($colname_Factura, "int"));


$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());
$row_Factura = mysql_fetch_assoc($Factura);
$totalRows_Factura = mysql_num_rows($Factura);


mysql_select_db($database_Ventas, $Ventas);
$query_Listado_Productos = "SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, (a.cantidad*a.pcompra) AS total, a.cantidad, a.pcompra, a.concatenacion, a.codcomprobante, b.nombre_producto AS Producto, c.nombre AS Marca, d.nombre_presentacion AS Presentacion, e.nombre_color AS Color FROM detalle_compras_oc a INNER JOIN producto b ON a.codigoprod = b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN presentacion d ON b.codigopresent = d.codigopresent INNER JOIN color e ON b.codigocolor = e.codigocolor WHERE a.codigo = '$colname_Listado_Productos'  group by a.codigoprod";
$Listado_Productos = mysql_query($query_Listado_Productos, $Ventas) or die(mysql_error());
$row_Listado_Productos = mysql_fetch_assoc($Listado_Productos);
$totalRows_Listado_Productos = mysql_num_rows($Listado_Productos);




class PDF extends FPDF
{
// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

function TablaBasica($row_Listado_Productos)
{


	$this->SetFont('Arial','I',8);
    //Cabecera
    //foreach($header as $col)
    $this->Cell(19,7,'Codigo P',1);
	$this->Cell(8,7,'Cant',1);
	$this->Cell(135,7,'Descripcion',1);
	$this->Cell(15,7,'P. Unidad',1);
	$this->Cell(15,7,'Total',1);
	$this->Ln();
    $this->SetFont('Arial','I',7);
  
}
}
// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',16);
$pdf->Cell(0,15,'                                    FACTURA DE COMPRA:  '.$row_Factura['codigo'],0,1);

$pdf->SetFont('Times','',9);
	//$pdf->Cell(0,25,'Tipo de Cliente:                        '.'JURIDICO',0,1);
	$pdf->Cell(0,5,'RAZON SOCIAL  :   '.$row_Factura['razonsocial'],0,1);
	$pdf->Cell(0,5,'RUC                       :   '.$row_Factura['ruc'],0,1);
	$pdf->Cell(0,5,'FECHA                  :   '.$row_Factura['fecha_emision'],0,1);
	$pdf->Cell(0,5,'CODIGO DE REFERENCIA :   '.$row_Factura['codigoref1'].' - '.$row_Factura['codigoref2'],0,1);
	$pdf->Cell(0,5,'DIRECCION         :   '.$row_Factura['direccion'],0,1);
	$pdf->Cell(0,5,'CIUDAD               :   '.$row_Factura['ciudad'].'                            '.'TELEFONO: '.$row_Factura['celular'],0,1);

$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,10,'DATOS DE PEDIDO',0,1);
$pdf->SetFont('Times','',7);


    $pdf->Cell(19,7,'CODIGO PROD',1);
	$pdf->Cell(8,7,'CANT',1);
	$pdf->Cell(135,7,'                                                                                                     DESCRIPCION',1);
	$pdf->Cell(15,7,'   P. UND',1);
	$pdf->Cell(15,7,'    TOTAL',1);
	$pdf->Ln();
    $pdf->SetFont('Arial','I',7);
$codigoprod= $row_Listado_Productos['codigoprod'];
 	    $cant = $row_Listado_Productos['cantidad'];
		$descrip = $row_Listado_Productos['Producto'];
		$preciound = $row_Listado_Productos['pcompra'];
		$preciototal = number_format($row_Listado_Productos['pcompra']*$cant,2);
	  	
		$pdf->Cell(19,5,$codigoprod,1);
      	$pdf->Cell(8,5,$cant,1);
	  	$pdf->Cell(135,5,$descrip,1);
	  	$pdf->Cell(15,5,number_format($preciound,2),1);
	  	$pdf->Cell(15,5,$preciototal,1);
    $pdf->Ln();
	while($fila = mysql_fetch_array($Listado_Productos))
		{
		$codigoprod= $fila['codigoprod'];
 	    $cant = $fila['cantidad'];
		$descrip = $fila['Producto'];
		$preciound = $fila['pcompra'];
		$preciototal = $fila['pcompra']*$cant;
	  	
		$pdf->Cell(19,5,$codigoprod,1);
      	$pdf->Cell(8,5,$cant,1);
	  	$pdf->Cell(135,5,$descrip,1);
	  	$pdf->Cell(15,5,number_format($preciound,2),1);
	  	$pdf->Cell(15,5,number_format($preciototal,2),1);
		$pdf->Ln();
	}
	$pdf->Ln(6);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(130,6,'                                                                                                                                                                                   Sub Total:   '.$row_Factura['subtotal'],0,1);
	$pdf->Cell(30,6,'                                                                                                                                                                                   IVA 12%:    '.$row_Factura['igv'],0,1); 
	$pdf->Cell(30,6,'                                                                                                                                                                                   Total:          '.number_format($row_Factura['montofact'],2),0,1);
	$pdf->Ln(30);
	$pdf->SetFont('Arial','',6);
	

$pdf->Ln();$pdf->Ln();

$pdf->Output();
?>