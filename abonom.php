<?php
// ALTER TABLE `nominas` ADD `cerrada` INT(1) NOT NULL DEFAULT '0' AFTER `montocotizacion`;
// INSERT INTO `configuracion` (`parametro`, `nombre`, `registro`) VALUES ('CodigoSuma', '039', NULL);
include("home.php");
?>
<head>
	<script type="text/javascript">// src="javascript.js"> 
		function xconfirmarprocesarnomina() 
		{
			return confirm("¿Está seguro que desea cerrar esta Nomina?")
		}
	</script>
</head>
<?php
extract($_GET);
extract($_POST);
extract($_SESSION);
if (!isset($_POST['fecha']))
{
	ver_nominas($db_con);
}
else
{
	procesar_nomimna($db_con, $_POST['fecha'], $_POST['fechafor']);
}

function procesar_nomimna($db_con, $fecha, $ff)
{
	?>
	<script language="javascript">
	//Creo una función que imprimira en la hoja el valor del porcentanje asi como el relleno de la barra de progreso
		function callprogress(vValor){
		 document.getElementById("progress-txt").innerHTML = vValor;
		 document.getElementById("progress-txt").innerHTML = '<div class="progress-bar" role="progressbar" style="width:'+vValor+'%; min-width:10%">'+vValor+'%</div>';
		}
	</script>
	<?php
	try
	{
		echo '<div class="col-md-4">';
		mensaje(array(
			'tipo'=>'info',
			'titulo'=>'Información',
			'texto'=>'Recopilando informaci&oacute;n de N&oacute;mina al '.$ff
			));
		echo '</div>';
		$db_con->begintransaction();
		$sql="select * from cotizacionesxcobrar where fecha=:fecha and cobrado = :cero order by cedula, concepto";
		$archivo=$db_con->prepare($sql);
		$archivo->execute(array(
			":fecha"=>$fecha,
			":cero"=>0,
			));
		$registros = $archivo->rowCount();
		$ValorTotal=$registros;
		$cuantos=0;
		echo '<div class="row">';
			echo '<div class="col-md-4">';
				echo 'Proceso Interno <div id="progress-txt" class="progress  progress-bar-success">';
					// echo '<div id="progress-bs" class="progress-bar" role="progressbar" style="width:30%; min-width:10%">';
					echo '<div class="progress-bar" role="progressbar" style="width:30%">';
						echo '0%';
					echo '</div>';
					echo '<span class="sr-only"></span>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		$ip = la_ip();
		$momento = ahora($db_con)['ahora'];
		while ($registro = $archivo->fetch(PDO::FETCH_ASSOC))
		{
			$cuantos++;
			$porcentaje = $cuantos * 100 / $ValorTotal; //saco mi valor en porcentaje
			echo "<script>callprogress(".round($porcentaje).")</script>"; 
			flush(); 
			ob_flush();
			$monto 		= $registro['montocotizacion'];
			$concepto 	= $registro['concepto'];
			$cedula 	= $registro['cedula'];
			$nroreg 	= $registro['idregistro'];
			$referencia = $registro['referencia'];
			$sumar 		= codigosuma($db_con, $concepto);
			if ($sumar == true)
			{
				if ($concepto == '039')
				{
					$sql = "update titulares set acumbs = acumbs + :monto, numcuota = numcuota+1, ult_cotizacion = :fecha, cotizacion = :monto where cedula = :cedula ";
					$titular=$db_con->prepare($sql);
					$titular->execute(array(
						":fecha"=>$fecha,
						":cedula"=>$cedula,
						":monto"=>$monto,
						));
				}
			}
			else
			{
				$sql = "select cuota, cuota_especial from prestamos where cedula = :cedula and referencia = :referencia ";
				$prestamo=$db_con->prepare($sql);
				$prestamo->execute(array(
					":cedula"=>$cedula,
					":referencia"=>$referencia,
					));
				$pr = $prestamo->fetch(PDO::FETCH_ASSOC);
				if ($pr['cuota'] == $monto) // pago de cuota normal
				{
					$especial = 0;
					$sql = "update prestamos set montopagado = montopagado + :monto, ultcan_sdp = ultcan_sdp+1 where cedula = :cedula and referencia = :referencia ";
				}
				else // pago de cuota especial
				{
					$especial = 1;
					$sql = "update prestamos set montopagadoespecial_ucla = montopagadoespecial_ucla + :monto, ultcan_especial = ultcan_especial +1 where cedula = :cedula and referencia = :referencia ";
				}
				$prestamo=$db_con->prepare($sql);
				$prestamo->execute(array(
					":cedula"=>$cedula,
					":monto"=>$monto,
					":referencia"=>$referencia,
					));
				$sql = "select monto_solicitado-montopagado as saldo, monto_solicitado-montopagadoespecial as saldoespecial  from prestamos where cedula = :cedula and referencia = :referencia ";
				$prestamo=$db_con->prepare($sql);
				$prestamo->execute(array(
					":cedula"=>$cedula,
					":referencia"=>$referencia,
					));
				$pr = $prestamo->fetch(PDO::FETCH_ASSOC);
				if (($pr['saldo'] < 0) and ($pr['saldoespecial'] < 0)) // ya pago
				{
					$sql = "update prestamos set status :cancelo where cedula = :cedula and referencia = :referencia ";
					$prestamo=$db_con->prepare($sql);
					$prestamo->execute(array(
						":cedula"=>$cedula,
						":referencia"=>$referencia,
						":cancelo"=>'C',
						));
				}
				$sql = "update cotizacionesxcobrar set cobrado = :uno, ip_modificado = :ip, fecha_modificado = :momento where idregistro = :nroreg";
					$cotiza=$db_con->prepare($sql);
					$cotiza->execute(array(
						":uno"=>1,
						":ip"=>$ip,
						":momento"=>$momento,
						":nroreg"=>$nroreg
						));

			}
		}
		$sql = "update nominas set cerrada = 1 where fechanomina = :fecha and visible = 1";
		$cotiza=$db_con->prepare($sql);
		$cotiza->execute(array(
				":fecha"=>$fecha,
			));
		$db_con->commit();
		echo '<div class="col-md-8">';
 		mensaje(['tipo'=>'success','titulo'=>'Información','texto'=>'<h2>Proceso Finalizado</h2>']);
 		echo '</div>';
	}
	catch (PDOException $e) 
	{
		$db_con->rollback();
 		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage()]);
		die('Fallo call'. $e->getMessage());
	}
}

