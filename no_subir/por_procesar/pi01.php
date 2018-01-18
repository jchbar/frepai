<?php
// pasar informacion de movimientos a frepai, cotizacion y prestamos o servicios (tentantivamente)
/*
en titulares
	update  `titulares` set status = 'ACTIVO' where status='A';
	update  `titulares` set status = 'JUBILA' where status='J';
*/

include_once('../dbconfig.php');
include_once('../funciones.php');
$actualizar_039=0;
$actualizar_099=0;
$actualizar_losdemas=0;
// coloca las cedulas de titulares con 0 adelante
$sql="SELECT cedula FROM `titulares` WHERE length(trim(cedula))<8";
$res=$db_con->prepare($sql);
$res->execute();
while ($fila = $res->fetch(PDO::FETCH_ASSOC))
{
	$original = $cedula=$fila['cedula'];
	$tamano=strlen(trim($cedula));
	$cedula=ceroizq($cedula,(8-tamano));
	// echo $cedula.'<br>';
	$sql="update titulares set cedula = :cedula where cedula = :original";
	$resu=$db_con->prepare($sql);
	$resu->execute(array(
		":cedula"=>$cedula,
		":original"=>$original,
		));
}
try
{
	if ($actualizar_039 == 1)
	{ 
		echo 'procesando 039<br>';
		act_039($db_con);
		echo 'terminado 039<br>';
	}
	if ($actualizar_099 == 1)
	{ 
		echo 'procesando 099<br>';
		act_099($db_con);
		echo 'terminado 099<br>';
	}
	if ($actualizar_losdemas == 1)
	{ 
		echo 'procesando los demas<br>';
		act_losdemas($db_con);
		echo 'terminado los demas<br>';
	}
}
catch(PDOException $e){
	die( $e->getMessage());
}

function act_039($db_con)
{
	$sql="select * from movimientos where codigo = 039 order by cedula";
	$res=$db_con->prepare($sql);
	$res->execute();
	$ultima_cotizacion='2017-12-31';
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$cedula=$fila['cedula'];
		$cedula=ceroizq($cedula,8);
		$monto=$fila['cuota'];
		$sql="SELECT cedula, status FROM `titulares` WHERE cedula=:cedula";
		// echo $sql. $cedula;
		$resu=$db_con->prepare($sql);
		$resu->execute(array(
			":cedula"=>$cedula,
			));
		$filau=$resu->fetch(PDO::FETCH_ASSOC);
		// echo $resu->rowCount();
		// echo $filau['status']);
		if ((trim($filau['status']) == 'ACTIVO') or (trim($filau['status']) == 'JUBILA'))
		{
			$sqlu="update titulares set cotizacion = :monto, ult_cotizacion = :ultima_cotizacion where cedula = :cedula";
			$resu=$db_con->prepare($sqlu);
			// echo $sqlu;
			$resu->execute(array(
				":cedula"=>$cedula,
				":monto"=>$monto,
				":ultima_cotizacion"=>$ultima_cotizacion,
				));
		}
		if ($monto == 0)
		{
			$sqlu="update titulares set status = :suspende where cedula = :cedula";
			$resu=$db_con->prepare($sqlu);
			// echo $sqlu;
			$resu->execute(array(
				":cedula"=>$cedula,
				":suspende"=>"SUSPEN",
				));
		}
	}
}

function act_099($db_con)
{
	$sql="select * from frepai order by cedula";
	$res=$db_con->prepare($sql);
	$res->execute();
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$cedula=$fila['cedula'];
		$cedula=ceroizq($cedula,8);
		$monto=$fila['cuota'];
		$sql="SELECT cedula FROM `ahorros_frepai` WHERE cedula=:cedula";
		// echo $sql. $cedula;
		$resu=$db_con->prepare($sql);
		$resu->execute(array(
			":cedula"=>$cedula,
			));
		$filau=$resu->fetch(PDO::FETCH_ASSOC);
		// echo $resu->rowCount();
		// echo $filau['status']);
		if ($resu->rowCount() > 0) 
		{
			$aporte=$fila['aporte_ord'];
			$dividendo=$fila['div_mensual'];
			$ahorros=$fila['disponible'];
			$status=$fila['status'];
			$sqlu="update ahorros_frepai set aporte_mensual= :aporte, dividendo_mensual= :dividendo, ahorrado= :ahorros, status= :status where cedula = :cedula";
			$resu=$db_con->prepare($sqlu);
			// echo $sqlu;
			$resu->execute(array(
				":cedula"=>$cedula,
				":aporte"=>$aporte,
				":dividendo"=>$dividendo,
				":ahorros"=>$ahorros,
				":status"=>$status,
				));
		}
		else
		{
			$aporte=$fila['aporte_ord'];
			$dividendo=$fila['div_mensual'];
			$ahorros=$fila['disponible'];
			$status=$fila['status'];
			$codigo=$fila['codigo'];
			$retiro=$fila['retiro'];
			$inscripcion=$fila['inscripcion'];
			$sqlu="insert into ahorros_frepai (cedula, aporte_mensual, dividendo_mensual, codigo, retiro, inscripcion, ahorrado, status) VALUES (:cedula, :aporte, :dividendo, :codigo, :retiro, :inscripcion, :ahorrado, :status)";
			$resu=$db_con->prepare($sqlu);
			// echo $sqlu;
			$resu->execute(array(
				":cedula"=>$cedula,
				":aporte"=>$aporte,
				":dividendo"=>$dividendo,
				":codigo"=>$codigo,
				":retiro"=>$retiro,
				":inscripcion"=>$inscripcion,
				":ahorrado"=>$ahorros,
				":status"=>$status,
				));
		}
	}
}

function act_losdemas($db_con)
{
	$sql="truncate table prestamos";
	$res=$db_con->prepare($sql);
	$res->execute();
	$sql="select * from movimientos where codigo != 039 order by cedula";
	$res=$db_con->prepare($sql);
	$res->execute();
	$ultima_cotizacion='2017-07-31';
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$cedula=$fila['cedula'];
		$cedula=ceroizq($cedula,8);
		$monto=$fila['monto'];
		$cuota=$fila['cuota'];
		$salgo=$fila['salgo'];
		$sqlu="insert into prestamos (cedula, referencia, concepto, fecha_solicitud, f_1cuota, ultcan_sdp, ) VALUES ()";
		$resu=$db_con->prepare($sqlu);
			// echo $sqlu;
		$resu->execute(array(
			":cedula"=>$cedula,
			":monto"=>$monto,
			":ultima_cotizacion"=>$ultima_cotizacion,
		));
	}

}

?>
