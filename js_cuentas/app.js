function load(page)
{
	var parametros = {"action":"ajax","page":page};
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'js_cuentas/lascuentas.php',
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
$('#dataPrint').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Botón que activó el modal
  var codigo = button.data('codigo') // Extraer la información de atributos de datos
  var nombre = button.data('nombre') // Extraer la información de atributos de datos
  var modal = $(this)
  modal.find('.modal-title').text('Visualizar Mayor Analítico de '+codigo)
  // modal.find('#codigo').text(codigo);
  modal.find('.modal-body #codigo').val(codigo)
  modal.find('.modal-body #nombre').val(nombre)
  $('.alert').hide();//Oculto alert
})

$('#dataUpdate').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
		  var codigo = button.data('codigo') // Extraer la información de atributos de datos
		  var id = button.data('id') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  var unidad = button.data('unidad') 
		  var fraccion = button.data('fraccion')
		  var valor = button.data('valor') 
		  var funcionalidad = button.data('funcionalidad') 
		  var medida = button.data('medida') 
		  var cntminimo = button.data('cntminimo') 
		  var cntmaxima = button.data('cntmaxima') 
		  // alert('cntminimo'+cntminimo+ ' cntmaxima '+cntmaxima);
		  var porcentaje = button.data('porcentaje') 
		  
		  var modal = $(this)
		  modal.find('.modal-title').text('Modificar Cuenta Contable: '+codigo +' '+nombre)
		  modal.find('.modal-body #id').val(id)
		  // alert(id)
		  modal.find('.modal-body #codigo').val(codigo)
		  modal.find('.modal-body #nombre').val(nombre)
		  modal.find('.modal-body #unidad').val(unidad)
		  modal.find('.modal-body #fraccion').val(fraccion)
		  modal.find('.modal-body #valor').val(valor)
		  modal.find('.modal-body #funcionalidad').val(funcionalidad)
		  modal.find('.modal-body #medida').val(medida)
		  modal.find('.modal-body #cntminimo').val(cntminimo)
		  modal.find('.modal-body #cntmaxima').val(cntmaxima)
		  modal.find('.modal-body #porcentaje').val(porcentaje)
		  $('.alert').hide();//Oculto alert
})
		
$('#dataDelete').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
//		  var id = button.data('id') // Extraer la información de atributos de datos
		  var codigo = button.data('codigo') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
//		  var unidad = button.data('unidad') 
		  var modal = $(this)
		  
		  modal.find('#id').val(id)
		  modal.find('.modal-body #codigo').val(codigo)
		  modal.find('.modal-body #nombre').val(nombre)
		  modal.find('.modal-title').text('Eliminar Codigo Contable ' +codigo + ' / '+nombre)
})

$( "#actualidarDatos" ).submit(function( event ) {
		var parametros = $(this).serialize();
			 $.ajax({
					type: "POST",
					url: "js_cuentas/modificar_cta.php",
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
		
$( "#guardarDatos" ).submit(function( event ) {
		var parametros = $(this).serialize();
			 $.ajax({
					type: "POST",
					url: "js_cuentas/agregar_cta.php",
					data: parametros,
					beforeSend: function(objeto){
						$("datos_ajax_register").html("Mensaje: Almacenando...");
					},
					success: function(datos){
						$("datos_ajax_register").html(datos);
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
					url: "js_cuentas/eliminar_cta.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("datos_ajax_delete").html("Mensaje: Verificando...");
					  },
					success: function(datos){
					$("datos_ajax_delete").html(datos);
					
					$('#dataDelete').modal('hide');
					load(1);
				  }
			});
		  event.preventDefault();
		});

$( "#PrintDatos" ).submit(function( event ) {
		var parametros = $(this).serialize();
			 $.ajax({
					type: "POST",
					url: "../extractoctas3.php",
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
