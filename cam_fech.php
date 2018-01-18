<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
include("home.php");
extract($_GET);
extract($_POST);
extract($_SESSION);
include("paginar.php");
// include("funciones.php");
// <script src="ajxconc.js" type="text/javascript"></script>
?>
<body>

<?php
$readonly=" readonly='readonly'";
$cta = $_GET['cta'];
$nactivo=$_GET['nactivo'];
$ip = la_ip();
if ($accionIn=="Verificar") 
{
   // echo '<div id="div1">';
    $sql= "SELECT *, date_format(enc_fecha, '%d/%m/%Y') as fecha FROM enc_contable where enc_clave=:numero"; 
	$result=$db_con->prepare($sql);
	$result->execute([":numero"=>$numero]);
	if ($result->rowCount() == 0)
	{
   		mensaje(['tipo'=>'danger','titulo'=>'Aviso','texto'=>'<h2>El N&uacute;mero de comprobante no existe</h2>']);
	}
	else 
	{
		echo "<form class='form-inline' action='cam_fech.php?accionIn=Procesar' name='form1' method='post' onsubmit='return explicacion_cheque(form1)'>";
	    pantalla_verificar($result, $accionIn, $numero, $db_con);
		echo "<input class='btn btn-info' type = 'submit' value = 'Procesar'>";
		echo '</form>';
	}
}
if ($accionIn=="Procesar") 
{
 
/*
    $fecha=convertir_fecha($fecha);
	$fecha=strtotime($fecha);
    $mes=date('m',$fecha);
	$ano=date('Y',$fecha);
	
    $fechanueva=convertir_fecha($fechanueva);
	$fechan=strtotime($fechanueva);
    $mesn=date('m',$fechan);
	$anon=date('Y',$fechan);
	echo $fechanueva; 
	echo $numero; 
*/
	$fecha=$_POST['fecha'];
	$lafecha=explode('-', $fecha);
	$mes=$lafecha[1];
	$ano=$lafecha[0];

	$fechanueva=$_POST['fechanueva'];
	$lafechan=explode('-', $fechanueva);
	$mesn=$lafechan[1];
	$anon=$lafechan[0];
	$numero=$_POST['numero'];

	/////////////////////////////////////////FECHA ORIGINAL Y FECHA NUEVA SON IGUALES///////////////////////////////////
//	if ($mes==$mesn and $ano==$anon)
	try
	{
		$sql= "SELECT * FROM detalle_contable where com_nrocom=:numero"; 
		$rs=$db_con->prepare($sql);
		$rs->execute([":numero"=>$numero]);
		// echo $sql;
		while ($row = $rs->fetch(PDO::FETCH_ASSOC))
		{
			$sql="UPDATE detalle_contable SET com_fecha=:fechanueva WHERE com_nrocom =:numero";
// 		   	echo $sql;
			$res=$db_con->prepare($sql);
			$res->execute([":fechanueva"=>$fechanueva,":numero"=>$numero]);
		// echo $sql;
			// or die ("<p />El usuario $usuario no tiene permisos para modificar Clientes <br>".mysql_error()."<br>".$sql);
	    }
		$sql="UPDATE enc_contable SET enc_fecha=:fechanueva WHERE enc_clave =:numero";
    //	echo $sql;
		$res=$db_con->prepare($sql);
		// echo $sql;
		$res->execute([":fechanueva"=>$fechanueva,":numero"=>$numero]);
 		mensaje(['tipo'=>'info','titulo'=>'Aviso','texto'=>'<h2>Cambio de Fecha a comprobante realizado</h2>']);
	} 
	catch (PDOException $e) 
	{
 		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage()]);
			die('Fallo call'. $e->getMessage());
	}
/*
	/////////////////////////////////////////FECHA ORIGINAL Y FECHA NUEVA SON DIFERENTES///////////////////////////////////
	else 
	{
	$sql= "SELECT * FROM detalle_contable where com_nrocom='$numero'"; 
	$rs=mysql_query($sql);
	while ($row = mysql_fetch_assoc($rs))
		{
	$sql="UPDATE detalle_contable SET com_fecha='$fechanueva' WHERE com_nrocom ='$numero'";
    echo $sql;
	mysql_query($sql) or die ("<p />El usuario $usuario no tiene permisos para modificar Clientes <br>".mysql_error()."<br>".$sql);
	    }
	$sql="UPDATE enc_contable SET enc_fecha='$fechanueva' WHERE enc_clave ='$numero'";
//    echo $sql;
	mysql_query($sql) or die ("<p />El usuario $usuario no tiene permisos para modificar Clientes <br>".mysql_error()."<br>".$sql);
	}
*/
	$accionIn=''; 
}
if (!$accionIn) 
{
	echo '<div id="div1">';
 	echo "<form action='cam_fech.php?accionIn=Verificar' name='form1' method='post' onsubmit='return explicacion_cheque(form1)'>";
    pantalla_act_comprobante($result,$accionIn);
	echo "<input class='btn btn-info' type = 'submit' value = 'Buscar'>";
	echo '</form>';
}   

