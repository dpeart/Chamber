<?php

include_once 'conn.php';

$name = $_POST['Name'];
//TODO: Make sure batch name doesn't already exist
$query = "INSERT INTO Batch (Name) VALUES ('$name')";

//connect to database
$mysqli = doDB();

$result = $mysqli->query($query) or exit($mysqli->error);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Some errors occured.' . $query));
}

?>