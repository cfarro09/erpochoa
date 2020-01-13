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

if (isset($_GET['id'])) {
  $id = $_GET['id'];
}
$querydetalle = "
    select
        dpro.*,p.nombre_producto, m.nombre as marca,
        pro.codigoproformas as nro_proforma,
        pro.*,
        CONCAT(c.paterno, ' ', c.materno, ' ', c.nombre) as ClienteNatural,
        c.direccion as direccionn,
        c.cedula, cj.razonsocial, cj.ruc, cj.direccion as direccionj
    from
        detalle_proforma dpro
    inner join proforma pro on pro.codigoproformas = dpro.codigoproforma
    inner join producto p on p.codigoprod = dpro.codigoprod
    inner join marca m on m.codigomarca = p.codigomarca
    left join cnatural c on c.codigoclienten = pro.codigoclienten
    left join  cjuridico cj on cj.codigoclientej = pro.codigoclientej
    where
        dpro.codigoproforma = $id";

$resultquery = mysql_query($querydetalle, $Ventas) or die(mysql_error());
while($res = mysql_fetch_assoc($resultquery)){
  array_push($detalle, $res);
}

// var_dump($detalle[15]);die();
class PDF extends FPDF
{
  function setHeader($id,$ruc,$razon_social,$fecha_emision,$cliente,$cedula,$direccion, $asunto,$referencia)
  {
    $this->Image('../assets/images/logoochoa.jpeg',4,4,40);
    $this->SetFont('Arial','B',12);
    $this->SetMargins(15,10);
    $this->Ln(10);
    $this->SetY(38);
    $this->Cell(0,8,utf8_decode("Proforma Nº $id"),0,2,'C');
    $this->Ln(1);
    $this->SetFont('Arial','B',9);
    $this->SetXY(40,16);
    $this->MultiCell(50,5,utf8_decode(strtoupper($razon_social)),0,'C');
    $this->SetX(40);
    $this->MultiCell(50,5,utf8_decode("RUC $ruc"),0,'C');
    $this->SetY(40);
    $this->Cell(0,5,"FECHA: $fecha_emision",0,1,'R');
    // $this->Ln(5);
    $this->Cell(0,5,utf8_decode("SEÑOR(ES)"),0,2,'L');
    $this->Cell(0,5,utf8_decode(strtoupper($cliente)),0,2,'L');
    $this->SetFont('Arial','',9);
    $this->Cell(0,5,utf8_decode($direccion),0,2,'L');
    $this->Cell(0,5,"Presente.-",0,2,'L');
    $this->Ln(3);
    $this->SetFont('Arial','B',9);
    $this->Cell(35,5,'ASUNTO: ',0,0,'L'); // maxleng 82
    $this->SetFont('Arial','',9);
    $this->Cell(0,5,utf8_decode(strtoupper($asunto)),0,1,'L');
    $this->SetFont('Arial','B',9);
    $this->Cell(35,5,'REFERENCIA: ',0,0,'L'); // maxleng 225
    $this->SetFont('Arial','',9);
    $this->MultiCell(0,5,utf8_decode(strtoupper($referencia)),0,'J');
    $this->Ln(2);
    // $this->SetFont('Arial','',9);
    // $this->MultiCell(40,5,utf8_decode('Estimados Señores:'),1,'L');
    // $this->MultiCell(40,5,utf8_decode('Por medio del presente los saludamos cordialmente y a la vez alcanzamos nuestra cotización de tuber'),1,'L');
  }

