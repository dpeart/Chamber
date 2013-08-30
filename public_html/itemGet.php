<?php
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$offset = ($page-1)*$rows;
	$result = array();
    //TODO: pass in idBatch
    $idBatch = 6;
    
	include 'conn.php';
	
    //$conn = doDB();
    
    $query = "SELECT * from meat where idBatch=$idBatch";
    $res = $conn->query($query) or exit($conn->error);
    
	$result["total"] = $res->num_rows;
    $res->close();
    
    //$rs = mysql_query("select * from users limit $offset,$rows");
    $query = "select * from meat where idBatch=$idBatch  limit  $offset,$rows";
	$res1 = $conn->query($query) or exit($conn->error);
	
    $items = array();
    while ($row = $res1->fetch_object())
      array_push($items, $row);
    $result["rows"] = $items;
    $res1->close();  

    echo json_encode($result);

?>