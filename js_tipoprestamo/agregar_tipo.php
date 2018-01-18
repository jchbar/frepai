<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors','1');
/*Inicia validacion del lado del servidor*/
//$errors[] = $messages[] = "";
include_once('../funciones.php');
include_once('../dbconfig.php');
if (empty($_POST['ultimo']))
{
	$errors[] = "Codigo vacio";
}
else if (empty($_POST['descripcion']))
{
	$errors[] = "Descripcion vacía";
}
else if ((!empty($_POST['ultimo'])) &&  (!empty($_POST['descripcion'])))
{
	$codigo = ceroizq($_POST['ultimo'],3);
	$descripcion = (isset($_POST['descripcion'])?$_POST['descripcion']:'n-a');
	$concepto = (isset($_POST['concepto'])?$_POST['concepto']:'n-a');
	$tipo_interes = (isset($_POST['estado'])?$_POST['estado']:'n-a');
	$nrocuotas = (isset($_POST['nrocuotas'])?$_POST['nrocuotas']:0);
	$interes = (isset($_POST['interes'])?$_POST['interes']:0);
	$renovacion = (isset($_POST['renovacion'])?$_POST['renovacion']:0);
	$dcto_mensual = (isset($_POST['dcto_mensual'])?$_POST['dcto_mensual']:0);
	$int_dif = (isset($_POST['int_dif'])?$_POST['int_dif']:2);
	$ip=la_ip();
	$usuario=el_usuario();
	$registro=ahora($db_con)['hoy1'];
	//$registro=$registro['hoy'];
	try
	{
		$sql="INSERT INTO tipoprestamo (codigo, descripcion, interes, renovacion, int_dif,	ip_nuevo, ip_modifica, fecha_registro, usuario, tipo_interes, visible, nrocuotas, nrofiadores, retab_pres, cuenta_pres, cuenta_int, otro_int, garantia, tiempo, albanco, aprobar, dcto_mensual, masdeuno, tope_monto, e_items, tope_ut, factor_ut, en_ajax, en_proyec, genera_pl, canc_pres, genera_com, restar_otros, incluir_otros, inicial, desc_cor, copias, pla_autor, montofijo, montofuturo, nom_planilla, otractaab, tipo, concepto
		) VALUES (:codigo, :descripcion, :interes, :renovacion, :int_dif, :ip, :ip, :registro, :usuario, :tipo_interes, :visible, :nrocuotas, :nrofiadores, :retab_pres, :cuenta_pres, :cuenta_int, :otro_int, :garantia, :tiempo, :albanco, :aprobar, :dcto_mensual, :masdeuno, :tope_monto, :e_items, :tope_ut, :factor_ut, :en_ajax, :en_proyec, :genera_pl, :canc_pres, :genera_com, :restar_otros, :incluir_otros, :inicial, :desc_cor, :copias, :pla_autor, :montofijo, :montofuturo, :nom_planilla, :otractaab, :tipo, :concepto)";
		$con=$db_con->prepare($sql);
		$cero=0;
		$visible=1;
		$vacio='';
		$tipo="Estatutario";
		$query_update = $con->execute(array(
			":codigo"=>$codigo,
			":descripcion"=>$descripcion,
			":interes"=>$interes,
			":nrocuotas"=>$nrocuotas,
			":renovacion"=>$renovacion,
			":int_dif"=>$int_dif,
			":ip"=>$ip,
			":registro"=>$registro,
			":usuario"=>$usuario,
			":tipo_interes"=>$tipo_interes,
			":visible"=>$visible,
			":nrofiadores"=>$visible,
			":retab_pres"=>$visible,
			":cuenta_pres"=>$vacio,
			":cuenta_int"=>$vacio,
			":otro_int"=>$vacio,
			":garantia"=>$visible,
			":tiempo"=>$visible,
			":albanco"=>$visible,
			":aprobar"=>$visible,
			":masdeuno"=>$cero,
			":dcto_mensual"=>$dcto_mensual,
			":tope_monto"=>$cero,
			":e_items"=>$cero,
			":tope_ut"=>$cero,
			":factor_ut"=>$cero,
			":en_ajax"=>$cero,
			":en_proyec"=>$cero,
			":canc_pres"=>$cero,
			":genera_com"=>$cero,
			":genera_pl"=>$cero,
			":restar_otros"=>$cero,
			":incluir_otros"=>$cero,
			":inicial"=>$cero,
			":montofijo"=>$cero,
			":montofuturo"=>$cero,
			":otractaab"=>$vacio,
			":pla_autor"=>$cero,
			":nom_planilla"=>$vacio,
			":copias"=>$visible,
			":desc_cor"=>$vacio,
			":tipo"=>$tipo,
			":concepto"=>$concepto,
		));
		if ($query_update)
		{
			$messages[] = "Los datos han sido guardados satisfactoriamente.";
		} 
		else
		{
			$errors []= "Lo siento algo ha salido mal intenta nuevamente."; // .mysqli_error($con);
		}
	}
	catch(PDOException $e){
		die($e->getMessage());
		mensaje(array(
			"titulo"=>"Error!",
			"tipo"=>"danger",
			"texto"=>$e->getMessage(),
			));
	}
} 
else 
{
	$errors []= "Error desconocido.";
}
if (isset($errors))
{
	foreach ($errors as $error) 
	mensaje(array(
		"titulo"=>"Error!",
		"tipo"=>"danger",
		"texto"=>$error,
	));
}
if (isset($messages))
{
	foreach ($messages as $message) 
	mensaje(array(
		"titulo"=>"Bien Hecho!",
		"tipo"=>"success",
		"texto"=>$message,
	));
}
?>	