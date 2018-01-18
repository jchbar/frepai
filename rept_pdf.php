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
$columna=3;
$rpl=300; 	// registros por listado
$crl=0;		// contador de registros por listado
$col_listado=0;
$nuevoarchivo=false;
$col_listado=0;
$header=array('Lin N°','Ubicacion','Cedula','Apellidos y Nombres','Estatus','FREPAI');
$alto=4;
$salto=$alto;
$w=array(15,20,20,60,25,15); // ,25,25,25,25,25,25);
$p[0]=25;
for ($posicion=1;$posicion<count($w);$posicion++) 
	$p[$posicion]=$p[$posicion-1]+$w[$posicion-1];

$pdf=new PDF('P','mm','Letter');
$pdf->SetFont('Arial','',10);
//$pdf->Open();
$fechadescuento=ahora($db_con)['ahora'];
$fechadescuento=substr($fechadescuento,0,10);
$sql="SELECT *, concat(trim(ape_tit), ' ',nom_tit) as nombre FROM titulares ORDER BY concat(trim(ape_tit), ' ',nom_tit)";
try
{
	$agrupados = $db_con->prepare($sql);
	// echo 'agrupar '.$agrupar;  die('espero');
	$agrupados->execute();
	$cont = 0;
	$linea=encabeza_prenom($header,$w,$p,$pdf,$salto,$alto,$fechadescuento, $db_con);
	while ($conceptos = $agrupados->fetch(PDO::FETCH_ASSOC))
	{
		$linea+=$salto;
		$pdf->SetY($linea);
		$cont++;
		$pdf->SetX($p[0]); $pdf->Cell($w[0],$alto,ceroizq($cont,4),0,0,'LRTB',0);
		$pdf->SetX($p[1]); $pdf->Cell($w[1],$alto,$conceptos["numero"],0,0,'LRTB',0); 
		$pdf->SetX($p[2]); $pdf->Cell($w[3],$alto,$conceptos["cedula"],0,0,'LRTB',0);  
		$pdf->SetX($p[3]); $pdf->Cell($w[4],$alto,$conceptos["nombre"],0,0,'LRTB');
		$pdf->SetX($p[4]); $pdf->Cell($w[4],$alto,$conceptos["status"],0,0,'LRTB');

		$cedula = $conceptos['cedula'];
		$sql2="select disponible, status, aporte_ord from frepai where cedula=:cedula";
		$frepai=$db_con->prepare($sql2);
		$frepai->execute(array(":cedula"=>$cedula));
		$muestro = $frepai->rowCount();
		if ($muestro > 0)
		{
			$rf=$frepai->fetch(PDO::FETCH_ASSOC);
			if ($rf['status'] != 'RETIRA')
			{
				$pdf->SetX($p[5]); $pdf->Cell($w[5],$alto,'X',0,0,'C');
			}
		}
		if ($linea>=250) 
		{
			$linea+=$alto;
			$pdf->SetY($linea);
			$pdf->SetX($p[0]);
			$pdf->Cell(0,0,'  ',1,0,'L',0);
			$linea=encabeza_prenom($header,$w,$p,$pdf,$salto,$alto,$fechadescuento, $db_con);
		}
	}
	$linea+=$salto;
	$pdf->SetY($linea);
	$pdf->SetFont('Arial','B',10);
	$pdf->SetX($p[4]); $pdf->Cell($w[4],$alto,'Total Titulares ',0,0,'L',1);
	$pdf->SetX($p[5]); $pdf->Cell($w[5],$alto,number_format($cont,0,".",","),0,0,'R',1);
	$pdf->SetFont('Arial','',10);
	$sql="select status, count(status) as cuantos from titulares group by status order by status";
	$agrupados = $db_con->prepare($sql);
	// echo 'agrupar '.$agrupar;  die('espero');
	$agrupados->execute();
	while ($conceptos = $agrupados->fetch(PDO::FETCH_ASSOC))
	{
		$linea+=$salto;
		$pdf->SetY($linea);
		$cont++;
		$pdf->SetX($p[4]); $pdf->Cell($w[4],$alto,'Socios '.$conceptos['status'],0,0,'L',1);
		$pdf->SetX($p[5]); $pdf->Cell($w[5],$alto,number_format($conceptos['cuantos'],0,".",","),0,0,'R',1);
	}

	$pdf->Output();
}
catch (PDOException $e) 
{
//	mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage()]);
		die('Fallo call'. $e->getMessage().$sql);
}
set_time_limit(30);

////////////////////////////////////////////////////

function encabeza_prenom($header,$w,$p,&$pdf,$salto,$alto,$fechadescuento, $db_con)
{
$pdf->SetFont('Arial','B',14);
$pdf->AddPage();
$linea=encabezado($pdf, 0, $db_con, '', '');
$pdf->SetFont('Arial','B',10);
$pdf->SetY($linea);
$pdf->SetX(0);
$pdf->Cell(200,0,"Reporte de Titulares al ".convertir_fechadmy($fechadescuento),0,0,'C',0);
$pdf->SetY($linea);
$pdf->SetFont('Arial','',7);
$linea+=5;
$pdf->SetX(170);
$pdf->Cell(20,0,'Realizado el '.date('d/m/Y h:i A'),0,0,'L'); 
//Títulos de las columnas

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
