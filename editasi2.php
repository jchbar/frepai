<?php
include("home.php");
extract($_GET);
extract($_POST);
extract($_SESSION);
// if (!$bloqueo AND $asiento AND $accion AND ($accion == 'altaapu' OR $accion == 'editapu')) {echo " onload=\"foco('cuenta1')\"";}
// else {echo " onload=\"foco('asiento')\"";}
if (!$asiento) {
	echo '<div class="signin-form">';
	echo '<div class="container">';
	echo '<form id="consulta" name="consulta" action="">'; 
	// onsubmit="ConsultarCuentasAsoc(\'ConsultarCuentasAsoc.php\'); return false">';
	echo '<h2 class="form-signin-heading">Asientos Contables</h2><hr />';
	echo '<div class="form-group form-inline">';
	echo '<input type="text" class="form-control" placeholder="N&uacute;mero de Asiento" name="asiento" id="asiento" aria-describedby="help-nombre" size="11" maxlength="11"/>';
	// echo '<span id="check-t"></span>';

	echo "<input type='submit' class=\"btn btn-primary\" id='formu' value='Buscar Asiento' />";
	echo '</div>';
	echo "</form>\n";
	exit;
}

$nomatach = $_FILES['fich']['name'];
if ($nomatach AND $asiento) {
	$tipo1    = $_FILES["fich"]["type"];
	$archivo1 = $_FILES["fich"]["tmp_name"];
	$tamanio1 = $_FILES["fich"]["size"];
	$fp = fopen($archivo1, "rb");
    $contenido = fread($fp, $tamanio1);
    $contenido = addslashes($contenido);
    fclose($fp);
}
if ($explicacion) {
	$sql="UPDATE enc_contable SET enc_explic = \"$explicacion\" WHERE enc_clave = '$asiento'";
	$res=$db_con->prepare($sql);
	$res->execute();
}

if ($accion == "altaapu1" AND ($elmonto >=0)) { // ($debe != 0 OR $haber != 0)) {
	include ("altaapu1.php");
 }

if ($accion == "editapu1" && ($elmonto >=0)) { // ($debe != 0 OR $haber != 0)) {
 	include ("editapu1.php");
}

if ($accion == "boapu") {
 	include ("borrapu1.php");
}

if ($accion == "boasi") {
 	$sql = "DELETE FROM detalle_contable WHERE com_nrocom = :asiento";
 	$res=$db_con->prepare($sql);
 	$res=$res->execute(array(":asiento"=>$asiento));
	if (!$res) die ("El usuario $usuario no tiene permiso para borrar Asientos.");
	$sql = "DELETE FROM enc_contable WHERE enc_clave = :asiento";
 	$res=$db_con->prepare($sql);
 	$res=$res->execute(array(":asiento"=>$asiento));
	echo "Asiento<span class='b'> ".$asiento." </span>borrado.\n";

	$cuento='borrado de asiento '.$asiento;
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	if (!$ip) {$ip = $_SERVER['REMOTE_ADDR'];}
	$usuario=$_SERVER['REMOTE_ADDR'];
	$sql_bita="insert into bitacora (cuento, ip, quien) values ('$cuento', '$ip', '$usuario')";
 	$res=$db_con->prepare($sql_bita);
 	$res=$res->execute();

	echo "</div></body></html>";exit;
}

