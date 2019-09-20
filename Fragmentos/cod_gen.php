<?php 
//-----Cadena Aletoria-----
$caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"; //posibles caracteres a usar
$numerodeletras=6; //numero de letras para generar el texto
$cadena = ""; //variable para almacenar la cadena generada
for($i=0;$i<$numerodeletras;$i++)
{
$cadena .= substr($caracteres,rand(0,strlen($caracteres)),1); /*Extraemos 1 caracter de los caracteres 
entre el rango 0 a Numero de letras que tiene la cadena */
}
//echo $cadena;

//-----Numero Aletorio-----
$numero = mt_rand(0,999999);
$final= $cadena.$numero;


//-----Fecha y Hora-----
$fech = date("Ymd");
$hora = date ("His");

//Concatenar numero + cadena + fecha + hora
$CodGen=$fech.$final;

//echo $codRegistro;
$_GET['codigo']=$CodGen;

?>


