<?php

include_once 'include.php';

$cmd = $_POST['cmd'];

switch ($cmd) {
  case 'list': {
      $query = 'SELECT * FROM Batch';
      break;
    }
  case 'id': {
    $query = 'SELECT * FROM Batch WHERE idBatch = ' . $_POST['val'] . ';';
    break;
  }  
  case 'meat': {
   $query = 'SELECT * from meat where idBatch=' . $_POST['id'] . ';';
   break;
  }
}

//connect to database
$mysqli = doDB();

$res = $mysqli->query($query) or exit($mysqli->error);

if ($res->num_rows==0) { 
    echo "no results"; 
} else {
  while ($r = $res->fetch_assoc())
    $rows[] = $r;
  $res->close();  
}

//$mysqli->close();

echo json_encode($rows);
?>