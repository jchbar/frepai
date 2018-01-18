<?php
session_start();
extract($_GET);
extract($_POST);
extract($_SESSION);
include("dbconfig.php");
$archivoabuscar=''.$_POST['archivo'];
$contenido = ''; // Contenido del archivo
$archivoabierto=fopen($archivoabuscar, "r");
header( "Content-Type: application/octet-stream");
header( "Content-Disposition: attachment; filename=".$_POST['archivo'].""); 
$lines = file($archivoabuscar);
foreach ($lines as $line_num => $linea) {
	$datos = explode("|", $linea);
	$contenido.=$datos[0]
	;
}
print($contenido);
fclose($archivoabierto);
?> 
