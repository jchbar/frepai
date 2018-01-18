<?php

//Copyright (C) 2000-2006  Antonio Grandío Botella http://www.antoniograndio.com
//Copyright (C) 2000-2006  Inmaculada Echarri San Adrián http://www.inmaecharri.com

//This file is part of Catwin.

//CatWin is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License, or
//(at your option) any later version.

//CatWin is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details:
//http://www.gnu.org/copyleft/gpl.html

//You should have received a copy of the GNU General Public License
//along with Catwin Net; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

/* *** COMPROBACIÓN AÑO ****************************** */

function anocont($fecha) {

	$result = mysql_query("SELECT anocont FROM empresa");
	$fila = mysql_fetch_array($result);

	$b = explode("/",$fecha);

	if ($fila[0] != "20".$b[2]) {return 0;}

	return "20".$b[2]."-".$b[1]."-".$b[0];

}

/* *** CABECERA LISTADO ASIENTOS ****************************** */

function cabasi($edborr) {

echo "<tr><thead>";

if ($edborr) {echo "<th width='200' colspan=2></th>";}

echo '<th width="100">Cuenta</th><th width="200">Descripci&oacute;n</th><th width="200">Concepto</th><th width="50">Referencia</th><th width="100">Debe</th><th width="100">Haber</th></tr></thead>';
// <th width="100">Descuadre</th></tr>';

}

/* *** LISTAR ASIENTO ****************************** */

