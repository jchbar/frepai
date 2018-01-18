<?php
include("home.php");
extract($_GET);
extract($_POST);
extract($_SESSION);
include_once('dbconfig.php');
// <script language="Javascript" src="selec_fecha.js" type='text/javascript'></script>
?>

<?php
if (!$asiento) 
{
	$onload="onload=\"foco('asiento')\"";
  $sql="SELECT con_compr FROM control";
  $fila=$db_con->prepare($sql);
  $fila->execute();
	$fila = $fila->fetch(PDO::FETCH_ASSOC);
	$asiento = $fila[0] + 1;
  $sql="UPDATE control SET con_compr = :asiento WHERE 1";
	$res1=$db_con->prepare($sql);
  $res1->execute(array(":asiento"=>$asiento));
	// Cojo el valor de la fecha en que se hizo el último Asiento
	$result = $db_con->prepare("SELECT date_format(con_ultfec,'%d/%m/%y') AS ultfechax FROM control");
  $result->execute();
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$fecha = $row['enc_fecha'];
}
 else 
 {
	$onload="onload=\"foco('cuenta11')\"";
	$readonly=" readonly='readonly'";
	$asiento = $_POST['asiento'];
//	echo 'la fecha '.$_POST['fecha'];
	$fecha = $_POST['fecha'];
	$fecha = $_POST['fecha'];
	$tipo =$_POST['tipo'];
	$debcre= $_POST['debcre'];
	$cuenta1= $_POST['cuenta1'];
	$referencia =$_POST['referencia'];
	$elmonto=$_POST['elmonto'];
}

?>

<body>
<?php
if ($elmonto) {
	include("altaasim2.php");
//	$cuadre = totalapu($asiento);
}

echo '<div class="signin-form">';
echo '<div class="container">';
echo '<form id="form1" name="form1" action="altaasim.php" method="post" onSubmit="return altaasim(form1)">';
echo '<h2 class="form-signin-heading">Asientos Simples</h2><hr />';
echo '<div class="form-group">';
/// echo '<input type="number" class="form-control" placeholder="N&uacute;mero de Asiento" name="asiento" id="asiento" aria-describedby="help-nombre" size="11" maxlength="11" '.$readonly.'/>';
//echo '<span id="check-t"></span>';
//echo '<input type="date" class="form-control" placeholder="N&uacute;mero de Asiento" name="date" id="date" aria-describedby="help-nombre" size="11" maxlength="11" '.$readonly.'/>';
$sqlf="SELECT DATE_FORMAT(now(),'%Y-%m-%d') as hoy, CONCAT(SUBSTR(NOW(),1,5),'01-01') AS inicio, CONCAT(SUBSTR(NOW(),1,8),'01') AS minimo, DATE_FORMAT(DATE_ADD(now(),INTERVAL -1 DAY),'%Y-%m-%d') as ayer, DATE_SUB(NOW(), INTERVAL 7 DAY) AS sietedias, DATE_SUB(NOW(), INTERVAL 1 DAY) AS ayer, DATE_SUB(NOW(), INTERVAL 30 DAY) AS treintadias";
$stmt=$db_con->prepare($sqlf);
$stmt->execute();
$fechas=$stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <div class="form-group">
            </div>
        </div>
    </div>
</div>
<?php
echo '</div>';
// http://www.malot.fr/bootstrap-datetimepicker/
// http://develoteca.com/controlar-la-entrada-de-fecha-con-bootstrap-3-datepicker/
// http://eonasdan.github.io/bootstrap-datetimepicker/ ejemplos de fecha
//<form enctype='multipart/form-data' name='form1' action='altaasim.php' method='post' onSubmit="return altaasim(form1)">

?>

<?php

