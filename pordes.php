<?php
session_start();
date_default_timezone_set('America/Caracas'); 
include("home.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php //include('head.html'); ?>
</head>

<body>
    <div class="container-fluid">
		<div class='col-xs-6'>	
		<?php
	 		mensaje(array(
		 		'tipo' => 'warning',
		 		'titulo' => 'Advertencia!',
		 		'texto' => '<h2>Modulo por desarrollar<h2>',
		 		));
	 	?>
		</div>
	</div>
</body>
</html>

