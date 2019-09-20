<?php
	$q=$_GET['q'];
	$my_data=mysql_real_escape_string($q);
	$mysqli=mysqli_connect('localhost','root','','ventas02') or die("Database Error");
	$sql="SELECT * FROM producto WHERE nombre_producto LIKE '%$my_data%' ORDER BY nombre_producto";
	$result = mysqli_query($mysqli,$sql) or die(mysqli_error());
	
	if($result)
	{
		while($row=mysqli_fetch_array($result))
		{
			echo $row['nombre_producto']."\n";
		}
	}
?>