<?php

$id = intval($_REQUEST['id']);

include 'conn.php';

$mysqli = doDB();

$sql = "delete from Meat where idMeat=$id";
$result = $mysqli->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Some errors occured.' . $sql));
}
?>