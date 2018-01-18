<?php
session_start();
/*
error_reporting(E_ALL);
ini_set('display_errors','1');
/*-----------------------
Autor: Obed Alvarado
http://www.obedalvarado.pw
Fecha: 12-06-2015
Version de PHP: 5.6.3
----------------------------*/

	# conectare la base de datos
	include_once('../funciones.php');
	$mensajes = $errors []= "";
	include_once('../dbconfig.php');
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	$action='ajax';
	if($action == 'ajax'){
		include '../pagination.php'; //incluir el archivo de paginación
		//las variables de paginación
		$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
		$per_page = 10	; //la cantidad de registros que desea mostrar
		$adjacents  = 4; //brecha entre páginas después de varios adyacentes
		$offset = ($page - 1) * $per_page;
		//Cuenta el número total de filas de la tabla*/
		// $count_query   = mysqli_query($con,"SELECT count(*) AS numrows FROM countries ");
		$estatus_pendiente=1;
		$consulta="select count(cue_codigo) as numrows from cuentas ";
		// echo $consulta;
		$con=$db_con->prepare($consulta);
		$count_query   = $con->execute();
		// if ($row= mysqli_fetch_array($count_query)){$numrows = $row['numrows'];}
		$numrows=$con->fetch(PDO::FETCH_ASSOC);
		$numrows=$numrows['numrows'];
		$total_pages = ceil($numrows/$per_page);
		$reload = 'cuentas.php';
		//consulta principal para recuperar los datos
		$paginas="select cue_codigo, cue_nombre, cue_saldo, cue_nivel, (cue_saldo-(cue_cre01+cue_cre02+cue_cre03+cue_cre04+cue_cre05+cue_cre06+cue_cre07+cue_cre08+cue_cre09+cue_cre10+cue_cre11+cue_cre12)+(cue_deb01+cue_deb02+cue_deb03+cue_deb04+cue_deb05+cue_deb06+cue_deb07+cue_deb08+cue_deb09+cue_deb10+cue_deb11+cue_deb12)) as cue_actual FROM cuentas ORDER BY cue_codigo LIMIT $offset,$per_page ";
		$con=$db_con->prepare($paginas);
		$query = $con->execute();
		// echo $paginas;
		
		if ($numrows>0){
			?>
		<table class="table table-bordered">
			  <thead>
				<tr>
				  <th>C&oacute;digo</th>
				  <th>Descripci&oacute;n</th>
				  <th>Saldo Inicial</th>
				  <th>Saldo Actual</th>
				  <th></th>
				</tr>
			</thead>
			<tbody>
			<?php
			while($row = $con->fetch(PDO::FETCH_ASSOC)){
				?>
				<tr>
					<td><?php echo $row['cue_codigo'];?></td>
					<td><?php echo substr($row['cue_nombre'],0,40);?></td>
					<td><?php echo number_format($row['cue_saldo'],2,'.',',');?></td>
					<td><?php echo number_format($row['cue_actual'],2,'.',',');?></td>
					<td>
						<button type="button" class="btn btn-info" data-toggle="modal" data-target="#dataUpdate" data-id="<?php echo $row['cue_codigo']?>" data-codigo="<?php echo $row['cue_codigo']?>" data-nombre="<?php echo $row['cue_nombre']?>" data-codigosudeca="<?php echo $row['codigosudeca']?>" data-nombresudeca="<?php echo $row['nombresudeca']?>"><i class='glyphicon glyphicon-edit'></i> Modificar</button>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $row['cue_codigo']?>" data-codigo="<?php echo $row['cue_codigo']?>" data-nombre="<?php echo $row['cue_nombre']?>" ><i class='glyphicon glyphicon-trash'></i> Eliminar</button>
						<?php
						if ($row['cue_nivel'] == 7)
						{
						?>
						<button type="button" class="btn btn-default" data-toggle="modal" data-target="#dataPrint" data-id="<?php echo $row['cue_codigo']?>" data-codigo="<?php echo $row['cue_codigo']?>" data-nombre="<?php echo $row['cue_nombre']?>" ><i class='glyphicon glyphicon-print'></i> Anal&iacute;tico</button>
						<?php
						}
						?>
					</td>
						<?php 
						?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
<!--
	<form action="opciones.php" method="POST">
		<div class="col-md-6">
		<button class="btn btn-danger" value="Regresar" name="Regresar">Regresar al Men&uacute;</button>
		</div>
	</form>
-->
	<?php
			
		} else {
			mensaje(array(
				"titulo"=>"Aviso!!!",
				"tipo"=>"warning",
				"texto"=>"<h4>Aviso!!!</h4> No hay datos para mostrar",
				));
		}
		?>
		<div class="table-pagination pull-left">
			<h3 class='text-right'>		
				<button type="button" class="btn btn-default" data-toggle="modal" data-target="#dataRegister"><i class='glyphicon glyphicon-plus'></i> Agregar</button>
			</h3>
		</div>
		<div class="table-pagination pull-right">
			<?php echo paginate($reload, $page, $total_pages, $adjacents);?>
		</div>
		<?php
	}
		
?>
