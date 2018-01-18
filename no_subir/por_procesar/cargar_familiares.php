<?php
// pasar informacion de dbf_socios a titulares
include_once('../dbconfig.php');
include_once('../funciones.php');
try
{
	$sql="truncate table beneficiarios ";
	$res=$db_con->prepare($sql);
	$res->execute();
	$sql="SELECT * FROM dbf_beneficiario ";
	$res=$db_con->prepare($sql);
	$res->execute();
	$ip_nuevo=la_ip();
	$fecha_registro=ahora($db_con)['ahora'];
//	$fecha_registro=explode('/',$fecha_registro);
//	$fecha_registro=$fecha_registro[2].'-'.$fecha_registro[0].'-'.$fecha_registro[1];
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$cedulafam=$fila['cedulafam'];
		$cedulaemp=$fila['cedulaemp'];
		$apellidos=$fila['apellidos'];
		$nombres=$fila['nombres'];
		$parentesco=$fila['parentesco'];
		$fecha_nac=$fila['fechanac'];
		$teltrabajo = $telcelular = $email = $telhabitacion = $usuario = $ip_modifica='';

		$sql="INSERT INTO beneficiarios (cedulafam, cedulaemp, apellidos, nombres, fechanac, parentesco, ip_registro, fecha_registro) VALUES (:cedulafam, :cedulaemp, :apellidos, :nombres, :fecha_nac, :parentesco, :ip_nuevo, :fecha_registro)";
			$res2=$db_con->prepare($sql);
			$res2->execute([
				":cedulafam"=>$cedulafam,
				":cedulaemp"=>$cedulaemp,
				":apellidos"=>$apellidos,
				":nombres"=>$nombres,
				":fecha_nac"=>$fecha_nac,
				":parentesco"=>$parentesco,
				":ip_nuevo"=>$ip_nuevo,
				":fecha_registro"=>$fecha_registro,
				]);

	}
	// coloca las cedulas de beneficiarios con 0 adelante
	echo 'colocando cero a empleados<br>';
	$sql="SELECT cedulaemp FROM `beneficiarios` WHERE length(trim(cedulaemp))<8";
	$res=$db_con->prepare($sql);
	$res->execute();
	flush(); 
	ob_flush();
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$original = $cedula=$fila['cedulaemp'];
		$tamano=strlen(trim($cedula));
		$cedula=ceroizq($cedula,(8-tamano));
		// echo $cedula.'<br>';
		$sql="update beneficiarios set cedulaemp = :cedula where cedulaemp = :original";
//		echo $sql.'-'.$original.'-'.$cedula;
		$resu=$db_con->prepare($sql);
		$resu->execute(array(
			":cedula"=>$cedula,
			":original"=>$original,
			));
	}

	echo 'colocando cero a familiares<br>';
	// coloca las cedulas de beneficiarios con 0 adelante
	$sql="SELECT cedulafam FROM `beneficiarios` WHERE length(trim(cedulafam))<8";
	$res=$db_con->prepare($sql);
	$res->execute();
	flush(); 
	ob_flush();
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$original = $cedula=$fila['cedulafam'];
		$tamano=strlen(trim($cedula));
		$cedula=ceroizq($cedula,(8-tamano));
		// echo $cedula.'<br>';
		$sql="update beneficiarios set cedulafam = :cedula where cedulafam = :original";
		$resu=$db_con->prepare($sql);
		$resu->execute(array(
			":cedula"=>$cedula,
			":original"=>$original,
			));
	}

}
catch(PDOException $e){
	die( $e->getMessage());
}
echo 'Finalizado';


?>
