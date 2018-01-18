<form id="eliminarDatos">
<div class="modal fade" id="dataDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Eliminacion de ....</h4>
      </div>
      <div class="modal-body">
      <input type="hidden" id="id" name="id">
      <h2 class="text-center text-muted">Estas seguro?</h2>
	  <p class="lead text-muted text-center" style="display: block;margin:10px">Esta acción eliminará de forma permanente el registro. Deseas continuar?</p>
			<div id="datos_ajax_register"></div>
	  			<div class="form-group form-inline col-xs-12">
				<label for="codigo" class="control-label">C&oacute;digo:</label>
				<input type="text" class="form-control" id="codigo" name="codigo" required readonly="readonly" maxlength="10">
			</div>
      <div>
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
		</div>
      <div class="modal-footer">
<!--
        <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancelar</button>
        -->
        <button type="submit" class="btn btn-lg btn-danger">Aceptar</button>
      </div>
    </div>
  </div>
</div>
</form>