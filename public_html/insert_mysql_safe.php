<?php

include_once 'safemysql.class.php';
include_once 'include.php';

$cmd = $_POST['cmd'];

switch ($cmd) {
  case 'createBatch': {
    $val = $_POST['val'];
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
    $query = "INSERT INTO Meat (idBatch, Name, Recipe, Culture, Mold) VALUES $val;";
   }
}

//connect to database
//$mysqli = doDB();

//$mysqli->query($query) or exit($mysqli->error);

//$mysqli->close();
?>