if ($asiento) {
	$result = "SELECT enc_clave, enc_explic FROM enc_contable WHERE enc_clave = '$asiento'";
	$res=$db_con->prepare($result);
	$res->execute();
	$con_830=$res->rowCount();
	$result = "SELECT com_nrocom FROM detalle_contable WHERE com_nrocom = '$asiento'";
	$res=$db_con->prepare($result);
	$res->execute();
	$con_820=$res->rowCount();
	if (($con_820 > 0) and ($con_830 == 0))
	{
		echo "<p />Asiento <span class='b'>$asiento</span> inexistente o Apunte Huérfano. Debe crear el encabezado</div></body></html>";
		exit;
	}
	if (($con_820 == 0) and ($con_830 > 0))
	{
		echo "<p />Asiento <span class='b'>$asiento</span>No tiene detalles</div></body></html>";
		exit;
	}
	$fila = $res->fetch(PDO::FETCH_ASSOC);
}
//echo '<table>';
// echo '<tr><td>';
echo '<div class="container">';
echo '<div class="signin-form">';
echo '<div class=col-xs-12 col-sm-6 col-md-6">';
// echo '</td><td>';
echo '</div>';
echo '<div class=col-xs-12 col-sm-6 col-md-6">';
echo '<table class="table">';
echo "<form enctype='multipart/form-data' name='justificante' action='editasi2.php?asiento=$asiento' method='post'>";
echo '<tr><td>';
echo "<label for='explicacion'>Explicaci&oacute;n</label> <textarea name='explicacion' rows='6' cols='90'>$fila[1]</textarea>";
echo '</td><td>';
echo "<label>Soporte</label> <input class='btn btn-info' type='file' name='fich' size='19' maxlength='19'>";
echo " (Si el asiento ya tiene un justificante ser&aacute; sustituido)";
echo " <input class='btn btn-default' type='submit' name='boton' value=\" >> \">";
	// echo $_ENV['COMPUTERNAME'];
	// if (($_SERVER['REMOTE_ADDR']=='192.168.1.9') OR ($_SERVER['REMOTE_ADDR']=='192.168.1.100') or ($_SERVER['REMOTE_ADDR']=='192.168.1.102') or ($_ENV['COMPUTERNAME']=='SERVERCAPPO'))	// permite borrar asiento
	echo "<br><a class='btn btn-danger' href='editasi2.php?asiento=$asiento&accion=boasi' onclick='return borrar_asiento()'>Borrar Asiento</a>&nbsp;&nbsp;&nbsp; - &nbsp;&nbsp;&nbsp;";
	echo "<a class='btn btn-success' href='editasi2.php?asiento=$asiento&accion=altaapu'>A&nacute;adir Registro</a><p />";
echo '</td><tr>';
echo '</table>';
echo '</div>';
echo '</div>';
echo '</div>';
echo "</form>";
// echo '</<td></tr>';

if ($accion == 'editapu') {
	include ("editapu.php");
}

if ($accion == 'altaapu') {
	include ("altaapu.php");
}
// echo 'asiento '.$asiento;
// die('llle');
echo "<table class = 'table'>";
cabasi(2);
// totalapu($asiento);
asiento($asiento,"1",$_SESSION['moneda'],$_SESSION['deci'],$_GET['bojust'], $db_con);
echo "</table><p />";

?>

</div></body></html>

<?php 
function pantalla_asiento($fechax,$elcargo, $cuenta1, $concepto, $referencia, $elmonto, $db_con)
{
// <th width="50">Fecha</th>
// <tr><td>
// <input type = 'text' maxlength='8' size='8' name='fecha' value='<?php echo $fechax;>' readonly='readonly' tabindex='3'>
// </td>
?>
<table class = 'table'>
<tr><th width="40"> </th><th width="100">Cuenta</th><th width="200">Concepto</th><th width="80">Referencia</th><th width="80">Monto</th></tr>
<td>
<?php 
$activar=' ';
if (($elcargo == '+')) {$activar='checked="checked"'; } else { $activar = ' '; }
?>
<input name="elcargo" type="checkbox" tabindex='4' <?php echo $activar;?> /> 
Cargo
</td><td>
<input class="form-control" placeholder="C&oacute;digo de Cuenta" type="text" size="20" tabindex='5' name='cuenta1' id="inputString" onKeyUp="lookup(this.value);" onBlur="fill();" value ="<?php echo $cuenta1;?>" autocomplete="off"/>
			<div class="suggestionsBox" id="suggestions" style="display: none;">
				<img src="upArrow.png" style="position: relative; top: -12px; left: 70px; "  alt="upArrow" />
				<div class="suggestionList" id="autoSuggestionsList">
				</div>
			</div>
		</div>

</td><td>
<input class="form-control" placeholder="Concepto" type = 'text' size='40' maxlength='60' name='concepto' tabindex='6' value ="<?php echo $concepto?>">
</td><td>
<input class="form-control" placeholder="Referencia" type = 'text' value ='<?php echo $referencia?>' size='10' maxlength='10' name='referencia' tabindex='7'>
</td><td>
<input class="form-control" placeholder="Monto de Registro" type = 'text' size='11' maxlength='11' name='elmonto' value='<?php echo $elmonto;?>' tabindex='8'>
<input type = "hidden" name="fecha" value="<?php echo $fechax;?>">
</td>
</tr>
<tr>
<?php
}
?>