function asiento($asiento, $edborr, $por, $deci, $bojust, $db_con) 
{
	$sql="SELECT * FROM enc_contable WHERE enc_clave = :asiento";
	//echo $sql;
	$result_enc = $db_con->prepare($sql);
	$result_enc->execute(array(":asiento" => $asiento));
	if ($result_enc) 
		$fichero = $result_enc->fetch(PDO::FETCH_ASSOC);

	$cols = 6;

	if ($edborr) {$cols = $cols+2;}

	$asi = $fichero; 
	$a=explode("-",$asi["enc_fecha"]);
	$sql="SELECT nro_registro, com_cuenta, com_descri, com_monto1, com_monto2, com_refere FROM detalle_contable WHERE com_nrocom = :asiento ORDER BY com_debcre, com_cuenta, com_refere, com_monto1, com_monto2, com_descri, nro_registro";
	$result = $db_con->prepare($sql);
	$result->execute(array(":asiento" => $asiento));
	echo "<tr><td colspan='$cols'><label class='form-control'>Asiento: <a class='btn btn-success' href='editasi2.php?asiento=$asiento'>".$asiento." <span class='badge'>".$result->rowCount()." registros </span></a> Fecha: ";
	echo $asi['enc_fecha'];// $a[2]."/".$a[1]."/".$a[0]; // substr($a[0],2,2);
	echo "</b>";
	// if ($fichero[0]) 
	if (strlen($asi["enc_soporte"]) > 0)
	{echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='imagen.php?asiento=$asiento' target='_blank'>Ver Soporte</a>&nbsp;&nbsp;&nbsp;<a href='editasi2.php?asiento=$asiento&bojust=$asiento' onclick='return borrar_justificante()'>Borrar Soporte</a>";}

	if (trim($fichero[1])) {
		echo "&nbsp;&nbsp;&nbsp;<a class='btn btn-warning' onclick=\"amplred('div$asiento')\">Explicaci&oacute;n (ver/ocultar)</a>";
		echo "<div id='div$asiento' style='display:none'>".$fichero[1]."</div>";
	}
	echo "<a class='btn btn-info' href='impcom.php?asiento=".$asiento."'>  Imprimir</a>";
	echo "</td></tr>";


	while ($fila = $result->fetch(PDO::FETCH_ASSOC)) 
	{
		echo "<tr>";
		if ($edborr) {
			echo "<td width='20'><a class='btn btn-default' href='editasi2.php?row_id=".$fila['nro_registro']."&asiento=$asiento&accion=editapu' target='_self'> <img src='imagenes/16-em-pencil.png' width='16' height='16' border='0' title='Editar' alt='Editar' /></a></td>";
			echo "<td width='20'><a class='btn btn-danger' href='editasi2.php?row_id=".$fila['nro_registro']."&asiento=$asiento&accion=boapu' onclick='return borrar_reg_820()'><img src='imagenes/16-em-cross.png' width='16' height='16' border='0' title='Eliminar'  alt='Eliminar' /></a></td>";
		}
		echo "<td ><a  target=\"_blank\" class='btn btn-default' href=\"extractoctas3.php?cuenta=".$fila["com_cuenta"]."&datos='no'\">".$fila["com_cuenta"]."</a></td>";
		
		$sqlcuenta="SELECT cue_nombre FROM cuentas where cue_codigo = :lacuenta";
		$rs=$db_con->prepare($sqlcuenta);
		$res=$rs->execute(array(":lacuenta"=>$fila["com_cuenta"]));
		if (!$res)
			die ("<p />Estimado usuario $usuario contacte al administrador Código 810-1");
		$filacuenta = $rs->fetch(PDO::FETCH_ASSOC);
		echo "<td>".$filacuenta["cue_nombre"]."</td>";
		
		echo "<td>".$fila["com_descri"]."</td>";
		echo "<td>".$fila["com_refere"]."</td><td width='100' align='right'>";
		
		if ($fila["com_monto1"] == 0)
		{
			echo "&nbsp;";
		} else {
			echo number_format($fila["com_monto1"],2,'.',',');
		}

		echo "</td><td align='right'>";
		
		
		if ($fila["com_monto2"] == 0)
		{
			echo "&nbsp;";
		} else {
			echo number_format($fila["com_monto2"],2,'.',',');
		}
		echo "</td></tr>";

	}

	$elmonto=$asi['enc_debe']-$asi['enc_haber'];
	if ($asi['enc_debe']-$asi['enc_haber'] != 0) {
		echo "<tr><td align='right' colspan=".($cols-2);
		echo "<span align='right' class='badge btn-danger'> Diferencia de ".number_format(($asi['enc_debe']-$asi['enc_haber']),2,',','.')."</span>";
		//echo "Diferencia de <span class='badge btn-danger'>".number_format(($asi['enc_debe']-$asi['enc_haber']),2,',','.')." </span>";
	}
	else 
		echo "<tr><td align='right' colspan=".($cols-2).">";
	echo "  SubTotales: </td><td align='right'>".number_format($asi['enc_debe'],2,',','.')."</td><td align='right'>".number_format($asi['enc_haber'],2,',','.')."</td>";

	echo "</tr><tr><td colspan='$cols'>&nbsp;</td></tr>
	<p>";
}
  
  
  /* *** ACTUALIZAR UNO O TODOS LOS ASIENTOS ****************************** */
  
  function totalapu($asiento) {
//  $comando="call totalapu('".$asiento."')"; // llamado de procedimiento


  if ($asiento) {$where = "WHERE com_nrocom = '$asiento'";}
  
  $rs = mysql_query("SELECT com_nrocom from sgcaf820 $where");
  if (mysql_num_rows($rs) < 30)
	$minimo=30;
else $minimo=mysql_num_rows($rs);
	set_time_limit($minimo);

  
  while ($fila = mysql_fetch_array($rs)) :
  
  $a = $fila[0];
  
  $rs1=mysql_query("SELECT SUM(com_monto1) AS tot_debe, SUM(com_monto2) AS tot_haber, COUNT(com_nrocom) as tot_items FROM sgcaf820 WHERE com_nrocom = '$a'");
  $fila1 = mysql_fetch_array($rs1);
  
  mysql_query("UPDATE sgcaf830 SET enc_debe = '$fila1[0]', enc_haber = '$fila1[1]', enc_item = '$fila[2]' WHERE enc_clave = '$a'");
  
  endwhile;
  
  return ($fila1[0] - $fila1[1]);

//	$resultado=mysql_query("select totalapu('".$asiento."')"); // llamado de procedimiento  
//	echo '<h1>el resultado $resultado';
	return $resultado;
  
  }
  
  /* *** TIPO DE ASIENTOS ****************************** */
  
  function tipoasi() {
  
  $fp = fopen("tipoasi.txt","r");
  while ($linea= fgets($fp,1024)){$array[] = $linea;}
  fclose($fp);
  return $array;
  
  }
  
  /* *** MONEDA ****************************** */
  
  function moneda() {
  
  $fp = fopen("moneda.txt","r");
  while ($linea= fgets($fp,1024)){$array[] = $linea;}
  fclose($fp);
  return $array;
  
  }
  
  /* *** ACTUALIZAR UNA O TODAS LAS SUBCUENTAS ****************************** */
  
  function totsubcuentas ($subcuenta) {
  
  if ($subcuenta) {$where = "WHERE cuenta = '$subcuenta'";}
  
  $rs = mysql_query("SELECT cuenta from subcuent $where");
  
  while ($fila = mysql_fetch_array($rs)) :
  
  $rs1 = mysql_query("SELECT SUM(debe) AS tot_debe, SUM(haber) AS tot_haber FROM apuntes WHERE apuntes.cuenta = ".$fila['cuenta']);
  $sum = mysql_fetch_array($rs1);
  
  $tot = $sum['tot_debe'] - $sum['tot_haber'];
  
  if ($tot > 0)
  {
  $result = mysql_query("UPDATE subcuent SET saldod = '$tot', saldoa = 0, sdebe = '".$sum['tot_debe']."', shaber = '".$sum['tot_haber']."' WHERE cuenta =".$fila['cuenta']) or die ("El usuario $usuario no tiene permisos para hacer Balances de Comprobación."); 
  } else {
  
  $result = mysql_query("UPDATE subcuent SET saldoa = 0 - '$tot', saldod = 0, sdebe = '".$sum['tot_debe']."', shaber = '".$sum['tot_haber']."' WHERE cuenta =".$fila['cuenta']) or die ("El usuario $usuario no tiene permisos para hacer Balances de Comprobación."); 
  
  }
  
  endwhile;
  
  if ($subcuenta) {totcuenta (substr($subcuenta,0,3));}
  
  }
  
  /* *** ACTUALIZAR UNA O TODAS LAS CUENTAS ****************************** */
  
  function totcuentas ($cuenta) {
  
  if ($cuenta) {$where = "WHERE cuenta = '$cuenta'";}
  
  $rs = mysql_query("SELECT cuenta, subgrupo from cuentas $where");
  
  while ($fila = mysql_fetch_array($rs)) :
  
  $rs1 = mysql_query("SELECT SUM(saldod) AS tot_debe, SUM(saldoa) AS tot_haber FROM subcuent WHERE cuenta LIKE '".$fila['cuenta']."%'");
  $sum = mysql_fetch_array($rs1);
  
  $tot = $sum['tot_debe'] - $sum['tot_haber'];
  
  if ($tot > 0)
  {
  $result = mysql_query("UPDATE cuentas SET sdo3cd = '$tot', sdo3ca = 0, sdebe = '".$sum['tot_debe']."', shaber = '".$sum['tot_haber']."' WHERE cuenta =".$fila['cuenta']) or die ("El usuario $usuario no tiene permisos para hacer Balances de Comprobación."); 
  } else {
  
  $result = mysql_query("UPDATE cuentas SET sdo3ca = 0 - '$tot', sdo3cd = 0, sdebe = '".$sum['tot_debe']."', shaber = '".$sum['tot_haber']."'  WHERE cuenta =".$fila['cuenta']) or die ("El usuario $usuario no tiene permisos para hacer Balances de Comprobación."); 
  
  }
  
  endwhile;
  
  if ($cuenta) {totsubgrupo (substr($fila['subgrupo']));}
  
  }
  
  /* *** ACTUALIZAR UNO O TODOS LOS SUBGRUPOS ****************************** */
  
  function totsubgrupos ($subgrupo) {
  
  if ($subgrupo) {$where = "WHERE subgrupo = '$subgrupo'";}
  
  $rs = mysql_query("SELECT subgrupo from subgrupo $where");
  
  while ($fila = mysql_fetch_array($rs)) :
  
  $rs1 = mysql_query("SELECT SUM(sdo3ca) AS tot_debe, SUM(sdo3cd) AS tot_haber FROM cuentas WHERE cuenta LIKE '".$fila['subgrupo']."%'");
  $sum = mysql_fetch_array($rs1);
  
  $tot = $sum['tot_debe'] - $sum['tot_haber'];
  
  if ($tot > 0)
  {
  $result = mysql_query("UPDATE subgrupo SET sdod2c = '$tot', sdoh2c = 0, sdebe = '".$sum['tot_debe']."', shaber = '".$sum['tot_haber']."' WHERE subgrupo =".$fila['subgrupo']) or die ("El usuario $usuario no tiene permisos para hacer Balances de Comprobación."); 
  } else {
  
  $result = mysql_query("UPDATE subgrupo SET sdoh2c = 0 - '$tot', sdod2c = 0, sdebe = '".$sum['tot_debe']."', shaber = '".$sum['tot_haber']."'  WHERE subgrupo =".$fila['subgrupo']) or die ("El usuario $usuario no tiene permisos para hacer Balances de Comprobación."); 
  
  }
  
  endwhile;
  
  } 
  
/*	
	SET @debe  = (SELECT SUM(com_monto1) FROM sgcaf820 WHERE com_nrocom=pcom_nrocom);
	SET @haber = (SELECT SUM(com_monto2) FROM sgcaf820 WHERE com_nrocom=pcom_nrocom);
	SET @items = (SELECT COUNT(com_nrocom) FROM sgcaf820 WHERE com_nrocom=pcom_nrocom);
	UPDATE sgcaf830 SET enc_debe=@debe, enc_haber=@haber, enc_item=@items WHERE enc_clave = pcom_nrocom;
*/

function ifecha() {

$ndia = date('N');
$nmes = date('n');

$semana['1'] = "Lunes";
$semana['2'] = "Martes";
$semana['3'] = "Miércoles";
$semana['4'] = "Jueves";
$semana['5'] = "Viernes";
$semana['6'] = "Sábado";
$semana['7'] = "Domingo";

$mes[1] = "Enero";
$mes[2] = "Febrero";
$mes[3] = "Marzo";
$mes[4] = "Abril";
$mes[5] = "Mayo";
$mes[6] = "Junio";
$mes[7] = "Julio";
$mes[8] = "Agosto";
$mes[9] = "Septiembre";
$mes[10] = "Octubre";
$mes[11] = "Noviembre";
$mes[12] = "Diciembre";

return $semana[$ndia]." ".date("d")." de ".$mes[$nmes]." de ".date("Y");

}

function convertir_fecha($mifecha)
{
//	echo $mifecha;
	$a=explode("/",$mifecha); 
	$elano=substr($a[0],0,2);
// 	if ($elano="20") $b=$a[2]."-".$a[1]."-".$a[0];
//	if ($elano="20") $b=$a[2].'-'.(($a[1]<10)?'0'.$a[1]:$a[1])."-".(($a[0]<10)?'0'.$a[0]:$a[0]);
//	else $b="20".$a[2]."-".(($a[1]<10)?'0'.$a[1]:$a[1])."-".(($a[0]<10)?'0'.$a[0]:$a[0]);
//	die('fecha recibida'.$mifecha);
	if ($elano="20") $b=$a[2].'-'.$a[1]."-".trim($a[0]);
	else $b="20".$a[2]."-".$a[1]."-".trim($a[0]);
	if ($mifecha=='//') $b='0000-00-00';
return $b;
}

function convertir_fechadmy($mifecha)
{
//	$mifecha=strtotime($mifecha);
//	echo $mifecha;
	$a=explode("-",$mifecha); 
	$elano=substr($a[0],0,2);
	if ($elano="20") $b=$a[2]."/".$a[1]."/".trim($a[0]);
	else $b=$a[2]."/".$a[1]."/"."20".trim($a[0]);
//	if ($elano="20") $b=(($a[2]<10)?'0'.$a[2]:$a[2])."/".(($a[1]<10)?'0'.$a[1]:$a[1])."/".$a[0];
//	else $b=$b=(($a[2]<10)?'0'.$a[2]:$a[2])."/".(($a[1]<10)?'0'.$a[1]:$a[1])."/"."20".$a[0];
	if ($mifecha=='--') $b='00/00/0000';
return $b;
}

function solicitar_fechas()
{
	$fechai="01/01/".date("Y");
	$fechaf=date("d")."/".date('n')."/".date("Y"); 
	echo 'Fecha Inicio: ';
	escribe_formulario_fecha_vacio("fechai","form1",$fechai,2,''); 
	// <input type='text' name='fechai' size='10' maxlength='10' value="<?php echo $fechai >">
	// <input type="button" name="selfechai" value="..."  onclick='displayDatePicker("fechai","","dmy")' />
	echo 'Fecha Final: ';
	// <input type='text' name='fechaf' size='10' maxlength='10' value=" <?php echo $fechaf >">
	// <input type="button" name="selfechaf" value="..."  onclick='displayDatePicker("fechaf","","dmy")' />
	escribe_formulario_fecha_vacio("fechaf","form1",$fechaf,3,''); 
}

function calcular_saldo($registro,$fechai)
{
	$a=explode("/",$fechai); 
	$elmesi=$a[1];
	$elsaldo=$registro['cue_saldo'];
	if ((! $fechai)) //  or ($elmes = '1'))
		return ($elsaldo);
/*	for ($i=1; $i<$elmesi; $i++)
	{
		if ($i<10) $mes='0'.$i; else $mes=$i;
		$debe='$registro["cue_deb'.$mes.'"]';
		$debe='$registro["cue_cre'.$mes.'"]';
		echo $debe; // $registro."['".$debe."']";
		$elsaldo+=$debe; // $registro["'".$debe."'"];
		$elsaldo-=$haber;
		echo $elsaldo."<br>";
	} 
*/
	$meses=$debe=$haber=0;
	foreach ($registro as $indice => $valor) {
//		echo "$registro[$indice]";
//		echo "indice =".$indice;
//		echo "valor =".$valor;
	if (substr($indice,0,7)=='cue_deb') $meses++;
	if ($meses < $elmesi ) {
// 		echo $meses.'-'.$elmesi.'/'."<br>";
		if (substr($indice,0,7)=='cue_deb') $debe+=$valor;  // echo $valor; }
			elseif (substr($indice,0,7)=='cue_cre') $haber+=$valor; // echo $haber; }
			}
	}
	$elsaldo=$elsaldo+($debe-$haber);
	
return $elsaldo;
}


function calcule_810($registro, $niveles, $elmes, $db_con)
{
	$elcodigo=$registro['com_cuenta'];
	if ($elmes < 10) $elmes='0'.$elmes;
	$fila = $niveles ;
	{
		$elnivel=$fila['con_nivel'];
		$codigo=substr($elcodigo,0,$elnivel);
		$debito='cue_deb'.$elmes;
		$credito='cue_cre'.$elmes;
		$eldebe=$registro['debe'];
		$elhaber=$registro['haber'];
		$sql="select cue_codigo from cuentas where cue_codigo='$codigo'";
//		echo $sql;
		$result = $db_con->prepare($sql);
		$res=$result->execute();
		if (!$res) die('Error en la F810-4 '.$sql); 
		if (($result->rowCount() < 1) and (strlen(trim($codigo)) > 16)) // and ($codigo != '')  and ($elnivel = '7') 
		{ 	// no existe la cuenta y la creo
			// busco el socio
			$socio=explode('-',$codigo);
			$socio='0'.$socio[6];
			$sql="select ape_prof, nombr_prof from ".$_SESSION['institucion']."sgcaf200 where cod_prof='$socio'";
//			echo $sql;
			$result = $db_con->prepare($sql);
			$res=$result->execute();
			if (!$res) die('Error en la F200-1 '.$sql); 
			$rsocio=$result->fetch(PDO::FETCH_ASSOC);
			$nombre=trim($rsocio['ape_prof']). ' '.$rsocio['nombr_prof'];
			$sql="insert into cuentas (cue_codigo, cue_nombre, cue_nivel, cue_saldo) values ('$codigo', '$nombre', '7', 0)";
//			echo $sql;
			$result = $db_con->prepare($sql); 
			$res=$result->execute();
			if (!$res) die('Error en la F810-5 '.$sql); 
		}
		$sql="update cuentas set $debito=$debito+'$eldebe', $credito=$credito+'$elhaber' where cue_codigo='$codigo'";
		$result = $db_con->prepare($sql);
		$res=$result->execute();
		if (!$res) ('Error en la F810-3 '.$sql); 
		$sql="select cue_codigo from cuentas where cue_codigo='$codigo'";
		$result = $db_con->prepare($sql);
		$res=$result->execute();
		if (!$res) die('Error en la F810-4 '.$sql); 
		// if ($result->rowCount() < 1) echo $sql.'<br>';
	}
//	echo $codigo;
}


function nombremes($numeromes) {

$nmes = $numeromes;
$mes[1] = "Enero";
$mes[2] = "Febrero";
$mes[3] = "Marzo";
$mes[4] = "Abril";
$mes[5] = "Mayo";
$mes[6] = "Junio";
$mes[7] = "Julio";
$mes[8] = "Agosto";
$mes[9] = "Septiembre";
$mes[10] = "Octubre";
$mes[11] = "Noviembre";
$mes[12] = "Diciembre";

return $mes[$nmes];
}

function chequear_procesar($mesprocesar, $losniveles, $elmes, $db_con, $ValorTotal, $cuantos)
{
if ($mesprocesar== 1) 
{
	$delmes=$elmes;
	echo "Procesando ".nombremes($elmes)." <br>";
	if ($delmes < 10) $delmes='0'.$delmes;
	$debito='cue_deb'.$delmes;	
	$credito='cue_cre'.$delmes;
	$sql="update cuentas set $debito=:cero, $credito=:cero";
	try
	{
		$result = $db_con->prepare($sql);
		$res = $result->execute([":cero"=>0]);
		if (!$res) 
		{
			mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Error en la F810-4 '.$sql.'</h2>'.$e->getMessage()]);
			exit;
		}
		for ($i = count($losniveles) - 1; $i >= 0; $i--) {
/*
	    	if (!mysql_data_seek($losniveles, $i)) {
		        echo "Cannot seek to row $i: " . mysql_error() . "\n";
		        continue;
	    	}
*/
/*
		    if (!($niveles = mysql_fetch_assoc($losniveles))) {
		        continue;
		    }
*/
	//		echo "nivel tomado ".$niveles['con_nivel'];
			procese($elmes, $losniveles[$i], $db_con);
			set_time_limit(60);
			$porcentaje = $cuantos * 100 / $ValorTotal; //saco mi valor en porcentaje
			// echo $cuantos.' '.$porcentaje.'/';
			echo "<script>callprogress(".round($porcentaje).")</script>"; //llamo a la función JS(JavaScript) para actualizar el progreso
	//		echo $porcentaje.'<br>';
			flush(); //con esta funcion hago que se muestre el resultado de inmediato y no espere a terminar todo el bucle con los 25 registros para recien mostrar el resultado
			ob_flush();

			return 1;
		}
		echo "Procesado ".nombremes($elmes)." <br>";
	}
	catch (PDOException $e) 
	{
		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$sql.$e->getMessage()]);
		die('');
	}
}
}

