function load(page)
{
	var parametros = {"action":"ajax","page":page};
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'js_tipoprestamo/losprestamos.php',
		data: parametros,
		 beforeSend: function(objeto){
		$("#loader").html("<img src='loader.gif'>");
	},
	success:function(data){
			$(".outer_div").html(data).fadeIn('slow');
			$("#loader").html("");
		}
	})
}
$('#dataRegister').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) // Botón que activó el modal
		  var cedula = button.data('cedula') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  var nacimiento = button.data('nacimiento') 
		  var modal = $(this)
		  modal.find('.modal-title').text('Inclusion de Titular' )
		  modal.find('.modal-body #id').val(id)
		  modal.find('.modal-body #cedula').val(cedula)
		  modal.find('.modal-body #nombre').text(nombre)
		  modal.find('.modal-body #nacimiento').text(nacimiento)
		  $('.alert').hide();//Oculto alert
})

$('#dataUpdate').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
		  var codigo = button.data('codigo') // Extraer la información de atributos de datos
		  var id = button.data('id') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  alert(button.data('nacimiento'));
		  var nacimiento = button.data('nacimiento') 
		  var habitacion = button.data('habitacion')
/*
		  var valor = button.data('valor') 
		  var funcionalidad = button.data('funcionalidad') 
		  var medida = button.data('medida') 
		  var cntminimo = button.data('cntminimo') 
		  var cntmaxima = button.data('cntmaxima') 
		  // alert('cntminimo'+cntminimo+ ' cntmaxima '+cntmaxima);
		  var porcentaje = button.data('porcentaje') 
*/		  
		  var modal = $(this)
		  modal.find('.modal-title').text('Modificar Datos: '+codigo +' '+nombre)
		  modal.find('.modal-body #id').val(id)
		  // alert(id)
		  modal.find('.modal-body #codigo').val(codigo)
		  modal.find('.modal-body #nombre').val(nombre)
		  modal.find('.modal-body #nacimiento').val(nacimiento)
		  modal.find('.modal-body #habitacion').val(habitacion)
		  /*
		  modal.find('.modal-body #valor').val(valor)
		  modal.find('.modal-body #funcionalidad').val(funcionalidad)
		  modal.find('.modal-body #medida').val(medida)
		  modal.find('.modal-body #cntminimo').val(cntminimo)
		  modal.find('.modal-body #cntmaxima').val(cntmaxima)
		  modal.find('.modal-body #porcentaje').val(porcentaje)
		  */
		  $('.alert').hide();//Oculto alert
})
		
$('#dataDelete').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
		  var id = button.data('id') // Extraer la información de atributos de datos
		  var codigo = button.data('codigo') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  var modal = $(this)
		  
		  modal.find('#id').val(id)
		  modal.find('.modal-body #codigo').val(codigo)
		  modal.find('.modal-body #nombre').val(nombre) 
		  modal.find('.modal-title').text('Eliminar Tipo de Prestamo ' +codigo + ' / '+nombre)
})

$( "#actualidarDatos" ).submit(function( event ) { // modificar
		var parametros = $(this).serialize();
			 $.ajax({
					type: "POST",
					url: "js_tipoprestamo/modificar_tipo.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#datos_ajax").html("Mensaje: Actualizando...");
					  },
					success: function(datos){
					$("#datos_ajax").html(datos);
					$('#dataUpdate').modal('hide');
					
					load(1);
				  }
			});
		  event.preventDefault();
});
		
$( "#guardarDatos" ).submit(function( event ) { // incluir
		var parametros = $(this).serialize();
	  console.log(parametros);
			 $.ajax({
					type: "POST",
					url: "js_tipoprestamo/agregar_tipo.php",
					// url: "js_tipoprestamo/nada.php",
					data: parametros,
					beforeSend: function(objeto){
						$("#datos_ajax").html("Mensaje: Almacenando...");
					},
					success: function(datos){
						$("#datos_ajax").html(datos);
						$('#dataRegister').modal('hide');
						load(1);
				  	}
			});
		  event.preventDefault();
});
		
		$( "#eliminarDatos" ).submit(function( event ) {
		var parametros = $(this).serialize();
			 $.ajax({
					type: "POST",
					url: "js_tipoprestamo/eliminar_tipo.php",
					data: parametros,
					 beforeSend: function(objeto){
						$(".datos_ajax_delete").html("Mensaje: Verificando...");
					  },
					success: function(datos){
					$(".datos_ajax_delete").html(datos);
					
					$('#dataDelete').modal('hide');
					load(1);
				  }
			});
		  event.preventDefault();
		});

