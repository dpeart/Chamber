<?php

include 'conn.php';

$mysqli = doDB();

$query = 'SELECT * FROM Status';
$res = $mysqli->query($query) or exit($mysqli->error);

$items = array();
while ($row = $res->fetch_object())
  array_push($items, $row);
$result["rows"] = $items;
$res->close();  

echo json_encode($result);

?>
