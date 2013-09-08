<?php
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$rows = isset($_REQUEST['rows']) ? intval($_REQUEST['rows']) : 10;
	$offset = ($page-1)*$rows;
	$result = array();
    //TODO: pass in idBatch
    $idBatch = isset($_REQUEST['idBatch']) ? intval($_REQUEST['idBatch']) : -1;
    
    if ($idBatch != -1) {
      include 'conn.php';

      $mysqli = doDB();

      $query = "SELECT * from Meat where idBatch=$idBatch";
      $res = $mysqli->query($query) or exit($mysqli->error);

      $result["total"] = $res->num_rows;
      $res->close();

      //$rs = mysql_query("select * from users limit $offset,$rows");
      //$query = "select * from Meat NATURAL JOIN Fermentation,Drying where idBatch=$idBatch  limit  $offset,$rows";
      $query = "SELECT m.idBatch, m.idMeat, m.Name, m.Recipe, m.Culture, m.Mold,
        d.`idDrying`, d.`Start Weight` as DSW, d.`End Weight` as DEW,
        d.`Start PH` as DSPH, d.`End PH` as DEPH,
        d.`Start Diameter` as DSD, d.`End Diameter` as DED,
        f.`idFermentation`, f.`Start Weight` as FSW, f.`End Weight` as FEW, 
        f.`Start PH` as FSPH, f.`End PH` as FEPH,
        f.`Start Diameter` as FSD, f.`End Diameter` as FED
        from Meat as m 
        left join Drying as d on m.idMeat=d.idMeat
        left join Fermentation as f on m.idMeat=f.idMeat
        where idBatch=$idBatch limit $offset, $rows;";
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