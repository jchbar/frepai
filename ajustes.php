<?php
session_start();
include("home.php");
include_once("dbconfig.php");
include_once("funciones.php");
$mostrarregresar=0;
?>
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
if ($accion == 'Buscar')  
{
	echo "<form action='ajustes.php?accion=Actualizar' name='form1' method='post' class='form-inline'>";
	extract($_POST);
	$lacedula = ceroizq(trim($_POST['cedula']),8);
	if (! $cedula) {
		$lacedula = $_SESSION['cedulasesion']; 
		}
	else 
		$_SESSION['cedulasesion']=$_POST['cedula'];
	if ($lacedula) 
	{ //  != ' ') {
		try
		{
			$sql="SELECT * FROM titulares where cedula = :lacedula";
			$result=$db_con->prepare($sql);
			$result->execute(array(":lacedula"=>$lacedula));
			$row= $result->rowCount(); // fetch(PDO::FETCH_ASSOC);
			if ($row < 1)
			{
				mensaje(array(
					"tipo"=>'danger',
					"texto"=>'<h1>Eeeeepa!!!! ese numero de cedula esta errado</h1>',
					));
				die('');
			}
			$rtit=$result->fetch(PDO::FETCH_ASSOC);
		    echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
			echo '<table class="table table-striped table-bordered table-hover" id="dataTables-example">';
		    // echo '<table class="table-bordered">';
			    echo '<tr>';
			    	echo '<th>Cedula </th><td>'.$lacedula.'</td>';
			    	echo '<th>Apellido(s) </th><td colspan="2">'.$rtit['ape_tit'].'</td>';
			    	echo '<th>Nombre(s) </th><td colspan="2">'.$rtit['nom_tit'].'</td>';
/*
			    echo '</tr>';
			    echo '<tr>';
			    	echo '<th>Ingreso UCLA </th><td>'.convertir_fechadmy($rtit['ing_ucla']).'</td>';
			    	echo '<th>Ingreso IPSTAUCLA </th><td colspan="2">'.convertir_fechadmy($rtit['ing_ipsta']).'</td>';
			    	echo '<th>Ingreso Nomina </th><td colspan="2">'.convertir_fechadmy($rtit['ing_nomina']).'</td>';
			    echo '</tr>';
/*
			    echo '<tr>';
			    	$cotiza_frepai=cotiza_frepai($lacedula, $db_con);
			    	echo '<th>Cotiza FREPAI </th><td>';
			    	if ($cotiza_frepai == 'Si')
			    	{
			    		$cotiza_frepai=datos_frepai($lacedula, $db_con);
			    		echo ' '.$cotiza_frepai['status'].'</td>';
				    	echo '<th>Aporte Ordinario</th><td>'.number_format($cotiza_frepai['aporte_ord'],2,'.',',').'</td>';
				    	echo '<th>Disponible </th><td>'.number_format($cotiza_frepai['disponible'],2,'.',',').'</td>';
			    	}
			    	else echo 'No </td>';
			    echo '</tr>';
			    echo '<tr>';
*/
			    	echo '<th>Estatus </th><td>'.$rtit['status'].'</td>';
/*
			    	echo '<th>Monto Acumulado </th><td>'.number_format($rtit['acumbs'],2,'.',',').'</td>';
*/
			    echo '</tr>';
			    compromisos($lacedula, $db_con);
		    echo '</table>';
		    echo '</div>';
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			// echo 'Fallo la conexion';
		}
	}
	echo '</form>';
}	// fin de ($accion == 'Buscar') 
if ($accion == 'Actualizar')  
{
	$registros = $_POST['registros'];
	extract($_POST);
	for ($i=0;$i<$registros;$i++)	
	{
		$variable='cuota'.($i+1);
		$variablee='cuotae'.($i+1);
		$id='id_registro'.($i+1);
		// echo 'variable '.$$variable .'variablee'.$$variablee. ' id '.$$id;
		$sql = "update prestamos set cuota = :variable, cuota_especial = :variablee where registro = :id";
		try
		{
			$rsf = $db_con->prepare($sql);
			$rsf->execute(array(
				":variable"=>$$variable,
				":variablee"=>$$variablee,
				":id"=>$$id,
			));
		}
		catch(PDOException $e)
		{
			echo $e->getMessage(); // echo 'Fallo la conexion';
		}
	}
	$accion = '';
}	// fin de ($accion == 'actualizar') 
		
