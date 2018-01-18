<?php


	$db_host = "localhost";
	$db_name = "ipsta_frepai";
	$db_user = "jhernandez";
	$db_pass = "nene14";
//require("../final.php");
	try{
/*
		$link = @mysql_connect($Servidor,$Usuario, $Password,'',65536) or die ("<p /><br /><p /><div style='text-align:center'>Disculpe... En estos momentos no hay conexión con el servidor, estamos realizando modificaciones.... inténtalo más tarde. Gracias....</div>");
	mysql_select_db('sica', $link);
*/
		$db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
		$db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e){
		echo $e->getMessage();
		mensaje(array(
			":titulo"=>"Error!",
			":tipo"=>"danger",
			":texto"=>$e->getMessage(),
			));
		// echo 'Fallo la conexion';
	}

?>