function ver_nominas($db_con)
{
	$sql="SELECT fechanomina, DATE_FORMAT(fechanomina,'%d/%m/%Y') as fnom, sum(montocotizacion) as sumatoria FROM `nominas` WHERE visible = 1 and cerrada = 0 group by fechanomina order by fechanomina desc";
	$stmt=$db_con->prepare($sql);
	$stmt->execute();
	if ($stmt->rowCount() > 0)
	{
	?>
	<div class="body-container">
	<div class="container">
		<div class="row">
		<div class="col-xs-6" class="text-center btn-inverse">
		<table class="table table-striped table-bordered table-hover" id="dataTables-example">
			<thead>
				<tr>
					<th>Fecha N&oacute;mina</th>
					<th>Monto Bs.</th>
					<th>
					</th>
				</tr>
			</thead>
			<tbody>
	<?php 
		while ($rg = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			echo '<tr>';
			echo '<td>'.$rg['fnom'].'</td>';
			echo '<td>'.number_format($rg['sumatoria'],2,'.',',').'</td>';
			echo '<form id="form1" name="form1" action="abonom.php" method="POST">'; // " onSubmit="return xconfirmarprocesarnomina(form1)>"';
				echo '<input type="hidden" name="fecha" value = "'.$rg['fechanomina'].'">';
				echo '<input type="hidden" name="fechafor" value = "'.$rg['fnom'].'">';
//					echo " <input class='btn btn-default' type='submit' name='boton' value=\" >> \">";
				echo '<td><input type="submit" class="btn btn-info" name="boton" value="Procesar"></td>'; //<i class="glyphicon glyphicon-edit"></i> 
			echo '</form>';
		}
	?>
			</tbody>
		</table>
		</div>
		</div>
	</div>
	</div>
	<?php
	}
	else
		echo '<span class="label label-danger">No se han conseguido resultados con los datos indicados</span>';
}

function codigosuma($db_con, $codigo)
{
	$sql="select * from configuracion where parametro = 'CodigoSuma' and nombre = :codigo";
	$archivo=$db_con->prepare($sql);
	$archivo->execute(array(
		":codigo"=>$codigo,
		));
	$registros = $archivo->rowCount();
	return ($registros > 0);
}
?>
