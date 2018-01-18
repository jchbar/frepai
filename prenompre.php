<?php
/*
select cedula, if(referencia='',concat(cedula,codigo),referencia) as referencia, codigo as concepto, '2017-11-30' as fecha_solicitud, '2017-11-30' as f_1cuota, monto as monto_solicitado, 0 as ultcan_sdp, 'A' as status, cuota, 0 as montoespecial, 0 as montopagado from movimientos where cuota > 0 and codigo != 39 
limit 10
*/
include("home.php");
extract($_GET);
extract($_POST);
extract($_SESSION);
$sql="select DATE_FORMAT(now(),'%m/%d/%Y') as hoy, DATE_ADD(NOW(), INTERVAL 24 MONTH) AS futuro, DATE_SUB(NOW(), INTERVAL 6 WEEK) AS pasado";
$stmt=$db_con->prepare($sql);
$stmt->execute();
$res=$stmt->fetch(PDO::FETCH_ASSOC);
$hoy=$res['hoy'];
$pasado=$res['pasado'];
$futuro=$res['futuro'];
?>

<script language="javascript">
function abrir2Ventanas(arreglo)
{
	window.open("prenomina.php?arreglo="+arreglo,"parte1","top=0,left=395,status=no,toolbar=no,scrollbar=yes,location=no,type=fullWindow,fullscreen");	
	window.open("detalle_dcto_pdf.php?arreglo="+arreglo,"parte2","top=0,left=395,status=no,toolbar=no,scrollbar=yes,location=no,type=fullWindow,fullscreen");	
}
</script>
<script language="javascript">
//Creo una función que imprimira en la hoja el valor del porcentanje asi como el relleno de la barra de progreso
function callprogress(vValor){
 document.getElementById("progress-txt").innerHTML = vValor;
 document.getElementById("progress-txt").innerHTML = '<div class="progress-bar" role="progressbar" style="width:'+vValor+'%; min-width:10%">'+vValor+'%</div>';
}
</script>
<?php
$ip = la_ip();
$momento = ahora($db_con)['ahora'];
if (!$accion) 
{
	echo "<div id='div1'>";
	echo "<form action='prenompre.php?accion=ListadoDeCuotas' name='form1' method='post' class='form-inline'>";
	echo '<fieldset><legend>Ultimas Referencias</legend>';
	echo '<div class="checkbox-inline">';
		echo '<h2><label><input name="nuevos" id="nuevos" type="checkbox" value="" checked>Solo Referencias Nuevas</label></h2>';
	echo '</div>';

	$sql="SELECT substr(referencia,4,4) as referencia FROM `prestamos` GROUP by substr(referencia,4,4) order by substr(referencia,4,4) DESC LIMIT 1";
	$result=$db_con->prepare($sql);
	$result->execute();
	$imprimir=array();
	$registros=0;
	echo '<table class="table" width="500" border="1">';
	echo '<tr><td>Referencia</td><td>Seleccionar</td></tr>';
	while ($r200 = $result->fetch(PDO::FETCH_ASSOC))
	{
		echo '<tr>';
		$registros++;
		echo '<td><label>'.$r200["referencia"].'</label></td>';
		echo '<td><input class="form-control" type="checkbox" id="imprimir'.$registros.'" name="imprimir'.$registros.'" value="'.$r200["referencia"].'"'.($registros==1?' checked ':'').'"></td>';
		echo '</tr>';
	}
	echo "<input type = 'hidden' value ='".$registros."' name='registros' id='registros'>";
	echo '</tr>';
	echo '<td align="center" colspan="2"><input type="submit" class="btn btn-info" name="Submit" value="Enviar" /></td>';
	echo '</form>';
	echo '</div>';
}	// !$accion

