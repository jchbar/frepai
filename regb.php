<?php
session_start();
include("home.php");
include_once("dbconfig.php");
include_once("funciones.php");
$mostrarregresar=0;
try
{
	$sql="select DATE_FORMAT(now(),'%m/%d/%Y') as hoy, DATE_ADD(NOW(), INTERVAL 24 MONTH) AS futuro, DATE_SUB(NOW(), INTERVAL 6 WEEK) AS pasado";
	$stmt=$db_con->prepare($sql);
	$stmt->execute();
	$res=$stmt->fetch(PDO::FETCH_ASSOC);
	$hoy=$res['hoy'];
	$pasado=$res['pasado'];
	$futuro=$res['futuro'];
}
catch(PDOException $e)
{
		echo $e->getMessage();
		// echo 'Fallo la conexion';
}

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
if ($accion == 'Desaparece')
{
	if (isset($_POST['btn-elimina']))
	{
		$sql="delete from beneficiarios where id_registro=:idregistro";
		try
		{
			$resultado=$db_con->prepare($sql);
			$resultado->execute(array(
				":idregistro"=>$_POST['idregistro']
				));
			$accion='Buscar';
		}
		catch(PDOException $e){
			echo $e->getMessage();
				mensaje(array(
					"tipo"=>'danger',
					"texto"=>'<h1>Error Inesperado al eliminar... Contacto soporte </h1>',
					));
				die('');
			// echo 'Fallo la conexion';
		}
	}
//	else echo 'no hago nada';
	$accion='Buscar';
}
//----------------------------
if ($accion == 'Eliminar')
{
	mensaje(array(
		"tipo"=>'danger',
		"texto"=>'Eliminar Beneficiario al Titular '.detalle_titular($_POST['cedula'], $db_con).'',
		));
		$sql="select * from beneficiarios where id_registro = :idregistro";
		$resultado=$db_con->prepare($sql);
		$resultado->execute(array(
			":idregistro"=>$_POST[idregistro]
			));
		$registro = $resultado->fetch(PDO::FETCH_ASSOC);
		
		echo "<form action='regb.php?accion=Desaparece' name='form2' method='post' class='form-inline'>";
		    echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
				echo '<input type="hidden" name="cedula" id="cedula" value="'.$_POST['cedula'].'">';
				echo '<input type="hidden" name="idregistro" id="idregistro" value="'.$_POST['idregistro'].'" readonly="readonly">';
				echo '<label for ="cedulafam">Cedula del Familiar</label>';
				echo '<input class="form-control" placeholder="Cedula del Familiar" type="text" id="cedulafam" name="cedulafam" value="'.$registro['cedulafam'].'" required readonly="readonly">';
				echo '<label for ="apellido">Apellidos</label>';
				echo '<input class="form-control" placeholder="Apellido(s) del Familiar" type="text" id="apellido" name="apellido" value="'.$registro['cedulafam'].'" required readonly="readonly">';
				echo '<label for ="nombre">Nombres</label>';
				echo '<input class="form-control" placeholder="Nombre(s) del Familiar" type="text" id="nombre" name="nombre" value="'.$registro['nombres'].'" required readonly="readonly"><br>';
				
				echo '<label for ="parentesco">Parentesco</label>';
				echo '<select name="parentesco" size="1" disabled>';
				$sql="select * from configuracion where parametro='Parentesco' order by nombre"; 
				$resultado=$db_con->prepare($sql);
				$resultado->execute();
				while ($fila2 = $resultado->fetch(PDO::FETCH_ASSOC)) {
					echo '<option value="'.$fila2['nombre'].'"' .($fila2['nombre'] == $registro['parentesco']?' selected':'').'>'.$fila2['nombre'].'</option>'; }
				echo '</select>'; 

				echo '<label for ="fecha_nac">Fecha de Nacimiento </label>';
				$mifecha=explode('-',$registro['fechanac']);
				$mifecha=$mifecha[1].'/'.$mifecha[2].'/'.$mifecha[0];
				echo '<input class="form-control" placeholder="Nombre(s) del Familiar" type="text" id="nombre" name="nombre" value="'.$registro['fechanac'].'" required readonly="readonly"><br>';
/*
				?>
				<div class='input-group date' id='fechanac'>
					<input type='text' placeholder="Fecha de Nacimiento" id="fechanac" name="fechanac" class="form-control" readonly="readonly" value=""/>
				    <span class="input-group-addon">
				    	<span class="glyphicon glyphicon-calendar"></span>
				    </span>
				</div>

				<?php 
*/
				echo "<br><input class='btn btn-danger' type = 'submit' id='btn-elimina' name='btn-elimina' value = 'Eliminar'>";
				echo "<br><input class='btn btn-warning' type = 'submit' id='btn-cancela' name='btn-cancela' value = 'Cancelar'>";


			echo '</div>';
		echo '<form>';
}
//----------------------------
if ($accion == 'Actualizar')
{
	$cedulaemp=$_POST['cedula'];
	$cedulafam=$_POST['cedulafam'];
	$apellido=$_POST['apellido'];
	$parentesco=$_POST['parentesco'];
	$nombre=$_POST['nombre'];
	$fecha_nac=explode('/',$_POST['fechanac']);
	$fecha_nac=$fecha_nac[2].'-'.$fecha_nac[0].'-'.$fecha_nac[1];
	$ip = la_ip();
	$registro=ahora($db_con)['ahora'];
	if (strlen(trim(detalle_titular($_POST['cedula'], $db_con))) > 1)
	{
		$sql="update beneficiarios set cedulafam = :cedulafam, apellidos = :apellidos, nombres = :nombres, parentesco = :parentesco, fechanac = :fecha_nac, ip_registro = :ip_registro, fecha_registro = :fecha_registro where id_registro=:idregistro";
		try
		{
			$resultado=$db_con->prepare($sql);
			$resultado->execute(array(
				':cedulafam'=>$cedulafam,
				':apellidos'=>$apellido,
				':nombres'=>$nombre,
				':parentesco'=>$parentesco,
				':fecha_nac'=>$fecha_nac,
				':ip_registro'=>$ip,
				':fecha_registro'=>$registro,
				":idregistro"=>$_POST['idregistro']
				));
			$accion='Buscar';
		}
		catch(PDOException $e){
			echo $e->getMessage();
				mensaje(array(
					"tipo"=>'danger',
					"texto"=>'<h1>Error Inesperado al modificar... Contacto soporte </h1>',
					));
				die('');
			// echo 'Fallo la conexion';
		}
	}
}
//----------------------------
if ($accion == 'Modificar')
{
	mensaje(array(
		"tipo"=>'warning',
		"texto"=>'Modificar Beneficiario al Titular '.detalle_titular($_POST['cedula'], $db_con).'',
		));
		$sql="select * from beneficiarios where id_registro = :idregistro";
		$resultado=$db_con->prepare($sql);
		$resultado->execute(array(
			":idregistro"=>$_POST[idregistro]
			));
		$registro = $resultado->fetch(PDO::FETCH_ASSOC);
		
		echo "<form action='regb.php?accion=Actualizar' name='form2' method='post' class='form-inline'>";
		    echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
				echo '<input type="hidden" name="cedula" id="cedula" value="'.$_POST['cedula'].'">';
				echo '<input type="hidden" name="idregistro" id="idregistro" value="'.$_POST['idregistro'].'">';
				echo '<label for ="cedulafam">Cedula del Familiar</label>';
				echo '<input class="form-control" placeholder="Cedula del Familiar" type="text" id="cedulafam" name="cedulafam" value="'.$registro['cedulafam'].'" required>';
				echo '<label for ="apellido">Apellidos</label>';
				echo '<input class="form-control" placeholder="Apellido(s) del Familiar" type="text" id="apellido" name="apellido" value="'.$registro['cedulafam'].'" required>';
				echo '<label for ="nombre">Nombres</label>';
				echo '<input class="form-control" placeholder="Nombre(s) del Familiar" type="text" id="nombre" name="nombre" value="'.$registro['nombres'].'" required><br>';
				
				echo '<label for ="parentesco">Parentesco</label>';
				echo '<select name="parentesco" size="1">';
				$sql="select * from configuracion where parametro='Parentesco' order by nombre"; 
				$resultado=$db_con->prepare($sql);
				$resultado->execute();
				while ($fila2 = $resultado->fetch(PDO::FETCH_ASSOC)) {
					echo '<option value="'.$fila2['nombre'].'"' .($fila2['nombre'] == $registro['parentesco']?' selected':'').'>'.$fila2['nombre'].'</option>'; }
				echo '</select>'; 

				echo '<label for ="fecha_nac">Fecha de Nacimiento</label>';
				$mifecha=explode('-',$registro['fechanac']);
				$mifecha=$mifecha[1].'/'.$mifecha[2].'/'.$mifecha[0];
				echo $mifecha;
				?>
				<div class='input-group date' id='fechanac'>
					<input type='text' placeholder="Fecha de Nacimiento" id="fechanac" name="fechanac" class="form-control" />
				    <span class="input-group-addon">
				    	<span class="glyphicon glyphicon-calendar"></span>
				    </span>
				</div>

				<script type="text/javascript">
					$('input[name="fechanac"]').daterangepicker({
						"singleDatePicker": true,
						"startDate": <?php echo $mifecha; ?>, // "11/07/2016", 
						// "endDate": "<?php echo $pasado; ?>", // "11/30/2016", 
						//"minDate": button.data('los18'), // "11/01/2016",
						// "maxDate": <?php echo $futuro; ?> // "11/30/2016"
					}, function(start, end, label) {
					//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
					});
				</script>

				<?php 
				echo "<br><input class='btn btn-info' type = 'submit' value = 'Actualizar'>";


			echo '</div>';
		echo '<form>';
}
//----------------------------
if ($accion == 'Guardar')
{
	$cedulaemp=$_POST['cedula'];
	$cedulafam=$_POST['cedulafam'];
	$apellido=$_POST['apellido'];
	$parentesco=$_POST['parentesco'];
	$nombre=$_POST['nombre'];
	$fecha_nac=explode('/',$_POST['fecha_nac']);
	$fecha_nac=$fecha_nac[2].'-'.$fecha_nac[0].'-'.$fecha_nac[1];
	$ip = la_ip();
	$registro=ahora($db_con)['ahora'];
	if (strlen(trim(detalle_titular($_POST['cedula'], $db_con))) > 1)
	{
		$sql="insert into beneficiarios (cedulafam, cedulaemp, apellidos, nombres, parentesco, fechanac, ip_registro, fecha_registro) values (:cedulafam, :cedulaemp, :apellidos, :nombres, :parentesco, :fechanac, :ip_registro, :fecha_registro)";
		try
		{
			$resultado=$db_con->prepare($sql);
			$resultado->execute(array(
				':cedulafam'=>$cedulafam,
				':cedulaemp'=>$cedulaemp,
				':apellidos'=>$apellido,
				':nombres'=>$nombre,
				':parentesco'=>$parentesco,
				':fechanac'=>$fecha_nac,
				':ip_registro'=>$ip,
				':fecha_registro'=>$registro,
				));
			$accion='Buscar';
		}
		catch(PDOException $e){
			echo $e->getMessage();
				mensaje(array(
					"tipo"=>'danger',
					"texto"=>'<h1>Error Inesperado al almacenar... Contacto soporte </h1>',
					));
				die('');
			// echo 'Fallo la conexion';
		}
	}
}
//----------------------------
if ($accion == 'Agregar')
{
	mensaje(array(
		"tipo"=>'info',
		"texto"=>'Agregar Beneficiario al Titular '.detalle_titular($_POST['cedula'], $db_con).'',
		));
		echo "<form action='regb.php?accion=Guardar' name='form2' method='post' class='form-inline'>";
		    echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
				echo '<input type="hidden" name="cedula" id="cedula" value="'.$_POST['cedula'].'">';
				echo '<label for ="cedulafam">Cedula del Familiar</label>';
				echo '<input class="form-control" placeholder="Cedula del Familiar" type="text" id="cedulafam" name="cedulafam" value="" required>';
				echo '<label for ="apellido">Apellidos</label>';
				echo '<input class="form-control" placeholder="Apellido(s) del Familiar" type="text" id="apellido" name="apellido" value="" required>';
				echo '<label for ="nombre">Nombres</label>';
				echo '<input class="form-control" placeholder="Nombre(s) del Familiar" type="text" id="nombre" name="nombre" value="" required><br>';
				
				echo '<label for ="parentesco">Parentesco</label>';
				echo '<select name="parentesco" size="1">';
				$sql="select * from configuracion where parametro='Parentesco' order by nombre"; 
				$resultado=$db_con->prepare($sql);
				$resultado->execute();
				while ($fila2 = $resultado->fetch(PDO::FETCH_ASSOC)) {
					echo '<option value="'.$fila2['nombre'].'">'.$fila2['nombre'].'</option>'; }
				echo '</select>'; 

				echo '<label for ="fecha_nac">Fecha de Nacimiento</label>';
				?>
				<div class='input-group date' id='fecha_nac'>
					<input type='text' placeholder="Fecha de Nacimiento" id="fecha_nac" name="fecha_nac" class="form-control" />
				    <span class="input-group-addon">
				    	<span class="glyphicon glyphicon-calendar"></span>
				    </span>
				</div>

				<script type="text/javascript">
					$('input[name="fecha_nac"]').daterangepicker({
						"singleDatePicker": true,
						"startDate": <?php echo $hoy; ?>, // "11/07/2016", 
						// "endDate": "<?php echo $pasado; ?>", // "11/30/2016", 
						//"minDate": button.data('los18'), // "11/01/2016",
						// "maxDate": <?php echo $futuro; ?> // "11/30/2016"
					}, function(start, end, label) {
					//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
					});
				</script>

				<?php 
				echo "<br><input class='btn btn-info' type = 'submit' value = 'Guardar'>";


			echo '</div>';
		echo '<form>';
}
//----------------------------
if ($accion == 'Buscar')  {
	// echo "<form action='regb.php?accion=Actualizar' name='form1' method='post' class='form-inline'>";
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
		// $accion = 'Editar'; 


		// revisar si esta actualizado los datos de socios
		$hoy=date("Y-m-d", time());
		$ord="fecha_solicitud";
		$estacedula=$lacedula; // substr($cedula,0,4).'.'.substr($cedula,4,3).'.'.substr($cedula,7,3);
		$sql = "SELECT * FROM titulares WHERE (cedula = :estacedula)"; 
		try
		{
			$rs = $db_con->prepare($sql);
			$rs->execute(array(
				":estacedula"=>$estacedula,
				));
		}
		catch(PDOException $e){
			echo $e->getMessage();
			// echo 'Fallo la conexion';
		}
		if ($rs->rowCount() > 0) // existe
		{
			$rtit=$rs->fetch(PDO::FETCH_ASSOC);
			if (($rtit['status']=='ACTIVO') or ($rtit['status']=='JUBILA'))
			{
				// busco en frepai
			    echo '<div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">';
				// echo '<label for ="nombre">Titular</label>';
				echo '<input class="form-control" type="text" id="nombre" value="'.$rtit['ape_tit']. ' '.$rtit['nom_tit'].'" readonly="readonly">';
				// echo '<div class="table-pagination pull-right"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#dataRegister"><i class="glyphicon glyphicon-plus"></i> Agregar</button></div>';
				echo "<form action='regb.php?accion=Agregar' name='form2' method='post' class='form-inline'>";
					echo '<div class="table-pagination pull-right"><input type="submit" value="Agregar" class="btn btn-default">';
					// <i class="glyphicon glyphicon-plus"></i> Agregar</button>
					echo '</div>';
					// echo "<div class='table-pagination pull-right'><td class='centro'><a class='btn btn-default' href='regb.php?accion=Agregar&'><img src='imagenes/page_wizard.gif' width='16' height='16' border='0' title='Agregar Beneficiario' alt='Agregar Beneficiario' /></a></div>";
					echo '<input type="hidden" name="cedula" id="cedula" value="'.$lacedula.'">';
				echo '</form>';
				echo '</div>';	


				$sql="SELECT * FROM beneficiarios WHERE cedulaemp =:cedula";
				try
				{
					$rsf = $db_con->prepare($sql);
					$rsf->execute(array(
						":cedula"=>$estacedula,
						));
					?>
				    <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
						<table class="table table-bordered">
						<!-- table class="table table-bordered" width="500" border="1"-->
						<tr>
							<th class="small">Ced.Familiar</th>
							<th class="small">Apellidos</th>
							<th class="small">Nombres</th>
							<th class="small">Parentesco</th>
							<th class="small">Fecha Nac.</th>
							<th class="small"></th>
						</tr>
					<?php
					while ($filas=$rsf->fetch(PDO::FETCH_ASSOC)) 
					{
						echo '<tr>';
						echo '<td class="small">';
							echo $filas['cedulafam'];
						echo '</td>';
						echo '<td class="small">';
							echo $filas['apellidos'];
						echo '</td>';
						echo '<td class="small">';
							echo $filas['nombres'];
						echo '</td>';
						echo '<td class="small">';
							echo $filas['parentesco'];
						echo '</td>';
						echo '<td class="small">';
							echo convertir_fechadmy($filas['fechanac']);
						echo '</td>';
						echo '<td class="small">';

						echo "<form action='regb.php?accion=Eliminar' name='form3' method='post' class='form-inline'>";
							echo '<div class="table-pagination pull-right"><input type="submit" value="Eliminar" class="btn btn-danger">';
							// <i class="glyphicon glyphicon-plus"></i> Agregar</button>
							echo '</div>';
							// echo "<div class='table-pagination pull-right'><td class='centro'><a class='btn btn-default' href='regb.php?accion=Agregar&'><img src='imagenes/page_wizard.gif' width='16' height='16' border='0' title='Agregar Beneficiario' alt='Agregar Beneficiario' /></a></div>";
							echo '<input type="hidden" name="cedula" id="cedula" value="'.$lacedula.'">';
							echo '<input type="hidden" name="idregistro" id="idregistro" value="'.$filas['id_registro'].'">';
						echo '</form>';

						echo "<form action='regb.php?accion=Modificar' name='form2' method='post' class='form-inline'>";
							echo '<div class="table-pagination pull-right"><input type="submit" value="Modificar" class="btn btn-warning">';
							// <i class="glyphicon glyphicon-plus"></i> Agregar</button>
							echo '</div>';
							// echo "<div class='table-pagination pull-right'><td class='centro'><a class='btn btn-default' href='regb.php?accion=Agregar&'><img src='imagenes/page_wizard.gif' width='16' height='16' border='0' title='Agregar Beneficiario' alt='Agregar Beneficiario' /></a></div>";
							echo '<input type="hidden" name="cedula" id="cedula" value="'.$lacedula.'">';
							echo '<input type="hidden" name="idregistro" id="idregistro" value="'.$filas['id_registro'].'">';
						echo '</form>';

/*
						<button type="button" class="btn btn-info" data-toggle="modal" data-target="#dataUpdate" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-nombre="<?php echo $row['nom_tit']?>" data-apellido="<?php echo $row['ape_tit']?>" data-nacimiento="<?php echo $row['fechanac']?>" data-habitacion="<?php echo $row['dir_hab']?>"><i class='glyphicon glyphicon-edit'></i> Modificar</button>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-nombre="<?php echo $row['nom_tit']. ', '.$row['ape_tit']?>" ><i class='glyphicon glyphicon-trash'></i> Eliminar</button>
						<?php
*/
						echo '</td>';
						echo '</tr>';

					}
					?>
					</table>


						<?php
//						echo '<input type="hidden" id="fecha_ingreso" name="fecha_ingreso" value="'.$rfre['inscripcion'].'">';
						echo '<input type="hidden" id="existe" name="existe" value="2">';		
						echo '<input type="hidden" id="cedula" name="cedula" value="'.$cedula.'">';		
				}
				catch(PDOException $e){
					echo $e->getMessage();
					// echo 'Fallo la conexion';
				}
			}
			else mensaje(['tipo'=>'danger','titulo'=>'Aviso!!!','texto'=>'<h2>El Titular no se encuentra activo</h2>']);
		}
	}
	// echo '</form>';
}	// fin de ($accion == 'Buscar') 

if (!$accion) {
	echo "<form action='regb.php?accion=Buscar' name='form1' method='post'>";
	echo '<div class="form-group form-inline row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
    echo '<label for="cedula">C&eacute;dula </label>';
	echo '<input class="form-control" name="cedula" type="text" id="cedula" value=""  size="10" maxlength="10" />';
	echo "<input class='btn btn-info' type = 'submit' value = 'Buscar'>";
	echo '</div>';
	echo '</form>';
}	// fin de (!$accion) 

function detalle_titular($cedula, $db_con)
{
	$sql="select * from titulares where cedula = :cedula limit 1";
	try
	{
		$resultado=$db_con->prepare($sql);
		$resultado->execute(array(
			':cedula'=>$cedula
			));
		$registro=$resultado->fetch(PDO::FETCH_ASSOC);
		return trim($registro['apellidos']).', '.trim($registro['nombre']).' CI: '.$cedula;

	}
	catch(PDOException $e){
		echo $e->getMessage();
			mensaje(array(
				"tipo"=>'danger',
				"texto"=>'<h1>Error Inesperado... Contacto soporte </h1>',
				));
			die('');
		// echo 'Fallo la conexion';
	}
}
?>
