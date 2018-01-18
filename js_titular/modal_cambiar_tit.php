<form id="actualizarStatus">
<div class="modal fade" id="dataStatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
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