function procese($elmes, $niveles, $db_con)
{
//	$sql="select com_cuenta, com_debcre, sum(com_monto1) as debe, sum(com_monto2) as haber from sgcaf820 where month(com_fecha)=$elmes group by com_cuenta order by com_cuenta";
	$sql="select fech_ejerc from institucion limit 1";
	try
	{
		$result=$db_con->prepare($sql);
		$result->execute();
		$registro=$result->fetch(PDO::FETCH_ASSOC);
		$ano=$registro['fech_ejerc'];
		$ano=explode('-',$ano);
		$ano=$ano[0];
		$numero=$niveles['con_nivel'];
		// chequeo de fallas
		if ($elmes < 10)
			$mimes='0'.$elmes;
		else $mimes=$elmes;
		$sql="select com_nrocom, sum(com_monto1) as debe, sum(com_monto2) as haber,sum(com_monto1)-sum(com_monto2) as diferencia from detalle_contable where substr(com_fecha,1,7)='$ano-$mimes' and length(trim(com_cuenta))=length('5-07-01-06-01-01-0001') group by com_nrocom order by sum(com_monto1)-sum(com_monto2) desc";
	//	echo $sql;
//		echo '<br>';
		$hay=false;
		$result = $db_con->prepare($sql);
		$res=$result->execute();
		if (!$res) die('Error en la F820-4'.$sql.' '); 
		while ($fila = $result->fetch(PDO::FETCH_ASSOC)) {
	//		echo substr(trim($fila['diferencia']),1,4).'<br>';
			if (substr(trim($fila['diferencia']),0,4)!= '0.00') {
				echo "<strong><a target=\"_blank\" href='editasi2.php?asiento=".$fila['com_nrocom']."'>".$fila['com_nrocom']."</a></strong> <br>";
				echo $fila['debe'].' '.$fila['haber'].' '.$fila['diferencia'].' '.'<br>';
				$hay=true;
			}
		}
		if ($hay == true) {
			mensaje(['tipo'=>'danger','titulo'=>'Aviso','texto'=>'<h2>Revisar los comprobantes anteriores que tienen inconveniente</h2>']);
			die('');
		}

		$sql="select left(com_cuenta,$numero) as com_cuenta, com_debcre, sum(com_monto1) as debe, sum(com_monto2) as haber from detalle_contable where month(com_fecha)=$elmes and year(com_fecha)=$ano group by com_cuenta, com_debcre order by com_cuenta";
	// 	 echo $sql;
	 	// die('');
		$result = $db_con->prepare($sql);
		$result->execute();
		// or die('Error en la F820-3 '.$sql.' '.mysql_error()); 
		$cantidad=$result->rowCount();
		if ($cantidad == 0) 
		{
			mensaje(['tipo'=>'danger','titulo'=>'Aviso','texto'=>'<h2>No existen movimientos en el mes $elmes revisar</h2>']);
			exit;
		}
		set_time_limit($cantidad);
		$ValorTotal=$cantidad;
		$cuantos=0;
		while ($fila = $result->fetch(PDO::FETCH_ASSOC)) {
			calcule_810($fila, $niveles, $elmes, $db_con);
		//echo $sql;
			$cuantos++;
			$porcentaje = $cuantos * 100 / $ValorTotal; //saco mi valor en porcentaje
			// echo $cuantos.' '.$porcentaje.'/';
			echo "<script>callprogress2(".round($porcentaje).")</script>"; //llamo a la función JS(JavaScript) para actualizar el progreso
	//		echo $porcentaje.'<br>';

			flush(); //con esta funcion hago que se muestre el resultado de inmediato y no espere a terminar todo el bucle con los 25 registros para recien mostrar el resultado
			ob_flush();
		}
		echo "</div>";
	}
	catch (PDOException $e) 
	{
		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$sql.$e->getMessage()]);
		die('');
	}