if ($accion == "ListadoDeCuotas")
{
	if (isset($_POST['nuevos']))
	{
		echo "<form action='prenompre.php?accion=Lista2DeCuotas' name='form1' method='post' class='form-inline'>";
		mensaje(['tipo'=>'info','titulo'=>'Aviso','texto'=>'<h3>Recolectando servicios</h3>']);
		echo '<input name="nuevos" id="nuevos" type="hidden" value="1">';
		echo '<fieldset><legend>Conceptos Registrados</legend>';
		flush(); 
		ob_flush();
		$arreglo=array();
		$reg_ref=0;
		for ($i=0;$i<$registros;$i++)	
		{
			$variable='imprimir'.($i+1);
			// echo $variable;
			if (!empty($$variable)) 
			{
				$reg_ref++;
				echo '<input type="hidden" id="imprimirref'.$reg_ref.'" name="imprimirref'.$reg_ref.'" value="'.$$variable.'">';
				$sql="select concepto from prestamos where substr(referencia,4,4) = :variable group by concepto order by concepto";
				$result=$db_con->prepare($sql);
				$valor=$$variable;
				$result->execute(array(
					":variable"=>$valor,
					));
				while ($r200 = $result->fetch(PDO::FETCH_ASSOC))
				{
					$concepto=$r200['concepto'];
					$consegui=0;
					for ($j=0;$j<count($arreglo);$j++)
						if ($arreglo[$j] == $concepto)
							$consegui=1;
					if ($consegui == 0)
						array_push($arreglo, $concepto);
				}
			}
		}
		echo "<input type = 'hidden' value ='".$reg_ref."' name='reg_ref' id='reg_ref'>";
		echo '<table class="table" >';
		echo '<tr><td>Concepto</td><td>Descripci&oacute;n</td><td>Seleccionar</td></tr>';
		$registros=0;
		for ($i=0;$i<count($arreglo);$i++)
		{
			echo '<tr>';
			$registros++;
			echo '<td><label>'.$arreglo[$i].'</label></td>';
			$concepto=$arreglo[$i];
			$sql="select casa from proveedores where codigo = :concepto";
			$result=$db_con->prepare($sql);
			$result->execute(array(":concepto"=>$concepto));
			$reg=$result->fetch(PDO::FETCH_ASSOC);
			echo '<td>'.$reg['casa'].'</td>';
			echo '<td><input class="form-control" type="checkbox" id="imprimir'.$registros.'" name="imprimir'.$registros.'" value="'.$arreglo[$i].'" checked "></td>';
			echo '</tr>';
		}
		echo "<input type = 'hidden' value ='".$registros."' name='registros' id='registros'>";
		echo '</tr>';
		echo '<td align="center" colspan="3"><input type="submit" class="btn btn-success" name="Submit" value="Enviar" /></td>';
		echo '</form>';
	}
	else $accion = "Lista2DeCuotas";
}

