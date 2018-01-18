<?php
session_start();
$mostrarerrores=0;
if ($mostrarerrores == 1)
{
	error_reporting(E_ALL);
	ini_set('display_errors','1');
}
date_default_timezone_set('America/Caracas'); 
if(!isset($_SESSION['user_session']))
{
	header("Location: index.php");
}

include_once 'dbconfig.php';

/*
$stmt = $db_con->prepare("SELECT * FROM sgcapass WHERE alias=:uid");
$stmt->execute(array(":uid"=>$_SESSION['user_session']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.:| IPSTAUCLA - FREPAI |:.</title>
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> 
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet">  
<script type="text/javascript" src="bootstrap/js/jquery.min.js"></script> 
<!-- <script type="text/javascript" src="bootstrap/js/jquery3.js"></script>  -->
	<script type="text/javascript" src="bootstrap/js/moment.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/validation.min.js"></script>
<script type="text/javascript" src="bootstrap/js/daterangepicker.js"></script> 
<link rel="stylesheet" type="text/css" href="bootstrap/css/daterangepicker.css" /> 
<script src="bootstrap/js/bootstrap.min.js"></script>

<script src="bootstrap/js/fileinput.min.js" type="text/javascript"></script>
<link href="bootstrap/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />

<!-- los enlaces para menu multinivel -->
<!-- SmartMenus jQuery Bootstrap Addon CSS -->
<link href="bootstrap/css/jquery.smartmenus.bootstrap.css" rel="stylesheet">
<!-- SmartMenus jQuery plugin -->
<script type="text/javascript" src="bootstrap/js/jquery.smartmenus.js"></script>
<!-- SmartMenus jQuery Bootstrap Addon -->
<script type="text/javascript" src="bootstrap/js/jquery.smartmenus.bootstrap.js"></script>
<!-- fin de los enlaces para menu multinivel -->
<link href="bootstrap/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- script type="text/javascript" src="ConsultarCtasAsoc.js"></script-->
<script type="text/javascript" src="javascript.js"></script>

<script>
   $(document).ready(function()
   {
      $("#mostrarmodal").modal("show");
   });
</script>
<style>
    #mdialTamanio{
      width: 70% !important;
    }
  </style>
</head>

<body>
	
<?php
{
	date_default_timezone_set('America/Caracas'); 
	include("funciones.php");
	menu_normal();
}
?>

<div class="body-container">
<div class="container">
    <div class='alert alert-success'>
		<button class='close' data-dismiss='alert'>&times;</button>
			<strong>Bienvenido <?php echo $_SESSION['user_session']; ?></strong>.
    </div>
</div>
<div class="container">

<table class="table">
<tr>
</td>
</tr>
</table>
    
    </div>
</div>

</div>
</div>


</div>

</div>
</body>
</html>

<?php

function buscarpermiso($valor,$permisomenu) {
	for ($i=0; $i<count($permisomenu);$i++) {
		if ($permisomenu[$i] == $valor) {
			return 1;}
	}
return 0;
}

function menu_normal()
{
?>
<!-- Navbar -->
<div class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="#"><img src="imagenes/logo.jpg" width="50" height="50"></a>
  </div>
  <div class="navbar-collapse collapse">

    <!-- Left nav -->
    <ul class="nav navbar-nav"> <!-- navbar-right"> -->
	  <!-- menu socios -->
		<li><a href="#">Afiliados<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">Actualizacion<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="regt.php">Titulares</a></li>
						<li><a href="regb.php">Beneficiarios</a></li>
						<li class="divider"></li>
						<li><a href="frepai.php">FREPAI</a></li>
						<li class="divider"></li>
						<!-- <li><a href="cuotas.php">Pago Cuotas</a></li> -->
						<li><a href="proveedores.php">Proveedores</a></li>
						<li><a href="conceptos.php">Conceptos</a></li>
						<li><a href="retiros.php">Retiros</a></li>
					</ul>
				</li>
				<li class="divider"></li>
				<li><a href="#">Reportes<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="rept.php">Titulares</a></li>
						<li><a href="repb.php">Carga Familiar</a></li>
						<li><a href="estadocuenta.php">Estado de Cuenta</a></li>
						<li><a href="cotiza.php">Cotizaciones</a></li>
						<!-- <li><a href="depor.php">Dep&oacute;sito Retiros</a></li> -->
						<li><a href="bibcot.php">Biblioteca Cotizaciones</a></li>
					</ul>
				</li>
			</ul>
	  </li>
	  <!-- fin menu socios -->
	  <!-- prestamos -->
		<li><a href="#">Pr&eacute;stamos<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">Actualizar<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="solpre.php">Solicitudes</a></li>
						<li><a href="abonom.php">Abonos a Nomina</a></li>
						<li><a href="tippre.php">Tipos de Prestamo</a></li>
						<li><a href="recing.php">Recibos de Ingreso</a></li>
						<li><a href="ajustes.php">Ajustes a Descuentos</a></li>
					</ul>
				</li>
				<li class="divider"></li>
				<li><a href="#">Reportes<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#">Emisi&oacute;n de Descuentos (Pre-N&oacute;mina)<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="prenompre.php">Ordinarios</a></li>
								<li class="divider"></li>
								<li><a href="prenomesp.php">Especiales</a></li>
							</ul>
						</li>
						<li><a href="vernompre.php">Biblioteca de Descuentos</a></li>
						<!-- <li><a href="depositobanco2.php">Nomina Deposito</a></li> -->
						<li><a href="salpre.php">Saldos de Prestamos</a></li>
						<!-- <li><a href="prenompre.php">Pre-N&oacute;mina</a></li> -->
						<li class="divider"></li>
						<li><a href="nomina.php">N&oacute;mina</a></li>
						<li class="divider"></li>
						<li class="disabled"><a class="disabled" href=".php">Prestamos Otorgados</a></li>
						<li class="disabled"><a class="disabled" href=".php">Distribucion 70%</a></li>
					</ul>
				</li>
			</ul>
	  </li>
	  <!-- fin menu prestamos -->

	  <!-- contabilidad -->
		<li><a href="#">Contabilidad<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">Asientos<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="altaasim.php">Simples</a></li>
						<li><a href="altaasigral.php">Generales</a></li>
						<li><a href="editasi2.php">Buscar/Editar</a></li>
					</ul>
				</li>
				<li><a href="#">Cuentas<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="cuentas.php">Alta</a></li>
						<li><a href="reiniciar.php">Reiniciar</a></li>
						<li><a href="cam_fech.php">Cambio de Fecha</a></li>
						<li><a href="precie.php">Pre-Cierre</a></li>
						<li><a href="ciecon.php">Cierre Contable</a></li>
					</ul>
				</li>
				<li class="divider"></li>
				<li><a href="#">Reportes<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="cueaso.php">Cuentas Asociadas</a></li>
						<li><a href="#">Balances<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="balcom.php">Comprobaci&oacute;n</a></li>
								<li><a href="balgen.php">General</a></li>
								<li><a href="estres.php">Estado de Resultados</a></li>
								<li><a href="resdia.php">Resumen de Diario</a></li>
							</ul>
						</li>
						<li><a href="#">Otros<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="diario.php">Diario</a></li>
								<li><a href="asidescu.php">Comprobantes Diferidos</a></li>
								<li><a href="#">Mayor Anal&iacute;tico<span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="extractoctas3.php">A&nacute;o Actual</a></li>
										<li><a href="extractoctas_hist.php">A&nacute;os Anteriores</a></li>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
	  </li>
	  <!-- fin menu contabilidad -->

	  <!-- menu cheques --
		<li><a href="#">Cheques<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">Actualizar<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="cheact.php">Cheques</a></li>
						<li><a href="chequeras.php">Chequeras</a></li>
						<li><a href="bancos.php">Bancos</a></li>
						<li><a href="conceptos.php">Conceptos</a></li>
						<li><a href="che_verif.php">Verificaci&oacute;n</a></li>
					</ul>
				</li>
				<li class="divider"></li>
				<li><a href="#">Reportes<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="cheimpr.php">Impresi&oacute;n</a></li>
						<li><a href="che_rel.php">Relaci&oacute;n</a></li>
						<li><a href="che_compr.php">Generar comprobantes</a></li>
						<li><a href="conciliacion.php">Conciliaci&oacute;n</a></li>
					</ul>
				</li>
			</ul>
	  </li>
	  <!-- fin menu cheques -->
	  
	  <!-- menu activos fijos  --
		<li><a href="#">Activos Fijos<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">Actualizar<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="lisact.php">Incorporaci&oacute;n</a></li>
						<li><a href="desact.php">Desincorporar</a></li>
						<li><a href="depact.php">Depreciaci&oacute;n</a></li>
						<li><a href="departamentos.php">Departamentos</a></li>
					</ul>
				</li>
				<li class="divider"></li>
				<li><a href="#">Reportes<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a target=\"_blank\" href="lisactpdf.php">Activos Fijos</a></li>
						<li><a href="desactpdf.php">Desincorporados</a></li>
						<li><a href="listotpdf.php">Totalmente Depreciados</a></li>
					</ul>
				</li>
			</ul>
	  </li>
	  <!-- fin menu cheques -->
          <!-- <ul class="nav navbar-nav navbar-right"> -->
            
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
			  <span class="glyphicon glyphicon-user"></span>&nbsp;Hola <?php echo $_SESSION['user_session']; ?>&nbsp;<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <!-- <li><a href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;View Profile</a></li> -->
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Salir</a></li>
              </ul>
            </li>
          
</div>
	

<?php
}


function ddls($hoy)
{
	$ddls= date('l', strtotime($hoy));
	return $ddls;
}
