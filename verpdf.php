<?php
$registro=$_GET['archivo'];
$valorflotante=(!empty($_GET['prestamo'])?$_GET['prestamo']:0);
include('dbconfig.php');
include('funciones.php');
$sql="select * from nominas where registro=:registro and visible=1";
$res=$db_con->prepare($sql);
$res->execute([":registro"=>$registro]);
if ($res->rowCount() > 0)
{
	$fila=$res->fetch(PDO::FETCH_ASSOC);
	if (($fila['concepto'] == '039'))
		$pdf = $fila['fechanomina'].'-'.$fila['concepto'].'.pdf';
	else
	{
		$pdf = $fila['fechanomina'].'.pdf';
		if ($valorflotante == 2)
		{
			echo "enre =>$valorflotante<=";
			$pdf = $fila['fechanomina'].'detalle_dcto.pdf';
		}
	}
	$carpeta='reportes_prenomina/';
/*
	echo 'prest'.$valorflotante;
	echo $carpeta.$pdf;
*/
	$fp = file_get_contents($carpeta.$pdf);
	// $fp = $carpeta.$pdf;
	 header('Content-type: application/pdf');
	// header('Content-Disposition: attachment; filename="'.$pdf.'"');
	// readfile($pdf);
	echo $fp;
}
else
	mensaje(array(
		"titulo"=>"Aviso!!!",
		"tipo"=>"danger",
		"texto"=>"<h4>Aviso!!!</h4> Registro Erroneo",
	));

?>
