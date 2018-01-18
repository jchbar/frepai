<?php
 
//$con = mysql_connect('localhost','pruebas','12345') or die('Error en conexion a la DB');
// mysql_select_db('jquery', $con) or die('Error al seleccionar la DB');
 include('../dbconfig.php');
$nombre = $_POST['name'];
$parametro = $_POST['parametro'];

$sql='insert into configuracion (parametro, nombre) values (:parametro, :nombre)';
$con=$db_con->prepare($sql);
$res=$con->execute(array(
	":parametro"=>$parametro,
	":nombre"=>$nombre,
	));
if($res){
	echo "1";
}
else{
	echo "2";
}
 
 
?>