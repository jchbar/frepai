<form id="actualidarDatos">
<div class="modal fade" id="dataUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      	<div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	<h4 class="modal-title" id="exampleModalLabel">Modificar Cuenta Contable</h4>
   		</div>
     	<div class="modal-body">
			<input type="hidden" class="form-control" id="id" name="id" required maxlength="10">
			<!-- modificar -->
			<div class="form-group form-inline col-xs-12">
				<label for="apellido" class="sr-only control-label">Apellido(s) </label>
				<input type="text" placeholder="Apellido(s)" class="form-control" id="apellido" name="apellido" value="" maxlength="20" size="20">
				<label for="nombre" class="sr-only control-label">Nombre(s) </label>
				<input type="text" placeholder="Nombre(s)" class="form-control" id="nombre" name="nombre" value="" maxlength="20" size="20">
			</div>
			<div class="form-group form-inline">
				<label for="estado" class="control-label">Estado Civil:</label>
				<?php
					include_once('../dbconfig.php');
					$comando="select nombre from configuracion where parametro = 'Civil' order by parametro";
					echo '<select class="form-control" name="estado" id="estado" size="1">';
					$con=$db_con->prepare($comando);
					$con->execute();
					while($row = $con->fetch(PDO::FETCH_ASSOC))
						echo '<option '.$row['nombre'].' selected="selected"  value="'.$row['nombre'].'">'.$row['nombre'].' </option>'; 
					echo '</select>'; 
				?>
				<div class="input-group">
					<label for="nacimiento" class="control-label">Fecha Nacimiento:</label>
					<input type="text" placeholder="Fecha Nacimiento" class="form-control" id="nacimiento" name="nacimiento" required maxlength="8" size="8">
	                <span class="input-group-addon">
			        	<span class="glyphicon glyphicon-calendar"></span>
	                </span>
				</div>
			</div>
			<div class="form-group form-inline col-xs-12">
				<label for="habitacion" class="sr-only control-label">Dir.Habitacion: </label>
				<input type="text" placeholder="Direcci&oacute;n Habitaci&oacute;n" class="form-control" id="habitacion" name="habitacion" required maxlength="50" size="50">
			</div>
			<div class="form-group form-inline col-xs-12">
				<label for="telhabitacion" class="sr-only control-label">Telf.Habitacion: </label>
				<input type="text" placeholder="Tel&eacute;fono Habitaci&oacute;n" class="form-control" id="telhabitacion" name="telhabitacion" required maxlength="12" size="20">
				<label for="telcelular" class="sr-only control-label">Telf.Celular: </label>
				<input type="text" placeholder="Tel&eacute;fono Celular" class="form-control" id="telcelular" name="telcelular" required maxlength="12" size="20">
			</div>
			<div class="form-group form-inline col-xs-12">
				<label for="email" class="sr-only control-label">Direccion de email: </label>
				<input type="text" placeholder="Direcci&oacute;n de email " class="form-control" id="email" name="email" required maxlength="50" size="50">
				<label for="cuenta" class="sr-only control-label">N&uacute;mero de Cuenta: </label>
				<input type="text" placeholder="N&uacute;mero de Cuenta" class="form-control" id="cuenta" name="cuenta" required maxlength="20" size="20">
				<label for="condicion" class="control-label">Condicion:</label>
				<?php
					include_once('../dbconfig.php');
					$comando="select nombre from configuracion where parametro = 'Condicion' order by parametro";
					echo '<select class="form-control disabled" name="condicion" id="condicion" size="1">';
					$con=$db_con->prepare($comando);
					$con->execute();
					while($row = $con->fetch(PDO::FETCH_ASSOC))
						//if ($row['nombre']=='Activo')
							echo '<option '.$row['nombre'].($row['nombre']=='Activo'?' selected="selected"':'').' value="'.$row['nombre'].'">'.$row['nombre'].' </option>'; 
						echo '</select>'; 
				?>
			</div>
			<div class="form-group form-inline col-xs-12">
				<label for="trabajo" class="sr-only control-label">Ubicacion Trabajo:</label>
				<?php
					include_once('../dbconfig.php');
					$comando="select nombre from configuracion where parametro = 'Trabajo' order by parametro";
					echo '<select class="form-control" name="trabajo" id="trabajo" size="1">';
					$con=$db_con->prepare($comando);
					$con->execute();
					while($row = $con->fetch(PDO::FETCH_ASSOC))
						echo '<option '.$row['nombre'].' selected="selected"  value="'.$row['nombre'].'">'.$row['nombre'].' </option>'; 
					echo '</select>'; 
				?>
				<label for="ingucla" class="control-label">Ingr. UCLA:</label>
				<input type="text" placeholder="Ingreso UCLA" class="form-control" id="ingucla" name="ingucla" required maxlength="8" size="8">
				<div class="input-group">
			    	<span class="input-group-addon">
			  			<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
			</div>
			<div class="form-group form-inline col-xs-12" >
				<label for="ingipsta" class="control-label">Ingr. IPSTAUCLA:</label>
				<input type="text" placeholder="Ingreso IPSTAUCLA" class="form-control" id="ingipsta" name="ingipsta" required maxlength="8" size="8">
				<div class="input-group">
			    	<span class="input-group-addon">
			  			<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
				<label for="inclnomina" class="control-label">Inclusion Nomina:</label>
				<input type="text" placeholder="Inclusion Nomina" class="form-control" id="inclnomina" name="inclnomina" required maxlength="8" size="8">
				<div class="input-group">
			    	<span class="input-group-addon">
			  			<span class="glyphicon glyphicon-calendar"></span>
			        </span>
				</div>
				<label for="teltrabajo" class="sr-only control-label">Telf.Tabajo</label>
				<input type="text" placeholder="Tel&eacute;fono Trabajo" class="form-control" id="teltrabajo" name="teltrabajo" required maxlength="12" size="20">
		    </div>
			<!-- fin modificar -->
      	</div>
      	<div class="modal-footer">
        	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        	<button type="submit" class="btn btn-primary">Actualizar datos</button>
      	</div>
    </div>
  </div>
</div>
</form>
