<?php
// 192.168.0.6/frepai/js/calpre.php?montoprestamo=100&num_cuotas=06+selected+&divisible=12&tipo_interes=AMORTIZADA&f_ajax=Calcular+Cuota&descontar_interes=0&monto_futuro=0&p_interes=12&montoespecial=20&num_cuotase=2
session_start();
include("../dbconfig.php");
$prueba='No';
if ($prueba == 'No')
{
	$p_interes=$_POST["p_interes"];
	$num_cuotas=$_POST["num_cuotas"];
	$num_cuotase=$_POST["num_cuotase"];
	$montoespecial=$_POST["montoespecial"];
	$montoprestamo=$_POST["montoprestamo"]-$_POST["montoespecial"];
	$divisible=$_POST["divisible"];
	$tipo_interes=strtoupper($_POST["tipo_interes"]);
	$descontar_interes=$_POST["descontar_interes"];
	$monto_futuro=$_POST["monto_futuro"];
}
else
{
	$p_interes=$_GET["p_interes"];
	$num_cuotas=$_GET["num_cuotas"];
	$num_cuotase=$_GET["num_cuotase"];
	$montoprestamo=$_GET["montoprestamo"]-$_GET["montoespecial"];
	$montoespecial=$_GET["montoespecial"];
	$divisible=$_GET["divisible"];
	$tipo_interes=strtoupper($_GET["tipo_interes"]);
	$descontar_interes=$_GET["descontar_interes"];
	$monto_futuro=$_GET["monto_futuro"];
}
if (($tipo_interes)=='NOAPLICA') {
	$cuota = number_format(($montoprestamo/$num_cuotas),2,'.',''); 
	$cuotae = number_format(($montoespecial/$num_cuotase),2,'.',''); 
	$interes = 0.00;
}
else if (($tipo_interes)=='DIRECTO_FUTURO') {
	$interes = number_format(directo($p_interes,$num_cuotas,$montoprestamo,$divisible),2,'.','');
	$interes= number_format(($montoprestamo*($p_interes/100)),2,'.','');
	$cuota = number_format((($montoprestamo+$interes)/$num_cuotas),2,'.',''); 

	$interese = number_format(directo($p_interes,$num_cuotase,$montoespecial,$divisible),2,'.','');
	$interese= number_format(($montoespecial*($p_interes/100)),2,'.','');
	$cuotae = number_format((($montoespecial+$interes)/$num_cuotase),2,'.',''); 
}
else if (($tipo_interes)=='DIRECTO') {
	$cuota = number_format(($montoprestamo/$num_cuotas),2,'.',''); 
	$interes = number_format(directo($p_interes,$num_cuotas,$montoprestamo,$divisible),2,'.','');

	$cuotae = number_format(($montoespecial/$num_cuotas),2,'.',''); 
	$interese = number_format(directo($p_interes,$num_cuotase,$montoespecial,$divisible),2,'.','');
}
else if (($tipo_interes)=='AMORTIZADA') {
	$cuota = number_format(cal2int($p_interes,$num_cuotas,$montoprestamo,$divisible),2,'.','');
	$interes= number_format(calint($montoprestamo,$p_interes,$num_cuotas,$divisible),2,'.','');

	$cuotae = number_format(cal2int($p_interes,$num_cuotase,$montoespecial,$divisible),2,'.','');
	$interese= number_format(calint($montoespecial,$p_interes,$num_cuotase,$divisible),2,'.','');
}
else {
	$cuota = number_format(cal2int($p_interes,$num_cuotas,$montoprestamo,$divisible),2,'.','');
	$interes= number_format(calint($montoprestamo,$p_interes,$num_cuotas,$divisible),2,'.','');

	$cuotae = number_format(cal2int($p_interes,$num_cuotase,$montoespecial,$divisible),2,'.','');
	$interese= number_format(calint($montoespecial,$p_interes,$num_cuotase,$divisible),2,'.','');
}
if ((($descontar_interes == 0) and ($tipo_interes)=='AMORTIZADA') or (($descontar_interes == 0) and ($tipo_interes)=='NO APLICA'))
	$interes = 0.00;

