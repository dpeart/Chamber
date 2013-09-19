<?php
function doDB() {
//global $mysqli;
  //Connect to database
 // $mysqli = new mysqli("p:localhost", "root", "", "Chamber");
  $mysqli = new mysqli("p:192.168.1.127", "root", "1Deseret", "Chamber");

  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
  }
  return $mysqli;
}
?>