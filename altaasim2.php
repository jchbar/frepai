<?php
/* *********** COMPROBACIÓN Nº ASIENTO **************** */
//echo $asiento.'zzz';
// echo 'n'.$_GET['n'] ;
//$sql="SELECT enc_clave FROM enc_contable WHERE enc_clave = '$asiento'")) AND $_GET['n'] == 1;
$sql="SELECT enc_clave FROM enc_contable WHERE enc_clave = '$asiento'";
// echo $sql;
$res=$db_con->prepare($sql);
// echo $sql;
$res->execute();
if ($res->rowCount()){
	$mensaje = "No se ha añadido el Asiento: Asiento <span class='b'>$asiento</span> ya existe.<p />";
}

/* ***************COMPROBACIÓN AÑO ****************** */

// $result = $db_con->prepare("SELECT anocont FROM empresa");
$sql="SELECT date_format(con_lpini,'%y') AS anocont FROM control";
$result = $db_con->prepare($sql);
$result->execute();
$fila = $result->fetch(PDO::FETCH_ASSOC);

$b = explode("/",$fecha);

// if ($fila[0] != "20".$b[2]) { $mensaje = "No se ha añadido el Asiento: El año no es el del ejercicio actual ($fila[0])<p />"; }

/* **************************************************** */

if (trim($mensaje) == "" ) { // AND $asiento <= 9999999000

//	$a=explode("/",$fecha); 
	//$a=explode("/",substr($_POST['daterange'],0,10)); 
	$a=substr($_POST['daterange'],0,10); 
	// die( 'fecha '.substr($_POST['daterange'],0,10));
	$b=$a; // $a[2]."-".$a[1]."-".$a[0];
	// echo 'la fecha b'.$b. $_POST['fecha'];
	// echo 'fecha '.$b;
	$nomatach = $_FILES['fich']['name'];
	if ($nomatach) {
		$tipo1    = $_FILES["fich"]["type"];
		$archivo1 = $_FILES["fich"]["tmp_name"];
		$tamanio1 = $_FILES["fich"]["size"];
		$fp = fopen($archivo1, "rb");
	    $contenido = fread($fp, $tamanio1);
	    $contenido = addslashes($contenido);
	    fclose($fp);
	}
	if ((reviso($_POST['cuenta1'], $db_con)) AND (reviso($_POST['cuenta2'], $db_con))) {
		$sql="SELECT enc_clave FROM enc_contable WHERE enc_clave = '$asiento'";
		$res=$db_con->prepare($sql);
		$res->execute();
		if ($res->rowCount() < 1)
		{
			$sql = "INSERT INTO enc_contable (enc_clave, enc_fecha, enc_desco, enc_desc1, enc_debe, enc_haber, enc_item, enc_dif, enc_igual, enc_refer, enc_sw, enc_explic) VALUES ('$asiento', '$b', '','',0,0,0,0,0,0,0,\"$explicacion\")"; 
			$res=$db_con->prepare($sql);
			$rs=$res->execute();
			if (!$rs) die ("El usuario $usuario no tiene permiso para añadir Asientos.");
		}
		if ($nomatach) {
			$sql="UPDATE enc_contable SET enc_explic = \"$explicacion\" WHERE enc_clave = '$asiento'";
			$res=$db_con->prepare($sql);
			$res->execute();
		}
		$ip = la_ip();
		$elcargo='+';
		$debe=$elmonto;
		$haber=0;
		// echo $_POST['asiento'].' * '. $b. ' * '.$_POST['cuenta1']. ' * '.$_POST['concepto'].' * '. $debe. ' * '.$haber. $ip.' * '.$_POST['referencia'];
		agregar_f820($_POST['asiento'], $b, '+', $_POST['cuenta1'], $_POST['concepto'], $debe, $haber, 0,$ip,0,$_POST['referencia'],'','S',0, $db_con);
		$elcargo='-';
		agregar_f820($_POST['asiento'], $b, '-', $_POST['cuenta2'], $_POST['concepto'], $debe, $haber, 0,$ip,0,$_POST['referencia'],'','S',0, $db_con);
		$sql="UPDATE control SET con_ultfec = '$b'";
		$res=$db_con->prepare($sql);
		$res->execute();
		$anadido = 1;
	}
	else { echo '<h2> No se ha agregado información, una de las cuentas presenta problemas</h2>'; }
}

function reviso($lacuenta, $db_con)
{
	$sql2="SELECT * FROM cuentas where cue_codigo =:lacuenta";
	$salida=$db_con->prepare($sql2);
	$salida->execute([":lacuenta"=>$lacuenta]);
//	echo $sql2;
	$filas = $salida->rowCount();
	return ($filas == 0?false:true);
}

?>
