<?php
	/*Inicia validacion del lado del servidor*/
session_start();
	include_once('../funciones.php');
	 if (empty($_POST['cedula'])){
			$errors[] = "C&eacute;culo vacía";
		} else if (empty($_POST['nombre'])){
			$errors[] = "Nombre vacío";
		}   else if (
			!empty($_POST['cedula']) && 
			!empty($_POST['nombre']) 
		){

		// $id=intval($_POST['id']);
		$cedula = $_POST['cedula'];
		$nombre = $_POST['nombre'];
		include_once('../dbconfig.php');
		$sql="UPDATE titulares SET nom_tit=:nombre WHERE cue_cedula=:cedula";
		$con=$db_con->prepare($sql);
		try
		{
			$query_update = $con->execute(array(
				":cedula"=>$cedula,
				":nombre"=>$nombre,
				));
		}
		catch(PDOException $e){
			mensaje(array(
				"titulo"=>"Error!",
				"tipo"=>"warning",
				"texto"=>'Falto definir alguno de los valores'.$e->getMessage().$sql,
				));
//			$con2->ventana_alerta('Fallo!!!...', 'Falto definir alguno de los valores', 'warning');
//			die($e->getMessage().'Falto definir alguno de los valores');
			// echo 'Fallo la conexion';
		}
		if ($query_update){
			$messages[] = "Los datos han sido actualizados satisfactoriamente.";
		} else{
			$errors []= "Lo siento algo ha salido mal intenta nuevamente."; // .mysqli_error($con);
		}
		} else {
			$errors []= "Error desconocido.";
		}

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