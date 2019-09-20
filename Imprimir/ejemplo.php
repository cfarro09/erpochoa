<?php
require('../fpdf/fpdf.php');
require_once('../Connections/Ventas.php');

$colname_Agregar = "-1";
if (isset($_GET['codigosao'])) {
  $colname_codigosao = $_GET['codigosao'];
}

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT O.codigosao, S.nombre as nombreserv, J.codigoclientej, N.codigoclienten, N.nombre, N.paterno, N.materno, N.cedula, J.razonsocial, J.ruc, O.fecha_recepcion, O.hora_recepcion, O.observacion_recepcion, P.nombre as nombrerecp, P.paterno as paternorecp FROM serviciosaofrecer O inner join servicios S ON O.codigosv=S.codigosv left join cjuridico J on J.codigoclientej=O.codigoclientej LEFT JOIN cnatural N on N.codigoclienten=O.codigoclienten inner join personal P on P.codigopersonal=O.personal_recepcion where O.codigosao='$colname_codigosao'";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
//$totalRows_Listado = mysql_num_rows($Listado);

//consulta de servicios en proceso o diagnostico
$query_Listado1 = "SELECT O.codigosao, H.fecha_enproceso, H.hora_enproceso, H.observacion_enproceso, P.nombre, P.paterno FROM serviciosaofrecer O inner join personal P on P.codigopersonal=O.personal_recepcion inner join hist_serv_enproceso H on H.codigosao=O.codigosao where O.codigosao='$colname_codigosao'";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
//$totalRows_Listado = mysql_num_rows($Listado);

//consulta de servicios en culminados o atendidos
$query_Listado2 = "SELECT O.codigosao, A.fecha_atendidos, A.hora_atendidos, A.observacion_atendidos, P.nombre, P.paterno FROM serviciosaofrecer O inner join personal P on P.codigopersonal=O.personal_recepcion inner join hist_serv_atendidos A on A.codigosao=O.codigosao where O.codigosao='$colname_codigosao'";
$Listado2 = mysql_query($query_Listado2, $Ventas) or die(mysql_error());
$row_Listado2 = mysql_fetch_assoc($Listado2);
//$totalRows_Listado = mysql_num_rows($Listado);

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
    $this->Cell(50,30,'REPORTE DE SERVICIOS',0,0,'C');
    // Salto de línea
    $this->Ln(20);
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

function TablaBasica($header,$fecharec,$horarec,$personalrec,$obsrec)
   {
    //Cabecera
    foreach($header as $col)
    $this->Cell(65,7,$col,1);
    $this->Ln();
    
      $this->Cell(65,5,$fecharec.' = '.$horarec,1);
      $this->Cell(65,5,$personalrec,1);
	  $this->Cell(65,5,$obsrec,1);
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

$pdf->Cell(0,30,'Tipo de Servicio '.$row_Listado['nombreserv'].' Codigo de Servicio '.$row_Listado['codigosao'],0,1);
if ($row_Listado['codigoclientej']>=1)
{	$pdf->Cell(0,8,'Tipo de Cliente:'.'JURIDICO',0,1);
	$pdf->Cell(0,8,'RAZON SOCIAL: '.$row_Listado['razonsocial'],0,1);
	$pdf->Cell(0,7,'RUC: '.$row_Listado['ruc'],0,1);

}
else
{	$pdf->Cell(0,8,'Tipo de Cliente:'.'NATURAL',0,1);
	$pdf->Cell(0,8,'Nombre del Cliente: '.$row_Listado['nombre'].' '.$row_Listado['paterno'].' '.$row_Listado['materno'],0,1);
	$pdf->Cell(0,7,'CEDULA: '.$row_Listado['cedula'],0,1);

}
$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,10,'Datos de Recepcion',0,1);
$pdf->SetFont('Times','',12);

//tabla de recipcion
//Títulos de las columnas
$header=array('Fecha y Hora Recepcion','Personal Recepcion', 'Observacion');
$pdf->AliasNbPages();
$pdf->SetY(90);
//$pdf->AddPage();
$fecharec=$row_Listado['fecha_recepcion'];
$horarec=$row_Listado['hora_recepcion'];
$personalrec=$row_Listado['nombrerecp'].' '.$row_Listado['paternorecp'];
$obsrec=$row_Listado['observacion_recepcion'];
$pdf->TablaBasica($header,$fecharec,$horarec,$personalrec,$obsrec);

$pdf->Ln();$pdf->Ln();

$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,8,'Datos de Diagnostico',0,1);
$pdf->SetFont('Times','',12);


//tabla de recipcion
//Títulos de las columnas
$header1=array('Fecha y Hora Recepcion','Personal Diagnostico', 'Observacion');
$pdf->AliasNbPages();
$pdf->SetY(114);
//$pdf->AddPage();
$fechadiag=$row_Listado1['fecha_enproceso'];
$horadiag=$row_Listado1['hora_enproceso'];
$personaldiag=$row_Listado1['nombre'].' '.$row_Listado1['paterno'];
$obsdiag=$row_Listado1['observacion_enproceso'];

$pdf->TablaBasica1($header1,$fechadiag,$horadiag,$personaldiag,$obsdiag);
$pdf->Ln();$pdf->Ln();

//Servicio Atendido
$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,8,'Datos de Entrega',0,1);
$pdf->SetFont('Times','',12);
//tabla de recipcion
//Títulos de las columnas





$header2=array('Fecha y Hora Terminado','Personal Entrega Bien', 'Observacion');
$pdf->AliasNbPages();
$pdf->SetY(139);
//$pdf->AddPage();
$fechater=$row_Listado2['fecha_atendidos'];
$horater=$row_Listado2['hora_atendidos'];
$personalter=$row_Listado2['nombre'].' '.$row_Listado2['paterno'];
$obster=$row_Listado2['observacion_atendidos'];

$pdf->TablaBasica2($header2,$fechater,$horater,$personalter,$obster);
$pdf->Ln();$pdf->Ln();

//Datos de Comprobante
$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,8,'Datos de Comprobante',0,1);
$pdf->SetFont('Times','',12);
//tabla de recipcion
//Títulos de las columnas
$header3=array('Tipo y Numero Comprobante','Forma de Pago', 'Monto');
$pdf->AliasNbPages();
$pdf->SetY(162);
//$pdf->AddPage();
$pdf->TablaBasica3($header3);
$pdf->Ln();$pdf->Ln();

$pdf->Output();
?>