<form id="actualidarDatos">
<div class="modal fade" id="dataUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      	<div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	<h4 class="modal-title" id="exampleModalLabel">Modificar Cuenta Contable</h4>
     	</div>
      	<div class="modal-body">
			<div class="form-group form-inline col-xs-12">
				<label for="codigo" class="control-label">C&oacute;digo :</label>
				<input readonly="readonly" type="text" class="form-control" id="codigo" name="codigo" required maxlength="10">
				<input type="hidden" class="form-control" id="id" name="id" required maxlength="10">

						<label for="nombre" class="sr-only control-label">Nombre </label>
						<input type="text" placeholder="Nombre" class="form-control" id="nombre" name="nombre" required maxlength="30" size="30">
					</div>
					<div class="form-group form-inline col-xs-12">
						<label for="rif" class="sr-only control-label">RIF </label>
						<input type="text" placeholder="rif" class="form-control" id="rif" name="rif" required maxlength="30" size="30">
					</div>
					<div class="form-group form-inline col-xs-12">
						<label for="casa" class="sr-only control-label">Casa </label>
						<input type="text" placeholder="Casa" class="form-control" id="casa" name="casa" required maxlength="30" size="30">
					</div>
					<div class="form-group form-inline col-xs-12">
						<label for="direccion" class="sr-only control-label">Direccion </label>
						<input type="text" placeholder="Direccion" class="form-control" id="direccion" name="direccion" required maxlength="60" size="30">
					</div>
					<div class="form-group form-inline col-xs-12">
						<label for="telf1" class="sr-only control-label">Telefono(s) </label>
						<input type="text" placeholder="Telefono 1" class="form-control" id="telf1" name="telf1" required maxlength="12" size="12">
						<input type="text" placeholder="Telefono 2" class="form-control" id="telf2" name="telf2" required maxlength="12" size="12">
					</div>
					<div class="form-group form-inline col-xs-12">
							<label for="nrocuotas" class="sr-only control-label">Nro. Cuotas(s) </label>
							<input type="number" placeholder="# Cuotas" class="form-control" id="nrocuotas" name="nrocuotas" value="" maxlength="20" size="20" min="1" max="20" step="1">
							<label for="interes" class="sr-only control-label">Interes </label>
							<input type="number" placeholder="Interes" class="form-control" id="interes" name="interes" value="" maxlength="20" size="20" min="1" max="20" step="1">
					</div>
					<div class="form-group form-inline col-xs-12">
							<label for="tipo" class="control-label">Tipo Interes:</label>
							<?php
								include_once('../dbconfig.php');
								$comando="select nombre from configuracion where parametro = 'TipoInteres' order by parametro";
								echo '<select class="form-control" name="estado" id="estado" size="1">';
								$con=$db_con->prepare($comando);
								$con->execute();
								while($row = $con->fetch(PDO::FETCH_ASSOC))
									echo '<option '.$row['nombre'].' selected="selected"  value="'.$row['nombre'].'">'.$row['nombre'].' </option>'; 
								echo '</select>'; 
							?>
					</div>
				</div>
			</div>
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Actualizar datos</button>
      </div>
    </div>
  </div>
</div>
</form>