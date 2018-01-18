<?php
	/*Inicia validacion del lado del servidor*/
session_start();

error_reporting(E_ALL);
ini_set('display_errors','1');
include('../funciones.php');
include_once('../dbconfig.php');
if (empty($_POST['codigo']))
{
	$errors[] = "C&oacute;digo vacía";
} else if (empty($_POST['nombre']))
{
	$errors[] = "Nombre vacío";
}   else if (
			!empty($_POST['codigo']) && 
			!empty($_POST['nombre']) 
		)
{
	try
	{
	$codigo = ceroizq($_POST['codigo'],3);
	$rif = ($_POST['rif']);
	$nombre = (isset($_POST['nombre'])?$_POST['nombre']:'n-a');
	$casa = (isset($_POST['casa'])?$_POST['casa']:'n-a');
	$direccion = (isset($_POST['direccion'])?$_POST['direccion']:'n-a');
	$telf1 = (isset($_POST['telf1'])?$_POST['telf1']:'n-a');
	$telf2 = (isset($_POST['telf2'])?$_POST['telf2']:'n-a');
	$tipo_interes = (isset($_POST['estado'])?$_POST['estado']:'n-a');
	$nrocuotas = (isset($_POST['nrocuotas'])?$_POST['nrocuotas']:0);
	$interes = (isset($_POST['interes'])?$_POST['interes']:0);
	$int_dif = (isset($_POST['int_dif'])?$_POST['int_dif']:2);
	$ip=la_ip();
	$usuario=el_usuario();
	$registro=ahora($db_con)['hoy1'];
		//$registro=$registro['hoy'];
	include_once('../dbconfig.php');
	$sql="UPDATE proveedores SET nombre = :nombre, rif = :rif, telefono =:telefono, casa = :casa, direccion = :direccion, telefono2 = :telefono2, ip_modifica = :ip, fecha_modifica = :registro, interes = :interes, tipo_interes = :tipo_interes, maxcuotas = :nrocuotas WHERE codigo = :codigo ";
	$con=$db_con->prepare($sql);
	$visible=1;
	$query_update = $con->execute(array(
			":nombre"=>$nombre,
			":rif"=>$rif,
			":telefono"=>$telf1,
			":codigo"=>$codigo,
			":casa"=>$casa,
			":direccion"=>$direccion,
			":telefono2"=>$telf2,
			":ip"=>$ip,
			":registro"=>$registro,
			":interes"=>$interes,
			":tipo_interes"=>$tipo_interes,
			":nrocuotas"=>$nrocuotas,
		));
		if ($query_update){
			$messages[] = "Los datos han sido actualizados satisfactoriamente.";
		} else{
			$errors []= "Lo siento algo ha salido mal intenta nuevamente."; // .mysqli_error($con);
		}
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
/*
if (isset($messages))
{
	foreach ($messages as $message) 
	mensaje(array(
		"titulo"=>"Bien Hecho!",
		"tipo"=>"success",
		"texto"=>$message,
	));
}
*/			
?>	