// $cuota = number_format(cal2int($p_interes,$num_cuotas,$montoprestamo,$divisible),2,'.','');
// $interes= number_format(calint($montoprestamo,$p_interes,$num_cuotas,$divisible),2,'.','');
$gtoadm=restaradministrativos($montoprestamo, $db_con);
$neto=(($montoprestamo+$montoespecial)+($descontar_interes==1?($interes+$interese):0))-$gtoadm;
$diferencia=$montoprestamo; // -$montoespecial;
/*
if (($monto_futuro)!=0) {
	$cuota=$montoprestamo/$num_cuotas;
}
*/
/*
if (($tipo_interes)=='DIRECTOFUTURO') {
	$interes = 0.00;
	$neto=(($montoprestamo)-$gtoadm);
}
*/
//echo '<?xml version="1.0">'; //  encoding="utf-8">';
// echo '<?xml version="1.0" encoding="ISO-8859-1">';
// echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; 
//echo '<script>document.getElementById("cuota").value= '.$cuota.'</script>';
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="utf-8"?>';
echo "<resultados>";
echo utf8_encode("<cuota>$cuota</cuota>");		// sirve asi y como esta abajo tambien
echo utf8_encode("<cuotae>$cuotae</cuotae>");		
//echo "<cuota>".$interes."</cuota>";
echo "<interes_diferido>".$interes."</interes_diferido>";
echo "<montoneto>".$neto."</montoneto>";
echo "<gastosadministrativos>".$gtoadm."</gastosadministrativos>";
echo "<tipo_interes>".$interes."</tipo_interes>";
echo "<diferencia>".$diferencia."</diferencia>";
echo "</resultados>";

function cal2int($interes, $mcuotas, $mmonpre_sdp, $factor_divisible = 12,$z=0,$i2=0)
{
	if ($interes > 0) {
			$i = ((($interes / 100)) / $factor_divisible);
//			echo 'i = '.$i.'<br>';
			$i2 = $i;
//			$_SESSION['i2']=$i2;
			$i_ = 1 + $i;
//			echo 'i_ = '.$i_.'<br>';
			$i_ = pow($i_,$mcuotas); 	// exponenciacion 
			$i_ = 1 / $i_;
			$i__ = 1 - $i_;
//			echo 'i__ = '.$i__.'<br>';
			$i___ = $i / $i__;
//			echo 'i___ = '.$i___.'<br>';
			$z = $mmonpre_sdp * $i___;
			}
		if ($interes ==0)
			$z = $mmonpre_sdp / $mcuotas;
//
//	    ((1 + i)^n) - 1
//	i =-----------------
//	           i
//
//		$this->result=$z;
//		$_SESSION['z']=$z;
		return $z;
	}

function directo($interes, $mcuotas, $mmonpre_sdp, $factor_divisible = 12)
{
	if ($interes > 0) {
		$_interes=$mmonpre_sdp * ($interes / 100);
		$z = ($mmonpre_sdp + $_interes) / $mcuotas; 
	}
	if ($interes ==0)
		$z = $mmonpre_sdp / $mcuotas;
	return $z;
}

function revertido($interes,$mcuotas,$mmonpre_sdp,$factor_divisible = 12)
{
	if ($interes > 0) {
		$_interes=$mmonpre_sdp / ($interes / 100);
		$z = ($mmonpre_sdp + $_interes) / $mcuotas; 
		}
	if ($interes ==0)
		$z = $mmonpre_sdp / $mcuotas;
	return $z;
}
	
function calint($monto, $interes, $mcuotas, $factor_divisible = 12,$cuota2=0)
{
		$y=cal2int($interes,$mcuotas,$monto,$factor_divisible,$z,$i2);
		if ($cuota2 != 0) $z=$cuota2;
//		echo $z.'------------'. $i2.'<br>';
		$k = $ia = $cu22 = $ac = $tc = $ta = 0;
		$_c1 = $monto;
		$i1 = $interes;
		$n = $mcuotas;
//		echo $z.'<br>';
		for ($k=0;$k<$n;$k++)
		{
			$i1 = $_c1*$i2;
			$cu22 = $z - $i1;
			$_c1 = $_c1-$cu22;
			$ia = $ia + $i1;
			$ac = $ac + $cu22;
			$ta = $ta+ $z;
//			echo $_c1.' - '.$ac.' - '.$ta.' - '.$i1.' - '.$ia.' - '.$ac.'<br>';
		}
		return $ia;
}
	

function restaradministrativos($montoprestamo, $db_con)
{
	$sql_deduccion="select * from obligaciones where activar = 1";
	$a_deduccion=$db_con->prepare($sql_deduccion);
	$a_deduccion->execute();
	$d_obligatorias=0;
	while($r_deduccion=$a_deduccion->fetch(PDO::FETCH_ASSOC)) {
		if ($r_deduccion['porcentaje'] == 0)
			$monto_deduccion=$r_deduccion['monto'];
		else $monto_deduccion=($montoprestamo)*($r_deduccion['porcentaje']/100);
		$d_obligatorias+=$monto_deduccion;
		}
	return $d_obligatorias;
}
?>