<?php require_once('../Connections/Ventas.php'); ?>
<?php
$totalfact=0;
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

mysql_select_db($database_Ventas, $Ventas);
$query_Configuracion = "SELECT * FROM configuracion";
$Configuracion = mysql_query($query_Configuracion, $Ventas) or die(mysql_error());
$row_Configuracion = mysql_fetch_assoc($Configuracion);
$totalRows_Configuracion = mysql_num_rows($Configuracion);

mysql_select_db($database_Ventas, $Ventas);
$query_Empresa = "SELECT * FROM empresa";
$Empresa = mysql_query($query_Empresa, $Ventas) or die(mysql_error());
$row_Empresa = mysql_fetch_assoc($Empresa);
$totalRows_Empresa = mysql_num_rows($Empresa);

$colname_Factura = "-1";
if (isset($_GET['codigoventas'])) {
  $colname_Factura = $_GET['codigoventas'];
  $colname_tiplocliente=$_GET['tipocliente'];
}
mysql_select_db($database_Ventas, $Ventas);
if($colname_tiplocliente=='j'){
$query_Factura = sprintf("SELECT a.codigoventas, a.codigoventa, a.tipocomprobante, a.codigobanco, a.numerotarjeta, a.codigocomprobante, a.tipo_pago, a.codigoclientej, a.subtotal, a.igv, a.total, a.fecha_emision, a.hora_emision, b.codigoclientej, b.razonsocial AS ClienteJuridico, b.codigoclientej, b.ruc, CONCAT (c.paterno, ' ',c.materno, ' ', c.nombre) AS Personal FROM ventas a INNER JOIN cjuridico b ON a.codigoclientej = b.codigoclientej INNER JOIN personal c ON a.codigopersonal = c.codigopersonal WHERE a.codigoventas = %s", GetSQLValueString($colname_Factura, "int"));
}
else
{
$query_Factura = sprintf("SELECT a.codigoventas, a.codigoventa, a.tipocomprobante, a.codigobanco, a.numerotarjeta, a.codigocomprobante, a.tipo_pago, a.codigoclientej, a.subtotal, a.igv, a.total, a.fecha_emision, a.hora_emision, b.codigoclienten, CONCAT (b.paterno, ' ',b.materno, ' ', b.nombre) AS ClienteJuridico, b.codigoclienten as codigoclientej, b.cedula as ruc, CONCAT (c.paterno, ' ',c.materno, ' ', c.nombre) AS Personal FROM ventas a INNER JOIN cnatural b ON a.codigoclienten = b.codigoclienten INNER JOIN personal c ON a.codigopersonal = c.codigopersonal WHERE a.codigoventas = %s", GetSQLValueString($colname_Factura, "int"));

}

$Factura = mysql_query($query_Factura, $Ventas) or die(mysql_error());
$row_Factura = mysql_fetch_assoc($Factura);
$totalRows_Factura = mysql_num_rows($Factura);

