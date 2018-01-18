<?php
$row_id=$_GET['row_id'];
$sql="SELECT * FROM detalle_contable WHERE nro_registro = :row_id";
$result = $db_con->prepare($sql);
$result->execute(array(":row_id"=>$row_id));
$fila = $result->fetch(PDO::FETCH_ASSOC);
$a=explode("-",$fila["com_fecha"]); 

echo "<form action='editasi2.php?asiento=".$asiento."&row_id=".$fila['nro_registro']."&accion=editapu1' name='form1' method='post'>";
$fecha=$a[2]."/".$a[1]."/".substr($a[0],2,2);
if (($fila['com_debcre']== '+')) $elmonto=$fila['com_monto1']; else $elmonto=$fila['com_monto2'];
pantalla_asiento($fecha,$fila['com_debcre'], $fila['com_cuenta'], $fila['com_descri'], $fila["com_refere"], $elmonto, $db_con);
echo "<tr><td colspan='6' class='dcha'>";

echo "<input class='btn btn-warning' type = 'submit' name = 'formu' value = 'Confirmar cambios' tabindex='10' onclick='return compruebafecha(form1)'>";
echo "</td></tr>";
echo "</table>";
echo "</form>";
?>
