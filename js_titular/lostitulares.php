<?php
session_start();
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
		$consulta="select count(cedula) as numrows from titulares ";
		// echo $consulta;
		$con=$db_con->prepare($consulta);
		$count_query   = $con->execute();
		$numrows=$con->fetch(PDO::FETCH_ASSOC);
		$numrows=$numrows['numrows'];
		$total_pages = ceil($numrows/$per_page);
		$reload = 'regt.php';
		//consulta principal para recuperar los datos
		$paginas="SELECT *, DATE_FORMAT(now(),'%m/%d/%Y') as hoy, DATE_FORMAT(date_sub(now(),interval (18*365) day),'%m/%d/%Y') AS los18, DATE_FORMAT(fechanac,'%m/%d/%Y') as nacio, DATE_FORMAT(ing_ucla,'%m/%d/%Y') as ingucla, DATE_FORMAT(ing_ipsta,'%m/%d/%Y') as ingipsta, DATE_FORMAT(inc_nomina,'%m/%d/%Y') as ingnomina FROM titulares ORDER BY cedula LIMIT $offset,$per_page ";
		$con=$db_con->prepare($paginas);
		$query = $con->execute();
		// echo $paginas;
		
		if ($numrows>0){
			?>
<!-- busqueda -->
					<div id="resultado"></div>

					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<form class="form-inline" name="search" role="form" method="POST" onkeypress="return event.keyCode != 13;">
									<!-- form-horizontal div class="input-group col-sm-11"> -->
										<label for="busqueda">Su busqueda</label>
										<input id="busqueda" name="busqueda" type="text" class="form-control" placeholder="Quiero buscar..." autocomplete="off"/>
											<button type="button" class="btn btn-default btnSearch">
												<span class="glyphicon glyphicon-search"> </span>
											</button> 
								</form>
						</div>
					</div>
					<script>$(".tablesearch").hide();</script>

					<script type="text/javascript">
					    $(document).ready(function()
					    {
					        //comprobamos si se pulsa una tecla
					        $("#busqueda").keyup(function(e){
					                                       
					              //obtenemos el texto introducido en el campo de búsqueda
					              consulta = $("#busqueda").val();
					              //hace la búsqueda                                                                
					              $.ajax({
					                    type: "POST",
								url: "js_titular/php/search.php",
								data: { query: consulta },
					                    dataType: "html",
					                    beforeSend: function(){
					                    //imagen de carga
							//				$(".tablesearch").fadeOut(300);
					                    	$("#resultado").html("<img src='loader.gif' />");
					                    },
					                    error: function(){
					                    alert("error petición ajax");
					                    },
					                    success: function(data){                                                    
										//	$(".tablesearch").fadeIn(300);
										//	$(this).data('timer', setTimeout(search, 100));

						                    $("#resultado").empty(); $("#resultado").append(data);
							                    //seleccionamos de la lista
						                    var lista = $('div#resultado');
						                    lista.bind("mousedown", function (e) {
						                    e.metaKey = false;
						                    }).selectable({
							                    stop: function () {
								                    var result = $("input#busqueda");
								                    var fakeText = $('p.hidden-tips-text').empty();
								                    $(".ui-selected", this).each(function () {
									                    var index = $(this).text();
									                    fakeText.append((index) + "");
									                });
								                    result.val(fakeText.text());
						        				}
					    					});          
					                  	}
					              	});       
					        	});                                                    
					    });
					  </script>
<!-- fin busqueda -->
		<table class="table table-striped table-bordered table-hover" id="dataTables-example">
			  <thead>
				<tr>
				  <th>C&eacute;dula</th>
				  <th>Apellido(s)</th>
				  <th>Nombre(s)</th>
				  <th>Acumulado Bs.</th>
				  <th># Cuota</th>
				  <th>FREPAI</th>
				  <th>Status</th>
				  <th></th>
				</tr>
			</thead>
			<tbody>
			<?php
			while($row = $con->fetch(PDO::FETCH_ASSOC)){
				?>
				<tr>
					<td><?php echo $row['cedula'];?></td>
					<td><?php echo substr($row['ape_tit'],0,20);?></td>
					<td><?php echo substr($row['nom_tit'],0,20);?></td>
					<td align="pull-right"><?php echo number_format($row['acumbs'],2,'.',',');
					// ver si tiene ahorros frepai lo muestro
					$cedula = $row['cedula'];
					$sql2="select disponible, status, aporte_ord from frepai where cedula=:cedula";
					$frepai=$db_con->prepare($sql2);
					$frepai->execute(array(":cedula"=>$cedula));
					$muestro = $frepai->rowCount();
					echo '<td>'.number_format($row['numcuota'],0,'.',',').'</td>';
/*
					if ($muestro == 1)
					{
						$rfre=$frepai->fetch(PDO::FETCH_ASSOC);
					 	echo "<span class='badge ".($rfre['status']=='ACTIVO'?'btn-success':'btn-danger')."'>".$rfre['disponible']." </span>";
					}
					?></td>
					if ($muestro == 1)
					 	echo "<span class='badge ".($rfre['status']=='ACTIVO'?'btn-success':'btn-danger')."'>".$rfre['aporte_ord']." </span>";
*/
					echo '<td>';
					if ($muestro == 1)
					{
						$rfre=$frepai->fetch(PDO::FETCH_ASSOC);
						echo '<button type="button" class="btn btn-success btn-circle" title="Aporte Mensual = '.$rfre['aporte_ord'].'Ahorros = '.$rfre['disponible'];
						//echo "><span class='badge ".($rfre['status']=='ACTIVO'?'btn-success':'btn-danger')."'>".$rfre['disponible']." </span>";
						echo '"><i class="fa fa-check"></i></button>';
						// echo '<button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Popover on top';	// no me funciona

//					 	echo "<span class='badge ".($rfre['status']=='ACTIVO'?'btn-success':'btn-danger')."'>".$rfre['disponible']." </span>";
//					 	echo "<span class='badge ".($rfre['status']=='ACTIVO'?'btn-success':'btn-danger')."'>".$rfre['aporte_ord']." </span>";
					}
					else 
						echo '<button type="button" class="btn btn-warning btn-circle"><i class="fa fa-times"></i></button>';
					echo '</td>';
					echo '<td>';
					echo $row['status'];
					$status = strtoupper($row['status']);
					if (($status == 'ACTIVO') or ($status == 'JUBILA'))
						echo '<button type="button" class="btn btn-success btn-circle" ><i class="fa  fa-arrow-circle-up "></i></button>';
					else 
						echo '<button type="button" class="btn btn-default btn-circle" ><i class="fa f fa-arrow-circle-down"></i></button>';
					echo '</td>';

					?>
					<td>
						<button type="button" class="btn btn-info" data-toggle="modal" data-target="#dataUpdate" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-nombre="<?php echo $row['nom_tit']?>" data-apellido="<?php echo $row['ape_tit'];?>" data-nacimiento="<?php echo $row['fechanac']?>" data-habitacion="<?php echo $row['dir_hab']?>" data-telhabitacion="<?php echo $row['telhabitacion']?>" data-telcelular="<?php echo $row['telcelular']?>" data-teltrabajo="<?php echo $row['teltrabajo']?>" data-email="<?php echo $row['email']?>" data-cuenta="<?php echo $row['cuenta']?>" data-ingucla="<?php echo $row['ingucla']?>" data-ingipsta="<?php echo $row['ingipsta']?>" data-inclnomina="<?php echo $row['ingnomina']?>"   ><i class='glyphicon glyphicon-edit'></i> Modificar</button>

						<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#dataStatus" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-status="<?php echo $row['status']?>" data-nombre="<?php echo $row['nom_tit']. ', '.$row['ape_tit']?>" ><i class='glyphicon glyphicon-refresh '></i> Cambiar Status</button>

						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $row['cedula']?>" data-cedula="<?php echo $row['cedula']?>" data-nombre="<?php echo $row['nom_tit']. ', '.$row['ape_tit']?>" ><i class='glyphicon glyphicon-trash'></i> Eliminar</button>
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
    <!-- Page-Level Demo Scripts - Tables - Use for reference 
    <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });
    </script>
    -->
