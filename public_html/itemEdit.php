<?php

$idMeat = intval($_REQUEST['idMeat']);
$name = $_REQUEST['Name'];
$recipe= $_REQUEST['Recipe'];
$culture = $_REQUEST['Culture'];
$mold = $_REQUEST['Mold'];
$idDrying = $_REQUEST['idDrying'];
$DSW = $_REQUEST['DSW'];
$DEW = $_REQUEST['DEW'];
$DSPH = $_REQUEST['DSPH'];
$DEPH = $_REQUEST['DEPH'];
$DSD = $_REQUEST['DSD'];
$DED = $_REQUEST['DED'];
$idFermentation = $_REQUEST['idFermentation'];
$FSW = $_REQUEST['FSW'];
$FEW = $_REQUEST['FEW'];
$FSPH = $_REQUEST['FSPH'];
$FEPH = $_REQUEST['FEPH'];
$FSD = $_REQUEST['FSD'];
$FED = $_REQUEST['FED'];

include 'conn.php';

$error = 0;

$mysqli = doDB();

$sql = "update Meat set Name='$name',Recipe='$recipe',Culture='$culture',Mold='$mold' where idMeat=$idMeat;";
$result = $mysqli->query($sql);

$sql = "update Drying set `Start Weight`='$DSW',`End Weight`='$DEW'," .
       "`Start PH`='$DSPH',`End PH`='$DEPH'," .
       "`Start Diameter`='$DSD', `End Diameter`='$DED'" .
        "where idDrying='$idDrying';";
$result = $mysqli->query($sql);
if (!$result) {
  $error = 1;
}

$sql = "update Fermentation set `Start Weight`='$FSW',`End Weight`='$FEW'," .
       "`Start PH`='$FSPH',`End PH`='$FEPH'," .
       "`Start Diameter`='$FSD', `End Diameter`='$FED'" .
        "where idFermentation=$idFermentation;";
$result = $mysqli->query($sql);
if (!$result) {
  $error = 1;
}

if (!$error){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}
?>