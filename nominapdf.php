<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
extract($_GET);
extract($_POST);
extract($_SESSION);
include_once 'dbconfig.php';
require('funciones.php');
require('fpdf/fpdf.php');
define('FPDF_FONTPATH','fpdf/font/');
//header('Content-type: application/pdf');
class PDF extends FPDF
{
/*
	// Cabecera de página
	function Header()
	{
	    // Logo
        // $this->Image('fpdf/logo/logo.jpg',10,0,20);
	    // Arial bold 15
	    $this->SetFont('Arial','B',15);
	    // Movernos a la derecha
	    $this->Cell(80);
	    // Título
	    //$this->Cell(30,10,'Title',1,0,'C');
	    // Salto de línea
	    $this->Ln(20);
	}
*/
	// Pie de página
	function Footer()
	{
	    // Posición: a 1,5 cm del final
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
$arreglo=stripslashes ($_GET['arreglo']);
$arreglo=unserialize($arreglo);
$referencia=$arreglo['referencia'];
$fechadescuento=$arreglo['fechadescuento'];
print_r($arreglo);
//"select *, concat(ape_tit,', ',nom_tit) as nombre from titulares, ".$fuente." where ".$condicion;
// echo $sql;
$columna=3;
$rpl=300; 	// registros por listado
$crl=0;		// contador de registros por listado
$col_listado=0;
$nuevoarchivo=false;
$col_listado=0;
$header=array('Lin N°','Ubicacion','Referencia','Cedula','Apellidos y Nombres','Credito','Cuota');
$alto=4;
$salto=$alto;
$w=array(15,20,20,20,50,25,25); // ,25,25,25,25,25,25);
$p[0]=15;
for ($posicion=1;$posicion<count($w);$posicion++) 
	$p[$posicion]=$p[$posicion-1]+$w[$posicion-1];

$pdf=new PDF('P','mm','Letter');
$pdf->SetFont('Arial','',10);
//$pdf->Open();
$sql_nopr=$sql;
// echo $sql_nopr;
try
{
	$agrupados = $db_con->prepare($agrupar);
//	echo 'agrupar '.$agrupar; 
	$agrupados->execute();
	while ($conceptos = $agrupados->fetch(PDO::FETCH_ASSOC))
	{
		$concepto=$conceptos['concepto'];
//	echo $sql_nopr;
		$a_nopr=$db_con->prepare($sql_nopr);
		$a_nopr->execute(array(":concepto"=>$concepto));
		$empezar=0;

		$registros=$a_nopr->rowCount();
		set_time_limit($registros);
		// echo $registros;
		$general = $tsaldo = $cont=0;
		$lascolumnas=$a_nopr->columnCount()-4;
		while ($r_nopr = $a_nopr->fetch(PDO::FETCH_ASSOC))
		{
			if ($empezar == 0)
			{
				$concepto = $r_nopr['concepto'];
				$linea=encabeza_prenom($header,$w,$p,$pdf,$salto,$alto,$fechadescuento, $db_con, $referencia, $concepto);
				$empezar = 1;
			}
			$linea+=$salto;
			$pdf->SetY($linea);
			$cont++;
			$pdf->SetX($p[0]); $pdf->Cell($w[0],$alto,ceroizq($cont,4),0,0,'LRTB',0);
			$pdf->SetX($p[1]); $pdf->Cell($w[1],$alto,$r_nopr["numero"],0,0,'LRTB',0); 
			$pdf->SetX($p[2]); $pdf->Cell($w[2],$alto,substr($r_nopr["referencia"],-5),0,0,'C',0); 
			$pdf->SetX($p[3]); $pdf->Cell($w[3],$alto,$r_nopr["cedula"],0,0,'LRTB',0);  
			$pdf->SetX($p[4]); $pdf->Cell($w[4],$alto,$r_nopr["nombre"],0,0,'LRTB');
			$pdf->SetX($p[5]); $pdf->Cell($w[5],$alto,number_format($r_nopr['saldo'],2,".",","),0,0,'R',0);
			$posicion=3;
			$t1=$r_nopr["montocotizacion"];
			$pdf->SetY($linea);
			$pdf->SetX($p[6]); $pdf->Cell($w[6],$alto,number_format($t1,2,".",","),0,0,'R',0);
			$general+=$t1;
			$tsaldo+=$r_nopr['saldo'];
			if ($linea>=250) 
			{
				$linea+=$alto;
				$pdf->SetY($linea);
				$pdf->SetX($p[0]);
				$pdf->Cell(0,0,'  ',1,0,'L',0);
				$linea=encabeza_prenom($header,$w,$p,$pdf,$salto,$alto,$fechadescuento, $db_con, $referencia, $concepto);
			}
		}
		$linea+=$salto;
		$pdf->SetY($linea);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetX($p[4]); $pdf->Cell($w[4],$alto,'Total General',0,0,'L',1);
		$pdf->SetX($p[5]); $pdf->Cell($w[5],$alto,number_format($tsaldo,2,".",","),0,0,'R',1);
		$pdf->SetX($p[6]); $pdf->Cell($w[6],$alto,number_format($general,2,".",","),0,0,'R',1);
		$pdf->SetFont('Arial','',7);
		$fechagen=ahora($db_con)['ahora'];
		$ip = la_ip();
		$sql="select fecha_registro, registro from nominas where fechanomina= :fechanomina and concepto= :concepto and visible = 1";
		$resn=$db_con->prepare($sql);
		$resn->execute(array(
			":fechanomina"=>$fechadescuento,
			":concepto"=>$concepto,
			));
		if ($resn->rowCount()) // habia una y modifico
		{
			$registro=$resn->fetch(PDO::FETCH_ASSOC);
			$registro=$registro['registro'];
			$sql="update nominas set visible = 0 where registro = :registro";
			$resn=$db_con->prepare($sql);
			$resn->execute(array(
				":registro"=>$registro,
				));
		}
		$sql="insert into nominas (fechanomina, fecha_registro, registros, ip, concepto, visible, montocotizacion) values (:fechadescuento, :fechagen, :cont, :ip, :concepto, :visible, :montocotizacion)";
		$resn=$db_con->prepare($sql);
		$resn->execute(array(
			":fechadescuento"=>$fechadescuento,
			":concepto"=>$concepto,
			":fechagen"=>$fechagen,
			":cont"=>$cont,
			":ip"=>$ip,
			":montocotizacion"=>$general,
			":visible"=>1,
			));
	}
	// echo $sql;
	$pdf->Output('F','reportes_prenomina/'.$fechadescuento.'-'.$concepto.'.pdf');
	$pdf->Output();
}
catch (PDOException $e) 
{
//	mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage()]);
		die('Fallo call'. $e->getMessage());
}
set_time_limit(30);

////////////////////////////////////////////////////

function encabeza_prenom($header,$w,$p,&$pdf,$salto,$alto,$fechadescuento, $db_con, $referencia, $concepto)
{
$pdf->SetFont('Arial','B',14);
$pdf->AddPage();
$linea=encabezado($pdf, 0, $db_con, $referencia, $concepto);
$pdf->SetFont('Arial','B',10);
$pdf->SetY($linea);
$pdf->SetX(0);
$pdf->Cell(200,0,"Prenómina a Cobrar a partir del ".convertir_fechadmy($fechadescuento),0,0,'C',0);
$pdf->SetY($linea);
$pdf->SetFont('Arial','',7);
$linea+=5;
$pdf->SetX(170);
$pdf->Cell(20,0,'Realizado el '.date('d/m/Y h:i A'),0,0,'L'); 
//Títulos de las columnas

	$sql="select * from proveedores where codigo=:concepto";
	$resc=$db_con->prepare($sql);
	$resc->execute(array(":concepto"=>$concepto));
	$reg=$resc->fetch(PDO::FETCH_ASSOC);

	$pdf->SetY($linea);
	$pdf->SetX($p[0]);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(60,$alto,'Concepto '.$reg['casa'],0,0,'L');

	$pdf->SetY($linea);
	$pdf->SetX($p[6]);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(60,$alto,'Referencia '.substr($referencia,1,8),0,0,'L');

$linea+=5;
$linea+=5;
$pdf->SetY($linea);
//$header=array($$arrtitulo);
//Colores, ancho de línea y fuente en negrita
$pdf->SetFillColor(200,200,200);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','B',10);
//Cabecera
for($i=0;$i<count($header);$i++){
	$pdf->SetY($linea);
	$pdf->SetX($p[$i]);
	$pdf->Cell($w[$i],$alto,$header[$i],1,0,'C',1);
}
//Restauración de colores y fuentes
$pdf->SetFillColor(224,235,255);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial','',10);
$linea+=$salto;
$linea+=$salto;
$pdf->SetY($linea);
$pdf->SetX($p[0]);
$pdf->Cell(0,0,'  ',1,0,'L',0);
return $linea;
}
?>