  function setDetalle($detalle,$razon_social)
  {
    $this->SetFillColor(230,230,230);
    $this->SetDrawColor(147,147,147);
    $this->Cell(20,8,'CANTIDAD',1,0,'C',true);
    $this->Cell(100,8,'DESCRIPCION',1,0,'C',true);
    $this->Cell(30,8,'P. UNIDAD',1,0,'C',true);
    $this->Cell(30,8,'TOTAL',1,1,'C',true);
    $this->SetFont('Arial','',9);
    for ($i=0; $i < 19; $i++) {
      $cantidad = (isset($detalle[$i]) ? $detalle[$i]['cantidad']: '');
      $descripcion = (isset($detalle[$i]) ? $detalle[$i]['nombre_producto']: '');
      $precio = (isset($detalle[$i]) ? $detalle[$i]['pventa']: '');
      $total = (isset($detalle[$i]) ? $detalle[$i]['totalventa']: '');
      $this->Cell(20,6,$cantidad,'LB',0,'C');
      $this->Cell(100,6,utf8_decode($descripcion),'LB',0);
      $this->Cell(30,6,$precio,'LB',0,'R');
      $this->Cell(30,6,$total,'LBR',1,'R');
      $this->Ln(0);
    }
    $this->SetFont('Arial','B',9);
    $this->Ln(0);
    $this->Cell(120,5,'',0,0);
    $this->SetFont('Arial','B',9);
    $this->Cell(30,5,'TOTAL: ','LBR',0,'R');
    $this->Cell(30,5,$detalle[0]['total'],'BR',1,'R');
    $this->Ln(2);
    $this->SetFont('Arial','UB',9);
    $this->Cell(120,5,'CONDICIONES DE VENTA',0,2);
    $this->Ln(2);
    $this->SetFont('Arial','',9);
    $this->Cell(40,5,'FORMA DE PAGO',0,0,'L');
    $this->Cell(0,5,'Al contado, Cheque o deposito en cuenta corriente de '.utf8_decode($razon_social),0,1,'L');
    $this->Cell(40,5,'FORMA DE ENTREGA',0,0,'L');
    $this->MultiCell(0,5,utf8_decode('Por parciales previa coordinación, después de confirmado la orden de compra y cancelado el pedido, con adelantos de material en stock de nuestro almacén.'),0,'J');
    $this->Cell(40,5,'LUGAR DE ENTREGA',0,0,'L');
    $this->MultiCell(0,5,utf8_decode('En obra, directo del transporte del fabricante desde Lima, siempre que el área geografica lo permita.'),0,'J');
    $this->Cell(40,5,'VALIDEZ DE OFERTA',0,0,'L');
    $this->MultiCell(0,5,utf8_decode('7 Dias, salvo alza de precios del fabricante, los precios incluyen el IGV'),0,'J');
    $this->Cell(40,5,'CUENTAS CORRIENTES',0,0,'L');
    $this->SetFont('Arial','B',9);
    $this->Cell(70,5,utf8_decode('BCP Nº 575-0008105-0-48'),0,0,'L');
    $this->Cell(70,5,utf8_decode('BBVA Nº 0265-0100007764'),0,1,'L');
  }
  
  function setFooter()
  {
    $this->Ln(2);
    $this->SetFont('Arial','',9);
    $this->MultiCell(0,5,utf8_decode('A la espera que nuestra contiazción se acepte, con la generación de la respectiva orden de compra y sin otro particular quedamos de Ustedes.'),0,'J');
  }
}

$pdf = new PDF();
$pdf->AddPage();
$cliente = (isset($detalle[0]['ClienteNatural']) ? $detalle[0]['ClienteNatural'] : $detalle[0]['razonsocial']);
$cedula = (isset($detalle[0]['cedula']) ? $detalle[0]['cedula'] : $detalle[0]['ruc']);
$direccion = (isset($detalle[0]['direccionn']) ? $detalle[0]['direccionn'] : $detalle[0]['direccionj']);
$pdf->setHeader($id,$datos[0]['value'],$datos[1]['value'],$detalle[0]['fecha_emision'],$cliente,$cedula,$direccion,$detalle[0]['asunto'],$detalle[0]['referencia']);
$pdf->setDetalle($detalle,$datos[1]['value']);
$pdf->setFooter();
$pdf->Output(utf8_decode("reporte_proforma_" . $id . ".pdf"), 'D');
// $pdf->Output();
?>