<?php

$idMeat = intval($_REQUEST['id']);
$name = $_REQUEST['Name'];
$recipe= $_REQUEST['Recipe'];
$culture = $_REQUEST['Culture'];
$mold = $_REQUEST['Mold'];

include 'conn.php';

$sql = "update meat set Name='$name',Recipe='$recipe',Culture='$culture',Mold='$mold' where idMeat=$idMeat;";
//$sql = "update users set firstname='$firstname',lastname='$lastname',phone='$phone',email='$email' where id=$id";
$result = $conn->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}
?>