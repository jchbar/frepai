<?php
session_start();
date_default_timezone_set('America/Caracas'); 
$_SESSION['institucion']="";
include("home.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php //include('head.html'); ?>
</head>

<body>
<?php 
include("js_tipoprestamo/modal_agregar_tipo.php");
include("js_tipoprestamo/modal_eliminar_tipo.php");
include("js_tipoprestamo/modal_modificar_tipo.php");
?>
    <div class="container-fluid">
	 
		<div class='col-xs-6'>	
			<h3>Tipos de Pr&eacute;stamos</h3>
		</div>
<!--
		<div class='col-xs-6'>
			<h3 class='text-right'>		
				<button type="button" class="btn btn-default" data-toggle="modal" data-target="#dataRegister"><i class='glyphicon glyphicon-plus'></i> Agregar</button>
			</h3>
		</div>
-->		
	  <div class="row">
		<div class="col-xs-12">
		<div id="loader" class="text-center"> <img src="loader.gif"></div>
		<div id="datos_ajax"></div>
		<div id="datos_ajax_register"></div>
		<div class="datos_ajax_delete"></div><!-- Datos ajax Final -->
		<div class="outer_div"></div><!-- Datos ajax Final -->
		</div>
	  </div>
	</div>
	
	<script src="js_tipoprestamo/app_tipo.js"></script>
	<script>
		$(document).ready(function(){
			load(1);
		});
	</script>
 </body>
</html>

