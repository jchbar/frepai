<?php
session_start();
include("home.php");
include_once("dbconfig.php");
include_once("funciones.php");
$mostrarregresar=0;
try
{
	$sql="select DATE_FORMAT(now(),'%m/%d/%Y') as hoy, DATE_ADD(NOW(), INTERVAL 24 MONTH) AS futuro, DATE_SUB(NOW(), INTERVAL 6 WEEK) AS pasado";
	$stmt=$db_con->prepare($sql);
	$stmt->execute();
	$res=$stmt->fetch(PDO::FETCH_ASSOC);
	$hoy=$res['hoy'];
	$pasado=$res['pasado'];
	$futuro=$res['futuro'];
}
catch(PDOException $e)
{
		echo $e->getMessage();
		// echo 'Fallo la conexion';
}

?>
<script src="js/js_solpre.js" type="text/javascript"> </script>
<body>

<?php
//$cedula = $_POST['cedula'];
$deci=2;
$sep_decimal='.';
$sep_miles=',';
$numasi=0;
$ip = la_ip();
$accion=$_GET['accion'];

//----------------------------
if ($accion == 'Buscar')  {
	echo "<form action='frepai.php?accion=Actualizar' name='form1' method='post' class='form-inline'>";
	extract($_POST);
	$lacedula = trim($_POST['cedula']);
	if (! $cedula) {
		$lacedula = $_SESSION['cedulasesion']; 
		}
	else 
		$_SESSION['cedulasesion']=$_POST['cedula'];
	if ($lacedula) { //  != ' ') {
		try
		{
			$sql="SELECT * FROM titulares where cedula = :lacedula";
			$result=$db_con->prepare($sql);
			$result->execute(array(":lacedula"=>$lacedula));
		}
		catch(PDOException $e){
			echo $e->getMessage();
			// echo 'Fallo la conexion';
		}
		$row= $result->rowCount(); // fetch(PDO::FETCH_ASSOC);
		if ($row < 1)
		{
			mensaje(array(
				"tipo"=>'danger',
				"texto"=>'<h1>Eeeeepa!!!! ese numero de cedula esta errado</h1>',
				));
			die('');
		}
		$row= $result->fetch(PDO::FETCH_ASSOC);
		echo "<input type = 'hidden' value ='".$row['cedula']."' name='cedula'>"; 
		$cedula=$row['cedula'];
		$tmensual=0;
		// $accion = 'Editar'; 


		// revisar si esta actualizado los datos de socios
		$hoy=date("Y-m-d", time());
		$ord="fecha_solicitud";
		$estacedula=$lacedula; // substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,3);
		$sql = "SELECT * FROM titulares WHERE (cedula = :estacedula)"; 
		try
		{
			$rs = $db_con->prepare($sql);
			$rs->execute(array(
				":estacedula"=>$estacedula,
				));
		}
		catch(PDOException $e){
			echo $e->getMessage();
			// echo 'Fallo la conexion';
		}
		if ($rs->rowCount() > 0) // existe
		{
			$rtit=$rs->fetch(PDO::FETCH_ASSOC);
			if (($rtit['status']=='ACTIVO') or ($rtit['status']=='JUBILA'))
			{
				// busco en frepai
				echo '<label for ="nombre">Titular</label>';
				echo '<input class="form-control" type="text" id="nombre" value="'.$rtit['ape_tit']. ' '.$rtit['nom_tit'].'" readonly="readonly">';
				$sql="SELECT * FROM frepai WHERE cedula =:cedula";
				try
				{
					$rsf = $db_con->prepare($sql);
					$rsf->execute(array(
						":cedula"=>$estacedula,
						));
					if ($rsf->rowCount() > 0) // existe en frepai
					{
						$existe=1;
						$rfre=$rsf->fetch(PDO::FETCH_ASSOC);
						// sr-only
						echo '<label for="aporte_mensual" class="control-label">Modificar Aporte Mensual </label>';
						echo '<input placeholder="Modificar Aporte Mensual" class="form-control" type="text" id="aporte_mensual" name="aporte_mensual" value="'.$rfre['aporte_ord'].'">';
						echo '<input type="hidden" id="fecha_ingreso" name="fecha_ingreso" value="'.$rfre['inscripcion'].'" readonly="readonly">';
						echo '<input type="hidden" id="existe" name="existe" value="1">';
						echo '<label for="status" class="sr-only control-label">Estatus</label>'; 	
						echo '<select class="form-control" name="status" id="status" size="1">';
						$estatus=$rfre['status'];
						echo '<option ACTIVO'.($estatus=='ACTIVO'?' selected="selected"':'').' value="ACTIVO">ACTIVO </option>'; 
						echo '<option RETIRA'.($estatus=='RETIRA'?' selected="selected"':'').' value="RETIRA">RETIRA </option>'; 
						echo '</select>'; 

					}
					else 
					{
						// class="sr-only control-label"
						mensaje(['tipo'=>'info','titulo'=>'Aviso!!!','texto'=>'Inclusion de Titular al Servicio FREPAI']);
						$existe=2;
						echo '<label for="aporte_mensual" >Aporte Mensual </label>';
						echo '<input placeholder="Aporte Mensual"  class="form-control" type="text" id="aporte_mensual" name="aporte_mensual" value="0" >';
					  	echo '<label for="fecha_ingreso">Fecha Ingreso FREPAI: </label>';
						?>

						<div class='input-group date' id='fecha_ingreso'>
							<input type='text' placeholder="Fecha Ingreso FREPAI" id="fecha_ingreso" name="fecha_ingreso" class="form-control" />
						    <span class="input-group-addon">
						    	<span class="glyphicon glyphicon-calendar"></span>
						    </span>
						</div>

						<script type="text/javascript">
							$('input[name="fecha_ingreso"]').daterangepicker({
								"singleDatePicker": true,
								"startDate": <?php echo $hoy; ?>, // "11/07/2016", 
								// "endDate": "<?php echo $pasado; ?>", // "11/30/2016", 
								//"minDate": button.data('los18'), // "11/01/2016",
								// "maxDate": <?php echo $futuro; ?> // "11/30/2016"
							}, function(start, end, label) {
						//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
							});
						</script>


						<?php
//						echo '<input type="hidden" id="fecha_ingreso" name="fecha_ingreso" value="'.$rfre['inscripcion'].'">';
						echo '<input type="hidden" id="existe" name="existe" value="2">';		
						echo '<input type="hidden" id="cedula" name="cedula" value="'.$cedula.'">';		
					}
				}
				catch(PDOException $e){
					echo $e->getMessage();
					// echo 'Fallo la conexion';
				}
			}
			else mensaje(['tipo'=>'danger','titulo'=>'Aviso!!!','texto'=>'<h2>El Titular no se encuentra activo</h2>']);
		}
//		else mensaje(['tipo'=>'error','titulo'=>'Aviso!!!','texto'=>'<h2>El Titular no se encuentra </h2>']);
		echo "<input class='btn btn-".($existe==2?"info":"warning")."' type = 'submit' value = '".($existe==2?"Inscribir":"Actualizar")."'>";
	}
	echo '</form>';
}	// fin de ($accion == 'Buscar') 
		
