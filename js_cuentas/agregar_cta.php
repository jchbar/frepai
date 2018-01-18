<?php
session_start();
/*Inicia validacion del lado del servidor*/
//$errors[] = $messages[] = "";
include_once('../funciones.php');
if (empty($_POST['codigo']))
{
	$errors[] = "Codigo vacío";
}
else if (empty($_POST['nombre']))
{
	$errors[] = "Nombre de cuenta vacío";
}
else if (!empty($_POST['codigo']) &&  !empty($_POST['nombre']))
{
	$codigo = $_POST['codigo'];
	$nombre = $_POST['nombre'];
	try
	{
		include_once('../dbconfig.php');
		$sql="select * from niveles order by con_nivel";
		$result=$db_con->prepare($sql);
		$result->execute();
		$tamano=strlen(trim($codigo));
		$niveles = 0;
		$elnivel=0;
		while($row=$result->fetch(PDO::FETCH_ASSOC)) 
		{
			$niveles ++;
			if ($tamano == $row['con_nivel'])
				$elnivel=$niveles;
		}
		$sql="SELECT cue_codigo FROM  cuentas WHERE cue_codigo = :codigo";
		$con=$db_con->prepare($sql);
		$query_update = $con->execute(array(
			":codigo"=>$codigo,
		));
		if ($con->rowCount() < 1)
		{
			$sql="INSERT INTO cuentas (cue_codigo, cue_nombre, cue_saldo, cue_nivel, cod_viejo, cue_deb01, cue_cre01, cue_deb02, cue_cre02, cue_deb03, cue_cre03, cue_deb04, cue_cre04, cue_deb05, cue_cre05, cue_deb06, cue_cre06, cue_deb07, cue_cre07, cue_deb08, cue_cre08, cue_deb09, cue_cre09, cue_deb10, cue_cre10, cue_deb11, cue_cre11, cue_deb12, cue_cre12, cue_deb13, cue_cre13, cue_deb14, cue_cre14, cue_deb15, cue_cre15, cue_deb16, cue_cre16, cue_deb17, cue_cre17, cue_deb18, cue_cre18, cue_deb19, cue_cre19) VALUES (:codigo, :nombre, :saldo, :nivel, :viejo, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01, :deb01, :cre01)";
			try
			{
				$con=$db_con->prepare($sql);
				$cero=0;
				$viejo='x';
//				$query_update=1;
				$query_update = $con->execute(array(
					":codigo"=>$codigo,
					":nombre"=>$nombre,
					":nivel"=>$elnivel,
					":saldo"=>$cero,
					":viejo"=>$viejo,
					":deb01"=>$cero,
					":cre01"=>$cero,
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