/*
	echo "<script>";
//	echo "document.getElementById('progreso').style.displaye='none';";

	echo "</script>";
*/
}

function cuenta_810($codigo, $nombre, $db_con)
{
	$sql810="select cue_codigo from cuentas where cue_codigo=:codigo";
	$a810->$db_con->prepare($sql810);
	$a810->execute(array(":codigo"=>$codigo));
	$c810=$a810->rowCount();
	if ($c810 == 0)	{ // no existe la agrego
		$sql810="insert into sgcaf810 (cue_codigo, cue_nombre, cue_nivel, cue_saldo) values 
			(:codigo, :nombre, :siete, :cero)";
		$r810=$db_con->prepare($sql810);
		$siete=7;
		$cero=0;
		$r810->execute(array(
			":codigo"=>$codigo,
			":nombre"=>$nombre,
			":siete"=>$siete,
			":cero"=>$cero,
			));
	}
}


function agregar_f820 ($pcom_nrocom, $pcom_fecha, $pcom_debcre, $pcom_cuenta, $pcom_descri, $elmonto, $pcom_monto2, $pcom_monto, $pcom_ip, $pcom_nroite, $pcom_refere, $pcom_tipmov, $agregar, $registro, $db_con)
{
	$pcom_monto1 = $pcom_monto2 = 0;
	if (($pcom_debcre =='+') or ($pcom_debcre == '1') or ($pcom_debcre == on)) 
		{ $pcom_monto1=$elmonto; $pcom_debcre = '+';}
		else { $pcom_debcre= '-';  $pcom_monto2 = $elmonto;} 
	if ($agregar == 'S') {
		$elsql="INSERT INTO detalle_contable (
com_nrocom, com_fecha, com_debcre, com_cuenta, com_descri, com_monto1, com_monto2, com_monto, com_ip, com_nroite, com_refere, com_tipmov, cobrado, fecha_cobro) VALUES (
'$pcom_nrocom', '$pcom_fecha', '$pcom_debcre', '$pcom_cuenta', '$pcom_descri', '$pcom_monto1', '$pcom_monto2', '$pcom_monto', '$pcom_ip', '$pcom_nroite', '$pcom_refere', '$pcom_tipmov', 0, '1001-01-01')"; 
//		$elsql="call sp_inc_r_820 (
// '$pcom_nrocom', '$pcom_fecha', '$pcom_debcre', '$pcom_cuenta', '$pcom_descri', '$pcom_monto1', '$pcom_monto2', '$pcom_monto', '$pcom_ip', '$pcom_nroite', '$pcom_refere', '$pcom_tipmov')"; 	
}
	else if ($agregar == 'N') {
			$elsql="UPDATE detalle_contable SET com_debcre='$pcom_debcre', com_cuenta='$pcom_cuenta', com_descri='$pcom_descri', com_monto1='$pcom_monto1', com_monto2='$pcom_monto2', com_ip='$pcom_ip', com_nroite='$pcom_nroite', com_refere='$pcom_refere', com_tipmov='$pcom_tipmov' WHERE nro_registro=$registro"; 
		}
		else {
			$elsql="DELETE FROM detalle_contable WHERE nro_registro = $registro";
			}
	$rs=$db_con->prepare($elsql);
	$res=$rs->execute();
