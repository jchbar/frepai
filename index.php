<?php
session_start();

if(isset($_SESSION['user_session'])!="")
{
	header("Location: home.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IPSTAUCLA - FREPAI</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> 
    <link href="bootrstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet">  
<script type="text/javascript" src="bootstrap/js/jquery3.js"></script>
<script type="text/javascript" src="bootstrap/js/validation.min.js"></script>
<link href="bootstrap/css/style.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="script.js"></script> 

</head>

<body>
    
<div class="signin-form">

	<div class="container">
     
       
       <form class="form-signin" method="post" id="login-form">
      
		<div align="center" class="form-group form-inline col-xs-12">
			<img src="imagenes/logo.jpg" width="100" height="100">
		</div>

        <h2 class="form-signin-heading">Ingreso al Sistema</h2><hr />
        
        <div id="error">
        <!-- error will be shown here ! -->
        </div>
        
        <div class="form-group">
        <input type="text" class="form-control" placeholder="Nombre de Usuario" name="nombre_usuario" id="nombre_usuario" aria-describedby="help-nombre" autocomplete="off"/>
        <span id="check-t"></span>
        </div>
        
        <div class="form-group">
        <input type="password" class="form-control" placeholder="Password/Clave" name="password" id="password" />
        </div>
       
     	<hr />
        
        <div class="form-group">
            <button type="submit" class="btn btn-default" name="btn-login" id="btn-login">
    		<span class="glyphicon glyphicon-log-in"></span> &nbsp; Validar
			</button> 
        </div>  
      
      </form>

    </div>
    
</div>
    
<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>