if ($accion == "Lista2DeCuotas")
{
	mensaje(['tipo'=>'info','titulo'=>'Aviso','texto'=>'<h3>Recolectando informacion</h3>']);
	echo '<fieldset><legend>Emision de Listados</legend>';
	flush(); 
	ob_flush();
	// echo 'nuevos '.isset($_POST['nuevos']);
	if (isset($_POST['nuevos']))
	{
		$condicion=' AND (';
		for ($i=0;$i<$reg_ref;$i++)	
		{
			$variable='imprimirref'.($i+1);
			$referencia=$$variable;
			if (!empty($$variable)) 
				$condicion.='(substr(referencia,4,4)="'.$$variable.'") OR ';
		}
		$tamano=strlen($condicion);
		$condicion=substr($condicion,0,($tamano-3));
		$condicion.=') AND (';

		for ($i=0;$i<$registros;$i++)	
		{
			$variable='imprimir'.($i+1);
			if (!empty($$variable)) 
				$condicion.='(concepto='.$$variable.') OR ';
		}
		$tamano=strlen($condicion);
		$condicion=substr($condicion,0,($tamano-3));
		$condicion.=') ';
	}
	else
		$condicion = '';
	echo $condicion;
//	$orden=' referencia';
	$orden=" substr(titulares.numero,1,4), titulares.ape_tit, titulares.nom_tit";

	$sql="select fecha from cotizacionesxcobrar order by fecha desc limit 1";
	$result=$db_con->prepare($sql);
	$result->execute();
	$fechadescuento=$result->fetch(PDO::FETCH_ASSOC);
	$fechadescuento=$fechadescuento['fecha'];

	$arreglo=array(
				"fechadescuento"=>$fechadescuento,
				"referencia"=>'0'.$referencia,
				"concepto"=>'X',
				"fuente"=>'prestamos',
				"condicion"=>$condicion,
				"orden"=>$orden,
				"sentencia"=>"select titulares.numero, referencia, concepto, prestamos.cedula, concat(trim(ape_tit),', ',nom_tit) as nombre, monto_solicitado as saldo, cuota as montocotizacion from prestamos, titulares where (titulares.cedula = prestamos.cedula) AND (concepto = :concepto) AND ((ucase(titulares.status) = 'ACTIVO') or (ucase(titulares.status)='JUBILA')) AND (cuota > 0) ".$condicion,
				"agrupar"=>"select concepto from prestamos, titulares where (titulares.cedula = prestamos.cedula) AND (cuota > 0) ".$condicion . " GROUP BY concepto ORDER BY concepto ",
	);
	$sql="select titulares.numero, referencia, concepto, prestamos.cedula, concat(trim(ape_tit),', ',nom_tit) as nombre, monto_solicitado as saldo, cuota as montocotizacion from prestamos, titulares where (titulares.cedula = prestamos.cedula) AND ((ucase(titulares.status) = 'ACTIVO') or (ucase(titulares.status)='JUBILA')) AND (cuota > 0) ".$condicion;
/*
	echo $sql;
					echo $r200['cedula'].' - ';
				if ($r200['cedula'] == '01408486')
					die('consegui');
*/
	// echo $sql;
	$arreglo=serialize($arreglo);
	$arreglo=urlencode($arreglo);

	echo '<div class="row">';
		echo '<div class="col-md-4">';
			echo 'Proceso Interno <div id="progress-txt" class="progress  progress-bar-success">';
				// echo '<div id="progress-bs" class="progress-bar" role="progressbar" style="width:30%; min-width:10%">';
				echo '<div class="progress-bar" role="progressbar" style="width:30%">';
					echo '0%';
				echo '</div>';
				echo '<span class="sr-only"></span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	try 
	{
		$db_con->begintransaction();
		$sql2="delete from cotizacionesxcobrar where fecha=:fechadescuento and concepto<>'039'";
		$res=$db_con->prepare($sql2);
		$res->execute([":fechadescuento"=>$fechadescuento]);
		$sql2="update  detalle_dcto set ";
		for ($i = 2; $i < 50; $i++)
		{
			$sql2.="colref".$i."='', colpre".$i."= :cero, ";
		}
		$tamano=strlen($sql2)-2;
		$sql2=substr($sql2,0,$tamano);
		$sql2.=" where fecha=:fechadescuento";
		$res=$db_con->prepare($sql2);
		$res->execute(array(
			":fechadescuento"=>$fechadescuento,
			":cero"=>0,
			));
		$res=$db_con->prepare($sql);
		$res->execute();
		$ValorTotal=$res->rowCount();
		$cero = $cuantos = 0;
		while ($fila=$res->fetch(PDO::FETCH_ASSOC))
		{
			$cuantos++;
			$porcentaje = $cuantos * 100 / $ValorTotal; 
			echo "<script>callprogress(".round($porcentaje).")</script>"; 
			flush(); 
			ob_flush();
			$montocotizacion=$fila['montocotizacion'];
			$concepto=$fila['concepto'];
			$cedula=$fila['cedula'];
			$saldo=$fila['saldo'];
			$referencia=$fila['referencia'];
			$sql="insert into cotizacionesxcobrar (fecha, cedula, montocotizacion, fecha_registro, ip_registro, cobrado, ip_modificado, fecha_modificado, concepto, referencia, saldo) VALUES (:fecha, :cedula, :montocotizacion, :fecha_registro, :ip_registro, :cobrado, :ip_registro, :fecha_registro, :concepto, :referencia, :saldo)";
			$resc=$db_con->prepare($sql);
			$result=$resc->execute(array(
				":fecha"=>$fechadescuento,
				":cedula"=>$cedula,
				":montocotizacion"=>$montocotizacion,
				":fecha_registro"=>$momento,
				":ip_registro"=>$ip,
				":cobrado"=>$cero,
				":concepto"=>$concepto,
				":saldo"=>$saldo,
				":referencia"=>$referencia,
				));
			$sql="select codigo from proveedores, prestamos where codigo=concepto group by codigo order by codigo";
			$resc=$db_con->prepare($sql);
			$resc->execute();
			$posiciond=1;
			$primeravez = 0;
			while ($filac=$resc->fetch(PDO::FETCH_ASSOC))
			{
				$posiciond++;
				/////////////
				// echo $filac['codigo']. ' - '.$concepto.'<br>' ;
				if ($filac['codigo']==$concepto) 
				{
					$lacolumnapres='colpre'.$posiciond;
					$lacolumnanro='colref'.$posiciond;
					// echo 'posiciond '.$posiciond.'<br>';
					$elnumero=$referencia;
					if ($primeravez == 0) 
					{
						$sql="select count(cedula) as cuantos from detalle_dcto where fecha = :fecha and cedula = :cedula and colref1= :cotizacion group by cedula";
						$resb=$db_con->prepare($sql);
						$resb->execute(array(
								":fecha"=>$fechadescuento,
								":cedula"=>$cedula,
								":cotizacion"=>'039',
							));
						if ($resb->rowCount() < 1)
						{
					 		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado. Debe hacer primero la cotizacion </h2>']);
								die('Fallo llamado');
						}
						$nrocuenta='';
						$primeravez = 1;
						// $sql_dcto="update detalle_dcto set ".$lacolumnapres." = ".$lacolumnapres."+'$lacuota', ".$lacolumnanro." = '$elnumero' where (cedula='$micedula')" ;
						$sql="update detalle_dcto set ".$lacolumnapres." = ".$lacolumnapres."+ :lacuota, ".$lacolumnanro." = :elnumero where (cedula =:cedula) and fecha = :fecha" ;
					}
					else
						$sql="update detalle_dcto set ".$lacolumnapres." = ".$lacolumnapres."+ :lacuota, ".$lacolumnanro." = :elnumero where (cedula =:cedula) and fecha = :fecha" ;
					$resdc=$db_con->prepare($sql);
					$resdc->execute(array(
							":lacuota"=>$montocotizacion,
							":elnumero"=>$elnumero,
							":cedula"=>$cedula,
							":fecha"=>$fechadescuento,
						));
				}
				/////////////
			}
			// echo $fechadescuento.'-'.$result.'---';
		}
		$db_con->commit();
	} catch (Exception $e) 
	{
		$db_con->rollback();
 		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage().$sql]);
			die('Fallo call'. $e->getMessage().$sql);
	}

	echo '<input type="submit" class="btn btn-info" name="Submit" value="Realizar Impresi&oacute;n de Listados" onClick="abrir2Ventanas(';
	echo "'";
	echo $arreglo;
	echo "'";
	echo ');">  ';
	echo '</legend>';
}


