<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<!--<CENTER> !-->
<P> 
<form name="examen" action="#">
Nombre: <input type="text" name="nombre" /><p>
1- CUAL NO ES UN MANEJADOR DE BASE DE DATOS? <BR>
<input name="opcion_01" type="radio" value="0" />SQLSERVER 
<input name="opcion_01" type="radio" value="0" />MYSQL 
<input name="opcion_01" type="radio" value="0" />POSTGREST 
<input name="opcion_01" type="radio" value="2" />JAVA </P>
<P>
2- QUE SIGNIFICA AMD ? <BR>
<input name="opcion_02" type="radio" value="2" />ADVANCE MICRO DEVICE 
<input name="opcion_02" type="radio" value="0" />MODELO DE DESARROLLO DE ARQUITECTURA 
<input name="opcion_02" type="radio" value="0" />AMBAS
<input name="opcion_02" type="radio" value="0" />NA
<P>
3- CUAL ES UN FABRICANTE DE MICROPROCESADORES?<BR>
<input name="opcion_03" type="radio" value="2" />INTEL 
<input name="opcion_03" type="radio" value="0" />MICROSOFT 
<input name="opcion_03" type="radio" value="0" />ADM 
<input name="opcion_03" type="radio" value="0" />NA
<P>
4- CUAL ES UN LENGUAJE DE PROGRAMACION ORIENTADO A OBJETOS?<BR>
<input name="opcion_04" type="radio" value="2" />JAVA 
<input name="opcion_04" type="radio" value="0" />HTML 
<input name="opcion_04" type="radio" value="0" />SQL 
<input name="opcion_04" type="radio" value="0" />POSTGREST
<P>
5- QUE CMS SE UTILIZA PARA DESARROLLAR UNA TIENDA VIRTUAL ONLINE?<BR>
<input name="opcion_05" type="radio" value="0" />DRUPAL 
<input name="opcion_05" type="radio" value="0" />JOOMLA 
<input name="opcion_05" type="radio" value="2" />PRESTASHOP 
<input name="opcion_05" type="radio" value="0" />WORDPRESS
<P>
6- QUE ES NETBEANS?<BR>
<input name="opcion_06" type="radio" value="0" />MANEJADOR DE DB
<input name="opcion_06" type="radio" value="0" />LENGUAJE DE PROGRAMACIÓN
<input name="opcion_06" type="radio" value="2" />IDE
<input name="opcion_06" type="radio" value="0" />SO
<P>
7- QUE SIGNIFICA CMS?<BR>
<input name="opcion_07" type="radio" value="2" />CONTENT MANAGEMENT SYSTEM
<input name="opcion_07" type="radio" value="0" />SISTEMA DE CAJERO
<input name="opcion_07" type="radio" value="0" />MANEJADOR DE DATOS
<input name="opcion_07" type="radio" value="0" />NA
<P>
8- CUAL NO ES UN SISTEMA OPERATIVO?<BR>
<input name="opcion_08" type="radio" value="2" />PYTHON
<input name="opcion_08" type="radio" value="0" />WINDOWS
<input name="opcion_08" type="radio" value="0" />LINUX
<input name="opcion_08" type="radio" value="0" />IOS
<P>
9-  CUAL NO ES UN PERIFERICO DE ENTRADA?                        <BR>
<input name="opcion_09" type="radio" value="2" />MOUSE
<input name="opcion_09" type="radio" value="0" />TECLADO
<input name="opcion_09" type="radio" value="0" />SCANNER
<input name="opcion_09" type="radio" value="0" />IOS

<P>
10- CUAL ES UN PERIFERICO DE ENTRADA?                        <BR>
<input name="opcion_10" type="radio" value="0" />MONITOR
<input name="opcion_10" type="radio" value="2" />TECLADO
<input name="opcion_10" type="radio" value="0" />IMPRESORA
<input name="opcion_10" type="radio" value="0" />USB                         <BR>

</center>
 <input name="btnCalcular" id="btnCalcular" type="submit" value="Calcular" />
 <input name="btnLimpiar" type="reset" value="Limpiar" />

 </form>   

<?PHP
if(isset($_GET['btnCalcular']))
{	$puntos1=$_GET['opcion_01'];
	$puntos2=$_GET['opcion_02'];
	$puntos3=$_GET['opcion_03'];
	$puntos4=$_GET['opcion_04'];
	$puntos5=$_GET['opcion_05'];
	$puntos6=$_GET['opcion_06'];
	$puntos7=$_GET['opcion_07'];
	$puntos8=$_GET['opcion_08'];
	$puntos9=$_GET['opcion_09'];
	$puntos10=$_GET['opcion_10'];
	$nombre=$_GET['nombre'];
	$nota=$puntos1+$puntos2+$puntos3+$puntos4+$puntos5+$puntos6+$puntos7+$puntos8+$puntos9+$puntos10;
	if($nota==20)
	{
		$pbuena=10;
		$pmala=0;
	}
	if($nota==18)
	{		$pbuena=9;
		$pmala=1;
	}
	if($nota==16)
	{		$pbuena=8;
		$pmala=2;
	}
	if($nota==14)
	{		$pbuena=7;
		$pmala=3;
	}
	if($nota==12)
	{		$pbuena=6;
		$pmala=4;
	}
	if($nota==10)
	{		$pbuena=5;
		$pmala=5;
	}
	if($nota==8)
	{		$pbuena=4;
		$pmala=6;
	}
	if($nota==6)
	{		$pbuena=3;
		$pmala=7;
	}
	if($nota==4)
	{		$pbuena=2;
		$pmala=8;
	}
	if($nota==2)
	{		$pbuena=1;
		$pmala=9;
	}
	if($nota==0)
	{		$pbuena=0;
		$pmala=10;
	}
		$nota=$pbuena*2-$pmala;

	echo $nombre." Tiene la siguiente Nota <br>SU NOTA ES "+$nota+"<br> Cantidad de Preguntas buenas es ".$pbuena."<br>Cantidad de Preguntas Malas es ".$pmala;
	
}
?>

</body>
</html>