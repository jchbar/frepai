<form id="eliminarDatos">
<div class="modal fade" id="dataDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Eliminacion de Tributo</h4>
      </div>
      <div class="modal-body">
      <input type="hidden" id="id" name="id">
      <h2 class="text-center text-muted">Estas seguro?</h2>
	  <p class="lead text-muted text-center" style="display: block;margin:10px">Esta acción eliminará de forma permanente el registro. Deseas continuar?</p>
			<div id="datos_ajax_register"></div>
	  			<div class="form-group form-inline col-xs-12">
				<label for="codigo" class="control-label">C&iacute;digo Contable:</label>
				<input type="text" class="form-control" id="codigo" name="codigo" required readonly="readonly" maxlength="10">
			</div>
			<div class="form-group col-xs-12">
				<label for="nombre" class="control-label col-xs-12">Descripci&oacute;n:</label>
				<input type="text" class="form-control" id="nombre" name="nombre" required readonly="readonly" maxlength="100" maxlength="50">
			</div>
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-lg btn-primary">Aceptar</button>
      </div>
    </div>
  </div>
</div>
</form>