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
if (isset($_GET['codigoventas'])) {
  $colname_Factura = $_GET['codigoventas'];
  $colname_tiplocliente=$_GET['tipocliente'];
}
mysql_select_db($database_Ventas, $Ventas);

$query_Factura = sprintf("SELECT v.codigoventas, v.subtotal, v.igv, v.total, v.fecha_emision, v.codigoclientej, v.codigoventa, j.razonsocial, n.cedula, n.nombre as nombre, n.paterno as paterno, n.materno as materno, j.razonsocial, j.ruc, pe.nombre as nombrep, pe.paterno as paternop, pe.materno as maternop FROM ventas v left join cnatural n on v.codigoclienten=n.codigoclienten left join cjuridico j on j.codigoclientej=v.codigoclientej inner join acceso a on a.codacceso=v.codacceso inner join personal pe on pe.codigopersonal=v.codigopersonal WHERE v.codigoventas = %s", GetSQLValueString($colname_Factura, "int"));


$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());
$row_Factura = mysql_fetch_assoc($Factura);
$totalRows_Factura = mysql_num_rows($Factura);

$colname_Listado_Productos = "-1";
if (isset($_GET['codigo'])) {
  $colname_Listado_Productos = $_GET['codigo'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Listado_Productos = "SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, (a.cantidad*a.pventa) AS total, a.cantidad, a.pventa, a.concatenacion, a.codcomprobante, b.nombre_producto AS Producto, c.nombre AS Marca, d.nombre_presentacion AS Presentacion, e.nombre_color AS Color FROM detalle_ventas a INNER JOIN producto b ON a.codigoprod = b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN presentacion d ON b.codigopresent = d.codigopresent INNER JOIN color e ON b.codigocolor = e.codigocolor WHERE codigo = '$colname_Listado_Productos'";
$Listado_Productos = mysql_query($query_Listado_Productos, $Ventas) or die(mysql_error());
$row_Listado_Productos = mysql_fetch_assoc($Listado_Productos);
$totalRows_Listado_Productos = mysql_num_rows($Listado_Productos);




class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
    $this->Image('../img/logo16102016.jpg',10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(50,10,'FACTURA DE VENTA',0,0,'C');
    // Salto de línea
    $this->Ln(5);
	$this->Cell(80);
    // Título
    $this->Cell(50,20,'HENRY FABIAN ARMIJOS FAJARDO',0,0,'C');
    $this->SetFont('Arial','',10);
	$this->Ln(5);
	$this->Cell(80);
    // Título
    $this->Cell(50,20,'Direccion: Santa Rosa S/N E./ 25 de junio y Sucre',0,0,'C');
	$this->Ln(5);
	$this->Cell(80);
    // Título
    $this->Cell(50,20,'Email: pcplus07@gmail.com - Telf 2967-000 - 5003184',0,0,'C');
    $this->Ln(5);
	$this->Cell(80);
    // Título
    $this->Cell(50,20,'Venta de Computadoras - Servicio Tecnico - Redes - Programas - Juegos y Desarrollo de Software',0,0,'C');
    $this->Ln(2);
	$this->Cell(20);
    // Título
    $this->Cell(50,20,'_________________________________________________________________________________________________________________________________________________________________',0,0,'C');
    
	// Salto de línea
    $this->Ln(12);
}

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
function TablaBasica1($header1,$fechadiag,$horadiag,$personaldiag,$obsdiag)
   {
    //Cabecera
 /*   foreach($header1 as $col1)
    $this->Cell(65,7,$col1,1);
    $this->Ln();
    
      $this->Cell(65,5,$fechadiag.' = '.$horadiag,1);
      $this->Cell(65,5,$personaldiag,1);
	  $this->Cell(65,5,$obsdiag,1);*/
   }
function TablaBasica2($header2,$fechater,$horater,$personalter,$obster)
   {
    //Cabecera
  /*  foreach($header2 as $col2)
    $this->Cell(65,7,$col2,1);
    $this->Ln();
    
      $this->Cell(65,5,$fechater.' = '.$horater,1);
      $this->Cell(65,5,$personalter,1);
	  $this->Cell(65,5,$obster,1);
   */}

function TablaBasica3($header3)
   {
    //Cabecera
  /*  foreach($header3 as $col3)
    $this->Cell(65,7,$col3,1);
    $this->Ln();
    
      $this->Cell(65,5,"hola",1);
      $this->Cell(65,5,"hola2",1);
	  $this->Cell(65,5,"hola2",1);*/
   }


}
// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
//for($i=1;$i<=40;$i++)
//datos basicos

$pdf->Cell(0,6,'CODIGO DE FACTURA:    '.$row_Factura['codigoventas'],0,1);
$pdf->Cell(0,6,'VENDEDOR:                           '.$row_Factura['nombrep'].' '.$row_Factura['paternop'].' ' .$row_Factura['maternop'],0,1);
$pdf->Cell(0,6,'FECHA DE EMISION:            '.$row_Factura['fecha_emision'],0,1);
if ($row_Factura['codigoclientej']>=1)
{	$pdf->Cell(0,7,'Tipo de Cliente:                        '.'JURIDICO',0,1);
	$pdf->Cell(0,7,'RAZON SOCIAL: '.$row_Factura['razonsocial'],0,1);
	$pdf->Cell(0,7,'RUC: '.$row_Factura['ruc'],0,1);

}
else
{	$pdf->Cell(0,7,'Tipo de Cliente:                        '.'NATURAL',0,1);
	$pdf->Cell(0,7,'Nombre del Cliente:                 '.$row_Factura['nombre'].' '.$row_Factura['paterno'].' '.$row_Factura['materno'],0,1);
	$pdf->Cell(0,7,'Cedula:                                     '.$row_Factura['cedula'],0,1);

}
$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,10,'DATOS DE PEDIDO',0,1);
$pdf->SetFont('Times','',8);

	//$this->SetFont('Arial','I',8);
    //Cabecera
    //foreach($header as $col)
    $pdf->Cell(19,7,'Codigo P',1);
	$pdf->Cell(8,7,'Cant',1);
	$pdf->Cell(135,7,'Descripcion',1);
	$pdf->Cell(15,7,'P. Unidad',1);
	$pdf->Cell(15,7,'Total',1);
	$pdf->Ln();
    $pdf->SetFont('Arial','I',7);