if (!$accion) {
	echo "<form action='ajustes.php?accion=Buscar' name='form1' method='post'>";
	echo '<div class="form-group form-inline row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
    echo '<label for="cedula">C&eacute;dula </label>';
    $prueba = 1;
	echo '<input class="form-control" name="cedula" type="text" id="cedula" value="'.($prueba == 1?"09623660":'').'" size="10" maxlength="10" />';
	echo "<input class='btn btn-info' type = 'submit' value = 'Buscar'>";
	echo '</div>';
	echo '</form>';
}	// fin de (!$accion) 

function cotiza_frepai($cedula, $db_con)
{
	$sql="SELECT * FROM frepai WHERE cedula =:cedula";
	try
	{
		$rsf = $db_con->prepare($sql);
		$rsf->execute(array(
			":cedula"=>$cedula,
		));
		return ($rsf->rowCount() > 0?'Si':'No');  // existe en frepai
	}
	catch(PDOException $e)
	{
		echo $e->getMessage(); // echo 'Fallo la conexion';
	}
}

function datos_frepai($cedula, $db_con)
{
	$sql="SELECT aporte_ord, disponible, status FROM frepai WHERE cedula =:cedula";
	try
	{
		$rsf = $db_con->prepare($sql);
		$rsf->execute(array(
			":cedula"=>$cedula,
		));
		$res=$rsf->fetch(PDO::FETCH_ASSOC);
		return $res;  // existe en frepai
	}
	catch(PDOException $e)
	{
		echo $e->getMessage(); // echo 'Fallo la conexion';
	}
}

function compromisos($cedula, $db_con)
{
	$sql="SELECT *, prestamos.registro as id_registro FROM prestamos, proveedores WHERE (prestamos.cedula = :cedula) and (concepto = proveedores.codigo)";
	try
	{
		$rsf = $db_con->prepare($sql);
		$rsf->execute(array(
			":cedula"=>$cedula,
		));
		if ($rsf->rowCount() > 0)
		{
			echo '<tr>';
				echo '<td align="center" colspan="12"><strong>Compromisos</strong>';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<th>Referencia</th>';
				echo '<th>Concepto</th>';
				echo '<th>Solicitud</th>';
				echo '<th>1er Dcto</th>';
				echo '<th>Saldo</th>';
				echo '<th>Saldo Esp.</th>';
				echo '<th>Dcto Ord.</th>';
				echo '<th>Dcto Esp.</th>';
				echo '<th>#Cuota</th>';
				echo '<th>#Cuota Esp.</th>';
			echo '</tr>';
			$registros=0;
			while ($res=$rsf->fetch(PDO::FETCH_ASSOC))
			{
			echo '<tr>';
				$registros++;
				echo '<td>'.$res['referencia'].'</td>';
				echo '<td>'.$res['casa'].'</td>';
				echo '<td>'.convertir_fechadmy($res['fecha_solicitud']).'</td>';
				echo '<td>'.convertir_fechadmy($res['f_1cuota']).'</td>';
				echo '<td>'.number_format($res['monto_solicitado']-$res['montopagado'],2,'.',',').'</td>';
				echo '<td>'.number_format($res['montoespecial']-$res['montopagado_especial'],2,'.',',').'</td>';
				echo '<td><input type="number" id="cuota'.$registros.'" name="cuota'.$registros.'" value='.$res['cuota'].'></td>';
				echo '<td><input type="number" id="cuotae'.$registros.'" name="cuotae'.$registros.'" value='.$res['cuota_especial'].'></td>';
				echo '<td>'.$res['ultcan_sdp'].'/'.$res['nrocuotas'].'</td>';
				echo '<td>'.$res['ultcan_especial'].'/'.$res['nrocuotasespeciales'].'</td>';
				echo '<td><input type="hidden" id="id_registro'.$registros.'" name="id_registro'.$registros.'" value="'.$res["id_registro"].'"></td>';
			echo '</tr>';
			}
			echo '<tr><td colspan="10" align="center">';
			echo "<input type = 'hidden' value ='".$registros."' name='registros' id='registros'>";
			echo '<input class="btn btn-success" type="submit" name="procesar" value="Ejecutar Cambios"/>';
			echo '</td></tr>';
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage(); // echo 'Fallo la conexion';
	}
}
?>
