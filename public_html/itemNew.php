<?php

$batch = $_REQUEST['idBatch'];
$name = $_REQUEST['Name'];
$recipe= $_REQUEST['Recipe'];
$culture = $_REQUEST['Culture'];
$mold = $_REQUEST['Mold'];
$DSW = $_REQUEST['DSW'];
$DEW = $_REQUEST['DEW'];
$DSPH = $_REQUEST['DSPH'];
$DEPH = $_REQUEST['DEPH'];
$DSD = $_REQUEST['DSD'];
$DED = $_REQUEST['DED'];
$FSW = $_REQUEST['FSW'];
$FEW = $_REQUEST['FEW'];
$FSPH = $_REQUEST['FSPH'];
$FEPH = $_REQUEST['FEPH'];
$FSD = $_REQUEST['FSD'];
$FED = $_REQUEST['FED'];
include 'conn.php';

$mysqli = doDB();

// Create a new Item
$query = "INSERT INTO Meat (idBatch, Name, Recipe, Culture, Mold) VALUES ('$batch','$name','$recipe','$culture','$mold');";
$result = $mysqli->query($query);
echo json_encode($query);

// Get idMeat for the newly created item
$idMeat = $mysqli->insert_id;
echo json_encode($idMeat);

//insert into Drying table using newly created idMeat
$sql = "insert into Drying (idMeat,`Start Weight`,`End Weight`," .
       "`Start PH`,`End PH`,`Start Diameter`,`End Diameter`)" .
        "values ('$idMeat','$DSW','$DEW','$DSPH','$DEPH','$DSD','$DED');";
$result = $mysqli->query($sql);
echo json_encode($sql);
if (!$result) {
  $error = 1;
}

// insert into Fermentation table using newly created idMeat
$sql = "insert into Fermentation (idMeat,`Start Weight`,`End Weight`," .
       "`Start PH`,`End PH`,`Start Diameter`,`End Diameter`)" .
        "values ('$idMeat','$FSW','$FEW','$FSPH','$FEPH','$FSD','$FED');";
$result = $mysqli->query($sql);
echo json_encode($sql);
if (!$result) {
  $error = 1;
}

if (!$error){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Some errors occured.' . $query));
}
?>