//	echo $elsql.'<br>';
	if (!$res) die ("<p />Estimado usuario $usuario contacte al administrador Código 820-1- <br><br>".$elsql);
// $final = explode(" ", microtime());
// $tiempo = ($final[1] + $final[0]) - ($comienzo[1] - $comienzo[0]); 
// echo "comando ejecutado en $tiempo segundos";
	
	$elsql="SELECT SUM(com_monto1) as debe, SUM(com_monto2) AS haber, COUNT(com_nrocom) as items FROM detalle_contable WHERE com_nrocom='$pcom_nrocom'";
	$rs=$db_con->prepare($elsql);
	$res=$rs->execute();
	// echo $elsql.'<br>';
	if (!$res) die ("<p />Estimado usuario $usuario contacte al administrador Código 830-1");
	$fila = $rs->fetch(PDO::FETCH_ASSOC);
	if ($rs->rowCount() > 0) 
	{
		$elsql="UPDATE enc_contable SET enc_debe='$fila[debe]', enc_haber='$fila[haber]', enc_item='$fila[items]',enc_fecha='$pcom_fecha' WHERE enc_clave = '$pcom_nrocom'";
			$rs=$db_con->prepare($elsql);
			$res=$rs->execute();
 		// echo $elsql;
			if (!$res) die ("<p />Estimado usuario $usuario contacte al administrador Código 830-2<br>".$sql);
	}
	// actualizar los niveles en la 810
	$losniveles = "SELECT * FROM niveles order by con_nivel"; 
	$rs=$db_con->prepare($losniveles);
	$res=$rs->execute();
//	echo $losniveles;
	if ($rs->rowCount() == 0) 
	{
		die("<p /><br /><p />No se han definido los niveles<span class='b'> error Niv-1</span> en la tabla");
		exit;
	}
	
	$elmes=strtotime($pcom_fecha);
	$elmes=date("m", $elmes);
	$primero=strlen($elmes);
//	echo $pcom_fecha.'-'.$elmes . '-'.$primero;
	if (($elmes < 10) and ($primero < 2)) $elmes='0'.$elmes;
	$losniveles=$rs->fetchall();
	for ($i = count($losniveles) - 1; $i >= 0; $i--) {
/*
    	if (!mysql_data_seek($losniveles, $i)) {
	        echo "Cannot seek to row $i: " . mysql_error() . "\n";
	        continue;
    	}
	    if (!($niveles = mysql_fetch_assoc($losniveles))) {
	        continue;
	    }
*/

		// $fila = $niveles ;
		$elnivel=$losniveles[$i]['con_nivel'];
		$codigo=substr($pcom_cuenta,0,$elnivel);
		$debito='cue_deb'.$elmes;
		$credito='cue_cre'.$elmes;
		$eldebe=$pcom_monto1;
		$elhaber=$pcom_monto2;
		$sql="update cuentas set $debito=$debito+'$eldebe', $credito=$credito+'$elhaber' where cue_codigo='$codigo'";
		$result = $db_con->prepare($sql);
		$result=$result->execute();
		if (!$result)
		 die('Error en la F810-3 '.$sql.' '.mysql_error()); 		
		//echo $sql."<br>";
	}
}


function exporta($result)
{
$f = fopen("datos.csv","w");
$sep = ";";

mysql_data_seek($result,0);
	while($reg = mysql_fetch_array($result) ) {
		$linea = $reg['cue_codigo'] . $sep . $reg['cue_nombre'] . $sep . $reg['cue_saldo']. $sep . $reg['danterior']. $sep . $reg['hanterior']. $sep . $reg['debe']. $sep . $reg['haber']; //pones cada campo separado con $sep.
	fwrite($f,$linea);
	}
fclose($f); 
$fichero = "./datos.csv";
header("Content-Description: File Transfer");
header( "Content-Disposition: filename=".basename($fichero) );
header("Content-Length: ".filesize($fichero));
header("Content-Type: application/force-download");
@readfile($fichero);
}

function ahorros($cedula)
{	
	$ahorros=0;
	$sql="select * from sgcaf200 where ced_prof='".$cedula."'";
	$resultado2=mysql_query($sql);
	$fila2 = mysql_fetch_assoc($resultado2);
	$ahorros=$fila2['hab_f_prof']+$fila2['hab_f_empr']+$fila2['hab_f_extr']+$fila2['hab_opsu'];
	return $ahorros;
}
function afectan($cedula)
{
	$afectan=0;
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$sql="select sum(monpre_sdp-monpag_sdp) as saldo from sgcaf360,sgcaf310 where (cedsoc_sdp='$micedula') and (codpre_sdp=cod_pres) and  (retab_pres= 1) and (stapre_sdp='A') and (renovado=0) group by cedsoc_sdp ";
//	echo $sql;
	$resultado2=mysql_query($sql);
	if (mysql_num_rows($resultado2) > 0)
	{
		$fila2 = mysql_fetch_assoc($resultado2);
		$afectan=$fila2['saldo'];
	}
	return $afectan;
}

function noafectan($cedula)
{
	$noafectan=0;
	$micedula=substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,4);
	$sql="select sum(monpre_sdp-monpag_sdp) as saldo from sgcaf360,sgcaf310 where (cedsoc_sdp='$micedula') and (codpre_sdp=cod_pres) and  (retab_pres= 0) and (stapre_sdp='A') and (renovado=0)  group by cedsoc_sdp ";
	$resultado2=mysql_query($sql);
	if (mysql_num_rows($resultado2) > 0)
	{
		$fila2 = mysql_fetch_assoc($resultado2);
		$noafectan=$fila2['saldo'];
//		echo $sql;
	}
	return $noafectan;
}

function fianzas($elcodigo)
{
	$fianzas=0;
	$sql="select (monto_fia-monlib_fia) as saldo from sgcaf320 where codfia_fia = '$elcodigo' and (tipmov_fia = 'F') group by codsoc_fia ";
	$resultado2=mysql_query($sql);
	if (mysql_num_rows($resultado2) > 0)
	{
		$fila2 = mysql_fetch_assoc($resultado2);
		$fianzas=$fila2['saldo'];
	}
	return $fianzas;
}

