<?php

//edatagrid changes the spaces to _
$idStatus = intval($_REQUEST['idStatus']);
$idBatch = intval($_REQUEST['idBatch']);
$Temp = floatval($_POST['Temp_Setpoint']);
$Humidity = floatval($_REQUEST['Humidity_Setpoint']);
$Readings = intval($_REQUEST['Store_Readings']);
$HKp = floatval($_REQUEST['PID_H_Kp']);
$HKi = floatval($_REQUEST['PID_H_Ki']);
$HKd = floatval($_REQUEST['PID_H_Kd']);
$TKp = floatval($_REQUEST['PID_T_Kp']);
$TKi = floatval($_REQUEST['PID_T_Ki']);
$TKd = floatval($_REQUEST['PID_T_Kd']);
$Sensor = floatval($_REQUEST['Sensor_Read_Timeout']);
$Window = floatval($_REQUEST['Window_Size']);
$Sample = floatval($_REQUEST['Sample_Delay']);
#$ = intval($_REQUEST['']);

include 'conn.php';

$mysqli = doDB();

$sql = "update Status set `idBatch`='$idBatch', `Temp Setpoint`='$Temp', `Humidity Setpoint`='$Humidity', `Store Readings`='$Readings', " .
        "`PID H Kp`='$HKp', `PID H Ki`='$HKi', `PID H Kd`='$HKd', `PID T Kp`='$TKp', `PID T Ki`='$TKi', `PID T Kd`='$TKd', " .
        "`Sensor Read Timeout`='$Sensor', `Window Size`='$Window', `Sample Delay`='$Sample' where `idStatus`='$idStatus'";
$result = $mysqli->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}
?>