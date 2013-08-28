<?php

include_once 'safemysql.class.php';
//include_once 'include.php';

//function doDB() {
//global $mysqli;
  //Connect to database
  $opts = array(
            'host' => "p:localhost",
            'user' => "David",
            'pass' => "",
            'db' => "Chamber"
          );
  
  $db = new SafeMySQL($opts);
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", $db->connect_error);
    exit();
  }
//  return $db;
//}

//connect to database
//$db = doDB();

$cmd = $_POST['cmd'];

switch ($cmd) {
  case 'list': {
      $res = $db->getOne("SELECT * FROM Batch;");
      break;
    }
  case 'id': {
    $res = $db->getAll("SELECT * FROM Batch WHERE idBatch = ?i",$_POST['val']);
    break;
  }  
  case 'meat': {
   $res = $db->getAll('SELECT * from meat where idBatch= ?i',$_POST['id']);
   break;
  }
} 

if ($res->numRows()==0) { 
    echo "no results"; 
} else {
  while ($r = $res->fetch_assoc())
    $rows[] = $r;
  $res->close(); 
}






//$db->close();


?>