if (($accion=='Abonar')) { // and ($nominasnormales == 'on')) {
// if ($nominasnormales == 'on') {
	$fechadescuento=$_POST['fechadescuento'];
	$nombre_archivo=$_POST['nombre_archivo'];
//	echo '<input type="hidden" name="nombre_archivo" value = "'.$nombre_archivo.'"/>';
	echo "<div id='div1'>";
	mensaje(['tipo'=>'info','titulo'=>'Información','texto'=>'<h2>Puede proceder luego de la impresi&oacute;n de los listados a realizar el abono a cuentas y el asiento contable y recuerde obtener descargar el archivo </h2><h1>'.$nombre_archivo.'</h1><h2> para enviar al banco (si aplica)']);
	/*
	echo '<h2>Puede proceder luego de la impresi&oacute;n de los listados a <br>realizar el abono a pr&eacute;stamos y el asiento contable y';
	echo '<br>recuerde obtener descargar el archivo </h2><h1>'.$nombre_archivo.'</h1><h2> para enviar al banco</h2>';
*/
	echo "<form action='prenompre.php?accion=Asiento' name='form1' method='post' onsubmit='return realiza_asiento_montepio(form1)'>";
	$fechadescuento=$_POST['fechadescuento'];
	echo '<input type="hidden" name="fechadescuento" value = "'.$fechadescuento.'">';
	echo '<input type="hidden" name="nombre_archivo" value = "'.$nombre_archivo.'"/>';
	echo '<input class="btn btn-success" type="submit" name="procesar" value="Generar Asiento Contable" />';
	echo '</form>';
	echo '</div>';
}	// ($accion=='ImpresionListados') 
if (($accion=='Asiento')) { 
/////
	mensaje(['tipo'=>'warning','titulo'=>'Información','texto'=>'<h2>Procedimiento aun no definido)']);
	$fechadescuento=$_POST['fechadescuento'];
	die('');
	$sql_360="select * from ".$_SESSION[institucion]."sgcaf360 where (dcto_sem) order by cod_pres"; //  limit 30"; //  limit 20";
	$a_360=$db_con->prepare($sql_360);
	$a_360->execute();
	$columna=3;
	$rpl=300; 	// registros por listado
	$crl=0;		// contador de registros por listado
	$col_listado=0;
	$nuevoarchivo=false;
	$condicion_sql='select codigo, cedula, nombre, nrocta, ';
	$col_listado=0;
	$max_cols=$a_360->rowCount();
	echo 'Realizando Calculo<br>';
	while ($r360 = $a_360->fetch(PDO::FETCH_ASSOC))
	{
		$col_listado++;
		$columna++;
		if (trim($r360['desc_cor'])!='') ;// $header[$columna]=$r360['desc_cor'] ;
		else ; // $header[$columna]=substr($r360['descr_pres'],0,12);
		$totales[$col_listado]=0;
		$campo='colpre'.$col_listado;
		$condicion_sql.=' colpre'.$col_listado;
		if ($col_listado != $max_cols) 
		{
			$arrtitulo.=', ';
			$condicion_sql.=', ';
		}
	}
	$sql_nopr=$condicion_sql." from ".$_SESSION[institucion]."sgcanopr where ('$fechadescuento' = fecha) order by nombre "; //  limit 20";
	$a_nopr=$db_con->prepare($sql_nopr);
	$a_nopr->execute();
	$registros=$a_nopr->rowCount();
	set_time_limit($registros);
	$lascolumnas=$a_nopr->columnCount()-4;
	while ($r_nopr = $a_nopr->fetch(PDO::FETCH_ASSOC))
	{
		$t1=0;
		for ($prestamos=1;$prestamos<=$lascolumnas;$prestamos++) {		// sumatoria de los prestamos
			$item='colpre'.$prestamos;
			$t1+=$r_nopr[$item];
			$totales[$prestamos]+=$r_nopr[$item];
		}
	}
	$general=0;
	for ($i=1;$i<count($totales);$i++)
		if ($totales[$i]!=0) {
			$general+=$totales[$i];
	}
	set_time_limit(30);

	$b=$fechadescuento;
	$b2 = date("Y-m-d");
	$c=explode('-',$b2);
	$asiento=$c[0].$c[1].$c[2].'001';
	echo "Generado Asiento Contable <strong><a target=\"_blank\" href='editasi2.php?asiento=$asiento'>$asiento </a></strong> <br>";
	$cuento='Nomina por cobrar al Banco de fecha '.convertir_fechadmy($b);
	$sql="select enc_clave FROM ".$_SESSION[institucion]."sgcaf830 where enc_clave = :asiento";
	$r=$db_con->prepare($sql);
	$res=$r->execute(array(":asiento"=>$asiento));
	if ($r->rowCount() < 1)
	{
		$sql = "INSERT INTO ".$_SESSION[institucion]."sgcaf830 (enc_clave, enc_fecha, enc_desco, enc_desc1, enc_debe, enc_haber, enc_item, enc_dif, enc_igual, enc_refer, enc_sw, enc_explic) VALUES ('$asiento', '$b', '$cuento','',0,0,0,0,0,0,0,'$cuento')"; 
		$r=$db_con->prepare($sql);
		$res=$r->execute();
		if (!$res) 
		{
			mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>El usuario no tiene permisos para añadir asientos </h2>']);
			die ("El usuario $usuario no tiene permiso para añadir Asientos.<br>".$sql);
		}
	}

	$sql="select * from ".$_SESSION[institucion]."sgcaf000 where tipo='CtaPrexCobAmo'";
	$result=$db_con->prepare($sql); 
	$result->execute();
	$cuentas=$result->fetch(PDO::FETCH_ASSOC);
	$cuenta_amortizacion=trim($cuentas['nombre']);
	$sql="select * from ".$_SESSION[institucion]."sgcaf000 where tipo='CtaPrexCobBco'";
	$result=$db_con->prepare($sql); 
	$result->execute();
	$cuentas=$result->fetch(PDO::FETCH_ASSOC);
	$cuentabanco=trim($cuentas['nombre']);			
	$referencia='';
	$debe=$general;
	agregar_f820($asiento, $b2, '+', $cuentabanco, 'Amort. Prest. p/cobrar banco al '.convertir_fechadmy($b), $debe, $haber, 0,$ip,0,$referencia,'','S',0, $db_con); 
	echo $sql;
	agregar_f820($asiento, $b2, '-', $cuenta_amortizacion, 'Total Retenciones del '.convertir_fechadmy($b), $debe, $haber, 0,$ip,0,$referencia,'','S',0, $db_con); 	

	$nombre_archivo=$_POST['nombre_archivo'];
	echo '<form action="depositotxt.php" method="post" name="form1" enctype="multipart/form-data">';
	echo '<input type="hidden" name="archivo" value = "'.$nombre_archivo.'">';
	echo '<input type="submit" name="procesar" value="Descargar Archivo '.$nombre_archivo.'" />';
	echo '</form>';

	$comando = "update ".$_SESSION[institucion]."sgcaf8co set fechanominamiercoles= now()";
	$resultado=$db_con->prepare($comando);
	$resultado->execute();
/////
}

