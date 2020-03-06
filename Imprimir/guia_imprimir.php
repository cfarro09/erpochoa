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

$resultquery = mysql_query("select value from propiedades where `key` = 'logo'", $Ventas) or die(mysql_error());
while($razsocial = mysql_fetch_assoc($resultquery)){
  array_push($datos, $razsocial);
}

$resultquery = mysql_query("select value from propiedades where `key` = 'direccion'", $Ventas) or die(mysql_error());
while($razsocial = mysql_fetch_assoc($resultquery)){
  array_push($datos, $razsocial);
}

$resultquery = mysql_query("select value from propiedades where `key` = 'telefono'", $Ventas) or die(mysql_error());
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
      v.dataguia, v.subtotal, v.igv, v.total, CONCAT(c.paterno,  ' ', c.materno, ' ', c.nombre) as ClienteNatural, c.cedula, cj.razonsocial, cj.ruc,
      c.direccion as direccionn,
      c.cedula, cj.razonsocial, cj.ruc, cj.direccion as direccionj
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

// echo '<pre>';
// print_r($guiatoshow);

// die();

class PDF extends FPDF
{
  function setHeader($direc,$logo,$id,$ruc,$razon_social,$fecha_emision,$cliente,$cedula,$direccion,$guiatoshow)
  {
    $cellMargin = 2 * 1.000125;
    $this->Image('../assets/images/'.$logo,4,4,40);
    $this->SetFont('Arial','B',12);
    $this->SetMargins(15,10);
    $this->Ln(10);
    $this->SetY(38);
    $this->SetFont('Arial','B',16);
    $this->SetXY(45,16);
    $this->MultiCell(80,5,utf8_decode(strtoupper($razon_social)),0,'C');
    $this->SetX(40);
    $this->SetFont('Arial','B',9);
    $this->MultiCell(90,5,$direc,0,'C');
    $this->SetXY(130,20);
    $this->SetXY(15,42);
    $this->SetFont('Arial','',9);
    $this->MultiCell(110,5,utf8_decode("Punto de partida: $direc"),1,'L');
    $this->Ln(2);
    
    $this->MultiCell(110,5,utf8_decode("Punto de llegada: $guiatoshow->puntollegada"),1,'L');
    $this->Ln(2);
    $this->SetFont('Arial','',7);
    $this->MultiCell(110,5,utf8_decode("DATOS DEL DESTINATARIO"),'LTR','C');
    $this->SetFont('Arial','',9);
    $this->MultiCell(110,5,utf8_decode("Nombre o Razon Social: $cliente"),'LR','L');
    $this->MultiCell(110,5,utf8_decode("R.U.C Nº: $cedula"),'LRB','L');
    $y1 = $this->GetY();
    
    // Columna 2
    $this->SetXY(130,20);
    $this->SetFont('Arial','',13);
    $this->MultiCell(65,10,utf8_decode("R.U.C.Nº. $ruc"),1,'C');
    $this->SetXY(130,30);
    $this->SetFont('Arial','B',15);
    $this->MultiCell(65,4,"",'LR','C');
    $this->SetXY(130,34);
    $this->MultiCell(65,7,utf8_decode("GUIA DE REMISION REMITENTE"),'LR','C');
    $this->SetXY(130,48);
    $this->SetTextColor(255,0,0);
    $this->MultiCell(65,7,utf8_decode("Nº ".str_pad($id, 6, "0", STR_PAD_LEFT)),'LRB','C');
    $this->SetTextColor(0,0,0);
    
    $this->SetXY(130,57);
    $this->SetFont('Arial','',7);
    $this->MultiCell(65,5,utf8_decode("DATOS DEL TRANSPORTISTA"),'LTR','C');
    $this->SetFont('Arial','',9);
    $y = $this->GetY();
    $this->SetXY(130,$y);

    $this->MultiCell(65,5,utf8_decode("Nombre o Razon Social: $guiatoshow->nombretransportista"),'LR','L');
    $y = $this->GetY();
    $this->SetXY(130,$y);
    $this->MultiCell(65,5,utf8_decode("R.U.C Nº: $guiatoshow->ructransportista"),'LRB','L');
    $y = $this->GetY();
    $this->SetXY(15,max($y,$y1));
    $this->Ln(3);
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
    for ($i=0; $i < 20; $i++) {
      $cantidad = (isset($detalle[$i]) ? $detalle[$i]->cantidad: '');
      $descripcion = (isset($detalle[$i]) ? $detalle[$i]->nombre_producto: '');
      $precio = (isset($detalle[$i]) ? $detalle[$i]->pventa : '');
      $total = (isset($detalle[$i]) ? $cantidad*$precio: '');
      $this->Cell(20,7,$cantidad,'LB',0,'C');
      $this->Cell(100,7,utf8_decode($descripcion),'LB',0);
      $this->Cell(30,7,$precio,'LB',0,'R');
      $this->Cell(30,7,$total,'LBR',1,'R');
      $this->Ln(0);
    }
    $this->SetFont('Arial','B',9);
    $this->Ln(2);
    $this->Cell(120,5,'',0,0);
    $this->Cell(30,5,'SUB-TOTAL: ',0,0,'R');
    $this->Cell(30,5,$detalle[0]->subtotal,0,1,'R');
    $this->Cell(120,5,'',0,0);
    $this->Cell(30,5,'IGV: ',0,0,'R');
    $this->Cell(30,5,$detalle[0]->igv,0,1,'R');
    $this->Cell(120,5,'',0,0);
    $this->Cell(30,5,'TOTAL: ',0,0,'R');
    $this->Cell(30,5,$detalle[0]->total,0,1,'R');
    
  }
  
  function setFooter($guiatoshow)
  {
    $this->SetDrawColor(0,0,0);
    $this->Ln(10);
    $this->SetFont('Arial','',8);
    $this->Cell(45,8,'Marca de la Unidad de Transporte:','LT',0,'L');
    $this->Cell(55,8,utf8_decode($guiatoshow->marcatransporte),'T',0,'L');
    $this->Cell(48,8,utf8_decode('Número de Certificado de Inscripción'),'T',0,'L');
    $this->Cell(33,8,utf8_decode($guiatoshow->certinscripcion),'TR',1,'L');
    $this->Cell(17,8,utf8_decode('Nº de placa:'),'LB',0,'L');
    $this->Cell(83,8,utf8_decode($guiatoshow->nroplaca),'B',0,'L');
    $this->Cell(39,8,utf8_decode('Nº de Licencia del Conductor:'),'B',0,'L');
    $this->Cell(42,8,utf8_decode($guiatoshow->nlicencia),'BR',0,'L');
  }
}

$pdf = new PDF();
  $pdf->AddPage();
  $cliente = (isset($headerventa['ClienteNatural']) ? $headerventa['ClienteNatural'] : $headerventa['razonsocial']);
  $cedula = (isset($headerventa['cedula']) ? $headerventa['cedula'] : $headerventa['ruc']);
  $direccion = (isset($headerventa['direccionn']) ? $headerventa['direccionn'] : $headerventa['direccionj']);
  $pdf->setHeader($datos[3]['value'],$datos[2]['value'],$guiatoshow->nguia, $datos[0]['value'],$datos[1]['value'],$guiatoshow->fecha, $cliente, $cedula,$direccion,$guiatoshow);
  $pdf->setDetalle($guiatoshow->productos);
  $pdf->setFooter($guiatoshow);
  $pdf->Output(utf8_decode("guia_" . $guiatoshow->nguia . ".pdf"), 'D');
