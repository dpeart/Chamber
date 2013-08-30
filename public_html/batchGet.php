<?php

$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$rows = isset($_REQUEST['rows']) ? intval($_REQUEST['rows']) : 10;
$offset = ($page-1)*$rows;
$result = array();

include 'conn.php';

$mysqli = doDB();

$query = 'SELECT * FROM Batch';
$res = $mysqli->query($query) or exit($mysqli->error);

$result["total"] = $res->num_rows;
$res->close();

//$rs = mysql_query("select * from users limit $offset,$rows");
$query = "select * from Batch limit $offset,$rows";
$res1 = $mysqli->query($query) or exit($mysqli->error);

$items = array();
while ($row = $res1->fetch_object())
  array_push($items, $row);
$result["rows"] = $items;
$res1->close();  

echo json_encode($result);

?>
