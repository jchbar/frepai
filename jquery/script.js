			$('document').ready(function(){
				$('#boton').click(function(){
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
					jQuery.post("jquery/procesar.php", {
						name:nombre,
						parametro:parametro
					}, function(data, textStatus){
						if(data == 1){
							$('#res').html("Datos insertados.");
							$('#res').css('color','green');
							document.getElementById("resultado").value="nuevo valor" ;
							// $('#resultado').html("nuevo valor");
						}
						else{
							$('#res').html("Ha ocurrido un error.");
							$('#res').css('color','red');
						}
					});
				});
			});
