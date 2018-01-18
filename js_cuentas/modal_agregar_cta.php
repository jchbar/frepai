<form id="guardarDatos">
	<div class="modal fade" id="dataRegister" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
		    	<div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title" id="exampleModalLabel">Agregar Cuenta Contable</h4>
		      	</div>
		      	<div class="modal-body">
					<div class="form-group form-inline col-xs-12">
						<label for="codigo" class="control-label">C&oacute;digo Contable:</label>
						<input type="text" class="form-control" id="codigo" name="codigo" required maxlength="20">
					</div>
					<div class="form-group col-xs-12">
						<label for="nombre" class="control-label col-xs-12">Descripci&oacute;n:</label>
						<input type="text" class="form-control" id="nombre" name="nombre" required maxlength="30" maxlength="30">
					</div>
<!--					
					<div class="form-group form-inline col-xs-12">
						<div class="form-group">
							<label for="codigo_sudeca" class="control-label">Cuenta SUDECA: </label>
							<input type="text" class="form-control" id="codigo_sudeca" name="codigo_sudeca" value="" maxlength="35" size="35">
						</div>
						<div class="form-group">
							<label for="nombre_sudeca" class="control-label">Nombre SUDECA:</label>
							<input type="text" class="form-control" id="nombre_sudeca" name="nombre_sudeca" value="" maxlength="30" size="30">
						</div>
					</div>
-->
				  <div class="form-group">
		          </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		        <button type="submit" class="btn btn-primary">Guardar datos</button>
		      </div>
		    </div>
		  </div>
	</div>
</form>