if (!$_POST['asiento']) {
/*
	$hoy = date("d/m/Y");
    $fechanueva=explode('/',$hoy);
	$fechanueva=$fechanueva[1].'/'.$fechanueva[0].'/'.$fechanueva[2];
	$sqlano="select substr(fech_ejerc,1,4) as ano from sgcaf100";
	$sqlfano=$db_con->prepare($sqlano);
  $sqlrano->execute();
	$sqlrano=$sqlfano->fetch(PDO::FETCH_ASSOC);
	$rango=$sqlrano['ano'];
	$sqlano='select substr(now(),1,4) as ano';
	$sqlfano=$db_con->prepare($sqlano);
  $sqlfano->execute();
	$sqlrano=$sqlfano->fetch(PDO::FETCH_ASSOC);
	if ($sqlrano['ano'] > $rango)
		$rango.=', '.$sqlrano['ano'];
	?>
	<input type="hidden" name="fecha" id="fecha" value=" <?php  echo $fechanueva; ?>"/>
   <span style="background-color: #ff8; cursor: default;"
         onmouseover="this.style.backgroundColor='#ff0';"
         onmouseout="this.style.backgroundColor='#ff8';"
         id="show_ingcapu" 
   ><?php  echo ($hoy); ?></span> *
<script type="text/javascript">
    Calendar.setup({
//		showAt(220, 250)
//		position       : 	{100,300},
        inputField     :    "fecha",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        displayArea    :    "show_ingcapu",       // ID of the span where the date is to be shown
//        daFormat       :    "%A, %B %d, %Y",// format of the displayed date
        daFormat       :    "%B %d, %Y",// format of the displayed date
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    true,
		weekNumbers    :    false, 
		range          :     [<?php echo $rango; ?>],

// desactivacion de 18 años pa' tras

					    });
</script>
	<?php 
*/
?>
<div class="form-group form-inline">
	<label for ="asiento">Asiento</label>
	<input class="form-control" type='text' name='asiento' value="<?php echo $asiento;?>" maxlength="11" size="11" <?php echo $readonly;?> > 

	<label for ="fecha">Fecha</label>

                <div class='input-group date' id='datetimepicker1'>
                <input type="text" name="daterange" id="daterange" value="01/01/2015" />
                <span class="input-group-addon">
  		        	<span class="glyphicon glyphicon-calendar"></span>
                </span>
  
                <script type="text/javascript">
                $(function() {
                    $('input[name="daterange"]').daterangepicker(
                    {
                        "singleDatePicker": true,
                        "timePicker": false,
                        "timePicker24Hour": false,
                        // timePickerIncrement: 10,
                        "applyLabel": "Guardar",
                        "cancelLabel": "Cancelar",
                        locale: {
                            format: 'YYYY-MM-DD', // HH:mm',
                        daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
                        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        customRangeLabel: 'Personalizado',
                        applyLabel: 'Aplicar',
//                        fromLabel: 'Desde',
 //                       toLabel: 'Hasta',
                    },
                        startDate: "<?php echo $fechas['minimo']?>",
                        endDate:  "<?php echo $fechas['hoy']?>", 
                        minDate: "<?php echo $fechas['inicio']?>",
                        maxDate: "<?php echo $fechas['hoy']?>", 
                        "ranges": {
                            "Hoy": [
                                "<?php echo substr($fechas['hoy'],0,10).' 00:00'?>",
                                "<?php echo $fechas['hoy']?>"
                            ],
                            "Ayer": [
                                "<?php echo substr($fechas['ayer'],0,10).' 00:00'?>",
                                "<?php echo $fechas['hoy']?>"
                            ],
                            "Ultimos 7 Dias": [
                                "<?php echo substr($fechas['sietedias'],0,10).' 00:00'?>",
                                "<?php echo $fechas['hoy']?>"
                            ],
                            "Ultimos 30 Dias": [
                                "<?php echo substr($fechas['treintadias'],0,10).' 00:00'?>",
                                "<?php echo $fechas['hoy']?>"
                            ]
                        },
                    }
                    );
                });
                </script>
                </div>
                </div>
<?php
	$temp = "Primer Registro:";
} else {
	echo $fecha.'<p />';
	echo "<input type = 'hidden' value ='".$fecha."' name='fecha'>"; 
	$temp = "Siguiente Registro:";
  $expli=$db_con->prepare("SELECT enc_explic FROM enc_contable WHERE enc_clave = '".$_POST['asiento']."'");
  $expli->execute();
	$expli = $expli->fetch(PDO::FETCH_ASSOC);
}
?>

<fieldset><legend><?php echo $temp;?></legend>

