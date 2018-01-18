<?php
session_start();
include("home.php");
include_once("dbconfig.php");
include_once("funciones.php");
include_once("paginar.php");
$mostrarregresar=0;
?>
<script src="js/js_solpre.js" type="text/javascript"> </script>
<?
<body>

<?php
//$cedula = $_POST['cedula'];
$deci=2;
$sep_decimal='.';
$sep_miles=',';
$numasi=0;
$ip = la_ip();
$accion=$_GET['accion'];

if ($accion == "Renovar") {	// seleccionar el tipo de prestamo nuevo de renovacion
	$_SESSION['numeroarenovar']=$_GET['nropre'];
}
if ($accion == "Renovacion") {	// selecciono el tipo de prestamo
	$mostrarregresar=1;
	echo '<div id="div1">';
	$cedula = $_GET['cedula'];
	$elprestamo = $_GET['nropre'];
	$temp = "";
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=Solicitar' name='form1' id='form1' method='post' onsubmit='return valpre(form1)'";
	echo "<input type = 'hidden' value ='".$cedula."' name='cedula'>";
	echo "<input type = 'hidden' value ='".$elprestamo."' name='elprestamo'>";
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$sql_360="select * from ".$_SESSION['institucion']."sgcaf360 where codigo=:elprestamo";
	die($sql_360);
	try
	{
		$a_360=$db_con->prepare($sql_360);
		$a_360->execute(array(":elprestamo"=>$elprestamo));
		$r_360=$a_360->fetch(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	try
	{
		$sql_310="select * from prestamos where (cedula=:micedula) and (concepto=:elprestamo) and (status=:estatus) and (! renovado)";
		$a_310=$db_con->prepare($sql_310);
		$a_310->execute(array(
				":micedula"=>$micedula,
				":elprestamo"=>$elprestamo,
				":estatus"=>"A",
			));
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	if ((! $r_360['masdeuno']) and ($a_310->rowCount() >= 1))	
			echo '<h2>No puede tener mas de un préstamo de este tipo</h2>';
	else {
		pantalla_completar_prestamo($cedula, $elprestamo, $db_con);
	}
	echo '</form>';
	echo '</div>';
}	// fin de ($accion == "Renovacion")

//----------------------------
if ($accion == 'Buscar')  {
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
		$accion = 'Editar'; 

		$conta = (isset($_GET['conta'])?$_GET['conta']:1);
		// if (!$_GET['conta'])  $conta = 1;

		// revisar si esta actualizado los datos de socios
		$hoy=date("Y-m-d", time());
		$ord="fecha_solicitud";
//		$losdias=dias_pasados($row['ultima_act'],$hoy);
//		if (($_SERVER['REMOTE_ADDR']!='192.168.1.9') AND ($_SERVER['REMOTE_ADDR']!='192.168.1.96') AND ($_SERVER['REMOTE_ADDR']!='192.168.1.3') AND ($_ENV['COMPUTERNAME']!='JCHB-DM1'))	// icono para cancelar prestamo
			//	jc / mendez viejo / mendez nuevo 
/*
		if ($losdias >= (365*2))
		{
			mensaje(array(
				"tipo"=>'danger',
				"texto"=>'<h1> Los datos del socio estan desactualizado con <strong>'.$losdias.'</strong> d&iacute;as. Actualicelo en la seccion de Asociado/Socios</h1>',
				));
			die('');
			die('<h1> Los datos del socio estan desactualizado con '.$losdias.' dias. Actualicelo en la seccion de Asociado/Socios</h1>') ;
		}
*/		
		$estacedula=$lacedula; // substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,3);
		$sql = "SELECT * FROM prestamos, proveedores WHERE (prestamos.cedula = :estacedula and status=:estatus and (! renovado)) and concepto=codigo ORDER BY fecha_solicitud DESC"; // ." LIMIT ".($conta-1).", 10";
		try
		{
			$rs = $db_con->prepare($sql);
			$rs->execute(array(
				":estacedula"=>$estacedula,
				":estatus"=>"A",
				));
		}
		catch(PDOException $e){
			echo $e->getMessage();
			// echo 'Fallo la conexion';
		}
//		echo "<table class='basica 100 hover' width='700'><tr>";
		echo "<table class='table table-bordered' width='700'><tr>";
		echo '<th colspan="2"></th><th width="80">Otorgado</th><th width="80">Descontar</th><th width="80">Referencia</th><th width="280">Tipo</th><th width="100">Prestamo</th><th width="100">Especial</th><th width="100">Normal</th><th width="100">Saldo</th><th width="80">Cuota</th><th>Cuota Esp.</th><th width="80">NC / CC</th></tr>';
// <th width="80">Fecha</th>
		if (pagina($numasi, $conta, 20, "Servicios Activos", $ord)) {$fin = 1;}
// 		bucle de listado
		while($row=$rs->fetch(PDO::FETCH_ASSOC)) {
			echo "<tr>";


//		echo "<td class='centro'><a href='extractoctas3.php?cuenta=".trim($row['cuent_pres']).'-'.substr(trim($row['codsoc_sdp']),1,4)."&datos=no&'><img src='imagenes/page_wizard.gif' width='16' height='16' border='0' title='Mayor Analítico' alt='Mayor Analítico' /></a></td>";
		echo "<td class='centro'><a href='solpre.php?accion=Ver&cedula=".$cedula."&nropre=".$row['referencia']."'><img src='imagenes/page_user_dark.gif' width='16' height='16' border='0' title='Consultar' alt='Consultar'/></a></td>";
		echo "<td class='centro'><a href='solpre.php?accion=ModificarCuota&cedula=".$cedula."&nropre=".$row['referencia']."'><img src='imagenes/modiftbl.jpg' width='16' height='16' border='0' title='Modificar Cuota' alt='Modificar Cuota'/></a></td>";
/*
		echo "<td class='centro'>";
		if ($row['renovacion']>1)
			if ($row['ultcan_sdp'] >= $row['renovacion']) {
				echo "<a href='solpre.php?accion=Renovar&cedula=".$cedula."&nropre=".$row['referencia']."'>";
				echo "<img src='imagenes/action_refresh_blue.gif' width='16' height='16' border='0' title='Renovar'  alt='Renovar' />";
				echo "</a>";
			}
			else echo ' ';
		else if ($row['renovacion'] == 1){ 
				echo "<a href='solpre.php?accion=ReAjustar&cedula=".$cedula."&nropre=".$row['referencia']."'>";
				echo "<img src='imagenes/icon_get_world.gif' width='16' height='16' border='0' title='ReAjustar' alt='ReAjustar' />";
				echo "</a>";
			}
			else echo ' ';

			echo "</td>";
			echo "<td class='centro'>";
			// if (($_SERVER['REMOTE_ADDR']=='192.168.1.9') OR ($_SERVER['REMOTE_ADDR']=='192.168.1.96') OR ($_SERVER['REMOTE_ADDR']=='192.168.1.3') or ($_ENV['COMPUTERNAME']=='JCHB-DM1'))	// icono para cancelar prestamo
			//	jc / mendez viejo / mendez nuevo 
			{
				echo "<a href='solpre.php?accion=CancelarPrestamo&cedula=".$cedula."&nropre=".$row['referencia']."'>";
				echo "<img src='imagenes/informe.png' width='16' height='16' border='0' title='Cancelar Prestamo'  alt='Renovar' />";
				echo "</a>";
			}
			// if (($_SERVER['REMOTE_ADDR']=='192.168.1.9') OR ($_SERVER['REMOTE_ADDR']=='192.168.1.91') or ($_ENV['COMPUTERNAME']=='JCHB-DM1')) // icono para modificar neto a depositar prestamo
			{
//				echo "<td class='centro'>";
				echo "<a href='solpre.php?accion=NetoDepositar&cedula=".$cedula."&nropre=".$row['referencia']."'>";
				echo "<img src='imagenes/informe.png' width='16' height='16' border='0' title='Neto a Depositar'  alt='Renovar' />";
				echo "</a>";
//				echo "</td>";
			}
			$nro=$row['referencia'];
			echo "<a target=\"_blank\" href=\"proyeccionpdf.php?prestamo=$nro\" onClick=\"info.html\', \'\',\'width=250, height=190\')\">";
			echo "<img src='imagenes/Papiro[1].gif' width='16' height='16' border='0' title='Proyeccion de Pagos'  alt='Proyeccion' />";
			echo "</a>"; 	
			echo "</td>";
*/		
			
//			echo "<td>".convertir_fechadmy($row['fecha_solicitud'])."</td>";
			echo "<td>";
			echo convertir_fechadmy($row['fecha_solicitud'])."</td>";
			echo "<td>";
			echo convertir_fechadmy($row['f_1cuota'])."</td>";
			echo "<td class='centro'>";
			echo $row['referencia'];
			echo "</td>";
			echo "<td class='centro'>(".$row['codigo'].')'.$row['casa']."</td>";
			echo "<td align='right'>";
			echo number_format($row['monto_solicitado'],2,'.',',');
			echo "</td>";
			echo "<td align='right'>";
			echo number_format($row['montoespecial'],2,'.',',');
			echo "</td>";
			echo "<td align='right'>";
			echo number_format(($row['monto_solicitado']-$row['montoespecial']),2,'.',',');
			echo "</td>";
			echo "<td align='right'>".number_format(($row['monto_solicitado']-$row['montopagado']),2,'.',',')."</td>";
			if ($row['dcto_mensual'] == 1) {			
				echo "<td align='right' span style='color: #f00;'>".number_format(($row['cuota']),2,'.',',')."</td>";
				echo "<td align='right' span style='color: #f00;'>".number_format(($row['cuota_especial']),2,'.',',')."</td>";
				$tmensual+=$row['cuota'];
			}
			else 
				echo "<td align='right' span style='color: #0000FF;'>".number_format(($row['cuota']),2,'.',',')."</td>";
				echo "<td align='right' span style='color: #f00;'>".number_format(($row['cuota_especial']),2,'.',',')."</td>";
				// verde #0f0;
			
			echo "<td class='centro'>".number_format($row['nrocuotas'],0,'.',',')." / ";
			echo "".number_format($row['ultcan_sdp'],0,'.',',')."</td>";
			echo "</tr>";
		}
		echo '<tr><td align="right" colspan="8">Total Descuento mensual: </td>';
		echo '<td align="right"><strong>'.number_format($tmensual,2,'.',',').'</strong></td></tr>';

		echo "</table>";
	}
}	// fin de ($accion == 'Buscar') 
		
if (!$accion) {
	echo "<form action='solpre.php?accion=Buscar' name='form1' method='post'>";
	echo '<div class="form-group form-inline row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
    echo '<label for="cedula">C&eacute;dula </label>';
	echo '<input class="form-control" name="cedula" type="text" id="cedula" value=""  size="10" maxlength="10" />';
	echo "<input class='btn btn-info' type = 'submit' value = 'Buscar'>";
	echo '</div>';
	$_SESSION['numeroarenovar']='';
	$_SESSION['cedulasesion']=''; 
	echo '</form>';
}	// fin de (!$accion) 
if ($accion == 'Ver') {
	echo "<div align='center' id='div1'>";
	$mostrarregresar=1;
	$cedula=$_GET['cedula'];
	$nropre=$_GET['nropre'];
	mostrar_prestamo($cedula,$nropre);
	echo "</div>";
}	// fin de ($accion == 'Ver')

if (($accion == "Editar") or ($accion=="Renovar")) {	// muestra datos para prestamo
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

	echo "<form enctype='multipart/form-data' action='solpre.php?accion=EscogePrestamo' name='form1' method='post' onsubmit='return valsoc(form1)'>";
	pantalla_prestamo($result,$cedula, $db_con, $accion);
	echo "<input type = 'hidden' value ='".$cedula."' name='cedula'>";
	$elstatus=$_SESSION['elstatus'];
/*
	echo '<fieldset><legend>Informaci&oacute;n Para Pr&eacute;stamo </legend>';
	
	// revisar si esta suspendido 
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$sqls="select * from ".$_SESSION['institucion']."suspende where ((cedula = :micedula) and  (activo = 1) and (now() < suspendido))";
//	echo $sqls;
	try
	{
		$resuls=$db_con->prepare($sqls);
		$resuls->execute(array(
			":micedula"=>$micedula,
			));
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$vacio=($resuls->rowCount()> 0?true:false);
	while ($fila2 = $resuls->fetch(PDO::FETCH_ASSOC)) {
		echo '<h2>No se pudo descontar prestamo '.$fila2['prestamo']. ' enviado para '.$fila2['fallo'].' por un monto de '.number_format($fila2['monto'],2,'.',',').' suspendido hasta '.$fila2['suspendido']. ' reportado por '.$fila2['reporto'].'</h2>';
	}
	if ($vacio == true) // esta suspendido
	{
		mensaje(array(
			"tipo"=>'danger',
			"texto"=>'<h1>No puede solicitar pr&eacute;stamos</h1>',
			));
		die('');
		die('<h1>No puede solicitar prestamos</h1>');
	}

	// fin revisar si esta suspendido //

	$sqlprestamos="";
	if ($_SESSION['disponibilidadprestamo'] > 0) {
		if (($elstatus == "ACTIVO") or ($elstatus == "JUBILA")) {
			$sqlprestamos.="select * from ".$_SESSION['institucion']."sgcaf360 where ";}
		else {
			mensaje(array(
				"tipo"=>'danger',
				"texto"=>'<h2>El socio NO tiene un estatus disponible para solicitar pr&eacute;stamos</h1>',
				));
			die('');
//			echo '<h2>El socio NO tiene un estatus disponible para solicitar préstamos</h2>';
			echo '</fieldset>';
		}
	}
	else {
//		$sqlprestamos="select * from sgcaf360 where (retab_pres = 0) or (codigo='004') or (codigo='021') or (codigo='011') or (codigo='012') or (codigo='013') and ";
		$sqlprestamos="select * from ".$_SESSION['institucion']."sgcaf360 where (retab_pres = 0) or (codigo='004') or (codigo='005') or (codigo='021')  and ";
		$sqlprestamos="select * from ".$_SESSION['institucion']."sgcaf360 where (retab_pres = 0) or (codigo='004') or (codigo='021') or (codigo='061') or (codigo='012') or (codigo='013') and ";
			mensaje(array(
				"tipo"=>'warning',
				"texto"=>'<h2>El socio NO TIENE disponibilidad para solicitar pr&eacute;stamos, sin embargo puede solicitar aquellos que <em>no afectan </em> disponibilidad</h1>',
				));
//			die('');
//		echo '<h2>El socio NO tiene disponibilidad para solicitar préstamos<br>Sin embargo puede solicitar aquellos que <em>no afectan </em>disponibilidad</h2>';
	}
	$sqlprestamos.="(tiempo <= ".$_SESSION['tiempoactivo'];
	$sqlprestamos.=") and (visible = 1) order by codigo";
*/
	$sqlprestamos="select codigo, casa from proveedores where visible = 1";
	echo '<div class="form-group form-inline row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
	echo '<td class="form-control">Seleccione Tipo</td>';
   	echo '<td class="rojo">';
	echo '<select class="form-control" name="elprestamo" size="1">';
//	echo $sqlprestamos;
	try
	{
		$resultado=$db_con->prepare($sqlprestamos);
		$resultado->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	while ($fila2 = $resultado->fetch(PDO::FETCH_ASSOC)) {
		echo '<option value="'.$fila2['codigo'].'">'.$fila2['codigo'].' - '.$fila2['casa'].'</option>'; }
	echo '</select> *'; 
	echo '</td>';

/*
	$condicionarflash=0;
	if ($condicionarflash == 1)
	{
	// revisar condicion especial 3 dias
	$flash=array('023','053','057','058');
	$yatiene=0;
	$fechadescuento='2013-08-31';
	$sqlespecial="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='".$micedula."' and concepto=codigo and status='A' and ! renovado) and (dcto_sem=0) and (f_1cuota <= '$fechadescuento') order by f_1cuota";
//	echo $sqlespecial;
	try
	{
		$resultespecial=$db_con->prepare($sqlespecial);
		$resultespecial->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}

	$fianzase = $afectane = $noafectane = $mensuale = $montocuotase = 0;
	while($roww=$resultespecial->fetch(PDO::FETCH_ASSOC)) {
		if ($roww['retab_pres']==1)
			$afectane +=($roww['monto_solicitado']-$roww['montopagado']);
		else $noafectane += ($roww['monto_solicitado']-$roww['montopagado']);
		if (($roww['concepto'] != '021') and ($roww['concepto'] != '023') and ($roww['concepto'] != '024') and ($roww['concepto'] != '025'))
		if ($roww['dcto_sem']==1)
			$mensuale += $roww['cuota'];
		$montocuotase+=$roww['cuota'];
		$tipo=$roww['concepto'];
		if (($tipo == $flash[0]) or ($tipo == $flash[1]) or ($tipo == $flash[2]) or ($tipo == $flash[3]))
			$yatiene=1;
	}
	$cuento='Ya posee un descuento tipo FLASH. NO puede solicitar otro por esta via';
	if ($yatiene == 1) 
	{
		echo '<tr><td colspan="8"><br><br><h2>'.$cuento.'</h2></td></tr>';
		echo '<script>alert("'.$cuento.'");</script> ';
		$_SESSION['motivo']=$cuento;
		echo '</table>';
			mensaje(array(
				"tipo"=>'warning',
				"texto"=>'<h1>'.$cuento.'</h1>',
				));
//			die('');
//		exit;
	}
	// no esta bloqueado y determino los maximos
	$maximomonto=0;
	if (($montocuotase >=0) and ($montocuotase <5000))
		$maximomonto=1500;
	else 
		if (($montocuotase >=5000) and ($montocuotase <10000))
			$maximomonto=1000;
		else $maximomonto=00;

	$cuento='Los descuentos para el $fechadescuento superan los 10,000. NO puede solicitar';
	if ($montocuotase > 10000) 
	{
		echo '<tr><td colspan="8"><br><br><h2>'.$cuento.'</h2></td></tr>';
		echo '<script>alert("'.$cuento.'");</script> ';
		$_SESSION['motivo']=$cuento;
		echo '</table>';
		echo '</table>';
			mensaje(array(
				"tipo"=>'warning',
				"texto"=>'<h1>'.$cuento.'</h1>',
				));
//			die('');
//		exit;
	}
	
	echo '<tr><th colspan="4"><br><h1>Monto Maximo para la Solicitud del Prestamo FLASH<br></th>';
	echo '<th colspan="4"><br><h1>'.number_format($maximomonto,2,'.',',').'<br></th></tr></h1>';
	}

	// fin revisar condicion especial 3 dias

*/
	if (!$_SESSION['numeroarenovar']) echo "<input class='btn btn-success' type = 'submit' value = 'Nuevo Prestamo'></form>\n"; 
	else echo "<input class='btn btn-info' type = 'submit' value = 'Renovar por'></form>\n"; 
	echo '</fieldset>';
	echo '</div>';
	echo '</div>';
} 	// fin de ($accion == "Editar")

if ($accion == "EscogePrestamo")  {	// selecciono el tipo de prestamo
	$mostrarregresar=1;
	echo '<div id="div1">';
	$cedula = $_POST['cedula'];
	$elprestamo = $_POST['elprestamo'];
	$temp = "";
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=Solicitar' name='form1' id='form1' method='post' onsubmit='return valpre(form1)'";
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
/*
	if ($r_360['montofijo'] != 0)
		$_SESSION['disponibilidadprestamo']=$r_360['montofijo']; // $disponible; 
*/
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
//	phpinfo();
/*
	$elprestamo = $_POST['elprestamo'];
	$estatus='S';
	$sql_360="select * from ".$_SESSION['institucion']."sgcaf360 where codigo='$elprestamo'";
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
	if ($r_360['aprobar'] == 1) $estatus= 'A';
*/
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

/*
	$laparte=$r_200['cod_prof'];
	$codigo=$laparte;
	$nroacta=$_POST['nroacta']; 
	$fechaacta=$_POST['fechaacta'];
	$_SESSION['status']=strtoupper($r_200['status']);
//	echo 'aqui viene '.$codigo;
	$_SESSION['micodigo']=$codigo;
	// una sola fecha directa

	$sql_acta="select fecha from cotizacionesxcobrar order by fecha desc limit 1";
//	echo $sql_acta;
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
	$fechaacta=$el_acta['fecha'];
*/
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
/*
	$primerdcto=$_POST['primerdcto'];
//	$primer_dcto=convertir_fechadmy($el_acta['f_dcto']);
	echo "<input type = 'hidden' value ='".$primerdcto."' name='primerdcto' id='primerdcto'>";
	$_SESSION['primerdcto']=$primerdcto;
	if ($r_360['restar_otros'] == 1) $accion='Restar';
	else 
	if ($r_360['genera_com'] == 1){
		// generar_comprobantes($sql_360);
		include('solpre_2.php');
//**********************************
	$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = $neto_cheque where cedula = '$micedula' and referencia='$referencia'";
	if ($elprestamo == '055')
		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 29999 where cedula = '$micedula' and referencia='$referencia'";
	if ($elprestamo == '064')
		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 19999 where cedula = '$micedula' and referencia='$referencia'";
	if ($elprestamo == '066')
//		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 29999 where cedula = '$micedula' and referencia='$referencia'";
		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 68600 where cedula = '$micedula' and referencia='$referencia'";
	if ($elprestamo == '068')
		// $sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 49500 where cedula = '$micedula' and referencia='$referencia'";
//		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 69300 where cedula = '$micedula' and referencia='$referencia'";
		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 98000 where cedula = '$micedula' and referencia='$referencia'";
	try
	{
		$resultado=$db_con->prepare($sql);
		$resultado->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	if ($r_360['albanco']==0)
	{
		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = 0 where cedula = '$micedula' and referencia='$referencia'";
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
	$_SESSION['elasiento']=$elasiento;		
	actualizar_acta($nroacta,$debe,$primerdcto);
////////////////////////////

	}
	if ($r_360['genera_pl'] == 1) 
		if ($r_360['nom_planilla'] == '') {
			echo 'Preparando para la impresion<br>';
			echo "<a target=\"_blank\" href=\"solprepdf.php?cedula=$cedula\" onClick=\"info.html\', \'\',\'width=250, height=190\')\">Imprimir Planilla de Préstamo </a>"; 	
		}
		else {
			echo 'Preparando para la impresion dinámica<br>';
			echo "<a target=\"_blank\" href='";
			echo $r_360['nom_planilla'];
			echo "?cedula=$cedula' onClick=\"info.html\', \'\',\'width=250, height=190\')\">Imprimir Planilla de Préstamo </a>"; 	
		}
//		echo 'codigo'.$r_360['codigot'] ;
		if (($r_360['codigo']=='055') or ($r_360['codigo']=='064') or ($r_360['codigo']=='066')or ($r_360['codigo']=='068')) // vivienda
		{
			echo "<br><a target=\"_blank\" href='";
			echo 'actdat_giros.php';
			echo "?cedula=$cedula' onClick=\"info.html\', \'\',\'width=250, height=190\')\">Verificar Datos del Socio<br></a>"; 	

			echo "<a target=\"_blank\" href='";
			echo 'imp_girospdf.php';
			echo "?cedula=$cedula' onClick=\"info.html\', \'\',\'width=250, height=190\')\">Imprimir Giros</a>"; 	
		}
	else echo '<h2>Este tipo de préstamo esta configurado para no realizar impresión de planilla</h2>';

	/// *****imprimri en otro momento, faltan los fiadores*****
*/
} // fin de ($accion == "Solicitar")

/*
if ($accion == "ReAjustar") {	//  para aquellos que solo aumentan el monto y varian la cuota
	$mostrarregresar=1;
	echo '<div id="div1">';
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=Reajuste' name='form1' id='form1' method='post' onsubmit='return valajuste(form1)'";
	$cedula = $_GET['cedula'];
	$elnumero = $_GET['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// busco el prestamo
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<fieldset><legend>'.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_200['cedula'].' / '.$r_200['cod_prof'].' / '.$elnumero.'</legend>';
	echo '<table align="center" class="basica 100 hover" width="500" border="1">';
//	echo $sql_310;
	echo '<tr><td>Monto Actual del Prestamo </td>';
	echo '<td>'.number_format($r_310['monto_solicitado']-$r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr><td>Indique monto a adicionar </td>';
	echo '<input name="nropre" type="hidden" id="nropre" value ="'.$elnumero.'">';
	echo '<input name="cedula" type="hidden" id="cedula" value ="'.$cedula.'">';
	echo '<td><input align="right" name="montoprestamo" type="text" id="montoprestamo" size="12" maxlength="12" value =0.00></td></tr>';
	echo '<tr><td>Indique Nueva Cuota (Opcional)</td>';
	echo '<td><input align="right" name="cuota" type="text" id="cuota" size="12" maxlength="12" value ="'.number_format($r_310['cuota'],2,'.','').'"></td>';
	echo '<tr>';
	echo '<td align="center" colspan="4"> '; 
	echo "<input type = 'submit' value = 'Ajustar'>"; 
	echo '</td></tr>';
	echo '</table>';
	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
//**********************
	echo '</div>';
}	// fin de ($accion == "ReAjustar")
if ($accion == "Reajuste") {	//  para aquellos que solo aumentan el monto y varian la cuota
	$mostrarregresar=1;
	echo '<div id="div1">';
	$cedula = $_POST['cedula'];
	$elnumero = $_POST['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// actualizo 
	$sql_310="update ".$_SESSION['institucion']."sgcaf310 set monto_solicitado = monto_solicitado + ".$_POST['montoprestamo'].", cuota = ".$_POST['cuota']." where (referencia = '$elnumero') and (cedula = '$micedula')";
//	echo $sql_310;
	$a_310=mysql_query($sql_310);
	// busco el prestamo y lo muestro actualizado
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<fieldset><legend>'.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_200['cedula'].' / '.$r_200['cod_prof'].' / '.$elnumero.'</legend>';
	echo '<table align="center" class="basica 100 hover" width="500" border="1">';
//	echo $sql_310;
	echo '<tr><td>Monto Actualizado del Prestamo </td>';
	echo '<td>'.number_format($r_310['monto_solicitado']-$r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '</table>';
	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
	echo '</div>';
}	// fin de ($accion == "Reajuste")

if ($accion == "ModificarCuota") {	//  para aquellos que solo aumentan el monto y varian la cuota
	$mostrarregresar=1;
	echo '<div id="div1">';
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=ModificaCuota' name='form1' id='form1' method='post' onsubmit='return valajuste(form1)'";
	$cedula = $_GET['cedula'];
	$elnumero = $_GET['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// busco el prestamo
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<fieldset><legend>'.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_200['cedula'].' / '.$r_200['cod_prof'].' / '.$elnumero.'</legend>';
	echo '<table align="center" class="basica 100 hover" width="500" border="1">';
//	echo $sql_310;
	echo '<tr><td>Monto Actual del Prestamo </td>';
	echo '<td>'.number_format($r_310['monto_solicitado']-$r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<input name="nropre" type="hidden" id="nropre" value ="'.$elnumero.'">';
	echo '<input name="cedula" type="hidden" id="cedula" value ="'.$cedula.'">';
	echo '<tr><td>Cuota Actual </td>';
	echo '<td><input align="right" name="cuotav" type="text" id="cuotav" size="12" readonly maxlength="12" value ="'.number_format($r_310['cuota'],2,'.','').'"></td>';
	echo '<tr>';
	echo '<tr><td>Indique Nueva Cuota </td>';
	echo '<td><input align="right" name="cuota" type="text" id="cuota" size="12" maxlength="12" value ="'.number_format($r_310['cuota'],2,'.','').'"></td>';
	echo '<tr>';
	echo '<td align="center" colspan="4"> '; 
	echo "<input type = 'submit' value = 'Ajustar'>"; 
	echo '</td></tr>';
	echo '</table>';
	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
//**********************
	echo '</div>';
}	// fin de ($accion == "ModificarCuota")
if ($accion == "ModificaCuota") {	//  para aquellos que solo aumentan el monto y varian la cuota
	$mostrarregresar=1;
	echo '<div id="div1">';
	$cedula = $_POST['cedula'];
	$elnumero = $_POST['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// actualizo 
	$sql_310="update ".$_SESSION['institucion']."sgcaf310 set cuota = ".$_POST['cuota']." where (referencia = '$elnumero') and (cedula = '$micedula')";
//	echo $sql_310;
	$a_310=mysql_query($sql_310);
	// busco el prestamo y lo muestro actualizado
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<fieldset><legend>'.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_200['cedula'].' / '.$r_200['cod_prof'].' / '.$elnumero.'</legend>';
	echo '<table align="center" class="basica 100 hover" width="500" border="1">';
//	echo $sql_310;
	echo '<tr><td>Monto Actualizado del Prestamo </td>';
	echo '<td>'.number_format($r_310['monto_solicitado']-$r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr><td>Cuota Asignada</td>';
	echo '<td>'.number_format($r_310['cuota'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '</table>';
	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
	echo '</div>';
}	// fin de ($accion == "ModificaCuota")



if ($accion == "Restar") {	// restar prestamos cuando va a cancelarlos
// phpinfo();
	echo '<div id="div1">';
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=Concretar' name='form1' id='form1' method='post' onsubmit='return valpreres(form1)'";
	$cedula = $_SESSION['cedula'];
	$elnumero = $_SESSION['elnumero'];
	$elprestamo = $_SESSION['elprestamo'];
	$montoprestamo = $_SESSION['montoprestamo'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
//**********************
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	$elcodigo=$r_200['cod_prof'];
	$elcodigo=substr($elcodigo,-4);
	// determino los prestamos que tiene y puede cancelar
//	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (incluir_otros=1) and (concepto != '$elprestamo') and (status='A' and ! renovado) and (concepto=codigo) order by referencia";
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (incluir_otros=1) and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '".$_SESSION['numeroarenovar']."') order by referencia";

	echo '<fieldset><legend>';
	echo $sql_310;
	echo '</legend></fieldset>';

	$a_310=mysql_query($sql_310);
	echo '<fieldset><legend>'.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_200['cedula'].' / '.$r_200['cod_prof'].' / '.$elnumero;
	$saldo = $reintegro = $suma = 0;
	$restados=array();
	if ($_SESSION['numeroarenovar']) {
		echo '<br>(Renovacion)<br>';
		$sql="select cuent_pres,codsoc_sdp,cuent_int from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (referencia='".$_SESSION['numeroarenovar']."') and status='A' and ! renovado and (cedula='$micedula')  and (concepto=codigo)";
//		echo $sql;
//	echo $sql_310;
		$a_31=mysql_query($sql);
		$r_31=mysql_fetch_assoc($a_31);
		$lacuenta=trim($r_31['cuent_pres']).'-'.substr($r_31[codsoc_sdp],1,4);
		array_push($restados,$lacuenta);
		$saldo=buscar_saldo_f810($lacuenta,'', $db_con);
		if ($saldo > 0)
			$suma+=$saldo;
		else $suma-=$saldo;  // $r_310['saldo'];


		$lacuenta=trim($r_31['cuent_int']).'-'.substr($r_31[codsoc_sdp],1,4);
//		echo 'la otra cuenta' .$lacuenta;
		array_push($restados,$lacuenta);
		$saldo=buscar_saldo_f810($lacuenta,'', $db_con);
		if ($saldo < 0)
			$reintegro+=$saldo;
		else $reintegro-=$saldo;  // $r_310['saldo'];	

	}
	else $suma = $reintegro = 0;
	echo '</legend>';
	echo '<table align="center" class="basica 100 hover" width="500" border="1">';
	$cancelar=array();
	$registros=0;
	$nomostrar=array();
	
	while($r_310=mysql_fetch_assoc($a_310)) {
		echo '<tr>';
		$lacuenta=trim($r_310['cuent_pres']).'-'.substr($r_200[cod_prof],1,4);
		echo '<td>'.$r_310['referencia'].'('.$lacuenta.')</td>';
		echo '<td>'.$r_310['casa'].'</td>';
		$saldo=buscar_saldo_f810($lacuenta,'', $db_con);
		array_push($nomostrar,$saldo);
//		echo $lacuenta.'<br>';
		echo '<td align="right">'.number_format($saldo,2,".",",").'</td>';
//		echo '<td align="right">'.number_format(($r_310['monto_solicitado']-$r_310['montopagado']),2,".",",").'</td>';
		$registros++;
		echo '<td class="centro azul"><input type="checkbox" id="cancelar'.$registros.'" name="cancelar'.$registros.'" value="'.$r_310["referencia"] .'" onClick="calccanc()" ';
// 		if ($_SESSION['numeroarenovar']==$r_310['referencia']) echo ' checked ';
		if (in_array($lacuenta,$restados)) echo ' checked ';

		// disabled="true" ';
		echo '></td>';
//		echo $_SESSION['numeroarenovar'].' - ' .$r_310['referencia'];
		echo '</tr>' ;
	}
	// prueba con saldos desde contabilidad 
	$sql3="select cue_codigo, substr(cue_codigo,-4) as socio from ".$_SESSION['institucion']."sgcaf810 where substr(cue_codigo,-4)='$elcodigo' order by cue_codigo";
	$a_310=mysql_query($sql3);

	while($r_310=mysql_fetch_assoc($a_310)) {
		$lacuenta=$cuentaoriginal=$r_310['cue_codigo'];
		$saldo=buscar_saldo_f810($lacuenta,'', $db_con);
		if (($saldo != 0) and (!in_array ($saldo, $nomostrar)))
//		if (! in_array($cuentaoriginal,$restados))
		{
			echo '<tr>';
			echo '<td>'.$r_310['cue_codigo'].'</td>';
			$cuenta=substr($r_310['cue_codigo'],0,16);
			$s810="select cue_nombre from ".$_SESSION['institucion']."sgcaf810 where cue_codigo='$cuenta'";
			$a810=mysql_query($s810);
			$r810=mysql_fetch_assoc($a810);
			echo '<td>'.$r810['cue_nombre'].'</td>';
			$lacuenta=$r_310['cuent_pres']; // trim($r_310['cuent_pres']).'-'.substr($r_200[cod_prof],1,4);
			echo '<td align="right">'.number_format($saldo,2,".",",").'</td>';
			$registros++;
			echo '<td class="centro azul"><input type="checkbox" id="cancelar'.$registros.'" name="cancelar'.$registros.'" value="SC'.$r_310["cue_codigo"] .'" onClick="calccanc()" ';
			if (in_array($cuentaoriginal,$restados)) echo ' checked disabled = "true" ' ;
			echo '></td>';
//			echo $cuentaoriginal;
			echo '</tr>' ;
		}
	}
	
	// fin prueba con saldos desde contabilidad 
	echo "<input type = 'hidden' value ='".$registros."' name='registros' id='registros'>";
	echo "<input type = 'hidden' value ='".$micedula."' name='micedula' id='micedula'>";
	echo "<input type = 'hidden' value ='".$cedula."' name='cedula' id='cedula'>";
	echo "<input type = 'hidden' value ='".$marcados."' name='marcados' id='marcados'>";
//	echo "<input type = 'hidden' value ='".$montoprestamo."' name='montoprestamo' id='montoprestamo'>";
	echo '<tr>';
	echo '<td> Monto del Prestamo</td><td>';
	echo '<input align="right" name="montoprestamo" type="text" id="montoprestamo" size="12" maxlength="12" readonly="readonly" value ="'.number_format($montoprestamo,2,'.','').'"></td>';
	echo '<td>Descuentos Administrativos (Inc.Int.Dif)</td><td>';
	$descuentos=restaradministrativos($montoprestamo)+$_POST['interes_diferido'];
	echo '<input align="right" name="descuentosadm" type="text" id="descuentosadm" size="12" maxlength="12" readonly="readonly" value ='.number_format($descuentos,2,'.','').'></td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td> Reintegros</td><td>';
	echo '<input align="right" name="reintegros" type="text" id="reintegros" size="12" maxlength="12" readonly="readonly" value ='.number_format($reintegro,2,'.','').'></td>';
	echo '<td></td><td>';
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td> Total a Cancelar </td><td>';
	$neto=$montoprestamo-($suma+$descuentos)+$reintegro;
	echo '<input align="right" name="cancelados" type="text" id="cancelados" size="12" maxlength="12" readonly="readonly" value ='.number_format($suma,2,'.','').'></td>';
	echo '<td>Neto a Recibir</td><td>';
	echo '<input align="right" name="netoarecibir" type="text" id="netoarecibir" size="12" maxlength="12" readonly="readonly" value ='.number_format($neto,2,'.','').'></td>';
	// solicitar fiadores 
	$registrosf=0;
	if ($r_360['n_fia_pres'] > 0)
	{
		echo '<tr>';
			echo '<td align="center" colspan="4" width="100"><strong>Datos de los Fiadores</strong>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td colspan="2" width="100">Cedula';
			echo '</td>';
			echo '<td colspan="2" width="100">Monto';
			echo '</td>';
		echo '</tr>';
		for ($i=0;$i<$r_360['n_fia_pres'];$i++)
		{
		echo '<tr>';
			echo '<td colspan="2" width="100">';
				echo '<input type="textbox" id="cedfia'.$registrosf.'" name="cedfia'.$registrosf.'" value="">'; 
			echo '</td>';
			echo '<td colspan="2" class="centro azul"><input type="textbox" id="fianza'.$registrosf.'" name="fianza'.$registrosf.'" value="0"'; 			$registrosf++;
			echo '>';
			echo '</td>';
		echo '</tr>';
		}
	}
	echo '<input type="hidden" id="registrosf" name="registrosf" value="'.$registrosf.'">'; 

	// fin solicitar fiadores 
	echo '<td align="center" colspan="4"> '; 
	echo "<input type = 'submit' value = 'Continuar/Imprimir'>"; 
	echo '</td></tr>';
	echo '</table>';
	echo '</fieldset>';
	echo '</form>';
//	echo '</div>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";

//**********************
	echo '</div>';
	$descuentos=restaradministrativos($montoprestamo)-$_POST['interes_diferido'];
//	die('restar otros'.$r_360['restar_otros']);
} 	// ($accion == "Restar")


if ($accion == "CancelarPrestamo") {	//  para cancelar prestamos
	$mostrarregresar=1;
	echo '<div id="div1">';
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=CancelaPrestamo' name='form1' id='form1' method='post' onsubmit='return valajuste(form1)'";
	$cedula = $_GET['cedula'];
	$elnumero = $_GET['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// busco el prestamo
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<fieldset><legend>'.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_200['cedula'].' / '.$r_200['cod_prof'].' / '.$elnumero.'</legend>';
	echo '<table align="center" class="basica 100 hover" width="500" border="1">';
//	echo $sql_310;
	echo '<tr><td>Monto Actual del Prestamo </td>';
	echo '<td>'.number_format($r_310['monto_solicitado']-$r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr><td>Indique Motivo de Cancelacion </td>';
	$sql_tipos="select * from ".$_SESSION['institucion']."sgcaf000 where tipo = 'cancelarprestamo' order by nombre";
	$sql_tipo=mysql_query($sql_tipos);
//		echo '111';
	echo '<td>';
	echo '<select id="concepto" name="concepto" size="1">';
	while ($filaa = mysql_fetch_assoc($sql_tipo)) 
	{
		echo '<option value="'.$filaa['nombre'].'" '.' selected>'.$filaa['nombre'].'</option>';
	}
  	echo '</select> ';	
	echo '</td>';
	echo '</tr>';

	echo '<input name="nropre" type="hidden" id="nropre" value ="'.$elnumero.'">';
	echo '<input name="cedula" type="hidden" id="cedula" value ="'.$cedula.'">';
//	echo '<td><input align="right" name="montoprestamo" type="text" id="montoprestamo" size="12" maxlength="12" value =0.00></td></tr>';
	echo '<tr><td>Estara Vigente hasta el </td><td>';
//	echo '<td><input align="right" name="cuota" type="text" id="cuota" size="12" maxlength="12" value ="'.number_format($r_310['cuota'],2,'.','').'"></td>';
	$sql_acta="select * from ".$_SESSION['institucion']."sgcafact where especial = 0 order by fecha desc limit 1";
	$las_actas=mysql_query($sql_acta);
	$el_acta=mysql_fetch_assoc($las_actas);
	$primerdcto=($el_acta['f_dcto']);
	$sql="select date_sub('$primerdcto',INTERVAL 1 DAY) as fecha";
	$rsql=mysql_query($sql);
	$asql=mysql_fetch_assoc($rsql);
	$primerdcto=($asql['fecha']);
	echo '<input align="right" name="primerdcto" type="text" id="primerdcto" value ="'.$primerdcto.'" readonly="readonly"></td>';
	echo '</td><tr>';
	echo '<td align="center" colspan="4"> '; 
	echo "<input type = 'submit' value = 'Realizar Cancelacion'>"; 
	echo '</td></tr>';
	echo '</table>';
	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
//**********************
	echo '</div>';
}	// fin de ($accion == "CancelarPrestamo")
if ($accion == "CancelaPrestamo") {	//  para aquellos que solo aumentan el monto y varian la cuota
	$mostrarregresar=1;
	echo '<div id="div1">';
	$cedula = $_POST['cedula'];
	$elnumero = $_POST['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// actualizo 
	$sql_310="update ".$_SESSION['institucion']."sgcaf310 set status='C', renovado = 1, renova_por = '".$concepto."', paga_hasta = '$primerdcto' where (referencia = '$elnumero') and (cedula = '$micedula')";
//	echo $sql_310;
	$a_310=mysql_query($sql_310);
	$cuento='registro de '.$concepto.' prestamo '.$elnumero.' de cedula '.$micedula;
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
	$usuario=$_SERVER['REMOTE_ADDR'];
	$sql="insert into sgcabita (cuento, ip, quien) values ('$cuento', '$ip', '$usuario')";
	$a_310=mysql_query($sql);
	
//	echo $sql;
	
	// busco el prestamo y lo muestro actualizado

	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<h1>Cancelacion Realizada</h1>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
	echo '</div>';
}	// fin de ($accion == "CancelaPrestamo")


if ($accion == "NetoDepositar") {	//  para modificar neto a depositar de los prestamos
	$mostrarregresar=1;
	echo '<div id="div1">';
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=NetoDeposito' name='form1' id='form1' method='post' onsubmit='return valajuste(form1)'";
	$cedula = $_GET['cedula'];
	$elnumero = $_GET['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// busco el prestamo
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<fieldset><legend>'.trim($r_200['ape_tit']). ', '.trim($r_200['nom_tit']).' / ';
	echo $r_200['cedula'].' / '.$r_200['cod_prof'].' / '.$elnumero.'</legend>';
	echo '<table align="center" class="basica 100 hover" width="500" border="1">';
//	echo $sql_310;
	echo '<tr><td>Monto Actual del Prestamo </td>';
	echo '<td>'.number_format($r_310['monto_solicitado']-$r_310['montopagado'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
	echo '<tr><td>Neto actual a Depositar del Prestamo </td>';
	echo '<td>'.number_format($r_310['netcheque'],$deci,'.','').'</td></tr>';
	echo '<tr><td>Indique Nuevo neto a Depositar </td>';
	echo '<input name="nropre" type="hidden" id="nropre" value ="'.$elnumero.'">';
	echo '<input name="cedula" type="hidden" id="cedula" value ="'.$cedula.'">';
	echo '<input name="netooriginal" type="hidden" id="netooriginal" value ="'.$r_310['netcheque'].'">';
	echo '<td><input align="right" name="netoprestamo" type="text" id="netoprestamo" size="12" maxlength="12" value ='.number_format($r_310['netcheque'],$deci,'.','').'></td></tr>';
	echo '<td align="center" colspan="4"> '; 
	echo "<input type = 'submit' value = 'Realizar Modificacion Neto a Depositar'>"; 
	echo '</td></tr>';
	echo '</table>';
	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
//**********************
	echo '</div>';
}	// fin de ($accion == "NetoDepositar")
if ($accion == "NetoDeposito") {	//  para aquellos que solo aumentan el monto y varian la cuota
	$mostrarregresar=1;
	echo '<div id="div1">';
	$cedula = $_POST['cedula'];
	$elnumero = $_POST['nropre'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$deci=$_SESSION['deci'];
	$sep_decimal=$_SESSION['sep_decimal'];
	$sep_miles=$_SESSION['sep_miles'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	// actualizo 
	$neto = $_POST['netoprestamo'];
	$sql_310="update ".$_SESSION['institucion']."sgcaf310 set netcheque = '$neto' where (referencia = '$elnumero') and (cedula = '$micedula')";
//	echo $sql_310;
	$a_310=mysql_query($sql_310);
	$cuento='registro de modificacion de monto neto de '.$_POST['netooriginal'].' prestamo '.$elnumero.' de cedula '.$micedula. ' por el nuevo neto de '.$neto;
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
	$usuario=$_SERVER['REMOTE_ADDR'];
	$sql="insert into sgcabita (cuento, ip, quien) values ('$cuento', '$ip', '$usuario')";
	$a_310=mysql_query($sql);
	
//	echo $sql;
	
	// busco el prestamo y lo muestro actualizado

	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (status='A' and ! renovado) and (concepto=codigo) and (referencia = '$elnumero') limit 1";
	//  order by referencia";
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<h1>Monto Neto Modificado</h1>';
	$lafoto='fotos/'.substr($cedula,2,8).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
	echo '</div>';
}	// fin de ($accion == "NetoDeposito")




if ($accion == "Concretar") {	// hacer los asientos y actualizar el prestamo, faltarian los fiadores
	$mostrarregresar=1;
	extract($_POST);
	$cedula = $_POST['cedula'];
	$sql_200="select * from titulares where cedula='$cedula'";
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	$elnumero = $_SESSION['elnumero'];
	$elprestamo = $_SESSION['elprestamo'];
	echo 'el prestamo ' .$elprestamo;
	echo 'el numero '.$elnumero;
	$referencia=$elnumero;
	$montoprestamo = $_SESSION['montoprestamo'];
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (referencia='$elnumero') and (concepto = '$elprestamo') and (status='A' and ! renovado) and (concepto=codigo)";
//	echo $sql_310;
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	$hoy = date("Y-m-d");
	$b = $hoy;
	$albanco=$r_310['monto_solicitado'];
	$elasiento = date("ymd").$r_310['codsoc_sdp'];
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
//	phpinfo();
	$primerdcto=$_POST['primerdcto'];
	$registros=$_POST['registros'];
	if ($r_310['genera_com'] == 1){
//		echo 'la830';
//		echo "Generando encabezado contable <strong>$elasiento </strong> <br>";
		echo "Generando encabezado contable <strong><a target=\"_blank\" href='editasi2.php?asiento=$elasiento'>$elasiento </a></strong> <br>";
		$desc='Prestamo Otorgado al socio '.$r_200['ape_tit']. ' '.$r_200['nom_tit'];
		$sql = "INSERT INTO ".$_SESSION['institucion']."sgcaf830 (enc_clave, enc_fecha, enc_desco, enc_desc1, enc_debe, enc_haber, enc_item, enc_dif, enc_igual, enc_refer, enc_sw, enc_explic) VALUES ('$elasiento', '$b', '$desc','',0,0,0,0,0,0,0,'$desc')"; 
		if (!mysql_query($sql)) die ("El usuario $usuario no tiene permiso para añadir Asientos.<br>".$sql);

		// cargo prestamo al socio
		$laparte=$r_310['codsoc_sdp'];
		$cargo=trim($r_310['cuent_pres']).'-'.substr($laparte,1,4);
		if ($r_310['int_dif'] == 1) {
			$cuenta_diferido=trim($r_310['cuent_int']).'-'.substr($laparte,1,4);
			
		}
		echo "Generando cargos del asiento <strong>$elasiento </strong>  <br>";
		$debe=$r_310['monto_solicitado'];
		if ($debe != 0) {
			$cuenta1=$cargo;
			agregar_f820($elasiento, $b, '+', $cuenta1, $r_310['casa'], $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
		}
		echo "Generando abonos del asiento <strong>$elasiento </strong>  <br>";
		$debe=$r_310['inicial'];
		$albanco-=$debe;
		if ($debe != 0) {
			$cuenta1=$cargo;
			agregar_f820($elasiento, $b, '-', $cuenta1, 'Inicial '.$r_310['casa'], $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
		}

		for ($i=0;$i<$registros;$i++)		// no es necesarios revisar el check si aparece es porq estan seleccionados para hacer el asiento 
		{
			$variable='cancelar'.($i+1);
			if (!empty($$variable)) 
			{
				echo "Cancelando prestamos / Generando Registros contables del asiento <strong>$elasiento </strong> del prestamo numero <strong>".$$variable."</strong><br>";
				if ((substr($$variable,0,2)!='SC')) {
					$s310="select cuent_pres, codsoc_sdp, casa, cuent_int from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360 where (cedula='$micedula') and (referencia = '".$$variable."') and (status='A' and ! renovado) and (concepto=codigo)";
					$a310=mysql_query($s310);
					$r310=mysql_fetch_assoc($a310);
				// saldo pendiente del prestamo
					$cuenta1=trim($r310['cuent_pres']).'-'.substr($r310[codsoc_sdp],1,4);
					$debe=buscar_saldo_f810($cuenta1,$elasiento, $db_con);
					$cargar=(($debe>0)?'-':'+');
					$debe=abs($debe);
					agregar_f820($elasiento, $b, $cargar, $cuenta1, 'Canc.'.$r310['casa'], $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
					if ($debe > 0)
						$albanco-=$debe;
					else $albanco+=$debe;
					$sql_312="insert into ".$_SESSION['institucion']."sgcaf312 (tipo, cuento, cuenta, monto, numero, cedula) VALUES ('$cargar','Canc.".$r310['casa']."', '$cuenta1', $debe, '$elnumero','$micedula')";
					$resultado=mysql_query($sql_312);

					// intereses
					$cuenta1=trim($r310['cuent_int']).'-'.substr($r310[codsoc_sdp],1,4);
					$debe=buscar_saldo_f810($cuenta1,$elasiento, $db_con);
					$cargar=(($debe<0)?'+':'-');
					if ($debe > 0)
						$albanco+=$debe;
					else $albanco-=$debe;

					$debe=abs($debe);
					agregar_f820($elasiento, $b, $cargar, $cuenta1, 'Int.'.$r310['casa'], $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
					$sql_312="insert into ".$_SESSION['institucion']."sgcaf312 (tipo, cuento, cuenta, monto, numero, cedula) VALUES ('$cargar','Int.".$r310['casa']."', '$cuenta1', $debe, '$elnumero','$micedula')";
					$resultado=mysql_query($sql_312);
				// cancelar el prestamo
					$arenovar=$$variable;
					$primerdcto=$_SESSION['primerdcto'];

					$sql_acta="select * from ".$_SESSION['institucion']."sgcafact where especial = 0 order by fecha desc limit 1";
					$las_actas=mysql_query($sql_acta);
					$el_acta=mysql_fetch_assoc($las_actas);
					$primerdcto=($el_acta['f_dcto']);
					$sql="select date_sub('$primerdcto',INTERVAL 1 DAY) as fecha";
					$rsql=mysql_query($sql);
					$asql=mysql_fetch_assoc($rsql);
					$primerdcto=($asql['fecha']);
					$numero=$_SESSION['numeroarenovar'];

					$sql="update ".$_SESSION['institucion']."sgcaf310 set renovado = 1, renova_por = '$arenovar', paga_hasta='$primerdcto' where referencia='$numero'" ;
					echo $sql.' 1 <br>';
					if (!mysql_query($sql)) die ("El usuario $usuario no tiene permiso para ajustar la renovacion<br>".$sql);
				}
				else {
					$cuenta1=substr($$variable,2,21);
					$debe=buscar_saldo_f810($cuenta1,$elasiento, $db_con);
					$cargar=(($debe>0)?'-':'+');
					$debe=abs($debe);
					agregar_f820($elasiento, $b, $cargar, $cuenta1, 'Pago Saldo ', $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
					if ($debe > 0)
						$albanco+=$debe;
					else $albanco-=$debe;
					
					if ($debe!=0.00) {
						$sql_312="insert into ".$_SESSION['institucion']."sgcaf312 (tipo, cuento, cuenta, monto, numero, cedula) VALUES ('$cargar',' Pago Saldo ".$cuenta."', '$cuenta1', $debe, '$elnumero','$micedula')";
//						echo $sql_312;
						$resultado=mysql_query($sql_312);
					}

				}
			}
		}
		// coloco las deducciones obligatorias activas
		if ($r_360['pla_autor'] == 0)
			$sql_deduccion="select * from ".$_SESSION['institucion']."sgcaf311 where activar = 1";
		else 
			$sql_deduccion="select * from ".$_SESSION['institucion']."sgcaf311 where activar = 2";
		$a_deduccion=mysql_query($sql_deduccion);
		$cargo=trim($r_360['cuent_pres']).'-'.substr($laparte,1,4);
		// cargo prestamo al socio
		$debe = $monto_solicitado;
		if ($r_360['int_dif'] == 1) {
			$cuenta_diferido=trim($r_360['cuent_int']).'-'.substr($laparte,1,4);
		}
		echo "Generando cargos del asiento <strong>$elasiento </strong>  <br>";
		$debe=$r_310['intereses']; // $intereses_diferidos;
		$intereses_diferidos=$debe;
		$albanco-=$debe;
		if ($debe != 0) {
			$cuenta1=$cuenta_diferido; // .'-'.substr($laparte,1,4);
			agregar_f820($elasiento, $b, '-', $cuenta1, $r_310['casa'], $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
		}
		$d_obligatorias=0;
		while($r_deduccion=mysql_fetch_assoc($a_deduccion)) {
			if ($r_deduccion['porcentaje'] == 0)
				$monto_deduccion=$r_deduccion['monto'];
			else $monto_deduccion=($r_310['monto_solicitado']-$r_310['inicial'])*($r_deduccion['porcentaje']/100);
			$d_obligatorias+=$monto_deduccion;
			$debe=$monto_deduccion;
			$albanco-=$debe;
			$cuenta1=trim($r_deduccion['cuenta']);
			agregar_f820($elasiento, $b, '-', $cuenta1, $r_deduccion['cuento']. ' '.trim($socio['ape_tit']). ' '.trim($socio['nom_tit']), $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
			$sql_312="insert into ".$_SESSION['institucion']."sgcaf312 (tipo, cuento, cuenta, monto, numero, cedula) VALUES ('-','".$r_deduccion['cuento']."', '$cuenta1', $monto_deduccion, '$elnumero','$micedula')";
//			echo $sql_312;
			$resultado=mysql_query($sql_312);
		}
		$debe = $albanco; //  - ($intereses_diferidos + $d_obligatorias); 
		$neto_cheque = $debe;
		if ($debe != 0) {
			if ($r_360['otractaab']=='')
			{
				$sql="select * from ".$_SESSION['institucion']."sgcaf000 where tipo='CtaSocxPag'";
				$result=mysql_query($sql); // or die ("<p />El usuario $usuario no pudo conseguir la cuenta x pagar<br>".mysql_error()."<br>".$sql);
				$cuentas=mysql_fetch_assoc($result);
//				echo 'cuenta buscada '.$cuentas['nombre'].'<br>';
				$cuenta1=trim($cuentas['nombre']).'-'.substr($laparte,1,4);
			}
			else 
			{
				$cuenta1=trim($r_360['otractaab']);
			}
//			echo 'cuenta mostrada '.$cuenta1.'<br>';
			agregar_f820($elasiento, $b, '-', $cuenta1, $r_310['casa'].' '.trim($socio['ape_tit']). ' '.trim($socio['nom_tit']), $debe, $haber, 0,$ip,0,$referencia,'','S',0); 
		}
		// agregar los fiadores
		for ($i=0;$i<$_POST['registrosf'];$i++)
		{
			$cedfia='cedfia'.$i;
			if ($$cedfia != '')
			{
				$montofia='fianza'.$i;
				if ($$montofia > 0) 
				{
					$sqlfia='insert into ".$_SESSION['institucion']."sgcaf320 
					(codsoc_fia, nropre_fia, codfia_fia, monlib_fia, tipmov_fia, monto_fia) values ("'.
					$r_200['cod_prof']. '", "'.$elnumero.'", "'.$$cedfia.'", 0, "F", '.$$montofia.')';
//					echo $sqlfia;
					mysql_query($sqlfia);
				}
			}
		}
		// fin agregar los fiadores
		$sql="update ".$_SESSION['institucion']."sgcaf310 set netcheque = $neto_cheque where cedula = '$micedula' and referencia='$referencia'";
//		echo $sql;
		$resultado=mysql_query($sql);
//		$sql="update ".$_SESSION['institucion']."sgcaf310 set renovado = 1, renova_por = '$elnumero', paga_hasta='$primerdcto-1' where referencia='$arenovar'" ;
//		echo $sql;
		$resultado=mysql_query($sql);
		$_SESSION['elasiento']=$elasiento;		
		actualizar_acta($nroacta,$debe,$primerdcto);
		if ($r_310['garantia']==2) 
			solicitar_fiadores($elnumero,$cedula);
		else 
			if ($r_310['genera_pl'] == 1) {
				echo 'Preparando para la impresion<br>';
				echo "<a target=\"_blank\" href=\"solprepdf.php?cedula=$cedula\" onClick=\"info.html\', \'\',\'width=250, height=190\')\">Imprimir Planilla de Préstamo </a>"; 
			}
		else echo '<h2>Este tipo de préstamo esta configurado para no realizar impresión de planilla</h2>';
	}
	/// *****imprimri en otro momento, faltan los fiadores*****	
}	// ($accion == "Concretar")
if ($accion == "Eliminar") {	// eliminar el fiador
	$elnumero=$_GET['nropre'];
	$lacedula=$_GET['cedula'];
	$elregistro=$_GET['registro'];
//	echo $elnumero. ' - '.$elregistro;
	$sql_320="delete from ".$_SESSION['institucion']."sgcaf320 where registro='$elregistro'";
	$a_320=mysql_query($sql_320);
//	echo $sql_320;
	solicitar_fiadores($elnumero,$lacedula);
} // fin d e($accion == "Eliminar")

//-----------------------------------
if ($accion == "Fiadores") {	// los fiadores
	$elnumero=$_SESSION['elnumero'];
	$lacedula=$_SESSION['lacedula'];
	$cedulafiador=$_POST['_unacedula'];
//	echo 'guardo el fiador y vuelvo a solicitar '.$elnumero. ' - ' .$lacedula;
	$sql_200="select cod_prof from titulares where cedula = '$cedulafiador'";
//	echo $sql_200;
	$a_200=mysql_query($sql_200);
	$r_200=mysql_fetch_assoc($a_200);
	$sql_310="select cod_prof from titulares, ".$_SESSION['institucion']."sgcaf310 where (referencia='$elnumero') and (cod_prof = codsoc_sdp)";
//	echo $sql_310;
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	if (mysql_num_rows($a_200) > 0) // existe el fiador
	{
		$elfiador=$r_200['cod_prof'];
		$afianzado=$r_310['cod_prof'];
		$monto=$_POST['monto_fianza'];
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
		$sql_320="insert into ".$_SESSION['institucion']."sgcaf320 (codsoc_fia, nropre_fia, codfia_fia, monlib_fia, tipmov_fia, monto_fia, ip) values 
		('$afianzado', '$elnumero','$elfiador',0,'F',$monto,'$ip')";
//		echo $sql_320;
		$a_320=mysql_query($sql_320);

	}
	else echo '<h2>Informacion del fiador no existe!!!!</h2>';
	solicitar_fiadores($elnumero,$lacedula);
}	// fin de ($accion == "Fiadores")

if ($mostrarregresar==1) { // ($accion == "Buscar") or ($accion == "Ver") or ($accion="EscogePrestamo")) {
	echo '<form enctype="multipart/form-data" name="formdepie" method="post" action="solpre.php?accion=Buscar">';
	echo '<input type = "hidden" value ="'.$_SESSION['cedulasesion'].'" name="cedula" id="cedula">';
// 	echo 'la cedula '.$_SESSION['cedulasesion'];
	echo '<div style="clear:both"></div>';
	echo '<p /><div class="noimpri" style="clear:both;text-align:center">';
	echo '<input type="submit" name="boton" value="regresar" tabindex="3">';
	echo '</div>';
	echo '</form>';
}
else 
	include("pie.php");
?>
</body></html>


<?php

function solicitar_fiadores($elnumero,$lacedula)
{
	echo '<div id="div1">';
	$_SESSION['elnumero']=$elnumero;
	$_SESSION['cedula']=$lacedula;
	echo "<form enctype='multipart/form-data' action='solpre.php?accion=Fiadores' name='form1' id='form1' method='POST' onsubmit='return valfiadores(form1)'>";
	// &elnumero=$elnumero&cedula=$cedula&
	echo "<form action='solpre.php?accion=Buscar' name='form1' method='post'>";
	$micedula=substr($lacedula,0,4).'.'.substr($lacedula,4,3).'.'.substr($lacedula,7,4);
	$sql_310="select * from ".$_SESSION['institucion']."sgcaf310, ".$_SESSION['institucion']."sgcaf360, titulares where (cedula='$micedula') and (referencia='$elnumero') and (status='A' and ! renovado) and (concepto=codigo) and (cedula='$lacedula') limit 1";
	// and (concepto = '$elprestamo') 
//	echo $sql_310;
	$a_310=mysql_query($sql_310);
	$r_310=mysql_fetch_assoc($a_310);
	echo '<fieldset><legend> Actualizacion de Fianzas: '.trim($r_310['casa']). ' / '.trim($r_310['ape_tit']). ', '.trim($r_310['nom_tit']).' / '.$r_310['cedula'].' / '.$r_310['cod_prof'].' / '.$elnumero.'</legend>';
	$elcodigo=$r_310['cod_prof'];
//	$sql_320="select * from sgcaf320, sgcaf200 where (nropre_fia = '$elnumero') and (codsoc_fia='$elcodigo') and (cedula='$cedula')";
	$sql_320="select * from ".$_SESSION['institucion']."sgcaf320, titulares where (nropre_fia = '$elnumero') and (codfia_fia=cod_prof)";
//	echo $sql_320;
	$a_320=mysql_query($sql_320);
	echo "<table class='basica 100 hover' width='750'><tr>";
	echo '<th colspan="1"></th><th width="80">Código</th><th width="80">Cédula</th><th width="200">Nombre</th><th width="280">Monto Fianza</th></tr>';
	$registros=$total=0;
	while($r_320=mysql_fetch_assoc($a_320)) {
////////////////////////////
		$registros++;
//			echo '<td class="centro azul"><input type="checkbox" id="eliminar'.$registros.'" name="eliminar'.$registros.'" value='.$r_320["registro"] .' onClick="eliminafianza()"> </td>' ;
		echo '<td class="centro azul">';
		echo "<a href='solpre.php?accion=Eliminar&cedula=".$lacedula."&nropre=".$elnumero."&registro=".$r_320['registro']."& '  onClick='return conf_elim_fiadores()'>";
		echo "<img src='imagenes/16-em-cross.png' width='16' height='16' border='0' title='Eliminar' alt='Eliminar'/>";
		echo "</a></td>";
		echo '<td>'.$r_320['codfia_fia'].'</td>';
		echo '<td>'.$r_320['cedula'].'</td>';
		echo '<td>'.trim($r_320['ape_tit']). ', '.trim($r_320['nom_tit']).'</td>';
		echo '<td align="right">'.number_format($r_320['monto_fia'],2,'.',',').'</td></tr>';
		$total+=$r_320['monto_fia'];
	}
	$unacedula='';
	echo '<tr><td align="right" colspan="4">Total Fianzas</td><td align="right">'.number_format($total,2,'.',',').'</td></tr>';
	echo '</table>';
	//-----------------
	echo "<table class='basica 100' width='500'>";
	echo '<tr><th width="400">Cedula o Nombre del Fiador</th><th width="100">Monto de la Fianza</th></tr>';
	echo '<tr><td width="400"> ';
	echo "<input type='text' size='20' tabindex='1' name='_unacedula' id='inputString5' onKeyUp='lookup5(this.value);' onBlur='fill5();' value ='$_unacedula' autocomplete='off'/>";
	echo '<div class="suggestionsBox5" id="suggestions5" style="display: none;">';
	echo '<img src="upArrow.png" style="position: relative; top: -12px; left: 70px; "  alt="upArrow" />';
	echo '<div class="suggestionList5" id="autoSuggestionsList5">';
	echo '</div>';
	echo '</div>';
	echo '</td><td width="100">';
	echo "<input type = 'text' size='12' maxlength='12' name='monto_fianza' tabindex='2' value ='0.00'>";
	echo '</td></tr><tr><td colspan="2">';
//	echo $registros. ' '.$r_310['fiadores'];
	if ($registros < $r_310['n_fia_pres'])
		echo "<input type='submit' name='boton' value=\"Guardar\" tabindex='3'>";
	else echo 'Tiene la cantidad maxima de fiadores';
	//-----------------
	echo '</td></tr></table></form>';
	echo '</fieldset>';
	echo '</div>';	
	if ($r_310['genera_pl'] == 1) {
//		echo 'Preparando para la impresion<br>';
		echo "<a target=\"_blank\" href=\"solprepdf.php?cedula=$lacedula\" onClick=\"info.html\', \'\',\'width=250, height=190\')\">Imprimir Planilla de Préstamo </a>"; 
	}
}

//---------------------
//---------------------
*/
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
/*
	$s_100="select ut from ".$_SESSION['institucion']."sgcaf100 limit 1";
	try
	{
		$a_100=$db_con->prepare($s_100);
		$a_100->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		// echo 'Fallo la conexion';
	}
	$r_100=$a_100->fetch(PDO::FETCH_ASSOC);
	$montounidadtributaria=$r_100['ut'];
	$maximodisponible=$_SESSION['disponibilidadprestamo'];
	$texto='';
	if ($_SESSION['disponibilidadprestamo'] <= 0)
		if ($r_360['tope_ut'] == 0)
			{ 
			$maximodisponible=$r_360['tope_monto']-($r_310['monto_solicitado']-$r_310['montopagado']);
			if ($maximodisponible <= 0) $texto='1';
			}
		else {
			$maximodisponible=($r_360['factor_ut']*$montounidadtributaria); // +$_SESSION['disponibilidadprestamo'];
			if ($r_360['e_items'] == 1)
			{
				$s_items="select sum(monto_solicitado-montopagado) as saldo from ".$_SESSION['institucion']."sgcaf310 where cedula='$micedula' and concepto='$tipo' and status='A' and (! renovado) group by cedula";
				try
				{
					$a_items=$db_con->prepare($s_items);
					$a_items->execute();
				}
				catch(PDOException $e){
					echo $e->getMessage();
					// echo 'Fallo la conexion';
				}
				$r_items=$a_items->fetch(PDO::FETCH_ASSOC);
				$maximodisponible-=$r_items['saldo'];
				if ($maximodisponible < 0)
					$maximodisponible=0;
				else $texto='1';
			}
		}
	if ($r_360['montofijo'] != 0)
		$_SESSION['disponibilidadprestamo']=$r_360['montofijo']; // $disponible; 
	if ($texto =='')
*/
		echo '<input class="form-control" align="right" name="monto_solicitado" type="text" id="monto_solicitado" size="12" maxlength="12" value="100';
/*
	echo ($texto==''?number_format($maximodisponible,2,'.',''):'Sin Disponibilidad'); 
	if ($texto =='')
*/
		echo '"/>';
//	echo "<input type = 'hidden' value ='".$maximodisponible."' name='elmaximo' id='elmaximo'>";
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
		echo '<input class="form-control" align="right" name="monto_especial" type="text" id="monto_especial" size="12" maxlength="12" value="20" onBlur="muestre()"/>';
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
	echo '<td><label for ="cuotae">Cuota Especial </td><td align="right">'; // .number_format($r_310['cuota'],2,',','.').'</td></tr>';
	echo '<input class="form-control"  align="right" name="cuotae" type="text" id="cuotae" size="12" maxlength="12" readonly="readonly" value ="0.00"></td></tr>';
	echo '<tr><td><label>Gastos Administrativos:</label> </td><td align="right">';
	echo '<input class="form-control" align="right" name="gastosadministrativos" type="text" id="gastosadministrativos" size="12" maxlength="12" readonly="readonly" value ="0.00"></td>';
/*
	echo '</td><td>Inicial</td><td align="right">';
	echo '<input class="form-control" align="right" name="inicial" type="text" id="inicial" size="12" maxlength="12" value ="0.00"';
	echo '>';
*/
//	echo '</td></tr><tr>';
	/*

	echo '<td>Acta / Fecha </td><td>'.$nroacta.' del '.convertir_fechadmy($fechaacta).'</td>'; */
	//echo '<td><label>Neto a Depositar</label><br><em>No incluye otros prestamos</em></td><td align="right">';
	echo '<td><label>Estimado a Recibir (con intereses)</label></td><td align="right">';
	echo '<input class="form-control" align="right" name="montoneto" type="text" id="montoneto" size="12" maxlength="12" readonly="readonly" value ="0.00"';
	echo '</td></tr><tr>';
//	echo '<tr><td align="center" colspan="4">';

//	echo '</td></tr>';
//	echo '<td><div id="contenedor">Valor</div></td>';

//	<input type="button" name="calculo" value="Calcular a" onClick="Cargarcontenido('mostrarpr.php','c=3', 'form1', 'contenido2')">	
//	if ($texto =='') {
	echo '<td align="center" colspan="2"> '; 
	//echo '<input class="btn btn-info" type="button" name="calculo" value="Calcular Cuota" onClick="ajax_call()">	';
	echo '<input class="btn btn-info" type="button" id="calculo" name="calculo" value="Calcular Cuota">	';
	echo '</td><td align="center" colspan="2"> ';
	echo "<input class='btn btn-success' type = 'submit' value = 'Crear Servicio'>"; 

	echo '</td>';
}
	echo '</table>';
	echo '</fieldset>';
//	echo 'numero a renovar '.$_SESSION['numeroarenovar'];
//	echo '</div>';
/*
	$lafoto='fotos/'.trim($cedula).'.jpg';
	echo "<br><br><img src='".$lafoto."' width='156' height='156' border='0' />";
	echo '</div>';
*/
//	echo '<div id="contenido2"></div>';
//}


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
 		<td><?php echo '<strong>'.$fila['cedula'].'</strong>';?></td>
		<td colspan="2" ><?php echo '<strong>'.$fila['ape_tit'].' '.$fila['nom_tit'] .'</strong>'?></td>
		<td><strong><?php echo convertir_fechadmy($fila['ing_ipsta']) ?></strong></td>
		<td><strong><?php echo convertir_fechadmy($fila['ing_ucla']) ?> </strong></td>
		<td><strong><?php echo cedad(convertir_fechadmy($fila['ing_ucla'])) ?> </strong></td>
		<td><strong><?php echo $fila['status'] ?></strong></td>
		<?php 
/*
			$ahorros=ahorros($cedula, $db_con);
			$afectan=afectan($cedula, $db_con);
			$noafectan=noafectan($cedula, $db_con);
			$sql='select * from titulares where cedula=:cedula';
			try
			{
				$result=$db_con->prepare($sql);
				$result->execute(array(
					":cedula"=>$cedula,
					));
			}
			catch(PDOException $e){
				echo $e->getMessage();
				// echo 'Fallo la conexion';
			}
			$fila = $result->fetch(PDO::FETCH_ASSOC);
			$fianzas=fianzas($fila['cedula'], $db_con);
//			$disponible=($totalahorros-$reserva)-($afectan+$fianzas);
			$disponible=disponibilidad($ahorros,$afectan,$noafectan,$fianzas, $db_con); 
			echo '<strong>';
	  		if ($disponible<=0)
				{
					$imagen='24-em-cross.png';
					$cuento_mostrar='Disponibilidad Negativa';
					$cuento_interno='disp_neg';
				}
			else {
					$imagen='24-em-check.png';
					$cuento_mostrar='Disponibilidad Positiva';
					$cuento_interno='disp_pos';
			}
			echo '<img src="imagenes/'.$imagen.'" width="22" height="19" alt="'.$cuento_mostrar.'" longdesc="'.$cuento_interno.'" />';
			echo number_format($disponible,$deci,$sep_decimal,$sep_miles); 
			echo '<img src="imagenes/'.$imagen.'" width="22" height="19" alt="'.$cuento_mostrar.'" longdesc="'.$cuento_interno.'" />';
//			$_SESSION['disponibilidadprestamo']=1234; // $disponible; 
			$_SESSION['disponibilidadprestamo']=$disponible; 
			$hoy=date("Y-m-d", time());
			$pasados=(dias_pasados($fila['ing_ipsta'],$hoy)/30) ;
		   $_SESSION['tiempoactivo']=intval($pasados);
*/
			$_SESSION['elstatus']=strtoupper($fila['status']);
		 ?></strong>
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
	echo '<td>Cuota Modificada </td><td align="right">'.number_format($r_310['cuota'],$deci,$sep_decimal,$sep_miles).'</td></tr>';
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
