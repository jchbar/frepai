<form id="guardarDatos" enctype="multipart/form-data">
	<div class="modal fade" id="dataRegister" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
		<div class="modal-dialog" role="document"> <!-- id="mdialTamanio"> -->
			<div class="modal-content">
		    	<div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title" id="exampleModalLabel">Incluir Tipo de Pr&eacute;stamo</h4>
		      	</div>
		      	<div class="modal-body">
					<div class="form-group form-inline col-xs-12">
						<label for="codigo" class="sr-only control-label">C&oacute;digo </label>
						<?php
							include_once('../dbconfig.php');
							$sql1="SELECT codigo as ultimo FROM tipoprestamo order by codigo desc limit 1";
							$res=$db_con->prepare($sql1);
							$res->execute();
							$reg=$res->fetch(PDO::FETCH_ASSOC);
							$reg=$reg['ultimo']+1;
							$reg=ceroizq($reg,3);

							echo '<input type="text" placeholder="C&oacute;digo de C&eacute;dula" class="form-control disabled" id="ultimo" name="ultimo" required maxlength="3" size="3" value="'.$reg.'" readonly="readonly" >';
						?>
						<label for="descripcion" class="sr-only control-label">C&oacute;digo </label>
						<input type="text" placeholder="Descripci&oacute;n" class="form-control" id="descripcion" name="descripcion" required maxlength="30" size="30">
					</div>
					<div class="form-group form-inline col-xs-12">
							<label for="nrocuotas" class="sr-only control-label">Nro. Cuotas(s) </label>
							<input type="number" placeholder="# Cuotas" class="form-control" id="nrocuotas" name="nrocuotas" value="" maxlength="20" size="20" min="1" max="20" step="1">
							<label for="interes" class="sr-only control-label">Interes </label>
							<input type="number" placeholder="Interes" class="form-control" id="interes" name="interes" value="" maxlength="20" size="20" min="1" max="20" step="1">
					</div>
					<div class="form-group form-inline col-xs-12">
							<label for="concepto" class="sr-only control-label">Concepto UCLA </label>
							<input type="number" placeholder="Concepto UCLA" class="form-control" id="concepto" name="concepto" value="" maxlength="3" size="20">
					</div>
					<div class="form-group form-inline col-xs-12">
							<label for="renovacion" class="sr-only control-label">Renovacion </label>
							<input type="number" placeholder="# Cuotas Renovar" class="form-control" id="renovacion" name="renovacion" value="" maxlength="20" size="20" min="1" max="20" step="1">
							  <label for="int_dif">
							    <input id="int_dif" name="int_dif" type="checkbox" value="1">
							    Inter&eacute;res cobrado por anticipado?
							  </label>
					</div>
					<div class="form-group form-inline col-xs-12">
						<label for="dcto_mensual">
						<input id="dcto_mensual" name="dcto_mensual" type="checkbox" value="1">
							Descontar Mensualmente?
						</label>
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
	<script>
	$("#file-3").fileinput({
		showCaption: false,
		browseClass: "btn btn-primary",
		fileType: "any"
	});
	</script>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		        <button type="submit" class="btn btn-primary">Guardar datos</button>
		      </div>
		    </div>
		  </div>
	</div>
</form>