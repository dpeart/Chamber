<?php

$start = isset($_REQUEST['start']);
$end = isset($_REQUEST['end']) ? intval($_REQUEST['end']) : -1;

include 'conn.php';

$mysqli = doDB();

$query = 'SELECT * FROM Readings WHERE idReadings BETWEEN $start AND $end';
$res = $mysqli->query($query) or exit($mysqli->error);

if ($res->num_rows==0) { 
    $rows = "no results"; 
} else {
  while ($r = $res->fetch_assoc())
    $rows[] = $r;
  $res->close();  
}
echo json_encode($rows);

?>
