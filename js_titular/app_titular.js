function load(page)
{
	var parametros = {"action":"ajax","page":page};
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'js_titular/lostitulares.php',
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
		$('input[name="nacimiento"]').daterangepicker({
				"singleDatePicker": true,
				"startDate":  button.data('los18'),  // "11/07/2016", 
				"endDate": button.data('hoy'), // "11/30/2016", 
				//"minDate": button.data('los18'), // "11/01/2016",
				"maxDate": button.data('los18') // "11/30/2016"
			}, function(start, end, label) {
//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			});

		$('input[name="ingucla"]').daterangepicker({
				"singleDatePicker": true,
				"startDate":  button.data('hoy'),  // "11/07/2016", 
				"endDate": button.data('hoy'), // "11/30/2016", 
				"minDate": button.data('los18'), // "11/01/2016",
				"maxDate": button.data('hoy') // "11/30/2016"
			}, function(start, end, label) {
//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			});

		$('input[name="ingipsta"]').daterangepicker({
				"singleDatePicker": true,
				"startDate":  button.data('hoy'),  // "11/07/2016", 
				"endDate": button.data('hoy'), // "11/30/2016", 
				"minDate": button.data('los18'), // "11/01/2016",
				"maxDate": button.data('hoy') // "11/30/2016"
			}, function(start, end, label) {
//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			});

		$('input[name="inclnomina"]').daterangepicker({
				"singleDatePicker": true,
				"startDate":  button.data('hoy'),  // "11/07/2016", 
				"endDate": button.data('hoy'), // "11/30/2016", 
				"minDate": button.data('los18'), // "11/01/2016",
				"maxDate": button.data('hoy') // "11/30/2016"
			}, function(start, end, label) {
//			  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			});

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
		  var apellido = button.data('apellido') // Extraer la información de atributos de datos
		  var nacimiento = button.data('nacimiento') 
		  var habitacion = button.data('habitacion')
		  var telhabitacion = button.data('telhabitacion')
		  var telcelular = button.data('telcelular')
		  var teltrabajo = button.data('teltrabajo')
		  var email = button.data('email')
		  var cuenta = button.data('cuenta')
		  var ingucla = button.data('ingucla')
		  var ingipsta = button.data('ingipsta')
		  var inclnomina = button.data('inclnomina')

		  var modal = $(this)
		  modal.find('.modal-title').text('Modificar Datos: '+id +' '+apellido + ' '+nombre)
		  modal.find('.modal-body #id').val(id)
		  modal.find('.modal-body #codigo').val(codigo)
		  modal.find('.modal-body #nombre').val(nombre)
		  modal.find('.modal-body #apellido').val(apellido)
		  modal.find('.modal-body #nacimiento').val(nacimiento)
		  modal.find('.modal-body #habitacion').val(habitacion)
		  modal.find('.modal-body #telhabitacion').val(telhabitacion)
		  modal.find('.modal-body #telcelular').val(telcelular)
		  modal.find('.modal-body #teltrabajo').val(teltrabajo)
		  modal.find('.modal-body #email').val(email)
		  modal.find('.modal-body #cuenta').val(cuenta)
		  modal.find('.modal-body #ingucla').val(ingucla)
		  modal.find('.modal-body #ingipsta').val(ingipsta)
		  modal.find('.modal-body #inclnomina').val(inclnomina)
		  $('.alert').hide();//Oculto alert
})

$('#dataDelete').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
		  var id = button.data('id') // Extraer la información de atributos de datos
		  var cedula = button.data('cedula') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  var modal = $(this)
		  
		  modal.find('#id').val(id)
		  modal.find('.modal-body #cedula').val(cedula)
		  modal.find('.modal-body #nombre').val(nombre) 
		  modal.find('.modal-title').text('Eliminar Titular ' +cedula + ' / '+nombre)
})
$('#dataStatus').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Botón que activó el modal
		  var id = button.data('id') // Extraer la información de atributos de datos
		  var cedula = button.data('cedula') // Extraer la información de atributos de datos
		  var nombre = button.data('nombre') // Extraer la información de atributos de datos
		  var status = button.data('status') 
		  var modal = $(this)
		  
		  modal.find('#id').val(id)
		  modal.find('.modal-body #cedula').val(cedula)
		  modal.find('.modal-body #nombre').val(nombre) 
		  modal.find('.modal-body #condicion').val(status) 
		  modal.find('.modal-title').text('Cambiar Status ('+status+') Titular ' +cedula + ' / '+nombre)
})


$( "#actualidarDatos" ).submit(function( event ) { // modificar
		var parametros = $(this).serialize();
			 $.ajax({
					type: "POST",
					url: "js_titular/modificar_tit.php",
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
//	  console.log(parametros);
			 $.ajax({
					type: "POST",
					url: "js_titular/agregar_tit.php",
					// url: "js_titular/nada.php",
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
					url: "js_titular/eliminar_tit.php",
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

$( "#actualizarStatus" ).submit(function( event ) { // modificar
		var parametros = $(this).serialize();
					console.log(parametros);
			 $.ajax({
					type: "POST",
					url: "js_titular/cambiar_tit.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#datos_ajax_delete").html("Mensaje: Actualizando...");
					  },
					success: function(datos){
					$("#datos_ajax_delete").html(datos);
					$('#dataStatus').modal('hide');
					
					load(1);
				  }
			});
		  event.preventDefault();
});
