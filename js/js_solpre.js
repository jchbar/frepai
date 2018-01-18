$('document').ready(function(){
/*
	$('#muestre').click(function(){
		var montoprestamo = $('#monto_solicitado').val();
		var montoespecial = $('#monto_especial').val();
		document.getElementById("monto_normal").value = montoprestamo-montoespecial;
	}
*/
	$('#calculo').click(function(){
/*
		if($('#nombre').val()==""){
			alert("Introduce el nombre");
			return false;
		}
		else{
			var nombre = $('#nombre').val();
		}
		if($('#parametro').val()==""){
			alert("Introduce la contrasena");
			return false;
		}
		else{
			var parametro = $('#parametro').val();
		}
*/
		var montoprestamo = $('#monto_solicitado').val();
		var montoespecial = $('#monto_especial').val();
		var num_cuotas = $('#lascuotas').val();
		var num_cuotase = $('#lascuotase').val();
		var p_interes = $('#interes').val();
		var divisible = $('#factor_division').val();
		var tipo_interes = $('#tipo_interes').val();
		var f_ajax = $('#calculo').val();
		var descontar_interes = $('#descontar_interes').val();
		var monto_futuro = $('#monto_futuro').val();
		jQuery.post("js/calpre.php", {
			montoprestamo:montoprestamo,
			montoespecial:montoespecial,
			num_cuotas:num_cuotas,
			num_cuotase:num_cuotase,
			p_interes:p_interes,
			divisible:divisible,
			tipo_interes:tipo_interes,
			f_ajax:f_ajax,
			descontar_interes:descontar_interes,
			monto_futuro:monto_futuro
		}, function(data, textStatus){
			document.getElementById("cuota").value 				= data.getElementsByTagName("cuota")[0].childNodes[0].nodeValue;
			document.getElementById("cuotae").value 			= data.getElementsByTagName("cuotae")[0].childNodes[0].nodeValue;
			document.getElementById("interes_diferido").value 	= data.getElementsByTagName("interes_diferido")[0].childNodes[0].nodeValue;
			document.getElementById("montoneto").value 			= data.getElementsByTagName("montoneto")[0].childNodes[0].nodeValue;
			document.getElementById("gastosadministrativos").value=data.getElementsByTagName("gastosadministrativos")[0].childNodes[0].nodeValue;
			document.getElementById("resultado_js").value 		= data.getElementsByTagName("cuotae")[0].childNodes[0].nodeValue;
			document.getElementById("monto_normal").value 		= data.getElementsByTagName("diferencia")[0].childNodes[0].nodeValue;
/*
			if(data == 1){
				$('#res').html("Datos insertados.");
				$('#res').css('color','green');
				// document.getElementById("resultado").value="nuevo valor" ;
				// $('#resultado').html("nuevo valor");
			}
			else{
				$('#res').html("Ha ocurrido un error.");
				$('#res').css('color','red');
			}
*/
		});
	});
});
