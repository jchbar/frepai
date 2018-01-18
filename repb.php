<?php
include("home.php");
extract($_GET);
extract($_POST);
extract($_SESSION);
?>

<script language="javascript">
function abrir2Ventanas(arreglo)
{
	window.open("repb_pdf.php?arreglo="+arreglo,"parte1","top=0,left=395,status=no,toolbar=no,scrollbar=yes,location=no,type=fullWindow,fullscreen");	
}
</script>
<?php
//	echo "<form action='prenompre.php?accion=Lista2DeCuotas' name='form1' method='post' class='form-inline'>";
	echo '<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-md-offset-1">';
	mensaje(['tipo'=>'info','titulo'=>'Aviso','texto'=>'<h3>Generar Reporte de Beneficiarios</h3>']);
	echo '<input type="submit" class="btn btn-info" name="Submit" value="Realizar Impresi&oacute;n del Listado" onClick="abrir2Ventanas(';
	echo "'";
	echo $arreglo;
	echo "'";
	echo ');">  ';
	echo '</div>';

//	echo '</form>';
?>
</body>
</html>

