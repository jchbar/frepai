<?php
include("home.php");
extract($_GET);
extract($_POST);
extract($_SESSION);
$sqlf="SELECT DATE_FORMAT(now(),'%Y-%m-%d %H:%i') as hoy, CONCAT(SUBSTR(NOW(),1,5),'01-01') AS inicio, CONCAT(SUBSTR(NOW(),1,8),'01') AS minimo, DATE_FORMAT(DATE_ADD(now(),INTERVAL -1 DAY),'%Y-%m-%d') as ayer, DATE_SUB(NOW(), INTERVAL 7 DAY) AS sietedias, DATE_SUB(NOW(), INTERVAL 1 DAY) AS ayer, DATE_SUB(NOW(), INTERVAL 30 DAY) AS treintadias";
$stmt=$db_con->prepare($sqlf);
$stmt->execute();
$fechas=$stmt->fetch(PDO::FETCH_ASSOC);
?>
    <div class="row">
        <div id="intro" class="col-md-6 col-md-offset-1">
            <div class="form-group">
                <label for="rango">Indique lapso de fecha para reporte</label>
                <input type="text" name="daterange" id="daterange" value="01/01/2015 - 01/31/2015" />

                <script type="text/javascript">
                $(function() {
                    $('input[name="daterange"]').daterangepicker(
                    {
                        "timePicker": true,
                        "timePicker24Hour": true,
                        // timePickerIncrement: 10,
                        "applyLabel": "Guardar",
                        "cancelLabel": "Cancelar",
                        "fromLabel": "Desde",
                        "toLabel": "Hasta",
                        locale: {
                            format: 'YYYY-MM-DD HH:mm',
                        daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
                        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        customRangeLabel: 'Personalizado',
                        applyLabel: 'Aplicar',
                        fromLabel: 'Desde',
                        toLabel: 'Hasta',
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
    </div>