if (!$accion) {
	echo "<form action='frepai.php?accion=Buscar' name='form1' method='post'>";
	echo '<div class="form-group form-inline row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
    echo '<label for="cedula">C&eacute;dula </label>';
	echo '<input class="form-control" name="cedula" type="text" id="cedula" value=""  size="10" maxlength="10" />';
	echo "<input class='btn btn-info' type = 'submit' value = 'Buscar'>";
	echo '</div>';
	echo '</form>';
}	// fin de (!$accion) 

if ($accion == 'Actualizar') 
{
	$cedula=$_POST['cedula'];
	$existe=$_POST['existe'];
	$monto=$_POST['aporte_mensual'];
	try
	{
		if ($existe == 1)
		{
			$sql="UPDATE frepai SET aporte_ord= :monto, status=:status WHERE cedula =:cedula";
			$rsf = $db_con->prepare($sql);
			$status=$_POST['status'];
			$rsf->execute(array(
			":cedula"=>$cedula,
			":status"=>$status,
			":monto"=>$monto,
				));
			mensaje(['tipo'=>'success','titulo'=>'Aviso!!!','texto'=>'<h2>Informacion actualizada correctamente</h2>']);
		}
		else
		{
			$sql="INSERT INTO frepai (cedula, nombre, aporte_ord, div_mensual, disponible, codigo, ubicacion, inscripcion, status) VALUES (:cedula, :nombre, :monto, :dividendo, :disponible, :codigo, :ubicacion, :inscripcion, :status)";
			$rsf = $db_con->prepare($sql);
			$cero=0;
			$nada='';
			$inscripcion=explode('/',$_POST['fecha_ingreso']);
			$inscripcion=$inscripcion[2].'-'.$inscripcion[0].'-'.$inscripcion[1];
			$rsf->execute(array(
				":cedula"=>$cedula,
				":nombre"=>$nada,
				":monto"=>$monto,
				":dividendo"=>$cero,
				":disponible"=>$cero,
				":codigo"=>99,
				":ubicacion"=>$nada,
				":inscripcion"=>$inscripcion,
				":status"=>'ACTIVO',
			));
			mensaje(['tipo'=>'success','titulo'=>'Aviso!!!','texto'=>'<h2>Informacion almacenada correctamente</h2>']);
		}
		$sql="INSERT INTO frepai_hist (cedula, monto, status, fecha_registro, ip_registro, accion)  VALUES (:cedula, :monto, :status, :registro, :ip, :accion)";
		$rsf = $db_con->prepare($sql);
		if ($existe==2)
		{
			$status='ACTIVO';
			$accion='Incluir';
		}
		else 
		{
			$status=$_POST['status'];
			$accion='Modificar';
		}
		$ip = la_ip();
		$registro=ahora($db_con)['ahora'];

		$rsf->execute(array(
			":cedula"=>$cedula,
			":monto"=>$monto,
			":status"=>$status,
			":registro"=>$registro,
			":ip"=>$ip,
			":accion"=>$accion,
		));
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
}
if ($accion == 'Ver') {
	echo "<div align='center' id='div1'>";
	$mostrarregresar=1;
	$cedula=$_GET['cedula'];
	$nropre=$_GET['nropre'];
	mostrar_prestamo($cedula,$nropre);
	echo "</div>";
}	// fin de ($accion == 'Ver')

if (($accion == "Editar")) {	// muestra datos para prestamo
	echo '<div id="div1">';
	try
	{
		$sql='SELECT * FROM titulares WHERE cedula = :cedula';
		$result=$db_con->prepare($sql);
		$result->execute(array(
			":cedula"=>$cedula,
			));
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$temp = "";

	echo "<form enctype='multipart/form-data' action='frepai.php?accion=EscogePrestamo' name='form1' method='post' onsubmit='return valsoc(form1)'>";
	pantalla_prestamo($result,$cedula, $db_con, $accion);
	echo "<input type = 'hidden' value ='".$cedula."' name='cedula'>";
	$elstatus=$_SESSION['elstatus'];
} 	// fin de ($accion == "Editar")

if ($accion == "EscogePrestamo")  {	// selecciono el tipo de prestamo
	$mostrarregresar=1;
	echo '<div id="div1">';
	$cedula = $_POST['cedula'];
	$elprestamo = $_POST['elprestamo'];
	$temp = "";
	echo "<form enctype='multipart/form-data' action='frepai.php?accion=Solicitar' name='form1' id='form1' method='post' onsubmit='return valpre(form1)'";
	echo "input type = 'hidden' value ='".$cedula."' name='cedula'>";
	echo "<input type = 'hidden' value ='".$elprestamo."' name='elprestamo'>";
	$micedula=$cedula; //substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$sql_360="select * from proveedores where codigo=:elprestamo";
	try
	{
		$a_360=$db_con->prepare($sql_360);
		$a_360->execute(array(
			":elprestamo"=>$elprestamo,
			));
		$r_360=$a_360->fetch(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$sql_310="select * from prestamos where (cedula='$micedula') and (concepto='$elprestamo') and (status='A') and (! renovado)";
	try
	{
		$a_310=$db_con->prepare($sql_310);
		$a_310->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	//if ((! $r_360['masdeuno']) and ($a_310->rowCount()) >= 1)
	if ($a_310->rowCount() >= 1)
	{
			mensaje(array(
				"tipo"=>'warning',
				"texto"=>'<h2>No puede tener mas de un préstamo de este tipo</h2>',
				));
			die('');
	}
	else {
		pantalla_completar_prestamo($cedula, $elprestamo, $db_con);
	}
	echo '</form>';
	echo '</div>';
}	// fin de ($accion == "EscogePrestamo")

if ($accion == "Solicitar") {	// aprobar
	$cedula = $_POST['cedula'];
	$elprestamo = $_POST['elprestamo'];
	$elnumero = $_POST['elnumero'];
//	echo 'llego sta cedula '.$cedula;
//	phpinfo();
	$primerdcto = ($_POST['primerdcto']);
//	die ('primer dectuentp: '.$_POST['primerdcto']);
	$monto_solicitado = $_POST['monto_solicitado'];
	$monto_especial = $_POST['monto_especial'];
	$_SESSION['cedula']=$cedula;
	$_SESSION['elnumero']=$elnumero;
	$_SESSION['elprestamo']=$elprestamo;
	$cuota = $_POST['cuota'];
	$cuotae = $_POST['cuotae'];
	$interes = $_POST['interes'];
	$lascuotas = $_POST['lascuotas'];
	$lascuotase = $_POST['lascuotase'];
	$micedula=$cedula; // substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$_SESSION['micedula']=$micedula;
	$_SESSION['micodigo']=$micedula;
	$sql_200="select * from titulares where cedula='$cedula'";
	try
	{
		$a_200=$db_con->prepare($sql_200);
		$a_200->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$r_200=$a_200->fetch(PDO::FETCH_ASSOC);

	// fin de una sola fecha directa
	$hoy = date("Y-m-d");
	$b = $hoy;
	$elasiento = $_POST['referencia']; // date("ymd").$codigo;
	$ip = la_ip();
	$intereses_diferidos=$_POST['interes_diferido'];
	$_SESSION['montoprestamo']=$monto_solicitado;
	
//	echo 'numero a renovar'.$_SESSION['numeroarenovar'];
	/////////////////
//	$primerdcto='0000-00-00';
	mensaje(array(
		"tipo"=>'info',
		"texto"=>"<br>Creando servicio nuevo numero <strong>$elnumero</strong><br>",
		));
	$sql="insert into prestamos (cedula, referencia, concepto, fecha_solicitud, f_1cuota, ultcan_sdp, monto_solicitado, montoespecial,  montopagado, montopagadoespecial, status, cuota, nrocuotas, nrocuotasespeciales, interes, renovado, renova_por, f_pago, netcheque, ip, montopagado_ucla, montopagadoespecial_ucla, cuota_especial) values (:micedula, :elnumero, :elprestamo, :hoy, :primerdcto, :cuotaspagadas, :monto_solicitado, :montoespecial,  :montopagado, :montopagadoespecial, :status, :cuota, :nrocuotas, :nrocuotasespeciales, :interes,  :renovado, :renovadopor, :f_pago, :netcheque, :ip, :montopagado_ucla, :montopagadoespecial_ucla, :cuotae)";
//	echo $sql;
	try
	{
		$las_actas=$db_con->prepare($sql);
		$las_actas->execute(array(
			":micedula"=>$micedula,
			":elnumero"=>$elnumero,
			":elprestamo"=>$elprestamo,
			":hoy"=>$hoy,
			":f_pago"=>$hoy,
			":primerdcto"=>$primerdcto,
			":monto_solicitado"=>$monto_solicitado,
			":montoespecial"=>$monto_especial,
			":cuotaspagadas"=>0,
			":montopagado"=>0,
			":montopagadoespecial"=>0,
			":netcheque"=>0,
			":montopagado_ucla"=>0,
			":montopagadoespecial_ucla"=>0,
			":renovado"=>0,
			":renovadopor"=>'',
			":status"=>"A",
			":cuota"=>$cuota,
			":cuotae"=>$cuotae,
			":nrocuotas"=>$lascuotas,
			":nrocuotasespeciales"=>$lascuotase,
			":interes"=>$interes,
			":monto_solicitado"=>$monto_solicitado,
			":ip"=>$ip,
			));
	mensaje(array(
		"tipo"=>'success',
		"texto"=>"Se ha creado con exito el servicio con referencia".$elnumero,
		));
	}
	catch(PDOException $e){
		echo $e->getMessage();
		mensaje(array(
			"tipo"=>'danger',
			"texto"=>"NO Se ha creado con exito el servicio con referencia".$elnumero.". Notifique a su administrador de sistemas",
		));
		// echo 'Fallo la conexion';
	}
} // fin de ($accion == "Solicitar")

function buscar_saldo_f810($cuenta, $asiento, $con)
{
	$sql_f810="select cue_saldo from sgcaf810 where cue_codigo=:cuenta";
//	echo $sql_f810;
	$lacuentas=$con->prepare($sql_f810); //  or die ("<p />El usuario $usuario no pudo conseguir el saldo contable<br>".mysql_error()."<br>".$sql);
	$lacuentas->execute(array(":cuenta"=>$cuenta));
	$lacuentas=$lacuentas->fetch(PDO::FETCH_ASSOC);
	$saldoinicial=$lacuenta['cue_saldo'];
//	echo 'el asiento '.$asiento.'<br>';
	$sql_f820="select com_monto1, com_monto2 from sgcaf820 where com_cuenta=:cuenta";
	if ($asiento == '')
		$sql_f820.="";
	else
		$sql_f820.=" and (com_nrocom <> '$asiento') ";
	$sql_f820.=" order by com_fecha";
//	echo $sql_f820.'<br>';
	$lacuentas=$con->prepare($sql_f820); //  or die ("<p />El usuario $usuario no pudo conseguir los movimientos contables<br>".mysql_error()."<br>".$sql);
	$lacuentas->execute(array(":cuenta"=>$cuenta));
	while($lascuenta=$lacuentas->fetch(PDO::FETCH_ASSOC)) {
		$saldoinicial+=$lascuenta['com_monto1'];
//		echo $saldoinicial.'<br>';
		$saldoinicial-=$lascuenta['com_monto2'];
//		echo $saldoinicial.'<br>';
	}
	return round($saldoinicial,2);
}
//--------------------------------------------
function pantalla_completar_prestamo($cedula, $tipo, $db_con)
{ 
	$sql_200="select * from titulares where cedula=:cedula";
	try
	{
		$a_200=$db_con->prepare($sql_200);
		$a_200->execute(array(":cedula"=>$cedula));
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$r_200=$a_200->fetch(PDO::FETCH_ASSOC);
	$laparte=$tipo; // $r_200['cod_prof'];
	$micedula=$cedula; // substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	// determino factor de anualidad
	$factor = 12;
	echo "<input type = 'hidden' value ='".$factor."' name='factor_division' id='factor_division'>";
	$elnumero=numero_prestamo($micedula, $laparte, $db_con);

	$sql_360="select * from proveedores where codigo='$tipo'";
	try
	{
		$a_360=$db_con->prepare($sql_360);
		$a_360->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$r_360=$a_360->fetch(PDO::FETCH_ASSOC);
	$sql_310="select * from prestamos, proveedores where prestamos.cedula=:micedula and referencia=:nropre";
	try
	{
		$a_310=$db_con->prepare($sql_310);
		$a_310->execute(array(
			":micedula"=>$micedula,
			":nropre"=>$elnumero,
			));
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$r_310=$a_310->fetch(PDO::FETCH_ASSOC);
	echo '<div class="col-md-12">';
	mensaje(array(
		"tipo"=>'info',
		"texto"=>trim($r_360['casa']). ' / '.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / '.$r_200['cedula'].' / '.$elnumero,
		));

	if 	($_SESSION['numeroarenovar']) echo ' <br>(Renovacion) ';
	echo '</legend>';
	$inspeccion = 0;
	if ($inspeccion == 1)
		echo '<input type="text" id="resultado_js">'; // valor para inspeccion
	else 
		echo '<input type="hidden" id="resultado_js">'; // valor para inspeccion
	echo '<input type="hidden" id="referencia" name="referencia" value ="'.$elnumero.'">'; 
	echo '<table class="table table-bordered" width="500" border="1">';
	echo '<tr>';
    echo '<td width="100"> <label>Tasa de Interes </label></td><td width="100" align="right">'.number_format($r_360['interes'],2,'.',',').'%</td>';
	echo "<input type = 'hidden' value ='".$r_360['interes']."' name='interes' id='interes'>";
	echo "<input type = 'hidden' value ='".$r_360['tipo_interes']."' name='tipo_interes' id='tipo_interes'>";
	echo "<input type = 'hidden' value ='".$elnumero."' name='elnumero' id='elnumero'>";
	echo "<input type = 'hidden' value ='".$r_200['cedula']."' name='cedula' id='cedula'>";
    echo '<td width="150"><label for="monto_solicitado" >Monto Solicitado </label></td><td width="100" align="right">';
	// -----------
		echo '<input class="form-control" align="right" name="monto_solicitado" type="text" id="monto_solicitado" size="12" maxlength="12" value="100';
		echo '"/>';
//	---------------
	echo '</td>';

	echo '<td rowspan="8">';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
	echo '</div>';
	echo '</td>';

	echo '</tr>';
	echo '<tr>';
	$hoy=date("d/m/Y", time());
	$sql_acta="select fecha from cotizacionesxcobrar order by fecha desc limit 1";
	try
	{
		$las_actas=$db_con->prepare($sql_acta);
		$las_actas->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$el_acta=$las_actas->fetch(PDO::FETCH_ASSOC);
	echo '<tr>';
    echo '<td width="150"><label for="monto_especial" >Monto Dcto. Especial </label></td><td width="100" align="right">';
		echo '<input class="form-control" align="right" name="monto_especial" type="text" id="monto_especial" size="12" maxlength="12" value="20"/>';
	echo '</td>';
    echo '<td width="150"><label for="monto_normal" >Monto Dcto. Normal </label></td><td width="100" align="right">';
		echo '<input class="form-control" align="right" name="monto_normal" type="text" id="monto_normal" size="12" maxlength="12" value="00" readonly="readonly"/>';
//	---------------
	echo '</td>';
	echo '</tr>';
	echo '<td><label>Fecha de solicitud </label></td><td>'.$hoy.'</td>';
    echo '<td><label>Monto Pagado </label></td><td  align="right">'.number_format(0,2,',','.').'</td></tr>';
	echo '<tr>';
	echo '<td><label>1er Descuento</label> </td><td>';
	echo convertir_fechadmy($el_acta['fecha']);
	$primerdcto=convertir_fechadmy($el_acta['fecha']);
	$primerdcto=($el_acta['fecha']);
	echo "<input type = 'hidden' value ='".$primerdcto."' name='primerdcto' id='primerdcto'>";
	echo '</td>';
    echo '<td><label>Saldo </label></td><td  align="right">'.number_format(0,2,',','.').'</td></tr>';
	echo '<tr>';
	echo '<td><label>CC/NC (Normal)<br><label>CC/NC (Especial)</td><td></label>'.'0'.' de ';
	echo '<select id="lascuotas" name="lascuotas" size="1">';
	for ($laposicion=$r_360['maxcuotas'];$laposicion >= 1;$laposicion--) 
	{
		echo '<option value="'.ceroizq($laposicion,2).'"'.($laposicion==$r_360['maxcuotas']?" selected ":"").'" >'.ceroizq($laposicion,2).' </option>'; 
	}
		// 
	echo '</select><br>0'.' de '; 

	echo '<select id="lascuotase" name="lascuotase" size="1">';
	for ($laposicion=$r_360['maxcuotas'];$laposicion >= 1;$laposicion--) 
	{
		echo '<option value="'.ceroizq($laposicion,2).'"'.($laposicion==1?" selected ":"").'" >'.ceroizq($laposicion,2).' </option>'; 
	}
		// 
	echo '</select>'; 
	echo '</td>';
    echo '<td><label>Cuota Normal </label></td><td  align="right">';
	// .number_format($r_310['cuota'],$deci,$sep_decimal,$sep_miles).;
	echo '<input class="form-control"  align="right" name="cuota" type="text" id="cuota" size="12" maxlength="12" readonly="readonly" value ="0.00">';
	echo '<input align="right" name="descontar_interes" type="hidden" id="descontar_interes" size="12" maxlength="12" readonly="readonly" value ='.$r_360['interes_diferido'].'>';
	echo '<input align="right" name="monto_futuro" type="hidden" id="monto_futuro" size="12" maxlength="12" readonly="readonly" value ="0">';
	echo '</td></tr>';
	echo '<tr>';
	
	$elasiento = $elnumero; // date("ymd").$codigo;
	echo '<tr><td><label>Intereses: </label></td><td align="right">';
	echo '<input class="form-control" align="right" name="interes_diferido" type="text" id="interes_diferido" size="12" maxlength="12" readonly="readonly" value ="0.00"></td>';
	echo '<td><label for ="cuotae">Cuota Especial </td><td align="right">'; // .number_format($r_310['cuota_ucla'],2,',','.').'</td></tr>';
	echo '<input class="form-control"  align="right" name="cuotae" type="text" id="cuotae" size="12" maxlength="12" readonly="readonly" value ="0.00"></td></tr>';
	echo '<tr><td><label>Gastos Administrativos:</label> </td><td align="right">';
	echo '<input class="form-control" align="right" name="gastosadministrativos" type="text" id="gastosadministrativos" size="12" maxlength="12" readonly="readonly" value ="0.00"></td>';
	echo '<td><label>Estimado a Recibir (con intereses)</label></td><td align="right">';
	echo '<input class="form-control" align="right" name="montoneto" type="text" id="montoneto" size="12" maxlength="12" readonly="readonly" value ="0.00"';
	echo '</td></tr><tr>';
	echo '<td align="center" colspan="2"> '; 
	//echo '<input class="btn btn-info" type="button" name="calculo" value="Calcular Cuota" onClick="ajax_call()">	';
	echo '<input class="btn btn-info" type="button" id="calculo" name="calculo" value="Calcular Cuota">	';
	echo '</td><td align="center" colspan="2"> ';
	echo "<input class='btn btn-success' type = 'submit' value = 'Crear Servicio'>"; 

	echo '</td>';
}
	echo '</table>';
	echo '</fieldset>';


//----------------------------------------------
function pantalla_prestamo($result,$cedula, $db_con, $accion)
{
	$fila = $result->fetch(PDO::FETCH_ASSOC);
	echo "<input type = 'hidden' value ='".$fila['cedula']."' name='cedula'>";
	if ($accion == 'Editar') { $lectura = 'readonly = "readonly"'; $activada="disabled" ; } else {$lectura=''; $activada='';}
	if ($accion == 'Anadir') {
		$elcodigo=nuevo_codigo(); 
		$ingreso=date("d/m/Y", time());
		}
	else  $elcodigo=$fila['cedula'];
	$lectura = 'readonly = "readonly"'; $activada="disabled" ; 
//	<form id="form1" name="form1" method="post" action="">
?>
  <label><fieldset><legend>Informaci&oacute;n Personal </legend>
  <table class='table table-bordered' width="639" border="1">
    <tr>
		<td colspan="1" width="100" >C&oacute;digo </td>
 		<td colspan="1" width="130">C&eacute;dula </td>
		<td colspan="2" width="127">Socio </td>
		<td colspan="1" width="127" scope="col">Ingreso </td>
		<td colspan="1" width="127" scope="col">Ing. UCLA </td>
		<td colspan="1" width="127" scope="col">Tiempo UCLA</td>
		<td>Estatus</td>
<!--
	    <td align="center" colspan="1" class="<php echo ($disponible<=0)?'rojo':'azul' >" >Disponibilidad Neta (FREPAI)</td> -->
	</tr>

    <tr>
		<td><?php echo '<strong>'.$elcodigo.'</strong>'; ?></td>
 		<td><?php echo '<strong>'.$fila['cedula'].'</strong>';?></td>
		<td colspan="2" ><?php echo '<strong>'.$fila['ape_tit'].' '.$fila['nom_tit'] .'</strong>'?></td>
		<td><strong><?php echo convertir_fechadmy($fila['ing_ipsta']) ?></strong></td>
		<td><strong><?php echo convertir_fechadmy($fila['ing_ucla']) ?> </strong></td>
		<td><strong><?php echo cedad(convertir_fechadmy($fila['ing_ucla'])) ?> </strong></td>
		<td><strong><?php echo $fila['status'] ?></strong></td>
	    <td>
		<?php 
			$_SESSION['elstatus']=strtoupper($fila['status']);
		 ?></strong></td>
	</tr>
</table>
</fieldset> 

<?php
if (strtoupper($fila['status']) == 'RETIRA')
{
	echo '<tr><td colspan="8"><br><br><h2>Socio Esta Retirado</h2></td></tr>';
	echo '<script>alert("Socio Esta Retirado");</script> ';
	$_SESSION['motivo']=$cuento;
	echo '</table>';
	exit;
}
}

function mostrar_prestamo($cedula,$nropre)
{
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	try
	{
		$a_200=$db_con->prepare($sql_200);
		$a_200->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$r_200=$a_200->fetch(PDO::FETCH_ASSOC);
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where cedula='$micedula' and referencia='$nropre' and (concepto=codigo)";
	try
	{
		$a_310=$db_con->prepare($sql_310);
		$a_310->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$r_310=$a_310->fetch(PDO::FETCH_ASSOC);
	echo '<fieldset><legend>'.trim($r_310['casa']). ' / '.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_310['cedula'].' / '.$r_310['codsoc_sdp'].'</legend>';
	echo '<table class="basica 100 hover" width="400" border="1">';
	echo '<tr>';
    echo '<td width="250">Tasa de Interes </td><td width="200" align="right">'.number_format($r_310['interes'],$deci,$sep_decimal,$sep_miles).'%</td>';
    echo '<td width="250">Monto Solicitado </td><td width="200" align="right">'.number_format($r_310['monto_solicitado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr>';
	echo '<td>Fecha de solicitud </td><td>'.convertir_fechadmy($r_310['fecha_solicitud']).'</td>';
    echo '<td>Monto Pagado </td><td  align="right">'.number_format($r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr>';
	echo '<td>1er Descuento </td><td>'.convertir_fechadmy($r_310['f_1cuota']).'</td>';
    echo '<td>Saldo </td><td  align="right">'.number_format($r_310['monto_solicitado']-$r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr>';
	echo '<td>CC/NC</td><td>'.$r_310['ultcan_sdp'].' de '.$r_310['nrocuotas'].'</td>';
    echo '<td>Cuota Original </td><td  align="right">'.number_format($r_310['cuota'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr>';
	echo '<td>Acta / Fecha </td><td>'.$r_310['nro_acta'].' del '.$r_310['fecha_acta'].'</td>';
	echo '<td>Cuota Modificada </td><td align="right">'.number_format($r_310['cuota_ucla'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '</tr>';
	echo '</table>';
	echo '</fieldset>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<img src='".$lafoto."' width='156' height='156' border='0' />";
}	

function actualizar_acta($nroacta, $monto, $primerdcto) {
	$sql="update ".$_SESSION['institucion']."sgcafact set eje_pre=eje_pre + $monto where ((acta ='$nroacta') and (f_dcto = '$primerdcto'))";
	try
	{
		$resultado=$db_con->prepare($sql);
		$resultado->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
}

function generar_comprobantes($sql_360)
{
}
?>
