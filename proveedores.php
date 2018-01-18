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
include("js_proveedores/modal_agregar_proveedor.php");
include("js_proveedores/modal_eliminar_proveedor.php");
include("js_proveedores/modal_modificar_proveedor.php");
?>
    <div class="container-fluid">
	 
		<div class='col-xs-6'>	
			<h3>Proveedores</h3>
		</div>
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
	
	<script src="js_proveedores/app_prov.js"></script>
	<script>
		$(document).ready(function(){
			load(1);
		});
	</script>
 </body>
</html>