function revisar($r200, $fechadescuento, $micedula, $ip, $gestor, $db_con, $montocotizacion, $momento, $referencia)
{
	$cero=0;
	$sql="insert into cotizacionesxcobrar (fecha, cedula, montocotizacion, fecha_registro, ip_registro, cobrado, ip_modificado, fecha_modificado, concepto, referencia, saldo) VALUES (:fecha, :cedula, :montocotizacion, :fecha_registro, :ip_registro, :cobrado, :ip_registro, :fecha_registro, :concepto, :referencia, :saldo)";
	try
	{
		$resc=$db_con->prepare($sql);
		$resc->execute(array(
			":fecha"=>$fechadescuento,
			":cedula"=>$micedula,
			":montocotizacion"=>$montocotizacion,
			":fecha_registro"=>$momento,
			":ip_registro"=>$ip,
			":cobrado"=>$cero,
			":concepto"=>39,
			":saldo"=>0,
			":referencia"=>$referencia,
		));
	}
	catch (PDOException $e) 
	{
 		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage()]);
			die('Fallo call'. $e->getMessage());
	}
	listadotxt($r200,$montocotizacion,$gestor);
}

function revision01($db_con, $fechadescuento)
{
	try
	{
//		echo 'La fecha del d&iacute;a de hoy es: '. date("d/m/Y"). ' Hora local del servidor es: '. date("G:i:s").'<br>'; 
		$sql="delete from cotizacionesxcobrar where fecha = :fechadescuento";
//		echo $sql.$fechadescuento;
		$stmt=$db_con->prepare($sql);
		$stmt->bindParam(":fechadescuento",$fechadescuento);
		$stmt->execute();
		if ($stmt)
		{
//			echo 'paso 1';
			$sql_amor="delete from cotizacionesxcobrar where (:fechadescuento = fecha)"; //  limit 30"; //  limit 20";
			$resultado=$db_con->prepare($sql_amor);
			$resultado->bindParam(":fechadescuento",$fechadescuento);
			$resultado->execute();
		//	echo $sql;
			if ($resultado)
			{
//				echo 'paso 2';
				$sql="select count(fecha) as cuantos, ip_registro from cotizacionesxcobrar where fecha = :fechadescuento group by fecha, ip_registro";
				$resultado=$db_con->prepare($sql);
				$resultado->bindParam(":fechadescuento",$fechadescuento);
				$resultado->execute();
				if ($resultado->rowCount()>0) 
				{
					$registro=$resultado->fetch(PDO::FETCH_ASSOC);
					$registro=$resultado['cuantos'];
					mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>No se puede procesar esta nomina existe una ya realizada con '.$registro['cuantos'].' registro generada desde la IP '.$registro['ip'].'</h2>']);
					return false;
				}
				return true;
			}
		}
	}
	catch (PDOException $e) {
		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>rev1.'.$e->getMessage().'</h2>']);
	    die('rev1.'.$e->getMessage());
	}
}



