<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors','1');
/*Inicia validacion del lado del servidor*/
//$errors[] = $messages[] = "";
include_once('../funciones.php');
include_once('../dbconfig.php');
if (empty($_POST['ultimo']))
{
	$errors[] = "Codigo vacio";
}
else if (empty($_POST['nombre']))
{
	$errors[] = "Descripcion vacía";
}
else if ((!empty($_POST['ultimo'])) &&  (!empty($_POST['nombre'])))
{
	$codigo = ceroizq($_POST['ultimo'],3);
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
	try
	{
		$sql="INSERT INTO proveedores (cedula, nombre, rif, telefono, casa, codigo, direccion, telefono2, ip_nuevo, ip_modifica, fecha_registro, fecha_modifica, visible, interes, tipo_interes, interes_diferido, maxcuotas) VALUES (:cedula, :nombre, :rif, :telefono, :casa, :codigo, :direccion, :telefono2, :ip, :ip, :registro, :registro, :visible, :interes, :tipo_interes, :int_dif, :maxcuotas)";
		$con=$db_con->prepare($sql);
		$cero=0;
		$visible=1;
		$vacio='';
		$tipo="Estatutario";
		$query_update = $con->execute(array(
			":cedula"=>'',
			":nombre"=>$nombre,
			":rif"=>$rif,
			":telefono"=>$telf1,
			":codigo"=>$codigo,
			":casa"=>$casa,
			":direccion"=>$direccion,
			":telefono2"=>$telf2,
			":ip"=>$ip,
			":registro"=>$registro,
			":visible"=>$visible,
			":interes"=>$interes,
			":tipo_interes"=>$tipo_interes,
			":int_dif"=>$int_dif,
			":maxcuotas"=>$nrocuotas,
		));
		if ($query_update)
		{
			$messages[] = "Los datos han sido guardados satisfactoriamente.";
		} 
		else
		{
			$errors []= "Lo siento algo ha salido mal intenta nuevamente."; // .mysqli_error($con);
		}
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