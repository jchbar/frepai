<?php
session_start();
date_default_timezone_set('America/Caracas'); 
include("home.php");
/*
if(!isset($_SESSION['usuario_sistema']))
{
	header("Location: index.php");
}
// include_once 'dbconfig.php';
include_once('opciones.php');
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php //include('head.html'); ?>
</head>

<body>
<?php 
include("js_cuentas/modal_agregar_cta.php");
include("js_cuentas/modal_eliminar_cta.php");
include("js_cuentas/modal_modificar_cta.php");
include("js_cuentas/modal_imprimir_cta.php");
?>
    <div class="container-fluid">
	 
		<div class='col-xs-6'>	
			<h3>Cuentas Contables</h3>
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
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript 
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	-->
	
	<script src="js_cuentas/app.js"></script>
	<script>
		$(document).ready(function(){
			load(1);
		});
	</script>
 </body>
</html>

