<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Ventas = "cosajosaochoa.com";
$database_Ventas = "cosajosaochoa_ventas";
$username_Ventas = "cosajosaochoa_robertojvh";
$password_Ventas = "1.robertojvh.1";
$Ventas = mysql_connect($hostname_Ventas, $username_Ventas, $password_Ventas) or trigger_error(mysql_error(),E_USER_ERROR); 

