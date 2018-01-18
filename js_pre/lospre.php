<?php
session_start();

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
		$consulta="select count(fechanomina) as numrows from nominas where (visible = 1) and (concepto != '039') ";
		// echo $consulta;
		$con=$db_con->prepare($consulta);
		$count_query   = $con->execute();
		$numrows=$con->fetch(PDO::FETCH_ASSOC);
		$numrows=$numrows['numrows'];
		$total_pages = ceil($numrows/$per_page);
		$reload = 'regt.php';
		//consulta principal para recuperar los datos
		$paginas="SELECT *, DATE_FORMAT(fechanomina,'%d/%m/%Y') as hoy, DATE_FORMAT(fecha_registro,'%d/%m/%Y %H:%i') as regis FROM nominas where (visible = 1) and (concepto != '039')  ORDER BY fechanomina DESC LIMIT $offset,$per_page ";
		$con=$db_con->prepare($paginas);
		$query = $con->execute();
		// echo $paginas;
		
		if ($numrows>0){
			?>
		<table class="table table-bordered">
			  <thead>
				<tr>
				  <th>Fecha N&oacute;mina</th>
				  <th>Registrada el</th>
				  <th>Registros</th>
				  <th>Monto Bs.</th>
				  <th></th>
				</tr>
			</thead>
			<tbody>
			<?php
			while($row = $con->fetch(PDO::FETCH_ASSOC)){
				?>
				<tr>
					<td><?php echo $row['hoy'];?></td>
					<td><?php echo $row['regis'];?></td>
					<td><?php echo number_format($row['regis'],0,',','.');?></td>
					<td align="pull-right"><?php echo number_format($row['montocotizacion'],2,'.',',');?></td>
					<td align="center">
					<?php
						echo "<a target='_blank' class='btn large btn-success' href='verpdf.php?archivo=".$row['registro'].".pdf&prestamo=1'>";
						echo "<i class='glyphicon glyphicon-print'></i> Nomina</a></td>";
						echo "<td align='center'><a target='_blank' class='btn large btn-info' href='verpdf.php?archivo=".$row['registro']."&prestamo=2.pdf'>";
						echo "<i class='glyphicon glyphicon-print'></i> Detalle de Descuento</a>";
/*
						<button type="button" class="btn btn-info" data-toggle="modal" data-target="#dataUpdate" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-nombre="<?php echo $row['nom_tit']?>" data-apellido="<?php echo $row['ape_tit']?>" data-nacimiento="<?php echo $row['fechanac']?>" data-habitacion="<?php echo $row['dir_hab']?>"><i class='glyphicon glyphicon-edit'></i> Modificar</button>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-nombre="<?php echo $row['nom_tit']. ', '.$row['ape_tit']?>" ><i class='glyphicon glyphicon-trash'></i> Eliminar</button>
*/
					?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
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
