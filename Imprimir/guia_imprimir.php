<?php
require('../fpdf/fpdf.php');
require_once('../Connections/Ventas.php');
mysql_select_db($database_Ventas, $Ventas);
$datos = array();
$detalle = array();

# Datos de la empresa
$resultquery = mysql_query("select value from propiedades where `key` = 'ruc'", $Ventas) or die(mysql_error());
while($ruc = mysql_fetch_assoc($resultquery)){
  array_push($datos, $ruc);
}

$resultquery = mysql_query("select value from propiedades where `key` = 'razonsocial'", $Ventas) or die(mysql_error());
while($razsocial = mysql_fetch_assoc($resultquery)){
  array_push($datos, $razsocial);
}

if (isset($_GET['idventas'])) {
  $idventas = $_GET['idventas'];
}
if (isset($_GET['idguia'])) {
  $idguia = $_GET['idguia'];
}
$querydetalle = "
    select 
      v.dataguia, v.subtotal, v.igv, v.total, CONCAT(c.paterno,  ' ', c.materno, ' ', c.nombre) as ClienteNatural, c.cedula, cj.razonsocial, cj.ruc
    from ventas v
    left join cnatural c on c.codigoclienten = v.codigoclienten
    left join  cjuridico cj on cj.codigoclientej = v.codigoclientej
    where v.codigoventas = $idventas";

$resultquery = mysql_query($querydetalle, $Ventas) or die(mysql_error());

$headerventa = mysql_fetch_assoc($resultquery); 

$guias = json_decode($headerventa["dataguia"]);

for ($i=0; $i < count($guias); $i++) { 
  if($guias[$i]->id == $idguia){
    $guiatoshow = $guias[$i];
    break;
  }
}
$guiatoshow->productos[0]->igv = $headerventa["igv"];
$guiatoshow->productos[0]->total = $headerventa["total"];
$guiatoshow->productos[0]->subtotal = $headerventa["subtotal"];

class PDF extends FPDF
{
  function setHeader($id,$ruc,$razon_social,$fecha_emision,$cliente,$cedula)
  {
    $this->SetFont('Arial','B',15);
    $this->SetMargins(15,10);
    $this->Ln(10);
    $this->Cell(0,8,utf8_decode("Guia Nº $id"),0,2,'C');
    $this->Ln(1);
    $this->SetFont('Arial','B',9);
    $this->Cell(140,8,utf8_decode("RAZON SOCIAL: ".strtoupper($razon_social)),0,0,'L');
    $this->Cell(0,8,"FECHA: $fecha_emision",0,1,'L');
    $this->Cell(0,8,utf8_decode("RUC: $ruc"),0,2,'L');
    $this->Ln(5);
    $this->Cell(0,8,utf8_decode("CLIENTE: ".strtoupper($cliente)),0,2,'L');
    $this->Cell(0,8,utf8_decode("RUC/DNI: $cedula"),0,2,'L');
    $this->Ln(5);
  }

  function setDetalle($detalle)
  {
    $this->SetFillColor(230,230,230);
    $this->SetDrawColor(147,147,147);
    $this->Cell(20,8,'CANTIDAD',1,0,'C',true);
    $this->Cell(100,8,'DESCRIPCION',1,0,'C',true);
    $this->Cell(30,8,'P. UNIDAD',1,0,'C',true);
    $this->Cell(30,8,'TOTAL',1,1,'C',true);
    $this->SetFont('Arial','',9);
    for ($i=0; $i < 17; $i++) {
      $cantidad = (isset($detalle[$i]) ? $detalle[$i]->cantidad: '');
      $descripcion = (isset($detalle[$i]) ? $detalle[$i]->nombre_producto: '');
      $precio = (isset($detalle[$i]) ? $detalle[$i]->pventa : '');
      $total = (isset($detalle[$i]) ? $cantidad*$precio: '');
      $this->Cell(20,8,$cantidad,'LB',0,'C');
      $this->Cell(100,8,utf8_decode($descripcion),'LB',0);
      $this->Cell(30,8,$precio,'LB',0,'R');
      $this->Cell(30,8,$total,'LBR',1,'R');
      $this->Ln(0);
    }
    $this->SetFont('Arial','B',9);
    $this->Ln(4);
    $this->Cell(120,8,'',0,0);
    $this->Cell(30,8,'SUB-TOTAL: ',0,0,'R');
    $this->Cell(30,8,$detalle[0]->subtotal,0,1,'R');
    $this->Cell(120,8,'',0,0);
    $this->Cell(30,8,'IGV: ',0,0,'R');
    $this->Cell(30,8,$detalle[0]->igv,0,1,'R');
    $this->Cell(120,8,'',0,0);
    $this->Cell(30,8,'TOTAL: ',0,0,'R');
    $this->Cell(30,8,$detalle[0]->total,0,1,'R');
    
  }
  
  function setFooter()
  {
    $this->Ln(24);
    $this->SetFont('Arial','B',10);
    $this->Cell(0,8,'GUIA VALIDA POR 7 DIAS O HASTA AGOTAR STOCK.',0,0,'C');
  }
}

$pdf = new PDF();
$pdf->AddPage();
$cliente = (isset($headerventa['ClienteNatural']) ? $headerventa['ClienteNatural'] : $headerventa['razonsocial']);
$cedula = (isset($headerventa['cedula']) ? $headerventa['cedula'] : $headerventa['ruc']);
$pdf->setHeader($guiatoshow->nguia, $datos[0]['value'],$datos[1]['value'],$guiatoshow->fecha, $cliente, $cedula);
$pdf->setDetalle($guiatoshow->productos);
// $pdf->setFooter();
$pdf->Output(utf8_decode("guia_" . $guiatoshow->nguia . ".pdf"), 'D');