<?php
pantalla_asiento_simple($fecha,$debcre, $cuenta1, $cuenta2, $concepto, $referencia, $elmonto,$db_con);
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
//	echo "<br><a class='btn btn-danger' href='editasi2.php?asiento=$asiento&accion=boasi' onclick='return borrar_asiento()'>Borrar Asiento</a>&nbsp;&nbsp;&nbsp; - &nbsp;&nbsp;&nbsp;";
//	echo "<a class='btn btn-success' href='editasi2.php?asiento=$asiento&accion=altaapu'>A&nacute;adir Registro</a><p />";
/*
echo "<label>Soporte Contable</label> <input type='file' name='fich' size='19' maxlength='19'>";
if ($_POST['asiento']) {echo " (Si el asiento ya tiene un soporte será sustituído)";}
echo "<br /><label>Explicación</label> <textarea name='explicacion' rows='4' cols='90'>$expli[0]</textarea>";
// echo "<p />";
*/
if ($_GET['n'] == 1) {
	echo "<input class='btn btn-info' type='submit' name='boton' value=\"Guardar Asiento\" tabindex='10' onclick='return valfecha(form1)'> ";
//	echo "<input type='submit' name='boton' value=\"Guardar Asiento\" tabindex='10' onclick='return reviso()'> ";
} else {
	echo "<input class='btn btn-success' type='submit' name='boton' value=\"Guardar Registro\" tabindex='10' onclick='return valfecha(form1)'>";
	if ($elmonto) {
		echo "&nbsp;&nbsp;&nbsp;<a href='altaasim.php?n=1'";
		if ($cuadre) {echo " onclick=\"return confirm('Asiento descuadrado ¿Continuar con nuevo Asiento?')\"";}
		echo ">Crear nuevo Asiento</a>";
	}
}
echo '</td><tr>';
echo '</table>';
echo '</div>';
?>
</fieldset><p style="clear:both">
<?php // <p /> 
?>
</form>

<?php

// echo $mensaje;

//if ($anadido) 
{

	echo "<table class='table' width='800'>";
	cabasi(2);
	asiento($asiento,"1",$_SESSION['moneda'],$_SESSION['deci'],$_GET['bojust'], $db_con);
	echo "</table>";

}

?>

</div></body></html>

<?php
function pantalla_asiento_simple($fechax,$elcargo, $cuenta1, $cuenta2, $concepto, $referencia, $elmonto, $db_con)
{
echo "<table class='table' width='800'>";
echo '<tr><th width="100">Cuenta Debe</th><th width="100">Cuenta Haber</th><th width="200">Concepto</th><th width="80">Referencia</th><th width="80">Monto</th></tr>';
// <th width="50">Fecha</th>
// <tr><td>
// <input type = 'text' maxlength='8' size='8' name='fecha' value='<?php echo $fechax;>' readonly='readonly' tabindex='3'>
// </td>
echo '<td width="100"> ';
$activar=' ';
if (($elcargo == '+')) {$activar='checked="checked"'; } else { $activar = ' '; }
//  || ($elcargo = 1)
// <input type='text' name='cuenta1' size='20' maxlengt='20' tabindex='5' value ="<?php echo $cuenta1;>"><br>
// <input type='text' name='cuenta2' size='20' maxlengt='20' tabindex='6' value ="<?php echo $cuenta2;>">
?>
	<input type="text" size="20" tabindex='5' name='cuenta1' id="inputString" onKeyUp="lookup(this.value);" onBlur="fill();" value ="<?php echo $cuenta1;?>" autocomplete="off"/>
			<div class="suggestionsBox" id="suggestions" style="display: none;">
				<img src="upArrow.png" style="position: relative; top: -12px; left: 70px; "  alt="upArrow" />
				<div class="suggestionList" id="autoSuggestionsList">
				</div>
			</div>
		</div>
</td><td width="100">
<input type="text" size="20" tabindex='5' name='cuenta2' id="inputString2" onKeyUp="lookup2(this.value);" onBlur="fill2();" value ="<?php echo $cuenta2;?>" autocomplete="off"/>
			<div class="suggestionsBox2" id="suggestions2" style="display: none;">
				<img src="upArrow.png" style="position: relative; top: -12px; left: 70px; "  alt="upArrow" />
				<div class="suggestionList2" id="autoSuggestionsList2">
				</div>
			</div>
		</div>
</td><td>
<input type = 'text' size='40' maxlength='60' name='concepto' tabindex='7' value ="<?php echo $concepto?>">
</td><td>
<input type = 'text' value ='<?php echo $referencia?>' size='10' maxlength='10' name='referencia' tabindex='8'>
</td><td>
<input type = 'text' size='11' maxlength='11' name='elmonto' value='<?php echo $elmonto;?>' tabindex='9'>
</td>
</tr>
<tr>

<?php
}
?>
