<?php
$b = explode("/",$fecha);
$a=explode("/",$fecha); 

$fecha = "20".$a[2]."-".$a[1]."-".$a[0];

$ip = $_SERVER['HTTP_CLIENT_IP'];
if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
agregar_f820($asiento, $fecha, $elcargo, $cuenta1, $concepto, $elmonto, $haber, 0,$ip,0,$referencia,'','N',$row_id, $db_con);

$row_id = 0;
//$body = 0;

?>
