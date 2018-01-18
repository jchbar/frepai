<?php
include("home.php");
extract($_GET);
extract($_POST);
extract($_SESSION);
?>

<script language="javascript">
function callprogress(vValor) { // , vgeneral){
 document.getElementById("progress-txt").innerHTML = vValor;
 document.getElementById("progress-txt").innerHTML = '<div class="progress-bar" role="progressbar" style="width:'+vValor+'%; min-width:10%">'+vValor+'%</div>';
}

function callprogress2(vgeneral){
 document.getElementById("progress-gral").innerHTML = vgeneral;
 document.getElementById("progress-gral").innerHTML = '<div class="progress-bar" role="progressbar" style="width:'+vgeneral+'%; min-width:10%">'+vgeneral+'%</div>';
}
</script>

<body>

<?php
if (!$_POST['procesar'])
{
	$validado=' '; // checked="checked"';
	echo "<form class='form-inline' enctype='multipart/form-data' action='reiniciar.php' method='post' name='form1'> \n";
	echo "<br>";
	echo '<div class="row">';
		echo '<div class="col-md-12">';
			echo '<fieldset><legend> Primer Trimestre </legend>';
			echo '<input class="form-control" name="enero" type="checkbox" tabindex="1" value="1" >'.$validado.'Enero';
			echo '<input class="form-control" name="febrero" type="checkbox" tabindex="2" value="1"'.$validado.'>Febrero';
			echo '<input class="form-control" name="marzo" type="checkbox" tabindex="3" value="1"'.$validado.'>Marzo';
			echo '</fieldset>';

			echo '<fieldset><legend> Segundo Trimestre </legend>';
			echo '<input class="form-control" name="abril" type="checkbox" tabindex="4" value="1"'.$validado.'>Abril';
			echo '<input class="form-control" name="mayo" type="checkbox" tabindex="5" value="1"'.$validado.'>Mayo';
			echo '<input class="form-control" name="junio" type="checkbox" tabindex="6" value="1"'.$validado.'>Junio';
			echo '</fieldset>';


		echo '</div>';
	echo '</div>';



	echo '<fieldset><legend> Tercer Trimestre </legend>';
	echo '<input class="form-control" name="julio" type="checkbox" tabindex="7" value="1"'.$validado.'>Julio';
	echo '<input class="form-control" name="agosto" type="checkbox" tabindex="8" value="1"'.$validado.'>Agosto';
	echo '<input class="form-control" name="septiembre" type="checkbox" tabindex="9" value="1"'.$validado.'>Septiembre';
	echo '</fieldset>';

	echo '<fieldset><legend> Cuarto Trimestre </legend>';
	echo '<input class="form-control" name="octubre" type="checkbox" tabindex="10" value="1"'.$validado.'>Octubre';
	echo '<input class="form-control" name="noviembre" type="checkbox" tabindex="11" value="1"'.$validado.'>Noviembre';
	echo '<input class="form-control" name="diciembre" type="checkbox" tabindex="12" value="1"'.$validado.'>Diciembre <br>';
	echo '</fieldset>';
	
	echo "<input class='btn btn-info' type='submit' name='procesar' value='Procesar'></form> \n";
//	include("pie.php");
//	echo "</div></body></html>";
//	exit;

// }
}
else
{
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<div id="progress-txt" class="progress  progress-bar-success">';
				// echo '<div id="progress-bs" class="progress-bar" role="progressbar" style="width:30%; min-width:10%">';
				echo '<div class="progress-bar" role="progressbar" style="width:30%">';
					echo '0%';
				echo '</div>';
				echo '<span class="sr-only"></span>';
			echo '</div>';

			echo '<div id="progress-gral" class="progress  progress-bar-success">';
				// echo '<div id="progress-bs" class="progress-bar" role="progressbar" style="width:30%; min-width:10%">';
				echo '<div class="progress-bar" role="progressbar" style="width:30%">';
					echo '0%';
				echo '</div>';
				echo '<span class="sr-only"></span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	$sql="SELECT * FROM ".$_SESSION['institucion']."sgcafniv order by con_nivel";
	$losniveles = $db_con->prepare($sql); 
	try
	{
		$losniveles->execute();
		if ($losniveles->rowCount() == 0) {
			mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>No se han definido los niveles</h2>']);
			exit;
		}
		$losniveles=$losniveles->fetchall();
	//		print_r($losniveles);
		// arreglar esto
		$arreglo=array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		$seleccion=0;
		for ($i=0; $i<count($arreglo); $i++)
		{
			$variable=$arreglo[$i];
			if ($$variable == 1)
				$seleccion++;
		}
		// fin arreglar esto
		$proceados=1;
		$proceados+=chequear_procesar($enero,$losniveles,1, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($febrero,$losniveles,2, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($marzo,$losniveles,3, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($abril,$losniveles,4, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($mayo,$losniveles,5, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($junio,$losniveles,6, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($julio,$losniveles,7, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($agosto,$losniveles,8, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($septiembre,$losniveles,9, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($octubre,$losniveles,10, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($noviembre,$losniveles,11, $db_con, $seleccion, $proceados);
		$proceados+=chequear_procesar($diciembre,$losniveles,12, $db_con, $seleccion, $proceados);
	}
	catch (PDOException $e) 
	{
		mensaje(['tipo'=>'warning','titulo'=>'Aviso','texto'=>'<h2>Fallo llamado</h2>'.$e->getMessage()]);
		die('Fallo call'. $e->getMessage());
	}
}
?>
</body></html>
