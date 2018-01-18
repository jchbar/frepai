<?php
//$fecha = $_GET['fecha'];
extract($_GET);
extract($_POST);
extract($_SESSION);
$asiento = $_GET['asiento'];
$sql="SELECT * FROM detalle_contable WHERE nro_registro = :row_id";
$result = $db_con->prepare($sql);
$result->execute(array(":row_id"=>$row_id));
if ($result->rowCount()>0)
{
	$fila=$result->fetch(PDO::FETCH_ASSOC);
	$b=$fila['com_fecha'];
	$elcargo=$fila['com_debcre'];
	$cuenta1=$fila['com_cuenta'];
	$concepto=$fila['com_descri'];
	$debe=$fila['com_monto1'];
	$haber=$fila['com_monto2'];
	$referencia=$fila['com_refere'];
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
//	echo $b;
	//$b = $fecha; 
	agregar_f820($asiento, $b, $elcargo, $cuenta1, $concepto, $debe, $haber, 0,$ip,0,$referencia,'','E',$row_id, $db_con);

	$sql="SELECT * FROM enc_contable WHERE enc_clave = :asiento";
//	echo $sql." aseinto ".$asiento;
	$result = $db_con->prepare($sql);
	$result->execute(array(":asiento"=>$asiento));

	if ($result->rowCount() == 0)
	{
		$sql="DELETE FROM enc_contable WHERE enc_clave = :asiento";
		$result = $db_con->prepare($sql);
		$result->execute(array(":asiento"=>$asiento));
		echo "<p />Se borró el último apunte de Asiento $asiento, por tanto se ha borrado el asiento $asiento en tabla Asientos";
	}

	$row_id = 0;
	//$nuevo = 0;
}
?>
