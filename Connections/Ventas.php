<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Ventas = "localhost";
$database_Ventas = "ochoa_050919";
$username_Ventas = "root";
$password_Ventas = "";
$Ventas = mysql_connect($hostname_Ventas, $username_Ventas, $password_Ventas) or trigger_error(mysql_error(),E_USER_ERROR); 

?>

