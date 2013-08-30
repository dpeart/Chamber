<?php

$batch = $_REQUEST['idBatch'];
$name = $_REQUEST['Name'];
$recipe= $_REQUEST['Recipe'];
$culture = $_REQUEST['Culture'];
$mold = $_REQUEST['Mold'];

include 'conn.php';

$mysqli = doDB();

$query = "INSERT INTO Meat (idBatch, Name, Recipe, Culture, Mold) VALUES ('$batch','$name','$recipe','$culture','$mold');";
$result = $mysqli->query($query);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Some errors occured.' . $query));
}
?>