function disponibilidad($ahorros,$afectan,$noafectan,$fianzas)
{
	$sql="select por_dispon from institucion limit 0,1 ";
	$resultado2=mysql_query($sql);
	$porcentaje=20;
	if (mysql_num_rows($resultado2) > 0)
	{
		$fila2 = mysql_fetch_assoc($resultado2);
		$porcentaje=$fila2['por_dispon'];
	}
	$porcentaje=100-$porcentaje;
	$porcentaje/=100;
	return (($ahorros*$porcentaje)-($afectan+$fianzas))	;
}

function ceroizq($laultima,$digitos)
{
	$tamano=$digitos-strlen($laultima);
	$nuevacadena="";
	// echo $tamano;
	// (5-$tamano)=$posicion)
	for ($posicion=1;$posicion <= $tamano;$posicion++) {
		$nuevacadena=$nuevacadena."0"; 
		// echo $nuevacadena."-";
		}
		// echo $nuevacadena."---------".$laultima;
	$nuevacadena=$nuevacadena.$laultima;
	// echo $nuevacadena;
	return $nuevacadena;
		
}

function cedad($fncido)
{

     $fdhoy = explode("@", date('d@m@Y'));
     $fpncido = explode('/', $fncido);

     if($fdhoy[1] == $fpncido[1])
     {
          if($fdhoy[0] >= $fpncido[0])
          {
               $edad = $fdhoy[2] - $fpncido[2];
          }else{
               $edad = $fdhoy[2] - $fpncido[2] - 1;
          }
     }elseif($fdhoy[1] <= $fpncido[1])
     {
          $edad = $fdhoy[2] - $fpncido[2] - 1;
     }elseif($fdhoy[1] > $fpncido[1])
     {
          $edad = $fdhoy[2] - $fpncido[2];
     }
     return $edad . ' a&ntilde;os ';
}

function dias_pasados($fechai, $fechaf)
{
	$fi = explode('-', $fechai);
	$ff = explode('-', $fechaf);
	$anoi=$fi[0];
	$mesi=$fi[1];
	$diai=$fi[2];
	$anof=$ff[0];
	$mesf=$ff[1];
	$diaf=$ff[2];
//	echo 'la fecha'.$anoi.$mesi.$diai.'<br>';
	//calculo timestam de las dos fechas
	$timestamp1 = mktime(0,0,0,$mesi,$diai,$anoi);
	$timestamp2 = mktime(0,0,0,$mesf,$diaf,$anof); 
	//resto a una fecha la otra
	$segundos_diferencia = $timestamp1 - $timestamp2;
	//echo $segundos_diferencia;
	//convierto segundos en días
	$dias_diferencia = $segundos_diferencia / (60 * 60 * 24); 
	//obtengo el valor absoulto de los días (quito el posible signo negativo)
	$dias_diferencia = abs($dias_diferencia);
	//quito los decimales a los días de diferencia
	$dias_diferencia = floor($dias_diferencia); 
	return $dias_diferencia;
}

/*! 
  @function num2letras () 
  @abstract Dado un n?mero lo devuelve escrito. 
  @param $num number - N?mero a convertir. 
  @param $fem bool - Forma femenina (true) o no (false). 
  @param $dec bool - Con decimales (true) o no (false). 
  @result string - Devuelve el n?mero escrito en letra. 

*/ 
function num2letras($num, $fem = true, $dec = true) { 
//if (strlen($num) > 14) die("El n?mero introducido es demasiado grande"); 
   $matuni[2]  = "dos"; 
   $matuni[3]  = "tres"; 
   $matuni[4]  = "cuatro"; 
   $matuni[5]  = "cinco"; 
   $matuni[6]  = "seis"; 
   $matuni[7]  = "siete"; 
   $matuni[8]  = "ocho"; 
   $matuni[9]  = "nueve"; 
   $matuni[10] = "diez"; 
   $matuni[11] = "once"; 
   $matuni[12] = "doce"; 
   $matuni[13] = "trece"; 
   $matuni[14] = "catorce"; 
   $matuni[15] = "quince"; 
   $matuni[16] = "dieciseis"; 
   $matuni[17] = "diecisiete"; 
   $matuni[18] = "dieciocho"; 
   $matuni[19] = "diecinueve"; 
   $matuni[20] = "veinte"; 
   $matunisub[2] = "dos"; 
   $matunisub[3] = "tres"; 
   $matunisub[4] = "cuatro"; 
   $matunisub[5] = "quin"; 
   $matunisub[6] = "seis"; 
   $matunisub[7] = "sete"; 
   $matunisub[8] = "ocho"; 
   $matunisub[9] = "nove"; 

   $matdec[2] = "veint"; 
   $matdec[3] = "treinta"; 
   $matdec[4] = "cuarenta"; 
   $matdec[5] = "cincuenta"; 
   $matdec[6] = "sesenta"; 
   $matdec[7] = "setenta"; 
   $matdec[8] = "ochenta"; 
   $matdec[9] = "noventa"; 
   $matsub[3]  = 'mill'; 
   $matsub[5]  = 'bill'; 
   $matsub[7]  = 'mill'; 
   $matsub[9]  = 'trill'; 
   $matsub[11] = 'mill'; 
   $matsub[13] = 'bill'; 
   $matsub[15] = 'mill'; 
   $matmil[4]  = 'millones'; 
   $matmil[6]  = 'billones'; 
   $matmil[7]  = 'de billones'; 
   $matmil[8]  = 'millones de billones'; 
   $matmil[10] = 'trillones'; 
   $matmil[11] = 'de trillones'; 
   $matmil[12] = 'millones de trillones'; 
   $matmil[13] = 'de trillones'; 
   $matmil[14] = 'billones de trillones'; 
   $matmil[15] = 'de billones de trillones'; 
   $matmil[16] = 'millones de billones de trillones'; 

   $num = trim((string)@$num); 
   if ($num[0] == '-') { 
      $neg = 'menos '; 
      $num = substr($num, 1); 
   }else 
      $neg = ''; 
   while ($num[0] == '0') $num = substr($num, 1); 
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
   $zeros = true; 
   $punt = false; 
   $ent = ''; 
   $fra = ''; 
   for ($c = 0; $c < strlen($num); $c++) { 
      $n = $num[$c]; 
      if (! (strpos(".,'''", $n) === false)) { 
         if ($punt) break; 
         else{ 
            $punt = true; 
            continue; 
         } 

      }elseif (! (strpos('0123456789', $n) === false)) { 
         if ($punt) { 
            if ($n != '0') $zeros = false; 
            $fra .= $n; 
         }else 

            $ent .= $n; 
      }else 

         break; 

   } 
   $ent = '     ' . $ent; 
   if ($dec and $fra and ! $zeros) { 
      $fin = ' coma'; 
      for ($n = 0; $n < strlen($fra); $n++) { 
         if (($s = $fra[$n]) == '0') 
            $fin .= ' cero'; 
         elseif ($s == '1') 
            $fin .= $fem ? ' una' : ' un'; 
         else 
            $fin .= ' ' . $matuni[$s]; 
      } 
   }else 
      $fin = ''; 
   if ((int)$ent === 0) return 'Cero ' . $fin; 
   $tex = ''; 
   $sub = 0; 
   $mils = 0; 
   $neutro = false; 
   while ( ($num = substr($ent, -3)) != '   ') { 
      $ent = substr($ent, 0, -3); 
      if (++$sub < 3 and $fem) { 
         $matuni[1] = 'una'; 
         $subcent = 'os' ; // 'as'; 
      }else{ 
         $matuni[1] = $neutro ? 'un' : 'uno'; 
         $subcent = 'os'; 
      } 
      $t = ''; 
      $n2 = substr($num, 1); 
      if ($n2 == '00') { 
      }elseif ($n2 < 21) 
         $t = ' ' . $matuni[(int)$n2]; 
      elseif ($n2 < 30) { 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      }else{ 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      } 
      $n = $num[0]; 
      if ($n == 1) { 
         $t = ' ciento' . $t; 
      }elseif ($n == 5){ 
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
      }elseif ($n != 0){ 
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
      } 
      if ($sub == 1) { 
      }elseif (! isset($matsub[$sub])) { 
         if ($num == 1) { 
            $t = ' mil'; 
         }elseif ($num > 1){ 
            $t .= ' mil'; 
         } 
      }elseif ($num == 1) { 
         $t .= ' ' . $matsub[$sub] . '?n'; 
      }elseif ($num > 1){ 
         $t .= ' ' . $matsub[$sub] . 'ones'; 
      }   
      if ($num == '000') $mils ++; 
      elseif ($mils != 0) { 
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
         $mils = 0; 
      } 
      $neutro = true; 
      $tex = $t . $tex; 
   } 
   $tex = $neg . substr($tex, 1) . $fin; 
   return ucfirst($tex); 
}
function suma_fechas($fecha,$ndias)
{
if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
list($dia,$mes,$año)=split("/", $fecha);
if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
list($dia,$mes,$año)=split("-",$fecha);
$nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
$nuevafecha=date("d/m/Y",$nueva);
return ($nuevafecha);
}

