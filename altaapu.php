<?php
echo "<form action='editasi2.php?asiento=".$asiento."&accion=altaapu1' name='form1' method=post>\n";


	echo "<table class='table'> \n"; 

	echo "<tr><th>Asiento</th><th>Descripcion</th></tr>\n";

	echo "<tr><td>\n";

	echo "<input class='form-control' type = 'text' value ='$asiento' size='11' maxlength='11' name='asiento' readonly='readonly' onfocus='form1.fecha.focus()' tabindex='1'>";
	echo "</td><td>\n";
	$sql="SELECT *, date_format(enc_fecha, '%d/%m/%y') AS fechax FROM ".$_SESSION['institucion']."sgcaf830 WHERE enc_clave = $asiento";
	$result = $db_con->prepare($sql); 
	$result->execute();
	$row = $result->fetch(PDO::FETCH_ASSOC);
	echo "<input class='form-control' type = text value ='".$row['enc_explic']."' size=150 maxlength=150 name=tipo readonly='readonly' tabindex=2>";
// 	echo "<input type = hydden name=fecha value ='".$result['fechax']."'>";
	$elmonto=abs($row[enc_debe]-$row[enc_haber]);
	echo "</td></tr></table><p />\n";

pantalla_asiento($row['enc_fecha'],$elcargo, $cuenta1, $concepto, $fila["com_refere"], $elmonto, $db_con);
?>

<tr>

<td colspan='7' class='dcha'>

<input class='btn btn-warning' type = 'submit'name = 'formu' value = 'A&ntilde;adir' tabindex='10' onclick='return compruebafecha(form1)'>

</td>

</tr>

</table>

</form>
