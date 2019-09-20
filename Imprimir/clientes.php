<?php error_reporting(0);
 
require('../fpdf/fpdf.php');
 require_once('../Connections/Ventas.php');

//Consulta la tabla productos solicitando todos los productos
 mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT * FROM cnatural order by codigoclienten desc";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
 
//Instaciamos la clase para genrear el documento pdf
$pdf=new FPDF();
 
//Agregamos la primera pagina al documento pdf
$pdf->AddPage();
 
//Seteamos el inicio del margen superior en 25 pixeles

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
}
 
$y_axis_initial = 25;
 
//Seteamos el tiupo de letra y creamos el titulo de la pagina. No es un encabezado no se repetira
$pdf->SetFont('Arial','B',12);
 
$pdf->Cell(40,6,'',0,0,'C');
$pdf->Cell(100,6,'LISTA DE CLIENTES',1,0,'C');
 
$pdf->Ln(10);
 
//Creamos las celdas para los titulo de cada columna y le asignamos un fondo gris y el tipo de letra
$pdf->SetFillColor(232,232,232);
 
$pdf->SetFont('Arial','B',10);
$pdf->Cell(125,6,'NOMBRE',1,0,'C',1);
 
$pdf->Cell(30,6,'CODIGO',1,0,'C',1);
//$pdf->Cell(30,6,'Foto',1,0,'C',1);
$pdf->Cell(30,6,'CIUDAD',1,0,'C',1);
 
$pdf->Ln(10);
 $dato=1;
//Comienzo a crear las fiulas de productos según la consulta mysql
// $fila = mysql_fetch_array($Listado);
    $nombre = $row_Listado['nombre'];
 
    $codigoclienten = $row_Listado['codigoclienten'];
	$ciudad = $row_Listado['ciudad'];
	
    //$imagen=$row['codigomarca'];
 
   
    $pdf->Cell(125,15,$nombre,1,0,'L',0);
 
       $pdf->Cell(30,15,$codigoclienten,1,0,'R',1);
	   $pdf->Cell(30,15,$ciudad,1,0,'R',1);
 
$pdf->Ln(15);

while($fila = mysql_fetch_array($Listado))
{
 
    $nombre = $fila['nombre'];
 
    $codigoclienten = $fila['codigoclienten'];
	$ciudad = $fila['ciudad'];
	
    //$imagen=$row['codigomarca'];
 
   
    $pdf->Cell(125,15,$nombre,1,0,'L',0);
 
       $pdf->Cell(30,15,$codigoclienten,1,0,'R',1);
	   $pdf->Cell(30,15,$ciudad,1,0,'R',1);
 
$pdf->Ln(15);
 
}
 
mysql_close($enlace);
 
//Mostramos el documento pdf
$pdf->Output();

 
?>
