<?php
include 'header.php' 
try{
	$conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
	if (!$conn) {
	echo "Error intentando conectar";
}else{
	echo "conectando correctamente";
}
} catch (Exception $e) {
	echo "eror intentando conectar";
}
include 'fooder.php';