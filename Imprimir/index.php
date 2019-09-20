<?php
include_once('PDF.php');
include_once('myDBC.php');
 
$seleccion = new myDBC();
 
$datosReporte = $seleccion->seleccionar_datos();
 
$pdf = new PDF();
 
$pdf->AddPage();
 
$miCabecera = array( 'Codigo', 'Cantidad', 'Descripcion','P. Unidad', 'Total');
 
$pdf->tablaHorizontal($miCabecera, $datosReporte);
 
$pdf->Output(); //Salida al navegador
?>