$colname_Listado_Productos = "-1";
if (isset($_GET['codigo'])) {
  $colname_Listado_Productos = $_GET['codigo'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Listado_Productos = sprintf("SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, (a.cantidad*a.pventa) AS total, a.cantidad, a.pventa, a.concatenacion, a.codcomprobante, b.nombre_producto AS Producto, c.nombre AS Marca, d.nombre_presentacion AS Presentacion, e.nombre_color AS Color FROM detalle_ventas a INNER JOIN producto b ON a.codigoprod = b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN presentacion d ON b.codigopresent = d.codigopresent INNER JOIN color e ON b.codigocolor = e.codigocolor WHERE codigo = %s", GetSQLValueString($colname_Listado_Productos, "text"));
$Listado_Productos = mysql_query($query_Listado_Productos, $Ventas) or die(mysql_error());
$row_Listado_Productos = mysql_fetch_assoc($Listado_Productos);
$totalRows_Listado_Productos = mysql_num_rows($Listado_Productos);
?>
<?php
include("Fragmentos/cabecera.php");
?>

    

<table width="100%" border="0" align="center">
<tbody>
<tr>
<td>

 
<table width="100%" border="0">
<tbody>
<tr >
<td width="70%"><img src="../img/<?php echo $row_Configuracion['logo']; ?>" width="250" height="100" alt=""/></td>
<td width="30%" rowspan="2">

<table width="100%" height="150" border="1" cellpadding="0" cellspacing="0" >
  <tbody>
    <tr >
      <td align="center" valign="middle"><h5><strong>R.U.C. N&deg; <?php echo $row_Empresa['ruc_empre']; ?></strong><br>
        <br>
      </h5>
        <h5><strong>FACTURA ELECTR&Oacute;NICA</strong><br>
          <br>
        </h5>
        <h5><strong>N&deg;</strong> <strong><?php echo $row_Factura['codigoventas']; ?></strong></h5></td>
    </tr>
  </tbody>
</table>

</td>
</tr>
<tr> 
<td valign="top">
    <h5 style="font-weight: bold" class="font-blue-soft"><?php echo $row_Empresa['nombre_empre']; ?></h5><br>
    <span class="font-xs font-blue-chambray"><?php echo $row_Empresa['detalle_empresa']; ?></span><br>
    <span class="font-xs font-blue-chambray"><?php echo $row_Empresa['detalle_sucursal']; ?></span></td>
</tr>
<tr>
  <td colspan="2" valign="top"><hr class="border-dark bor"></td>
  </tr>
</tbody>
</table>

 



</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td ><table width="100%" border="0">
  <tbody>
    <tr>
      <td width="33%" valign="top"><strong class="font-ms font-blue-soft">Facturado a:</strong><br>
          <span class="font-xs font-blue-chambray"><?php echo $row_Factura['ClienteJuridico']; ?></span><br>
        <strong class="font-ms font-blue-soft">RUC N&deg; Cliente: </strong>
        <span class="font-xs font-blue-chambray"><?php echo $row_Factura['ruc']; ?></span></td>
      <td width="33%" valign="top"><strong class="font-ms font-blue-soft">C&oacute;digo Cliente:</strong> 
          <span class="font-xs font-blue-chambray"><?php echo $row_Factura['codigoclientej']; ?></span></td>
      <td width="33%" valign="top"><strong class="font-ms font-blue-soft">C&oacute;digo Vendedor:</strong> 
          <span class="font-xs font-blue-chambray"><?php echo $row_Factura['codigoclientej']; ?></span><br>
        <strong class="font-ms font-blue-soft"> Vendedor: </strong>
        <span class="font-xs font-blue-chambray"><?php echo $row_Factura['Personal']; ?></span></td>
    </tr>
  </tbody>
</table></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td><table width="100%" border="1">
  <tbody>
    <tr>
      <td width="20%" align="left" valign="top"><strong class="font-ms font-blue-soft">Fecha y Hora de Emisi&oacute;n:<br>
      </strong>
          <span class="font-xs font-blue-chambray"><?php echo $row_Factura['fecha_emision']; ?> <?php echo $row_Factura['hora_emision']; ?></span><br>        
        </td>
        <td width="20%" align="left" valign="top"><strong class="font-ms font-blue-soft">Fecha y Hora de Vencimiento:</strong><br> <span class="font-xs font-blue-chambray"><?php echo $siguiente=date("Y-m-d",strtotime($row_Factura[fecha_emision])+86400)." 00:00:00";?></span> <br></td>
      <td width="20%" align="left" valign="top"><strong class="font-ms font-blue-soft">Moneda:</strong><br>
          <span class="font-ms font-blue-chambray">Dolar Estadounidense</span></td>
      <td width="20%" align="left" valign="top"><strong class="font-ms font-blue-soft">N&uacute;mero de Pedido:</strong></td>
      <td width="20%" align="left" valign="top"><strong class="font-ms font-blue-soft">Otro Documento:</strong></td>
    </tr>
    <tr>
      <td colspan="5" align="left" valign="top"><table width="100%" border="0">
        <tbody>
          <tr>
              <td width="33%"><strong class="font-ms font-blue-soft">C&oacute;digo de Pago:</strong> <span class="font-xs font-blue-chambray"><?php echo $row_Factura['codigocomprobante']; ?></span></td>
                        <?php 
            if ($row_Factura['tipo_pago']== 'p_c')
            {
            $tipo_pago = "Al Contado";
            }
            else if ($row_Factura['tipo_pago']== 'tcrc')
            {
            $tipo_pago = "Tarjeta de Credito";
            }
            else 
            {
            $tipo_pago = "Otros";
            }

            ?>
            
              <td width="33%"><strong class="font-ms font-blue-soft">Condici&oacute;n de Pago:</strong> <span class="font-xs font-blue-chambray"><?php echo $tipo_pago ?></span></td>
              <td width="33%"><strong class="font-ms font-blue-soft">Orden de Compra:</strong> <span class="font-xs font-blue-chambray"><?php echo $row_Factura['codigoventa']; ?></span></td>
          </tr>
        </tbody>
      </table></td>
      </tr>
  </tbody>
</table></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>
    <table width="100%" border="1" >
      <tbody>
        <tr>
          <td width="10%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">C&oacute;digo</strong></td>
          <td width="6%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">Cantidad</strong></td>
          <td width="6%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">Presentaci&oacute;n</strong></td>
          <td width="30%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">Producto</strong></td>
          <td width="8%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">Valor Unitario</strong></td>
          <td width="6%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">Descuento</strong></td>
          <td width="8%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">Precio Unitario</strong></td>
          <td width="10%" colspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">IVA / ISC</strong></td>
          <td width="16%" rowspan="2" align="center" valign="middle"><strong class="font-ms font-blue-soft">Total</strong></td>
          </tr>
        <tr>
          <td width="10%" align="center" valign="middle"><strong class="font-ms font-blue-soft">%</strong></td>
          <td width="5%" align="center" valign="middle"><strong class="font-ms font-blue-soft">Monto</strong></td>
          </tr>
          <?php do { ?>
          <tr>
          <td><span class="font-xs font-blue-chambray"><?php echo $row_Listado_Productos['codigodetalleproducto']; ?></span></td>
          <td><span class="font-xs font-blue-chambray"><center><?php echo $row_Listado_Productos['cantidad']; ?></center></span></td>
          <td><span class="font-xs font-blue-chambray"><?php echo $row_Listado_Productos['Presentacion']; ?></span></td>
          <td><span class="font-xs font-blue-chambray"><?php echo $row_Listado_Productos['Producto']; ?> <?php echo $row_Listado_Productos['Marca']; ?> <?php echo $row_Listado_Productos['Color']; ?></span></td>
          <td><span class="font-xs font-blue-chambray"><center><?php  echo "&#36; ".$row_Listado_Productos['pventa']; ?></center></span></td>
          <td><span class="font-xs font-blue-chambray">&nbsp;</span></td>
          <td><span class="font-xs font-blue-chambray"><?php  echo "&#36; ".$row_Listado_Productos['pventa']; ?></span></td>
          <td><span class="font-xs font-blue-chambray"><center>14%</center></span></td>
          <td><span class="font-xs font-blue-chambray"><?php  echo "&#36; ".($row_Listado_Productos['pventa']*0.14); ?></span></td>
          <td><span class="font-xs font-blue-chambray"><?php  echo "&#36; ".($row_Listado_Productos['total']);
		  $totalfact=$totalfact+$row_Listado_Productos['total']; ?></span></td>
          </tr>
           <?php } while ($row_Listado_Productos = mysql_fetch_assoc($Listado_Productos)); ?>
        <tr>
        
        
        <?php 

$numero = $row_Factura['total']; 
$cambio = valorEnLetras($numero); 


//echo "numero = $numero"; 
//echo "<br>"; 
//echo "cambio = $cambio"; ?>
            <td colspan="10"><strong class="font-ms font-blue-soft">Son:</strong><span class="font-xs font-blue-chambray"><?php echo $cambio?></span></td>
            <?php 

function valorEnLetras($x) 
{ 
if ($x<0) { $signo = "menos ";} 
else      { $signo = "";} 
$x = abs ($x); 
$C1 = $x; 

$G6 = floor($x/(1000000));  // 7 y mas 

$E7 = floor($x/(100000)); 
$G7 = $E7-$G6*10;   // 6 

$E8 = floor($x/1000); 
$G8 = $E8-$E7*100;   // 5 y 4 

$E9 = floor($x/100); 
$G9 = $E9-$E8*10;  //  3 

$E10 = floor($x); 
$G10 = $E10-$E9*100;  // 2 y 1 


$G11 = round(($x-$E10)*100,0);  // Decimales 
////////////////////// 

$H6 = unidades($G6); 

if($G7==1 AND $G8==0) { $H7 = "Cien "; } 
else {    $H7 = decenas($G7); } 

$H8 = unidades($G8); 

if($G9==1 AND $G10==0) { $H9 = "Cien "; } 
else {    $H9 = decenas($G9); } 

$H10 = unidades($G10); 

if($G11 < 10) { $H11 = "0".$G11; } 
else { $H11 = $G11; } 

///////////////////////////// 
    if($G6==0) { $I6=" "; } 
elseif($G6==1) { $I6="Millón "; } 
         else { $I6="Millones "; } 
          
if ($G8==0 AND $G7==0) { $I8=" "; } 
         else { $I8="Mil "; } 
          
$I10 = "D&oacute;lares con "; 
$I11 = "/100 Centavos"; 

$C3 = $signo.$H6.$I6.$H7.$I7.$H8.$I8.$H9.$I9.$H10.$I10.$H11.$I11; 

return $C3; //Retornar el resultado 

} 

function unidades($u) 
{ 
    if ($u==0)  {$ru = " ";} 
elseif ($u==1)  {$ru = "Un ";} 
elseif ($u==2)  {$ru = "Dos ";} 
elseif ($u==3)  {$ru = "Tres ";} 
elseif ($u==4)  {$ru = "Cuatro ";} 
elseif ($u==5)  {$ru = "Cinco ";} 
elseif ($u==6)  {$ru = "Seis ";} 
elseif ($u==7)  {$ru = "Siete ";} 
elseif ($u==8)  {$ru = "Ocho ";} 
elseif ($u==9)  {$ru = "Nueve ";} 
elseif ($u==10) {$ru = "Diez ";} 

elseif ($u==11) {$ru = "Once ";} 
elseif ($u==12) {$ru = "Doce ";} 
elseif ($u==13) {$ru = "Trece ";} 
elseif ($u==14) {$ru = "Catorce ";} 
elseif ($u==15) {$ru = "Quince ";} 
elseif ($u==16) {$ru = "Dieciseis ";} 
elseif ($u==17) {$ru = "Decisiete ";} 
elseif ($u==18) {$ru = "Dieciocho ";} 
elseif ($u==19) {$ru = "Diecinueve ";} 
elseif ($u==20) {$ru = "Veinte ";} 

elseif ($u==21) {$ru = "Veintiun ";} 
elseif ($u==22) {$ru = "Veintidos ";} 
elseif ($u==23) {$ru = "Veintitres ";} 
elseif ($u==24) {$ru = "Veinticuatro ";} 
elseif ($u==25) {$ru = "Veinticinco ";} 
elseif ($u==26) {$ru = "Veintiseis ";} 
elseif ($u==27) {$ru = "Veintisiente ";} 
elseif ($u==28) {$ru = "Veintiocho ";} 
elseif ($u==29) {$ru = "Veintinueve ";} 
elseif ($u==30) {$ru = "Treinta ";} 

elseif ($u==31) {$ru = "Treintayun ";} 
elseif ($u==32) {$ru = "Treintaydos ";} 
elseif ($u==33) {$ru = "Treintaytres ";} 
elseif ($u==34) {$ru = "Treintaycuatro ";} 
elseif ($u==35) {$ru = "Treintaycinco ";} 
elseif ($u==36) {$ru = "Treintayseis ";} 
elseif ($u==37) {$ru = "Treintaysiete ";} 
elseif ($u==38) {$ru = "Treintayocho ";} 
elseif ($u==39) {$ru = "Treintaynueve ";} 
elseif ($u==40) {$ru = "Cuarenta ";} 

elseif ($u==41) {$ru = "Cuarentayun ";} 
elseif ($u==42) {$ru = "Cuarentaydos ";} 
elseif ($u==43) {$ru = "Cuarentaytres ";} 
elseif ($u==44) {$ru = "Cuarentaycuatro ";} 
elseif ($u==45) {$ru = "Cuarentaycinco ";} 
elseif ($u==46) {$ru = "Cuarentayseis ";} 
elseif ($u==47) {$ru = "Cuarentaysiete ";} 
elseif ($u==48) {$ru = "Cuarentayocho ";} 
elseif ($u==49) {$ru = "Cuarentaynueve ";} 
elseif ($u==50) {$ru = "Cincuenta ";} 

elseif ($u==51) {$ru = "Cincuentayun ";} 
elseif ($u==52) {$ru = "Cincuentaydos ";} 
elseif ($u==53) {$ru = "Cincuentaytres ";} 
elseif ($u==54) {$ru = "Cincuentaycuatro ";} 
elseif ($u==55) {$ru = "Cincuentaycinco ";} 
elseif ($u==56) {$ru = "Cincuentayseis ";} 
elseif ($u==57) {$ru = "Cincuentaysiete ";} 
elseif ($u==58) {$ru = "Cincuentayocho ";} 
elseif ($u==59) {$ru = "Cincuentaynueve ";} 
elseif ($u==60) {$ru = "Sesenta ";} 

elseif ($u==61) {$ru = "Sesentayun ";} 
elseif ($u==62) {$ru = "Sesentaydos ";} 
elseif ($u==63) {$ru = "Sesentaytres ";} 
elseif ($u==64) {$ru = "Sesentaycuatro ";} 
elseif ($u==65) {$ru = "Sesentaycinco ";} 
elseif ($u==66) {$ru = "Sesentayseis ";} 
elseif ($u==67) {$ru = "Sesentaysiete ";} 
elseif ($u==68) {$ru = "Sesentayocho ";} 
elseif ($u==69) {$ru = "Sesentaynueve ";} 
elseif ($u==70) {$ru = "Setenta ";} 

elseif ($u==71) {$ru = "Setentayun ";} 
elseif ($u==72) {$ru = "Setentaydos ";} 
elseif ($u==73) {$ru = "Setentaytres ";} 
elseif ($u==74) {$ru = "Setentaycuatro ";} 
elseif ($u==75) {$ru = "Setentaycinco ";} 
elseif ($u==76) {$ru = "Setentayseis ";} 
elseif ($u==77) {$ru = "Setentaysiete ";} 
elseif ($u==78) {$ru = "Setentayocho ";} 
elseif ($u==79) {$ru = "Setentaynueve ";} 
elseif ($u==80) {$ru = "Ochenta ";} 

elseif ($u==81) {$ru = "Ochentayun ";} 
elseif ($u==82) {$ru = "Ochentaydos ";} 
elseif ($u==83) {$ru = "Ochentaytres ";} 
elseif ($u==84) {$ru = "Ochentaycuatro ";} 
elseif ($u==85) {$ru = "Ochentaycinco ";} 
elseif ($u==86) {$ru = "Ochentayseis ";} 
elseif ($u==87) {$ru = "Ochentaysiete ";} 
elseif ($u==88) {$ru = "Ochentayocho ";} 
elseif ($u==89) {$ru = "Ochentaynueve ";} 
elseif ($u==90) {$ru = "Noventa ";} 

elseif ($u==91) {$ru = "Noventayun ";} 
elseif ($u==92) {$ru = "Noventaydos ";} 
elseif ($u==93) {$ru = "Noventaytres ";} 
elseif ($u==94) {$ru = "Noventaycuatro ";} 
elseif ($u==95) {$ru = "Noventaycinco ";} 
elseif ($u==96) {$ru = "Noventayseis ";} 
elseif ($u==97) {$ru = "Noventaysiete ";} 
elseif ($u==98) {$ru = "Noventayocho ";} 
else            {$ru = "Noventaynueve ";} 
return $ru; //Retornar el resultado 
} 

function decenas($d) 
{ 
    if ($d==0)  {$rd = "";} 
elseif ($d==1)  {$rd = "Ciento ";} 
elseif ($d==2)  {$rd = "Doscientos ";} 
elseif ($d==3)  {$rd = "Trescientos ";} 
elseif ($d==4)  {$rd = "Cuatrocientos ";} 
elseif ($d==5)  {$rd = "Quinientos ";} 
elseif ($d==6)  {$rd = "Seiscientos ";} 
elseif ($d==7)  {$rd = "Setecientos ";} 
elseif ($d==8)  {$rd = "Ochocientos ";} 
else            {$rd = "Novecientos ";} 
return $rd; //Retornar el resultado 
} 
?>
          </tr>
        </tbody>
    </table>
   </td>
</tr>
<tr>
<td><table width="100%" border="0">
  <tbody>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="60%">
      <table width="100%" border="1">
  <tbody>
    <tr>
        <td valign="top"><strong class="font-ms font-blue-soft">Observaciones:</strong>
        <br>
        <span class="font-xs font-blue-chambray">Somos Agentes de retencion del I.V.A.</span>
        </td>
    </tr>
  </tbody>
</table>

      </td>
      <td width="5%">&nbsp;</td>
      <td width="45%"><table width="100%" border="1">
        <tbody>
          <tr>
            <td valign="top"><span class="font-xs font-blue-chambray"></span>
              <span class="font-xs font-blue-chambray"><p>SUB TOTAL: <?php echo($totalfact-$totalfact*0.14); ?></p></span>
              <span class="font-xs font-blue-chambray"><p>I.V.A.: <?php echo($totalfact*0.14); ?></p></span>
              <span class="font-xs font-blue-chambray"><p>TOTAL: <?php echo($totalfact); ?></p></span></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table></td>
</tr>
<tr>
    <td class="font-xs">Representaci&oacute;n imprensa de la FACTURA ELECTR&Oacute;NICA</td>
</tr>
<tr>
<td><hr class="border-dark"></td>
</tr>
</tbody>
</table>

                  
<?php include("Fragmentos/pie.php"); 
?>
<?php
mysql_free_result($Configuracion);

mysql_free_result($Empresa);

mysql_free_result($Factura);

mysql_free_result($Listado_Productos);
?>