function restar_fechas($fecha,$ndias)
{
if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
list($dia,$mes,$año)=split("/", $fecha);
if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
list($dia,$mes,$año)=split("-",$fecha);
$nueva = mktime(0,0,0, $mes,$dia,$año) - $ndias * 24 * 60 * 60;
$nuevafecha=date("d/m/Y",$nueva);
return ($nuevafecha);
}

function restaradministrativos($montoprestamo)
{
	$sql_deduccion="select * from sgcaf311 where activar = 1";
	$a_deduccion=mysql_query($sql_deduccion);
	$d_obligatorias=0;
	while($r_deduccion=mysql_fetch_assoc($a_deduccion)) {
		if ($r_deduccion['porcentaje'] == 0)
			$monto_deduccion=$r_deduccion['monto'];
		else $monto_deduccion=($montoprestamo)*($r_deduccion['porcentaje']/100);
		$d_obligatorias+=$monto_deduccion;
		}
	return $d_obligatorias;
}
	
function cal_int($interes,$mcuotas,$mmonpre_sdp,$factor_divisible = 12,$z=0,&$i2)
{
	if ($interes > 0) {
		$i = ((($interes / 100)) / $factor_divisible);
		$i2 = $i;
/*
		$sql="CREATE TEMPORARY TABLE interes_temp (
		elinteres decimal (12,8) NOT NULL default 0.00
      ) ;";
		$rsql=mysql_query($sql);
// 		$asql=mysql_fetch_assoc($rsql);
		$sql="insert into interes_temp (elinteres) values ('$i2');";
		$rsql=mysql_query($sql);
// 		$asql=mysql_fetch_assoc($rsql);
*/		
//		echo ' lo que trae i2 '.$i2.'<br>';
		$i_ = 1 + $i;
		$i_ = pow($i_,$mcuotas); 	// exponenciacion 
		$i_ = 1 / $i_;
		$i__ = 1 - $i_;
		$i___ = $i / $i__;
		$z = $mmonpre_sdp * $i___;
	}
	if ($interes ==0)
		$z = $mmonpre_sdp / $mcuotas;
/*
	    ((1 + i)^n) - 1
	i =-----------------
	           i
*/
	return $z;
}


function actualizar_fiador($socio,$monto,$prestamo)
{
	if ($prestamo!='x')
		$sql="select * from sgcaf320 where nropre_fia='$prestamo' and tipmov_fia='F'";
	else $sql="select * from sgcaf320 where codsoc_fia='$socio' and tipmov_fia='F'";
	$resultado = mysql_query($sql) or die('1.-'.mysql_error());
	while ($registro = mysql_fetch_assoc($resultado)) {
		$aliberar = $monto * ($registro['porafi_fia'] / 100);
		$liberado = $registro['monlib_fia'] + $aliberar;
		$actualizar="update sgcaf320 set monlib_fia = $liberado' ";
		if ($liberado >= $registro['monto_fia'])
			$actualizar .= ", tipmov_fia = 'L' ";
		$actualizar.= " where registro = ".$registro['registro'];
		$resulta2=mysql_query($actualizar) or die('2.-'.mysql_error());
	}
}

function existe_cuenta($codigo)
{
	$sql="select cue_codigo from sgcaf810 where cue_codigo='$codigo'";
	$result = mysql_query($sql) or die('Error en la F810-4 '.$sql.' '.mysql_error()); 		
	if ((mysql_num_rows($result) < 1))
	{ 	// no existe la cuenta y la creo
		// busco el socio
		$socio=explode('-',$codigo);
		$socio='0'.$socio[6];
		$sql="select ape_prof, nombr_prof from sgcaf200 where cod_prof='$socio'";
		$result = mysql_query($sql) or die('Error en la F200-1 '.$sql.' '.mysql_error()); 
		$rsocio=mysql_fetch_assoc($result);
		$nombre=trim($rsocio['ape_prof']). ' '.$rsocio['nombr_prof'];
		$sql="insert into sgcaf810 (cue_codigo, cue_nombre, cue_nivel, cue_saldo) values ('$codigo', '$nombre', '7', 0)";
		$result = mysql_query($sql) or die('Error en la F810-5 '.$sql.' '.mysql_error()); 
	}
}

/* *** CABECERA LISTADO ASIENTOS HISTORICOS *** */

function cabasi_his($edborr) {

echo "<tr>";

//if ($edborr) {echo "<th width='200' colspan=0></th>";}

echo '<th width="100">Cuenta</th><th width="200">Descripci&oacute;n</th><th width="200">Concepto</th><th width="50">Referencia</th><th width="100">Debe</th><th width="100">Haber</th></tr>';
// <th width="100">Descuadre</th></tr>';

}

/* *** LISTAR ASIENTO HISTORICO *** */

