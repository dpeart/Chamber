<?php

$idBatch = intval($_REQUEST['id']);
$name = $_REQUEST['Name'];

include 'conn.php';

$mysqli = doDB();

$sql = "update batch set Name='$name' where idBatch=$idBatch;";
$result = $mysqli->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}
?>