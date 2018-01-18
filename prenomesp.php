<?php
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
	echo "<form action='prenomesp.php?accion=ListadoDeCuotas' name='form1' method='post' class='form-inline'>";
	echo '<fieldset><legend>Informaci&oacute;n Para Cuotas Especiales (Extraordinarias)</legend>';
	echo '<label for="fechapago">Fecha de pre-n&oacute;mina: </label>';

	$sql="select montocotizacion FROM cotizacionesxcobrar ORDER BY fecha DESC LIMIT 1";
	$stmt=$db_con->prepare($sql);
	$stmt->execute();
	$res=$stmt->fetch(PDO::FETCH_ASSOC);
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

		<label for="montocotizacion" class="sr-only control-label">Monto de Aporte Extraordinario a aplicar</label>
		<input type="text" placeholder="Monto de Cotizacion a aplicar" class="form-control" id="montocotizacion" name="montocotizacion" value="<?php echo $res['montocotizacion']; ?>" min="1000" step="1" maxlength="20" size="20">
<?php
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

	$fechaarchivo=explode('/',$_POST['fechapago']);
	$fechadescuento=$fechaarchivo[2].'-'.$fechaarchivo[0].'-'.$fechaarchivo[1];
	$fechaarchivo=$fechaarchivo[2].$fechaarchivo[0].$fechaarchivo[1];
	$montocotizacion=$_POST['montocotizacion'];
	$nombre_archivo = 'cotizacionesxcobrar/'.$fechaarchivo.'.txt';

	$sql2="delete from cotizacionesxcobrar where fecha=:fechadescuento";
	$res=$db_con->prepare($sql2);
	$res->execute([":fechadescuento"=>$fechadescuento]);

	$todobien=(revision01($db_con, $fechadescuento));
	if ($todobien == false)
	{
		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Las siguiente informaci&oacute;n tiene problemas en pr&eacute;stamo, corregir</h2>']);
		exit('');
		die ('<h2> Las siguiente informaci&oacute;n tiene problemas en pr&eacute;stamo, corregir');
	}
	else
	{
		// mensaje(['tipo'=>'info','titulo'=>'Información','texto'=>'<h2>No se han conseguido inconvenientes con los datos principales</h2>']);
	}

	// die('probando');
	$contenido = $nombre_archivo;
	if (fopen($nombre_archivo, 'w')) echo ''; 
	else 
	{
		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>No se puede crear el archivo, revise permisos</h2>']);
		exit('');
	}
	// Asegurarse primero de que el archivo existe y puede escribirse sobre el.
	$registros=$registros_socios; // $a_200->rowCount();
	// echo 'registro socios limit '.$registros;
	if ($registros < 30)
		$registros = 50;
	set_time_limit($registros);
	if (is_writable($nombre_archivo)) {

		// En nuestro ejemplo estamos abriendo $nombre_archivo en modo de adicion.
		// El apuntador de archivo se encuentra al final del archivo, asi que
		// alli es donde ira $contenido cuando llamemos fwrite().
		if (!$gestor = fopen($nombre_archivo, 'a')) {
			mensaje(['tipo'=>'warning','titulo'=>'Error!','texto'=>'<h2>No se puede abrir el archivo ($nombre_archivo) revise permisologia</h2>']);
			exit('');
//			echo "<h2>No se puede abrir el archivo ($nombre_archivo) revise permisologia</h2>";
//			exit;
		}
		else 
		{
			echo "<div id='div1'>";
			// echo "<form action='prenomesp.php?accion=Abonar' name='form1' method='post' onsubmit='return realizo_abono(form1)'>";
			echo '<input type="hidden" name="nombre_archivo" value = "'.$nombre_archivo.'"/>';
			// $fechadescuento=$_POST['fechadelpago'];
			mensaje(['tipo'=>'info','titulo'=>'Información','texto'=>'Recopilando informaci&oacute;n Para Cotizaciones al '.convertir_fechadmy($fechadescuento)]);

			$sql="select cedula, concat(trim(ape_tit), ', ', trim(nom_tit)) as nombre, ".$montocotizacion." AS cotizacion, cuenta from titulares where (ucase(status) = :statusA) or (ucase(status)=:statusJ) order by cedula";
			$maximo=20;

			$a_200=$db_con->prepare($sql);
// $a_200->bindValue(':limit', (int) $maximo, PDO::PARAM_INT);
			$a_200->bindValue(":statusA","ACTIVO");
			$a_200->bindValue(":statusJ","JUBILA");
			$a_200->execute();
//			echo $sql_200;
			$registros = $a_200->rowCount();
			$ValorTotal=$registros;
			$cuantos=0;
			$concepto='039';
			$referencia = $concepto .substr($fechaarchivo,2,4);
			while ($r200 = $a_200->fetch(PDO::FETCH_ASSOC))
			{
				$cedula=$r200['cedula'];
				$micedula=$cedula; // substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);

				$cuantos++;
				$porcentaje = $cuantos * 100 / $ValorTotal; //saco mi valor en porcentaje

				echo "<script>callprogress(".round($porcentaje).")</script>"; 
				flush(); 
				ob_flush();
				$laref=ceroizq($cuantos,5);
				revisar($r200, $fechadescuento, $micedula, $ip, $gestor, $db_con, $montocotizacion, $momento, $referencia.$laref);
			} 
			echo '<input type="hidden" name="fechadescuento" value="'.$fechadescuento.'">';
			// mensaje(['tipo'=>'success','titulo'=>'Informaci&oacute;n preparada','texto'=>'<h2>Se ha generado el archivo '.$nombre_archivo.'<br> para su procesamiento a banco</h2>']);
			mensaje(['tipo'=>'success','titulo'=>'Informaci&oacute;n preparada','texto'=>'<h2>Se ha generado la prenomina de las cotizaciones, puede proceder a la generacion de la nomina']);

			// probamdo enviar arreglos por url
			$fuente='cotizacionesxcobrar';
			$conceptos="select concepto from ".$fuente." where fecha=:fechadescuento group by concepto";
			$res=$db_con->prepare($conceptos);
			$res->execute([":fechadescuento"=>$fechadescuento]);

			$condicion="fecha='".$fechadescuento."' and titulares.cedula=cotizacionesxcobrar.cedula and ";
			while ($rg=$res->fetch(PDO::FETCH_ASSOC))
				$condicion.='(concepto='.$rg['concepto'].') OR ';

			$tamano=strlen($condicion);
			$condicion=substr($condicion,0,($tamano-3));
			$condicion.='';

			$arreglo=array(
				"fechadescuento"=>$fechadescuento,
				"concepto"=>'X',
				"referencia"=>$referencia,
				"fuente"=>'prestamos',
				"condicion"=>$condicion,
				"orden"=>"cotizacionesxcobrar.cedula",
				"sentencia"=>"select *, concat(trim(ape_tit),', ',trim(nom_tit)) as nombre from titulares,". $fuente." where (titulares.cedula=cotizacionesxcobrar.cedula) and (concepto = :concepto) AND fecha='".$fechadescuento."'", // .$condicion,
				"agrupar"=>"select concepto from ". $fuente." where fecha='".$fechadescuento."' group by concepto",
				);
			// echo '--->agrupar '."select *, concat(trim(ape_tit),', ',trim(nom_tit)) as nombre from titulares,". $fuente." where ".$condicion;
			$arreglo=serialize($arreglo);
			$arreglo=urlencode($arreglo);
			/*
			echo '<input type="submit" class="btn btn-info" name="Submit" value="Realizar Impresi&oacute;n de Listados" onClick="abrir2Ventanas(';
			echo "'";
			echo $fechadescuento;
			echo "'";
			echo ');">  ';
			*/
			echo '<input type="submit" class="btn btn-info" name="Submit" value="Realizar Impresi&oacute;n de Listados" onClick="abrir2Ventanas(';
			echo "'";
			echo $arreglo;
			echo "'";
			echo ');">  ';

			echo '</legend>';
			echo '</form>';
			echo '</div>';	
		}
		fclose($gestor);
	}
	else {
		mensaje(['tipo'=>'error','titulo'=>'Aviso!!!','texto'=>'<h2><h2>No se puede crear el archivo ($nombre_archivo) revise permisologia</h2>']);
		exit;
	}