function pantalla_act_comprobante($result,$accionIn) {
$deci=$_SESSION['deci'];
$sep_decimal=$_SESSION['sep_decimal'];
$sep_miles=$_SESSION['sep_miles'];
//echo $sql; 
if ($accionIn == '!$accionIn') { $lectura = 'readonly = "readonly"'; $activada="disabled" ; } else {$lectura=''; $activada='';}
	echo '<div class="form-group form-inline row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
  	echo '<label><fieldset><legend>Cambio de Fecha en Comprobante</legend>';
    echo '<label for="numero" Nro. de Comprobante</label>';
	echo '<input placeholder="Nro. de Comprobante" class="form-control" type="text" id="numero" name="numero" value="" size="20" maxlength="20" />*';
}

function pantalla_verificar($result,$accionIn, $numero, $db_con) {
$deci=$_SESSION['deci'];
$sep_decimal=$_SESSION['sep_decimal'];
$sep_miles=$_SESSION['sep_miles'];
//echo $sql; 
$row=$result->fetch(PDO::FETCH_ASSOC);
if ($accionIn == 'Verificar') { $lectura = 'readonly = "readonly"'; $activada="disabled" ; } else {$lectura=''; $activada='';}
  	echo '<fieldset><legend>Cambio de Fecha a Comprobante</legend>';
  	echo '<table class="table">';
    echo '<td><label for="numero"> Nro. de Comprobante</label></td><td>';
	echo '<input name="numero" type="text" id="numero" value="'.$numero.'" '.$lectura.' size="12" maxlength="12" /></td>';
	echo '<td><label for ="fecha">Fecha</label><td>';
	echo '<input name="fecha" type="text" id="fecha" value="'.$row['fecha'].'" '.$lectura .' size="10" maxlength="10" /></td><tr>';
	echo '<td><label for="concepto">Concepto</label></td><td colspan="3">';
	echo '<input name="concepto" type="text" id="concepto" value="'.$row['enc_desco'].'" '.$lectura.' size="60" maxlength="60" /></td><tr>';
	echo '<td><label for="monto">Monto</label></td><td>';
	echo '<input name="monto" type="text" id="monto" value="'.number_format($row['enc_debe'],2,".",",").' "'. $lectura.' size="12" maxlength="12" /></td>';
	echo '<td><label for ="fechanueva">Fecha Nueva</label></td><td>';
//	<input type="hidden" name="fechanueva" id="fechanueva" value=" <php  echo ($row['fecha']); >"/>
    $fechanueva=explode('/',$row['fecha']);
	$fechanueva=$fechanueva[1].'/'.$fechanueva[0].'/'.$fechanueva[2];
	$sqlano='select substr(fech_ejerc,1,4) as ano from institucion';
	$sqlfano=$db_con->prepare($sqlano);
	$sqlfano->execute();
	$sqlrano=$sqlfano->fetch(PDO::FETCH_ASSOC);
	$rango=$sqlrano['ano'];
	$sqlano='select substr(now(),1,4) as ano';
	$sqlfano=$db_con->prepare($sqlano);
	$sqlfano->execute();
	$sqlrano=$sqlfano->fetch(PDO::FETCH_ASSOC);
	if ($sqlrano['ano'] > $rango)
		$rango.=', '.$sqlrano['ano'];
//	<input type="hidden" name="fechanueva" id="fechanueva" value=" <?php  echo $fechanueva; "/>

	$sqlf="SELECT DATE_FORMAT(now(),'%Y-%m-%d') as hoy, CONCAT(SUBSTR(NOW(),1,5),'01-01') AS inicio, CONCAT(SUBSTR(NOW(),1,8),'01') AS minimo, DATE_FORMAT(DATE_ADD(now(),INTERVAL -1 DAY),'%Y-%m-%d') as ayer, DATE_SUB(NOW(), INTERVAL 7 DAY) AS sietedias, DATE_SUB(NOW(), INTERVAL 1 DAY) AS ayer, DATE_SUB(NOW(), INTERVAL 30 DAY) AS treintadias";
	$stmt=$db_con->prepare($sqlf);
	$stmt->execute();
	$fechas=$stmt->fetch(PDO::FETCH_ASSOC);
	?>

	<div class='input-group date' id='datetimepicker1'>
    	<input type="text" name="fechanueva" id="fechanueva" value="01/01/2015" />
        <span class="input-group-addon">
  			<span class="glyphicon glyphicon-calendar"></span>
        </span>
  
        <script type="text/javascript">
        $(function() {
        	$('input[name="fechanueva"]').daterangepicker(
            {
                        "singleDatePicker": true,
                        "timePicker": false,
                        "timePicker24Hour": false,
                        // timePickerIncrement: 10,
                        "applyLabel": "Guardar",
                        "cancelLabel": "Cancelar",
                        locale: {
                            format: 'YYYY-MM-DD', //  HH:mm',
                        daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
                        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        customRangeLabel: 'Personalizado',
                        applyLabel: 'Aplicar',
//                        fromLabel: 'Desde',
 //                       toLabel: 'Hasta',
                    },
                        startDate: "<?php echo $fechas['hoy']?>",
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
<?

?>
</table>
 	&nbsp;</td></tr> 

<?php 
}
/*
update wp_postmeta set meta_value= replace(meta_value, '.com.ve/site/', '.com.ve/') 
*/
?>
