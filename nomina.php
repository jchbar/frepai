<?php
// nomina.php
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
	window.open("nominapdf.php?arreglo="+arreglo,"parte1","top=0,left=395,status=no,toolbar=no,scrollbar=yes,location=no,type=fullWindow,fullscreen");	
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
	echo "<form action='nomina.php?accion=ListadoDeCuotas' name='form1' method='post' class='form-inline'>";
	echo '<fieldset><legend>Informaci&oacute;n Para N&oacute;mina</legend>';
	echo '<label for="fechapago">Fecha: </label>';

?>
    <div class='input-group date' id='fechapago'>
    	<input type='text' id="fechapago" name="fechapago" class="form-control"  readonly/>
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>

   <script type="text/javascript">
		$('input[name="fechapago"]').daterangepicker({
				"singleDatePicker": true,
				"startDate": <?php echo $hoy; ?>, // "11/07/2016", 
				"endDate": "<?php echo $pasado; ?>", // "11/30/2016", 
				//"minDate": button.data('los18'), // "11/01/2016",
				// "maxDate": <?php echo $futuro; ?> // "11/30/2016"
			}, function(start, end, label) {
//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			});

   </script>

<?php
/*
	echo '<td><label>N&oacute;mina Especial</label></td>';
*/
	echo '<td><input class="form-control" type="hidden" id="especial" name="especial" value="0"></td>';

	echo '<input type="submit" class="btn btn-success" name="Submit" value="Enviar" />';
	echo '</form>';
	echo '</div>';
}	// !$accion

if (($accion=='ListadoDeCuotas')) 
{
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

	$todobien=true;
	$registros_socios=0;
	try 
	{
		$fechaarchivo=explode('/',$_POST['fechapago']);
		$fechadescuento=$fechaarchivo[2].'-'.$fechaarchivo[0].'-'.$fechaarchivo[1];
		$fechaarchivo=$fechaarchivo[2].$fechaarchivo[0].$fechaarchivo[1];
		$nombre_archivo = 'cobranza/'.$fechaarchivo.'.txt';
		$especial=(isset($_POST['especial'])?1:2);

		$sql="select fecha from cotizacionesxcobrar ORDER BY fecha DESC limit 1 ";
		$res=$db_con->prepare($sql);
		$res->execute();
		$res=$res->fetch(PDO::FETCH_ASSOC);
		$ultimafecha=$res['fecha'];

		$sql="select cedula, sum(montocotizacion) as suma FROM cotizacionesxcobrar WHERE fecha = :fecha GROUP BY cedula ORDER BY cedula ";
		$res=$db_con->prepare($sql);
		$res->execute([":fecha"=>$ultimafecha]);
		$contenido = $nombre_archivo;
		if (fopen($nombre_archivo, 'w')) echo ''; 
		if (is_writable($nombre_archivo)) 
		{
			if (!$gestor = fopen($nombre_archivo, 'a')) 
			{
				mensaje(['tipo'=>'warning','titulo'=>'Error!','texto'=>'<h2>No se puede abrir el archivo ($nombre_archivo) revise permisologia</h2>']);
				exit('');
			}
			else 
			{
				echo "<div id='div1'>";
				// echo "<form action='nomina.php?accion=Abonar' name='form1' method='post' onsubmit='return realizo_abono(form1)'>";
				echo '<input type="hidden" name="nombre_archivo" value = "'.$nombre_archivo.'"/>';
				// $fechadescuento=$_POST['fechadelpago'];
				mensaje(['tipo'=>'info','titulo'=>'Información','texto'=>'Generando archivo N&oacute;mina al '.convertir_fechadmy($fechadescuento)]);

				$rp = $res->rowCount();
				$cuantos=0;
				$concepto='0359';

				while ($rg_pr = $res->fetch(PDO::FETCH_ASSOC))
				{
					$cuantos++;
					$porcentaje = ($cuantos * 100) / $rp; 
					echo "<script>callprogress(".round($porcentaje).")</script>"; 
					flush();
					ob_flush();
					$cadena=quitar_ceros($rg_pr['cedula'],10);
					$cadena.=$concepto;
					$monto=$rg_pr['suma'];
					$tamano=12-strlen($monto);
					$cadena.=replicate(' ',$tamano).$monto;
					$cadena.=chr(13).chr(10);

					if (fwrite($gestor, $cadena) === FALSE) 
					{
						echo "No se puede escribir al archivo ($nombre_archivo)";
						exit;
					}
				}
			}
		}
		else
		{
			mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>No se puede crear el archivo, revise permisos</h2>']);
			exit('');
		}
		fclose($gestor);
		echo '<form action="depositotxt.php" method="post" name="form1" enctype="multipart/form-data">';
		echo '<input type="hidden" name="archivo" value = "'.$nombre_archivo.'">';
		echo '<input class="btn btn-success" type="submit" name="procesar" value="Descargar Archivo '.$nombre_archivo.'" />';
		echo '</form>';
	}
	catch (Exception $e) 
	{
		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>No se puede crear el archivo, revise permisos</h2>'].$e->getMessage());
		exit('');
	}

/*
	echo '<input type="hidden" name="fechadescuento" value="'.$fechadescuento.'">';
	mensaje(['tipo'=>'success','titulo'=>'Informaci&oacute;n preparada','texto'=>'<h2>Se ha generado el archivo '.$nombre_archivo.'<br> para su procesamiento </h2>']);

	$arreglo=array(
		"fechadescuento"=>$fechadescuento,
		"referencia"=>$referencia,
		);
	$arreglo=serialize($arreglo);
	$arreglo=urlencode($arreglo);
	echo '<input type="submit" class="btn btn-info" name="Submit" value="Realizar Impresi&oacute;n de Nomina" onClick="abrir2Ventanas(';
	echo "'";
	echo $arreglo;
	echo "'";
	echo ');">  ';

	echo '</legend>';
	echo '</form>';
	echo '</div>';	
*/
	set_time_limit(30);
}	// ($accion=='ListadoDeCuotas')

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
	echo "<form action='nomina.php?accion=Asiento' name='form1' method='post' onsubmit='return realiza_asiento_montepio(form1)'>";
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

function registro($codigo, $db_con, $fechadescuento, $gestor, $especial, $referencia)
{
	if (($codigo != '039') and ($codigo != '099'))
	{
		if ($especial == 1)
			$sqlpre="select * from prestamos where (concepto = :codigo) and (f_1cuota <= :fechadescuento) and (monto_solicitado > montopagado_ucla) order by cedula";
		else
			$sqlpre="select * from prestamos where (concepto = :codigo) and (f_1cuota <= :fechadescuento) and (monto_solicitado > montopagadoespecial_ucla) order by cedula";
	}
	else if ($codigo == '039')
	{
		$sqlpre="select distinct cedula, -montocotizacion as monto_solicitado, -montocotizacion as montopagadoespecial_ucla, -montocotizacion as montoespecial_ucla, -montocotizacion as montopagado_ucla, montocotizacion as cuota, montocotizacion as cuotae  from cotizacionesxcobrar where (concepto = :codigo) and (substr(fecha,1,7) = :fechadescuento) order by cedula";
		// echo $sqlpre;
		// ((upper(status) = 'ACTIVO') or (upper(status) = 'JUBILA')) and (
		$fechadescuento=substr($fechadescuento,0,7);
	}
	else // 099
	{
		$sqlpre="select distinct cedula, -aporte_ord as monto_solicitado, -aporte_ord as montopagadoespecial_ucla, -aporte_ord as montoespecial_ucla, -aporte_ord as montopagado_ucla, aporte_ord as cuota, aporte_ord as cuotae  from frepai where (upper(status) = 'ACTIVO') and (codigo = :codigo) and (inscripcion <= :fechadescuento) order by cedula";
		$fechadescuento=substr($fechadescuento,0,7);
	}
	$prestamos=$db_con->prepare($sqlpre);
	// echo $sqlpre.'<br>';
	$prestamos->execute(array(
		":fechadescuento"=>$fechadescuento,
		":codigo"=>$codigo,
		));
	if ($prestamos->rowCount()>0)
	while ($rg_pr = $prestamos->fetch(PDO::FETCH_ASSOC))
	{
		$cedula=$rg_pr['cedula'];
		$datos=datos_socio($cedula, $db_con);
		if (($datos['status'] == 'ACTIVO') or ($datos['status'] == 'JUBILA'))
		{
			$nombre=$datos['nombre'];
			$ubicacion=$datos['numero'];
			$monto = $rg_pr['monto_solicitado']-(($especial==2)?$rg_pr['montopagado_ucla']:$rg_pr['montoespecial_ucla']);
			$cuota=(($especial==2)?$rg_pr['cuota']:$rg_pr['cuotae']);
			if (($codigo != '039') and ($codigo != '099'))
				$saldo=$monto-$cuota;
			else $saldo=0;
	// echo $fechadescuento;
			$sql="insert into archivosalida (cedula, nombre, ubicacion, monto, cuota, saldo, codigo, referencia) VALUES (:cedula, :nombre, :ubicacion, :monto, :cuota, :saldo, :codigo, :referencia)";
			try
			{
				$resc=$db_con->prepare($sql);
				$resc->execute(array(
					":cedula"=>$cedula,
					":nombre"=>$nombre,
					":ubicacion"=>$ubicacion,
					":monto"=>$monto,
					":referencia"=>$referencia,
					":cuota"=>$cuota,
					":saldo"=>$saldo,
					":codigo"=>$codigo,
				));
			}
			catch (PDOException $e) 
			{
		 		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage()]);
					die('Fallo call'. $e->getMessage());
			}
		}
//		listadotxt($r200,$montocotizacion,$gestor);
	}
}

function datos_socio($cedula, $db_con)
{
	$sqls="select concat(ape_tit, ', ', nom_tit) as nombre, numero, status from titulares where cedula=:cedula";
	$socio=$db_con->prepare($sqls);
	$socio->execute(array(":cedula"=>$cedula));
	return $socio->fetch(PDO::FETCH_ASSOC);
}


function listadotxt($r200, $totalxsocio, $gestor)
{
//0201082457570200015888V07333526        00000000000008937ABARCA DE G.TERESA G.                                                 00CAPPOUCL              *
//0201082457510200129328V16770549        00000000000000010Xx  CARRASCO R. TONDIS MIGUEL                                         00CAPPOUCL              *
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

function quitar_ceros($cedula, $digitos)
{
	$sigo=1;
	while ($sigo == 1)
	{
		if (substr($cedula,0,1) == '0')
		{
			$cedula=substr($cedula,1,10);
		}
		else $sigo=0;
	}
	$tamano=$digitos-strlen($cedula);
	$cedula=$cedula.replicate(' ',$tamano);
	return $cedula;
}

?>
</body>
</html>

