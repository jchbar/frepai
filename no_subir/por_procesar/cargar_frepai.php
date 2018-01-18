<?php
// pasar informacion de dbf_socios a titulares
include_once('../dbconfig.php');
include_once('../funciones.php');
try
{
	$sql="truncate table ahorros_frepai ";
	$res=$db_con->prepare($sql);
	$res->execute();
	$sql="SELECT * FROM dbf_frepai ";
	$res=$db_con->prepare($sql);
	$res->execute();
	$ip_nuevo=la_ip();
	$fecha_registro=ahora($db_con)['ahora'];
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$cedula=$fila['cedula'];
		$aporte_mensual=$fila['aporte_ord'];
		$dividendo_mensual=$fila['div_mensual'];
		$ahorrado=$fila['disponible'];
		$codigo=$fila['codigo'];
		$fecha=$fila['fecha'];
		$ubicacion=$fila['ubicacion'];
		$inscripcion=$fila['inscripcion'];
		$status=$fila['status'];
		$teltrabajo = $telcelular = $email = $telhabitacion = $usuario = $ip_modifica='';
		$cotizacion = 0;
		// fechas
		$fecha=explode('/',$fecha); $fecha=$fecha[0].'-'.$fecha[1].'-'.$fecha[2];
		$inscripcion=explode('/',$inscripcion); $inscripcion=$inscripcion[0].'-'.$inscripcion[1].'-'.$inscripcion[2];
		// verifico fechas
		// echo substr($fechanac,0,1).'<br>';
		$fechanac = (substr($fechanfechaac,0,1) == '-'?'1001-01-01':$fecha);
		$inscripcion = (substr($inscripcion,0,1) == '-'?'1001-01-01':$inscripcion);

		$sql="INSERT INTO ahorros_frepai (cedula, aporte_mensual, dividendo_mensual, codigo, inscripcion,  status, ahorrado, ip_nuevo, fecha_registro, ip_modifica) VALUES (:cedula, :aporte_mensual, :dividendo_mensual, :codigo, :inscripcion,  :status, :ahorrado, :ip_nuevo, :fecha_registro, :ip_modifica)";
			$res2=$db_con->prepare($sql);
			$res2->execute([
				":cedula"=>$cedula,
				":aporte_mensual"=>$aporte_mensual,
				":dividendo_mensual"=>$dividendo_mensual,
				":codigo"=>$codigo,
				":inscripcion"=>$inscripcion,
				":status"=>$status,
				":ahorrado"=>$ahorrado,
				":ip_nuevo"=>$ip_nuevo,
				":ip_modifica"=>$ip_modifica,
				":fecha_registro"=>$fecha_registro,
				]);

	}
	// coloca las cedulas de titulares con 0 adelante
	$sql="SELECT cedula FROM `ahorros_frepai` WHERE length(trim(cedula))<8";
	$res=$db_con->prepare($sql);
	$res->execute();
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$original = $cedula=$fila['cedula'];
		$tamano=strlen(trim($cedula));
		$cedula=ceroizq($cedula,(8-tamano));
		// echo $cedula.'<br>';
		$sql="update ahorros_frepai set cedula = :cedula where cedula = :original";
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