function asiento_his($asiento, $edborr, $por, $deci, $bojust) {

// if ($bojust == $asiento) {mysql_query("UPDATE asientos SET fich = '', tipofich='' WHERE asiento = '$asiento'");}

$result = mysql_query("SELECT * FROM histf830 WHERE enc_clave = $asiento");
if ($result) {$fichero = mysql_fetch_array($result);}

$cols = 4;

if ($edborr) {$cols = $cols+2;}

$asi = $fichero; // mysql_fetch_array($result);
$a=explode("-",$asi["enc_fecha"]);
echo "<tr><td colspan='$cols'>Asiento: ".$asiento."</a> Fecha: ";
echo $a[2]."/".$a[1]."/".$a[0]; // substr($a[0],2,2);
echo "</b>";

echo "</td></tr>";

$comando="SELECT nro_registro, com_cuenta, com_descri, com_monto1, com_monto2, com_refere FROM histf820 WHERE com_nrocom = '$asiento' ORDER BY com_debcre, com_cuenta, com_refere";
$result = mysql_query($comando);
// echo $comando;

while ($fila = mysql_fetch_array($result)) {

	echo "<tr>";
	if ($edborr) {
	}
	echo "<td width='100'>".$fila["com_cuenta"]."</a></td>";
	
	$sqlcuenta="SELECT cue_nombre FROM histf810 where cue_codigo = '".$fila["com_cuenta"]."'"; // revisar
	$rs=mysql_query($sqlcuenta) or die ("<p />Estimado usuario $usuario contacte al administrador Código 810-1");
	$filacuenta = mysql_fetch_array($rs);
	echo "<td width='200'>".$filacuenta["cue_nombre"]."</td>";
	echo "<td width='200'>".$fila["com_descri"]."</td>";
	echo "<td width='50'>".$fila["com_refere"]."</td><td width='100' class='dcha'>";
	
	if ($fila["com_monto1"] == 0)
	{
		echo "&nbsp;";
	} else {
		echo number_format($fila["com_monto1"],2,'.',',');
	}

	echo "</td><td class='dcha'>";
	
	
	if ($fila["com_monto2"] == 0)
	{
		echo "&nbsp;";
	} else {
		echo number_format($fila["com_monto2"],2,'.',',');
	}
	echo "</td></tr>";

}

$elmonto=$asi['enc_debe']-$asi['enc_haber'];
if ($asi['enc_debe']-$asi['enc_haber'] != 0) {
	echo "<tr><td align='right' class='danger' colspan=".($cols-2);
	echo "<span align='right'> Diferencia de ".number_format(($asi['enc_debe']-$asi['enc_haber']),2,',','.')."</span>";
}
else 
	echo "<tr><td align='right' colspan=".($cols-2).">";
echo "  SubTotales: </td><td align='right' class='info'>".number_format($asi['enc_debe'],2,',','.')."</td><td align='right'>".number_format($asi['enc_haber'],2,',','.')."</td>";

echo "</tr><tr><td colspan='$cols' class='success'>&nbsp;</td></tr>
<p>";
  
  }
  
 /* *** ACTUALIZAR UNO O TODOS LOS ASIENTOS HISTORICOS ****************************** */
  
  function totalapu_his($asiento) {
  
  if ($asiento) {
		$where = "WHERE com_nrocom = '$asiento'";
	}
  
  $rs = mysql_query("SELECT com_nrocom from histf820 $where");
  
  while ($fila = mysql_fetch_array($rs)) :
  
  $a = $fila[0];
  
  $rs1=mysql_query("SELECT SUM(com_monto1) AS tot_debe, SUM(com_monto2) AS tot_haber, COUNT(com_nrocom) as tot_items FROM histf820 WHERE com_nrocom = '$a'");
 $fila1 = mysql_fetch_array($rs1);
  
	  mysql_query("UPDATE histf830 SET enc_debe = '$fila1[0]', enc_haber = '$fila1[1]', enc_item = '$fila[2]' WHERE enc_clave = '$a'");
  
  endwhile;
  
  return ($fila1[0] - $fila1[1]);
  
  }
  
function numero_prestamo($micedula, $laparte, $db_con)
{
	// determino nuevo numero de prestamo
//	$sql_310="select nropre_sdp from sgcaf310 where (cedsoc_sdp='$micedula') and (substr(nropre_sdp,1,5)='$laparte') order by nropre_sdp desc limit 1";
//	echo $sql_310;
	$rango=ahora($db_con)['ahora'];
	$rango=substr($rango,1,7);
	$tipo=$laparte;
	$sql_310="select count(referencia) as cantidad from prestamos where (concepto=:tipo) and substr(fecha_solicitud,1,7)=:rango";
	$a_310=$db_con->prepare($sql_310);
	$a_310->execute(array(
		":tipo"=>$tipo,
		":rango"=>$rango,
		));
	$elnumero=$a_310->fetch(PDO::FETCH_ASSOC);
	$elnumero=$elnumero['cantidad']+1;
	$elnumero=ceroizq($elnumero,5);
	// $elnumero=substr($elnumero['nropre_sdp'],5,3);
// echo 'este es el '.$elnumero;
/*
	$buscarhasta=200;
	for ($unnumero=$elnumero; $unnumero < $buscarhasta; $unnumero++)
	{
		$elnumero=$elnumero+1;
		$lnumero=$laparte.ceroizq($elnumero,3);
		echo 'Buscando '.$lnumero;
		$sql_310="select * from sgcaf310 where (nropre_sdp='$lnumero')";
		$a_310=mysql_query($sql_310);
		if ((mysql_num_rows($a_310) < 1))
			$unnumero = $buscarhasta+1;
//		else echo ' ya existe<br>';
	}
*/
	$rango=substr($rango,1,2).substr($rango,4,2);
	$elnumero=$tipo.$rango.$elnumero;
	// fin de generar nuevo numero
	return $elnumero;
}


function mensaje($arreglo)
{
//echo "<div class='alert alert-success'>	<button class='close' data-dismiss='alert'>&times;</button>	<strong>Bienvenido alguien</strong> </div>";
echo "<div class='alert alert-".$arreglo['tipo']."'>";
echo "<button class='close' data-dismiss='alert'>&times;</button>";
echo $arreglo['texto'];
echo "</div>";
}

function la_ip()
{
	// $ip = $_SERVER['HTTP_CLIENT_IP'];
	// if (!$ip) 
	$ip = $_SERVER['REMOTE_ADDR'];
	return $ip;
}

function el_usuario()
{
	$usuario=$_SERVER['REMOTE_ADDR'];
	return $usuario;
}

function ahora($db_con)
{
	$sql="SELECT DATE_FORMAT(now(),'%m/%d/%Y') as hoy, DATE_FORMAT(now(),'%Y-%m-%d') as hoy1, DATE_FORMAT(date_sub(now(),interval (18*365) day),'%m/%d/%Y') AS los18, now() as ahora";
	$con=$db_con->prepare($sql);
	$query = $con->execute();
	$fila=$con->fetch(PDO::FETCH_ASSOC);
	return $fila;
}

function encabezado(&$pdf, $linea, $db_con, $referencia, $concepto)
{
	$sql="select * from configuracion where parametro='id_instituto'";
	$resc=$db_con->prepare($sql);
	$resc->execute();
	$linea=10;
	$alto=7;
	$pdf->SetY($linea);
	$pdf->SetX(0);
	$pdf->Image('imagenes/logo.jpg',20,10,20);
	while ($reg = $resc->fetch(PDO::FETCH_ASSOC))
	{
		$pdf->SetY($linea);
		$pdf->SetX(0);
	    $pdf->SetFont('Arial','B',12);
	    $pdf->Cell(220,$alto,$reg['nombre'],0,0,'C');
	    $linea+=$alto;
	}
	return $linea+=$alto;
}
?>