$codigoprod= $row_Listado_Productos['codigoprod'];
 	    $cant = $row_Listado_Productos['cantidad'];
		$descrip = $row_Listado_Productos['Producto'];
		$preciound = $row_Listado_Productos['pventa'];
		$preciototal = $row_Listado_Productos['pventa']*$cant;
	  	
		$pdf->Cell(19,5,$codigoprod,1);
      	$pdf->Cell(8,5,$cant,1);
	  	$pdf->Cell(135,5,$descrip,1);
	  	$pdf->Cell(15,5,$preciound,1);
	  	$pdf->Cell(15,5,$preciototal,1);
    $pdf->Ln();
	while($fila = mysql_fetch_array($Listado_Productos))
		{
		$codigoprod= $fila['codigoprod'];
 	    $cant = $fila['cantidad'];
		$descrip = $fila['Producto'];
		$preciound = $fila['pventa'];
		$preciototal = $fila['pventa']*$cant;
	  	
		$pdf->Cell(19,5,$codigoprod,1);
      	$pdf->Cell(8,5,$cant,1);
	  	$pdf->Cell(135,5,$descrip,1);
	  	$pdf->Cell(15,5,$preciound,1);
	  	$pdf->Cell(15,5,$preciototal,1);
		$pdf->Ln();
	}
	$pdf->Ln(12);
	$pdf->SetFont('Arial','',12);
	$pdf->Cell(0,7,'Sub Total:                            '.$row_Factura['subtotal'],0,1);
	$pdf->Cell(0,7,'IVA:                                      '.$row_Factura['igv'],0,1);
	$pdf->Cell(0,7,'Total:                                    '.$row_Factura['total'],0,1);
	$pdf->Ln(30);
	$pdf->SetFont('Arial','',6);
	

$pdf->Ln();$pdf->Ln();

$pdf->Output();
?>