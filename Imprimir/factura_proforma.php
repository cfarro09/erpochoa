<?php
require('../fpdf/fpdf.php');
require_once('../Connections/Ventas.php');


$colname_Agregar = "-1";
if (isset($_GET['codigoproforma'])) {
  $colname_codigoproforma = $_GET['codigoproforma'];
  $colname_codigo = $_GET['codigo'];
  $colname_tipocliente = $_GET['tipocliente'];
}

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT p.codigoproformas, p.subtotal, p.igv, p.total, p.fecha_emision, p.codigoclientej, p.codigoproforma, n.cedula, n.nombre as nombre, n.paterno as paterno, n.materno as materno, j.razonsocial, j.ruc, pe.nombre as nombrep, pe.paterno as paternop, pe.materno as maternop FROM proforma p left join cnatural n on p.codigoclienten=n.codigoclienten left join cjuridico j on j.codigoclientej=p.codigoclientej inner join acceso a on a.codacceso=p.codacceso inner join personal pe on pe.codigopersonal=p.codigopersonal where p.codigoproformas='$colname_codigoproforma'";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);

//consulta de detalle de proformas
$query_Listado1 = "SELECT p.codigoproformas, p.codigoproforma, d.cantidad, pd.codigoprod, pd.nombre_producto, d.pventa FROM proforma p inner join detalle_ventas d on p.codigoproforma=d.codcomprobante inner join producto pd on pd.codigoprod=d.codigoprod where p.codigoproformas='$colname_codigoproforma'";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);

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
    $this->Cell(50,10,'PROFORMA DE VENTA',0,0,'C');
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

function TablaBasica($row_Listado1)
{
	//consulta de detalle de proformas
//	$query_Listado1 = "SELECT p.codigoproformas, p.codigoproforma, d.cantidad, pd.codigoprod, pd.nombre_producto, d.pventa FROM proforma p inner join detalle_ventas d on p.codigoproforma=d.codcomprobante inner join producto pd on pd.codigoprod=d.codigoprod where p.codigoproformas=4";
//	$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
//	$row_Listado1 = mysql_fetch_assoc($Listado1);

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
    while($fila = $row_Listado1)
	{
		$codigoprod= $fila['codigoprod'];
 	    $cant = $fila['cantidad'];
		$descrip = $fila['nombre_producto'];
		$preciound = $fila['pventa'];
		$preciototal = $fila['pventa']*$cant;
	  	
		$this->Cell(19,5,$codigoprod,1);
      	$this->Cell(8,5,$cant,1);
	  	$this->Cell(135,5,$descrip,1);
	  	$this->Cell(15,5,$preciound,1);
	  	$this->Cell(15,5,$preciototal,1);
	}
}
function TablaBasica1($header1,$fechadiag,$horadiag,$personaldiag,$obsdiag)
   {
    //Cabecera
    foreach($header1 as $col1)
    $this->Cell(65,7,$col1,1);
    $this->Ln();
    
      $this->Cell(65,5,$fechadiag.' = '.$horadiag,1);
      $this->Cell(65,5,$personaldiag,1);
	  $this->Cell(65,5,$obsdiag,1);
   }
function TablaBasica2($header2,$fechater,$horater,$personalter,$obster)
   {
    //Cabecera
    foreach($header2 as $col2)
    $this->Cell(65,7,$col2,1);
    $this->Ln();
    
      $this->Cell(65,5,$fechater.' = '.$horater,1);
      $this->Cell(65,5,$personalter,1);
	  $this->Cell(65,5,$obster,1);
   }

function TablaBasica3($header3)
   {
    //Cabecera
    foreach($header3 as $col3)
    $this->Cell(65,7,$col3,1);
    $this->Ln();
    
      $this->Cell(65,5,"hola",1);
      $this->Cell(65,5,"hola2",1);
	  $this->Cell(65,5,"hola2",1);
   }


}
// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
//for($i=1;$i<=40;$i++)
//datos basicos

$pdf->Cell(0,6,'CODIGO DE PROFORMA:    '.$row_Listado['codigoproformas'].' - '.$row_Listado['codigoproforma'],0,1);
$pdf->Cell(0,6,'VENDEDOR:                           '.$row_Listado['nombrep'].' '.$row_Listado['paternop'].' '.$row_Listado['maternop'],0,1);
$pdf->Cell(0,6,'FECHA DE EMISION:            '.$row_Listado['fecha_emision'],0,1);
if ($row_Listado['codigoclientej']>=1)
{	$pdf->Cell(0,7,'Tipo de Cliente:                        '.'JURIDICO',0,1);
	$pdf->Cell(0,7,'RAZON SOCIAL: '.$row_Listado['razonsocial'],0,1);
	$pdf->Cell(0,7,'RUC: '.$row_Listado['ruc'],0,1);

}
else
{	$pdf->Cell(0,7,'Tipo de Cliente:                        '.'NATURAL',0,1);
	$pdf->Cell(0,7,'Nombre del Cliente:                 '.$row_Listado['nombre'].' '.$row_Listado['paterno'].' '.$row_Listado['materno'],0,1);
	$pdf->Cell(0,7,'Cedula:                                     '.$row_Listado['cedula'],0,1);

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
$codigoprod= $row_Listado1['codigoprod'];
 	    $cant = $row_Listado1['cantidad'];
		$descrip = $row_Listado1['nombre_producto'];
		$preciound = $row_Listado1['pventa'];
		$preciototal = $row_Listado1['pventa']*$cant;
	  	
		$pdf->Cell(19,5,$codigoprod,1);
      	$pdf->Cell(8,5,$cant,1);
	  	$pdf->Cell(135,5,$descrip,1);
	  	$pdf->Cell(15,5,$preciound,1);
	  	$pdf->Cell(15,5,$preciototal,1);
    $pdf->Ln();
	while($fila = mysql_fetch_array($Listado1))
		{
		$codigoprod= $fila['codigoprod'];
 	    $cant = $fila['cantidad'];
		$descrip = $fila['nombre_producto'];
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
	$pdf->Cell(0,7,'Sub Total:                            '.$row_Listado['subtotal'],0,1);
	$pdf->Cell(0,7,'IVA:                                      '.$row_Listado['igv'],0,1);
	$pdf->Cell(0,7,'Total:                                    '.$row_Listado['total'],0,1);
	$pdf->Ln(30);
	$pdf->SetFont('Arial','',6);
	$pdf->Cell(0,7,'Valido por 5 dias o hasta agotar Stock',0,1);

$pdf->Ln();$pdf->Ln();

$pdf->Output();
?>