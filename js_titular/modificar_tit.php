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

	$cedula = ceroizq($_POST['cedula'],8);
	$nombre = (isset($_POST['nombre'])?$_POST['nombre']:'n-a');
	$apellido = (isset($_POST['apellido'])?$_POST['apellido']:'n-a');
	$estado = (isset($_POST['estado'])?$_POST['estado']:'n-a');
	$nacimiento = (isset($_POST['nacimiento'])?$_POST['nacimiento']:'1001-01-01');
	$habitacion = (isset($_POST['habitacion'])?$_POST['habitacion']:'n-a');
	$trabajo = (isset($_POST['trabajo'])?$_POST['trabajo']:'n-a');
	$cuenta = (isset($_POST['cuenta'])?$_POST['cuenta']:'n-a');
	$ingucla = (isset($_POST['ingucla'])?$_POST['ingucla']:'1001-01-01');
	$ingipsta = (isset($_POST['ingipsta'])?$_POST['ingipsta']:'1001-01-01');
	$inclnomina = (isset($_POST['inclnomina'])?$_POST['inclnomina']:'1001-01-01');
	$condicion = (isset($_POST['condicion'])?$_POST['condicion']:'n-a');
	$telhabitacion = (isset($_POST['telhabitacion'])?$_POST['telhabitacion']:'n-a');
	$teltrabajo = (isset($_POST['teltrabajo'])?$_POST['teltrabajo']:'n-a');
	$email = (isset($_POST['email'])?$_POST['email']:'n-a');
	$telcelular = (isset($_POST['telcelular'])?$_POST['telcelular']:'n-a');
	$fechanac=explode('/', $nacimiento); $fechanac=$fechanac[2].'-'.$fechanac[0].'-'.$fechanac[1];
	$ingucla=explode('/', $ingucla); $ingucla=$ingucla[2].'-'.$ingucla[0].'-'.$ingucla[1];
	$ingipsta=explode('/', $ingipsta); $ingipsta=$ingipsta[2].'-'.$ingipsta[0].'-'.$ingipsta[1];
	$inclnomina=explode('/', $inclnomina); $inclnomina=$inclnomina[2].'-'.$inclnomina[0].'-'.$inclnomina[1];

		include_once('../dbconfig.php');
		$sql="UPDATE titulares SET ape_tit = :apellido, nom_tit = :nombre, status = :condicion, fechanac = :nacimiento, dir_hab = :habitacion, dir_tra = :trabajo, cuenta =:cuenta, civil = :estado, ing_ucla = :ingucla, ing_ipsta = :ing_ipsta, inc_nomina = :inclnomina, ip_modifica = :ip, fecha_modifica = :registro, teltrabajo = :teltrabajo, telcelular = :telcelular, email = :email, telhabitacion = :telhabitacion WHERE cue_cedula=:cedula";
		$con=$db_con->prepare($sql);
		try
		{
			$query_update = $con->execute(array(
					":cedula"=>$cedula,
					":numero"=>$numero,
					":nombre"=>$nombre,
					":apellido"=>$apellido,
					":condicion"=>$condicion,
					":nacimiento"=>$fechanac,
					":habitacion"=>$habitacion,
					":trabajo"=>$trabajo,
					":cuenta"=>$cuenta,
					":estado"=>$estado,
					":ingucla"=>$ingucla,
					":ingipsta"=>$ingipsta,
					":inclnomina"=>$inclnomina,
					":ip"=>$ip,
					":registro"=>$registro,
					":teltrabajo"=>$teltrabajo,
					":telcelular"=>$telcelular,
					":email"=>$email,
					":telhabitacion"=>$telhabitacion,
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