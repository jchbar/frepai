<?php
// pasar informacion de dbf_socios a titulares
include_once('../dbconfig.php');
include_once('../funciones.php');
try
{
	$sql="truncate table titulares ";
	$res=$db_con->prepare($sql);
	$res->execute();
	$sql="SELECT *, substr(nombre,1,20) as ape_tit, substr(nombre,21,20) as nom_tit FROM dbf_socio ";
	$res=$db_con->prepare($sql);
	$res->execute();
	$ip_nuevo=la_ip();
	$fecha_registro=ahora($db_con)['ahora'];
	while ($fila = $res->fetch(PDO::FETCH_ASSOC))
	{
		$cedula=$fila['cedula'];
		$numero=$fila['numero'];
		$ape_tit=substr($fila['ape_tit'],0,20);
		$nom_tit=substr($fila['nom_tit'],0,20);
		$dir_hab=$fila['habitacion'];
		$dir_tra=$fila['trabajo'];
		$fechanac=$fila['fechnac'];
		$status=$fila['status'];
		$civil=$fila['edocivil'];
		$ing_ucla=$fila['ingucla'];
		$ing_ipsta=$fila['ingipsta'];
		$inc_nomina=$fila['incnomina'];
		$numcuota=$fila['numcuota'];
		$acumbs=$fila['acumbs'];
		$conyuge=$fila['conyuge'];
		$cuenta=$fila['cuenta'];
		$tipocta=''; // $fila['tipocta'];
		$teltrabajo = $telcelular = $email = $telhabitacion = $usuario = $ip_modifica='';
		$cotizacion = 0;

		// fechas
		$fechanac=explode('/',$fechanac); $fechanac=$fechanac[0].'-'.$fechanac[1].'-'.$fechanac[2];
		$ing_ucla=explode('/',$ing_ucla); $ing_ucla=$ing_ucla[0].'-'.$ing_ucla[1].'-'.$ing_ucla[2];
		$ing_ipsta=explode('/',$ing_ipsta); $ing_ipsta=$ing_ipsta[0].'-'.$ing_ipsta[1].'-'.$ing_ipsta[2];
		$inc_nomina=explode('/',$inc_nomina); $inc_nomina=$inc_nomina[0].'-'.$inc_nomina[1].'-'.$inc_nomina[2];
		// verifico fechas
		// echo substr($fechanac,0,1).'<br>';
		$fechanac = (substr($fechanac,0,1) == '-'?'1001-01-01':$fechanac);
		$ing_ucla = (substr($ing_ucla,0,1) == '-'?'1001-01-01':$ing_ucla);
		$ing_ipsta = (substr($ing_ipsta,0,1) == '-'?'1001-01-01':$ing_ipsta);
		$inc_nomina = (substr($inc_nomina,0,1) == '-'?'1001-01-01':$inc_nomina);


		$sql="INSERT INTO titulares (cedula, numero, ape_tit, nom_tit, dir_hab, dir_tra, fechanac, status, civil, ing_ucla, ing_ipsta, inc_nomina, numcuota, acumbs, ip_nuevo, fecha_registro, conyuge, cuenta, tipocta, ip_modifica, usuario, teltrabajo, telcelular, email, telhabitacion, cotizacion) VALUES (:cedula, :numero, :ape_tit, :nom_tit, :dir_hab, :dir_tra, :fechanac, :status, :civil, :ing_ucla, :ing_ipsta, :inc_nomina, :numcuota, :acumbs, :ip_nuevo, :fecha_registro, :conyuge, :cuenta, :tipocta, :ip_modifica, :usuario, :teltrabajo, :telcelular, :email, :telhabitacion, :cotizacion)";
			$res2=$db_con->prepare($sql);
			$res2->execute([
				":cedula"=>$cedula,
				":numero"=>$numero,
				":ape_tit"=>$ape_tit,
				":nom_tit"=>$nom_tit,
				":dir_hab"=>$dir_hab,
				":dir_tra"=>$dir_tra,
				":fechanac"=>$fechanac,
				":status"=>$status,
				":civil"=>$civil,
				":ing_ucla"=>$ing_ucla,
				":ing_ipsta"=>$ing_ipsta,
				":inc_nomina"=>$inc_nomina,
				":numcuota"=>$numcuota,
				":acumbs"=>$acumbs,
				":ip_nuevo"=>$ip_nuevo,
				":ip_modifica"=>$ip_modifica,
				":fecha_registro"=>$fecha_registro,
				":conyuge"=>$conyuge,
				":cuenta"=>$cuenta,
				":tipocta"=>$tipocta,
				":usuario"=>$usuario,
				":teltrabajo"=>$teltrabajo,
				":email"=>$email,
				":telcelular"=>$telcelular,
				":telhabitacion"=>$telhabitacion,
				":cotizacion"=>$cotizacion,
				]);

	}
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
}
catch(PDOException $e){
	die( $e->getMessage());
}
echo 'Finalizado';


?>
