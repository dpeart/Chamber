<?php

include_once 'conn.php';

$cmd = $_POST['cmd'];

switch ($cmd) {
  case 'createBatch': {
    $val = $_POST['val'];
    //TODO: Make sure batch name doesn't already exist
    $query = "INSERT INTO Batch (Name) VALUES ('$val');";
    break;
  }
  
  case 'deleteBatch': {
    $val = $_POST['val'];
    $query = "DELETE FROM Batch WHERE idBatch = $val;";
    break;
  }
  
  case 'updateBatch': {
    $val = $_POST['val'];
    //INSERT INTO `chamber`.`meat` (`idBatch`, `Name`, `Recipe`, `Culture`, `Mold`) VALUES ('17', 'a', 'b', 'c', 'd');
    $query = "INSERT INTO Meat (idBatch, Name, Recipe, Culture, Mold) VALUES $val;";
   }
}

//connect to database
$mysqli = doDB();

$mysqli->query($query) or exit($mysqli->error);

echo json_encode($query);
//$mysqli->close();
?>