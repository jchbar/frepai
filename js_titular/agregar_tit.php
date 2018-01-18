<?php
session_start();
/*
error_reporting(E_ALL);
ini_set('display_errors','1');
/*Inicia validacion del lado del servidor*/
//$errors[] = $messages[] = "";
include_once('../funciones.php');
include_once('../dbconfig.php');
if (empty($_POST['cedula']))
{
	$errors[] = "Cedula vacia";
}
else if (empty($_POST['nombre']) or empty($_POST['apellido']))
{
	$errors[] = "Nombre/Apellido de titular vacío";
}
else if ((!empty($_POST['cedula'])) &&  (!empty($_POST['nombre'])))
{
	$cedula = ceroizq($_POST['cedula'],8);
	$numero = $_POST['ubic_pres'].'0'.$_POST['ultimo']; 
	// (isset($_POST['numero'])?ceroizq($_POST['numero'],10):'n-a');
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
/*
	$archivo=$_FILES['file-3'];
	print_r($archivo);
	echo 'archivo '. ($_FILES['file-3']['name']) ;
	if ($_FILES['file-3']['name']!=='') 
	{
	    $archivo = $_FILES['file-3'];
	    // $extension = pathinfo($archivo['file-3'], PATHINFO_EXTENSION);
		// $time = time();
	    // $nombre = "{$_POST['nombre_archivo']}_$time.$extension";
	    $nombre = $cedula;
	    if (move_uploaded_file($archivo['tmp_name'], "fotos/nombre")) {
	        die('subi');
	    } else {
	        die('no subio');
	    }
	}
	else die('no vino na');
	die('espero');
*/
//	$condicion = (isset($_POST['condicion'])?$_POST['condicion']:'n-a');
	$ip=la_ip();
	$usuario=el_usuario();
	$registro=ahora($db_con)['hoy1'];
	//$registro=$registro['hoy'];
	try
	{
		$sql="SELECT cedula FROM titulares WHERE cedula = :cedula";
		$con=$db_con->prepare($sql);
		$query_update = $con->execute(array(
			":cedula"=>$cedula,
		));
		if ($con->rowCount() < 1)
		{
			$sql="INSERT INTO titulares (cedula, numero, ape_tit, nom_tit, status, fechanac, dir_hab, dir_tra, cuenta, civil, ing_ucla, ing_ipsta, inc_nomina, ip_nuevo, ip_modifica, fecha_registro, conyuge, tipocta, usuario, numcuota, acumbs, teltrabajo, telcelular, email, telhabitacion) VALUES (:cedula, :numero, :apellido, :nombre, :condicion, :nacimiento, :habitacion, :trabajo, :cuenta, :estado, :ingucla, :ingipsta, :inclnomina, :ip, :ip, :registro, :conyuge, :tipocta, :usuario, :cero, :cero, :teltrabajo, :telcelular, :email, :telhabitacion)";
			try
			{
				$con=$db_con->prepare($sql);
				$cero=0;
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
					":conyuge"=>$cero,
					":tipocta"=>$cero,
					":cero"=>$cero,
					":usuario"=>$usuario,
					":teltrabajo"=>$teltrabajo,
					":telcelular"=>$telcelular,
					":email"=>$email,
					":telhabitacion"=>$telhabitacion,
				));
			}
			catch(PDOException $e)
			{
				die($e->getMessage());
			mensaje(array(
				"titulo"=>"Error!",
				"tipo"=>"danger",
				"texto"=>$e->getMessage(),
				));
			}
			if ($query_update)
			{
				$messages[] = "Los datos han sido guardados satisfactoriamente.";
			} 
			else
			{
				$errors []= "Lo siento algo ha salido mal intenta nuevamente."; // .mysqli_error($con);
			}
		}
		else 	$errors []= "Lo siento algo ha salido mal intenta nuevamente. Duplicidad"; // .mysqli_error($con);
	}
	catch(PDOException $e){
		die($e->getMessage());
		mensaje(array(
			"titulo"=>"Error!",
			"tipo"=>"danger",
			"texto"=>$e->getMessage(),
			));
	}
} 
else 
{
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