function listadotxt($r200, $totalxsocio, $gestor)
{
	$cadena='02'.$r200['cuenta'];
	$cadena.=$r200['cedula']; // substr($r200['ced_prof'],0,1).substr($r200['ced_prof'],2,8).replicate(' ',8);
	$monto=$totalxsocio*100;
//	$monto=explode('.',$totalxsocio);
	// quito el punto
	$sinpunto='';

	for ($i=0;$i<strlen($monto);$i++)
		if (substr($monto,$i,1)!= '.')
			$sinpunto.=substr($monto,$i,1);

	$monto=ceroizq($sinpunto,17);
	$cadena.=$monto;
	$nombre=trim($r200['nombre']);
	if ($nombre == '') $nombre='IPSTAUCLA - REVISAR';
	$nombre=substr(trim($nombre),0,40);
	$rellenar=replicate(' ',40-strlen($nombre));
	$cadena.=$nombre.$rellenar;
	$cadena.=replicate(' ',30).'00'.'IPSTAUCL'.replicate(' ',14).'*'.chr(13).chr(10);

//echo $cadena.'<br>';
	if (fwrite($gestor, $cadena) === FALSE) {
		echo "No se puede escribir al archivo ($nombre_archivo)";
		exit;
	}
	
}

function replicate($caracterarepetir,$cantidaddeveces)
{
	$resultado='';
	for ($i=0;$i<$cantidaddeveces;$i++)
		$resultado.=$caracterarepetir;
	return $resultado;
}

?>
</body>
</html>

