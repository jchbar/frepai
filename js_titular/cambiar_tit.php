<?php
	/*Inicia validacion del lado del servidor*/
session_start();
//echo '<script>lleg</script>';
	include_once('../funciones.php');
	//.$_POST['cedula']);
	 if (empty($_POST['id'])){
			$errors[] = "C&eacute;culo vacía";
		}   
	else 
	if ( !empty($_POST['id']))
	{
		// $id=intval($_POST['id']);
		$cedula = $_POST['id'];
		$condicion = (isset($_POST['condicion'])?$_POST['condicion']:'n-a');
		include_once('../dbconfig.php');
		$sql="UPDATE titulares SET status = :condicion WHERE cedula=:cedula";
		// die($sql);
		$con=$db_con->prepare($sql);
		try
		{
			$query_update = $con->execute(array(
					":cedula"=>$cedula,
					":condicion"=>$condicion,
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
	} 
	else {
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