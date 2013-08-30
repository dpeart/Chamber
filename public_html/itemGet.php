<?php
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$rows = isset($_REQUEST['rows']) ? intval($_REQUEST['rows']) : 10;
	$offset = ($page-1)*$rows;
	$result = array();
    //TODO: pass in idBatch
    $idBatch = isset($_REQUEST['idBatch']) ? intval($_REQUEST['idBatch']) : -1;
    //$idBatch = 6;
    if ($idBatch != -1) {
      include 'conn.php';

      $mysqli = doDB();

      $query = "SELECT * from meat where idBatch=$idBatch";
      $res = $mysqli->query($query) or exit($mysqli->error);

      $result["total"] = $res->num_rows;
      $res->close();

      //$rs = mysql_query("select * from users limit $offset,$rows");
      $query = "select * from meat where idBatch=$idBatch  limit  $offset,$rows";
      $res1 = $mysqli->query($query) or exit($mysqli->error);

      $items = array();
      while ($row = $res1->fetch_object())
        array_push($items, $row);
      $result["rows"] = $items;
      $res1->close();  
    } else {
      $result = "Missing or invalid idBatch: $idBatch";
}
    echo json_encode($result);

?>