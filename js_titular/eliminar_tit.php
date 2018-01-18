<?php
/*Inicia validacion del lado del servidor*/
session_start();
include_once('../funciones.php');
if (empty($_POST['cedula'])){
	$errors[] = "ID vacío";
}
else 
if (!empty($_POST['cedula']))
{
	// escaping, additionally removing everything that could be (html/javascript-) code
//	$id=intval($_POST['id']);
	$id=$_POST['id'];
	$cedula=$_POST['cedula'];
	$messages[] = "Como aun esta en desarrollo falta verificar otros items para eliminar";
	include_once('../dbconfig.php');
/*
	$sql="SELECT count(com_cuenta) AS cuantos FROM ".$_SESSION[institucion]."sgcaf820 WHERE com_cuenta=:cedula GROUP BY com_cuenta";
	try
	{
		$con=$db_con->prepare($sql);
		$query_delete = $con->execute(array(':cedula' =>$cedula));
	}
	catch(PDOException $e){
		mensaje(array(
			"titulo"=>"Error!",
			"tipo"=>"warning",
			"texto"=>'Falto definir alguno de los valores'.$e->getMessage(),
			));
	}
	if ($con->rowCount() < 1)
	{
*/
		$sql="DELETE FROM titulares WHERE cedula=:cedula";
		$con=$db_con->prepare($sql);
		$query_delete = $con->execute(array(':cedula' =>$cedula));
		if ($query_delete)
			$messages[] = "Los datos han sido eliminados satisfactoriamente.";
		else
			$errors []= "Lo siento algo ha salido mal intenta nuevamente."; // .mysqli_error($con);
/*
	}
	else
		$errors []= "Lo siento... La cuenta tiene movimientos, debe revisar primero"; 
*/
	}
else $errors []= "Error desconocido.";
if (isset($errors))
{
	foreach ($errors as $error) 
	mensaje(array(
		"titulo"=>"Error!",
		"tipo"=>"danger",
		"texto"=>$error,
	));
}
if (isset($messages))
{
	foreach ($messages as $message) 
	mensaje(array(
		"titulo"=>"Bien Hecho!",
		"tipo"=>"success",
		"texto"=>$message,
	));
}

?>	