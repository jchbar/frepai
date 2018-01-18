function load(page)
{
	var parametros = {"action":"ajax","page":page};
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'js_proveedores/losproveedores.php',
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
		  modal.find('.modal-title').text('Inclusion de Proveedor' )
		  modal.find('.modal-body #id').val(id)
		  modal.find('.modal-body #cedula').val(cedula)
		  modal.find('.modal-body #nombre').text(nombre)
		  modal.find('.modal-body #nacimiento').text(nacimiento)
		  $('.alert').hide();//Oculto alert
})

$('#dataUpdate').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
		  var codigo = button.data('id') // Extraer la información de atributos de datos
		  var id = button.data('id') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  var casa = button.data('casa') 
		  var rif = button.data('rif') 
		  var direccion = button.data('direccion')
		  var telf1 = button.data('telf1')
		  var telf2 = button.data('telf2')
		  var nrocuotas = button.data('nrocuotas')
		  var interes = button.data('interes')
		  var modal = $(this)
		  modal.find('.modal-title').text('Modificar Datos: '+codigo +' '+nombre)
		  modal.find('.modal-body #id').val(id)
		  modal.find('.modal-body #codigo').val(codigo)
		  modal.find('.modal-body #nombre').val(nombre)
		  modal.find('.modal-body #rif').val(rif)
		  modal.find('.modal-body #casa').val(casa)
		  modal.find('.modal-body #direccion').val(direccion)
		  modal.find('.modal-body #telf1').val(telf1)
		  modal.find('.modal-body #telf2').val(telf2)
		  modal.find('.modal-body #nrocuotas').val(nrocuotas)
		  modal.find('.modal-body #interes').val(interes)
		  $('.alert').hide();//Oculto alert
})
		
$('#dataDelete').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
		  var codigo = button.data('id') // Extraer la información de atributos de datos
		  var id = button.data('id') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  var casa = button.data('casa') 
		  var rif = button.data('rif') 
		  var direccion = button.data('direccion')
		  var telf1 = button.data('telf1')
		  var telf2 = button.data('telf2')
		  var nrocuotas = button.data('nrocuotas')
		  var interes = button.data('interes')
		  var modal = $(this)
		  modal.find('.modal-title').text('Eliminar Datos: '+codigo +' '+nombre)
		  modal.find('.modal-body #id').val(id)
		  modal.find('.modal-body #codigo').val(codigo)
		  modal.find('.modal-body #nombre').val(nombre)
		  modal.find('.modal-body #rif').val(rif)
		  modal.find('.modal-body #casa').val(casa)
		  modal.find('.modal-body #direccion').val(direccion)
		  modal.find('.modal-body #telf1').val(telf1)
		  modal.find('.modal-body #telf2').val(telf2)
		  modal.find('.modal-body #nrocuotas').val(nrocuotas)
		  modal.find('.modal-body #interes').val(interes)
		  $('.alert').hide();//Oculto alert
})

$( "#actualidarDatos" ).submit(function( event ) { // modificar
		var parametros = $(this).serialize();
			 $.ajax({
					type: "POST",
					url: "js_proveedores/modificar_proveedor.php",
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
					url: "js_proveedores/agregar_proveedor.php",
					// url: "js_proveedores/nada.php",
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
					url: "js_proveedores/eliminar_proveedor.php",
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

