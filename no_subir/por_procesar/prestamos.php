<?php
include_once('../dbconfig.php');
include_once('../funciones.php');
// coloca las cedulas con 0 adelante
$sql="SELECT cedula FROM `prestamos` WHERE length(trim(cedula))<8";
$res=$db_con->prepare($sql);
$res->execute();
while ($fila = $res->fetch(PDO::FETCH_ASSOC))
{
	$original = $cedula=$fila['cedula'];
	$tamano=strlen(trim($cedula));
	$cedula=ceroizq($cedula,(8-tamano));
	// echo $cedula.'<br>';
	$sql="update prestamos set cedula = :cedula where cedula = :original";
	$resu=$db_con->prepare($sql);
	$resu->execute(array(
		":cedula"=>$cedula,
		":original"=>$original,
		));
}
?>
