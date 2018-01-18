<?php
/*Inicia validacion del lado del servidor*/
session_start();
include_once('../funciones.php');
if (empty($_POST['codigo'])){
	$errors[] = "ID vacío";
}
else 
if (!empty($_POST['codigo']))
{
	// escaping, additionally removing everything that could be (html/javascript-) code
//	$id=intval($_POST['id']);
	$codigo=$_POST['codigo'];
	include_once('../dbconfig.php');
	$sql="SELECT count(com_cuenta) AS cuantos FROM detalle_contable WHERE com_cuenta=:codigo GROUP BY com_cuenta";
	try
	{
		$con=$db_con->prepare($sql);
		$query_delete = $con->execute(array(':codigo' =>$codigo));
	}
	catch(PDOException $e)
	{
		mensaje(array(
			"titulo"=>"Error!",
			"tipo"=>"warning",
			"texto"=>'Falto definir alguno de los valores'.$e->POSTMessage(),
			));
	}
	if ($con->rowCount() < 1)
	{
		$sql="DELETE FROM cuentas WHERE cue_codigo=:codigo";
		$con=$db_con->prepare($sql);
		$query_delete = $con->execute(array(':codigo' =>$codigo));
		if ($query_delete)
			$messages[] = "Los datos han sido eliminados satisfactoriamente.";
		else
			$errors []= "Lo siento algo ha salido mal intenta nuevamente."; // .mysqli_error($con);
	}
	else
		$errors []= "Lo siento... La cuenta tiene movimientos, debe revisar primero"; 
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