//	echo 'La fecha final de hoy es: '. date("d/m/Y"). ' Hora local del servidor es: '. date("G:i:s").'<br>'; 
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
	echo "<form action='prenomesp.php?accion=Asiento' name='form1' method='post' onsubmit='return realiza_asiento_montepio(form1)'>";
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
	$sql_360="select * from ".$_SESSION[institucion]."sgcaf360 where (dcto_sem) order by cod_pres"; 
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

	$comando = "update ".$_SESSION['institucion']."sgcaf8co set fechanominamiercoles= now()";
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
		$sql="select * from prestamos where cedula=:cedula and (((montoespecial - montopagadoespecial) >0) and (f_1cuota < :fecha))";
		$resc=$db_con->prepare($sql);
		$resc->execute([
			":cedula"=>$micedula,
			":fecha"=>$fechadescuento,
			]);
		while ($registro = $resc->fetch(PDO::FETCH_ASSOC))
		{
			$montocotizacion=$registro['cuota_especial'];
			$concepto=$registro['concepto'];
			$saldo=$registro['montoespecial']-$registro['montopagadoespecial'];
			$referencia=$registro['referencia'];
			$sql="insert into cotizacionesxcobrar (fecha, cedula, montocotizacion, fecha_registro, ip_registro, cobrado, ip_modificado, fecha_modificado, concepto, referencia, saldo) VALUES (:fecha, :cedula, :montocotizacion, :fecha_registro, :ip_registro, :cobrado, :ip_registro, :fecha_registro, :concepto, :referencia, :saldo)";
			$resp=$db_con->prepare($sql);
			$resp->execute(array(
				":fecha"=>$fechadescuento,
				":cedula"=>$micedula,
				":montocotizacion"=>$montocotizacion,
				":fecha_registro"=>$momento,
				":ip_registro"=>$ip,
				":cobrado"=>$cero,
				":concepto"=>$concepto,
				":saldo"=>$saldo,
				":referencia"=>$referencia,
			));
		}
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

?>
</body>
</html>

