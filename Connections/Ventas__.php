<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Ventas = "localhost";
$database_Ventas = "ventasochoa";
$username_Ventas = "cfarro";
$password_Ventas = "Test2019,";
$Ventas = mysql_pconnect($hostname_Ventas, $username_Ventas, $password_Ventas) or trigger_error(mysql_error(),E_USER_ERROR); 
?>