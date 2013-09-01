<?php

$idMeat = intval($_REQUEST['idMeat']);
$name = $_REQUEST['Name'];
$recipe= $_REQUEST['Recipe'];
$culture = $_REQUEST['Culture'];
$mold = $_REQUEST['Mold'];

include 'conn.php';

$mysqli = doDB();

$sql = "update Meat set Name='$name',Recipe='$recipe',Culture='$culture',Mold='$mold' where idMeat=$idMeat;";
$result = $mysqli->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}
?>