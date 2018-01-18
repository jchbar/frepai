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
		$per_page = 8	; //la cantidad de registros que desea mostrar
		$adjacents  = 4; //brecha entre páginas después de varios adyacentes
		$offset = ($page - 1) * $per_page;
		//Cuenta el número total de filas de la tabla*/
		$estatus_pendiente=1;
		$consulta="select count(codigo) as numrows from ".$_SESSION['institucion']."tipoprestamo ";
		$con=$db_con->prepare($consulta);
		$count_query   = $con->execute();
		$numrows=$con->fetch(PDO::FETCH_ASSOC);
		$numrows=$numrows['numrows'];
		$total_pages = ceil($numrows/$per_page);
	//	 die( $consulta);
		$reload = 'tippre.php';
		//consulta principal para recuperar los datos
		$paginas="SELECT * FROM ".$_SESSION['institucion']."tipoprestamo ORDER BY codigo LIMIT $offset,$per_page ";
		// die( $paginas);
		$con=$db_con->prepare($paginas);
		$query = $con->execute();
		
		if ($numrows>0){
			?>
		<table class="table table-bordered">
			  <thead>
				<tr>
				  <th>C&oacute;digo</th>
				  <th>Descripci&oacute;n(s)</th>
				  <th>%</th>
				  <th># Cuota</th>
				  <th>Concepto UCLA</th>
				  <th></th>
				</tr>
			</thead>
			<tbody>
			<?php
			while($row = $con->fetch(PDO::FETCH_ASSOC)){
				?>
				<tr>
					<td><?php echo $row['codigo'];?></td>
					<td><?php echo $row['codigo'];?></td>
					<td align="pull-right"><?php echo number_format($row['interes'],2,'.',',');?></td>
					<td><?php echo number_format($row['nrocuotas'],0,'.',',');?></td>
					<td><?php echo substr($row['concepto'],0,3);?></td>
					<td>
						<button type="button" class="btn btn-info" data-toggle="modal" data-target="#dataUpdate" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-nombre="<?php echo $row['nom_tit']?>" data-apellido="<?php echo $row['ape_tit']?>" data-nacimiento="<?php echo $row['fechanac']?>" data-habitacion="<?php echo $row['dir_hab']?>"><i class='glyphicon glyphicon-edit'></i> Modificar</button>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $row['codigo']?>" data-codigo="<?php echo $row['codigo']?>" data-nombre="<?php echo $row['descripcion']?>" ><i class='glyphicon glyphicon-trash'></i> Eliminar</button>
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
<!-- <form action="opciones.php" method="POST">
		<div class="col-md-6">
		<button class="btn btn-danger" value="Regresar" name="Regresar">Regresar al Men&uacute;</button>
		</div>
	</form> -->
		<div class="table-pagination pull-left">
			<h3 class='text-right'>	
			<?php 
				$paginas="SELECT DATE_FORMAT(now(),'%m/%d/%Y') as hoy, DATE_FORMAT(date_sub(now(),interval (18*365) day),'%m/%d/%Y') AS los18";
				$con=$db_con->prepare($paginas);
				$query = $con->execute();
				$row = $con->fetch(PDO::FETCH_ASSOC);
				?> 
				<button type="button" class="btn btn-default" data-toggle="modal" data-target="#dataRegister" data-los18="<?php echo $row['los18']; ?>" data-hoy="<?php echo $row['hoy']; ?>"><i class='glyphicon glyphicon-plus'></i> Agregar</button>

			</h3>
		</div>
		<div class="table-pagination pull-right">
			<?php echo paginate($reload, $page, $total_pages, $adjacents);?>
		</div>
		
			<?php
			
		} else {
			mensaje(array(
				"titulo"=>"Aviso!!!",
				"tipo"=>"warning",
				"texto"=>"<h4>Aviso!!!</h4> No hay datos para mostrar",
				));
		}
	}
		
?>
