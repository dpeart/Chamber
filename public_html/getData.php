<?php
$mergedData = array();
 
$offset = isset($_REQUEST['offset']) ? intval($_REQUEST['offset']) : 0;
$numRows = isset($_REQUEST['numRows']) ? intval($_REQUEST['numRows']) : -1;

include 'conn.php';

$mysqli = doDB();

if ($numRows == -1) {
  $numRows = 1000000;
}

//$query = "SELECT  FROM Readings WHERE idReadings;";
//echo json_encode($offset);
$query = "SELECT `idReadings`,`Internal Temperature` FROM Chamber.Readings WHERE idReadings > $offset;";
//$query = "SELECT `idReadings`,`Internal Temperature` FROM Chamber.Readings LIMIT $offset, $numRows;";

//echo json_encode($query);
$res = $mysqli->query($query) or exit($mysqli->error);

//loop through the first set of data and pull out the values we want, then format
if ($res) {
  //If there are no rows returned, then don't return any data
  if ($res->num_rows) {
    $lasty = -99999;
    foreach($res as $r)
    {
      $x = intval($r['idReadings']) * 1000;
      $y = floatval($r['Internal Temperature']);
      if ($lasty != $y) 
      {
        $data1[] = array ($x, $y);
        $lasty = $y;
      }
    }
    $res->close();
    //send our data values to $mergedData, add in your custom label and color
    // yaxis: 1 is temperature, yasis: 2 is percentage
    $mergedData[] =  array('label' => "IntTemp" , 'data' => $data1);
  }
}
 
//Get the second set of data you want to graph from the database
$query = "SELECT `idReadings`,`External Temperature` FROM Chamber.Readings WHERE idReadings > $offset;";
//$query = "SELECT `idReadings`,`External Temperature` FROM Chamber.Readings LIMIT $offset, $numRows;";
//echo json_encode($query);
$res = $mysqli->query($query) or exit($mysqli->error);
 
if ($res) {
  //If there are no rows returned, then don't return any data
  if ($res->num_rows) {
    $lasty = -99999;
    foreach($res as $r) {
      // Convert from seconds to miliseconds for Flot
      $x = intval($r["idReadings"]) * 1000;
      $y = floatval($r["External Temperature"]);
      if ($lasty != $y) 
      {
        $data2[] = array ($x, $y);
      }
    }
    $res->close(); 
    //send our data values to $mergedData, add in your custom label and color
    $mergedData[] = array('label' => "ExtTemp" , 'data' => $data2);
  }
}

//$query = "SELECT  FROM Readings WHERE idReadings;";
//echo json_encode($offset);
$query = "SELECT `idReadings`,`Internal Humidity` FROM Chamber.Readings WHERE idReadings > $offset;";
//$query = "SELECT `idReadings`,`Internal Humidity` FROM Chamber.Readings LIMIT $offset, $numRows;";

//echo json_encode($query);
$res = $mysqli->query($query) or exit($mysqli->error);

if ($res) {
  //If there are no rows returned, then don't return any data
  if ($res->num_rows) {
    $lasty = -99999;
    //loop through the first set of data and pull out the values we want, then format
    foreach($res as $r)
    {
      $x = intval($r['idReadings']) * 1000;
      $y = floatval($r['Internal Humidity']);
      if ($lasty != $y) 
      {
        $data3[] = array ($x, $y);
      }
    }
    $res->close();
    //send our data values to $mergedData, add in your custom label and color
    $mergedData[] =  array('label' => "IntHumid" , 'data' => $data3);
  }
}
//Get the second set of data you want to graph from the database
$query = "SELECT `idReadings`,`External Humidity` FROM Chamber.Readings WHERE idReadings > $offset;";
//$query = "SELECT `idReadings`,`External Humidity` FROM Chamber.Readings LIMIT $offset, $numRows;";
//echo json_encode($query);
$res = $mysqli->query($query) or exit($mysqli->error);

if ($res) {
  //If there are no rows returned, then don't return any data
  if ($res->num_rows) {
    $lasty = -99999;
    foreach($res as $r)
    {
      // Convert from seconds to miliseconds for Flot
      $x = intval($r['idReadings']) * 1000;
      $y = floatval($r['External Humidity']);
      if ($lasty != $y) 
      {
        $data4[] = array ($x, $y);
      }
    }
    $res->close(); 
    //send our data values to $mergedData, add in your custom label and color
    $mergedData[] = array('label' => "ExtHumid" , 'data' => $data4);
  }
}

//Get the second set of data you want to graph from the database
$query = "SELECT `idReadings`,`PID T Output` FROM Chamber.Readings WHERE idReadings > $offset;";
//$query = "SELECT `idReadings`,`PID T Output` FROM Chamber.Readings LIMIT $offset, $numRows;";
//echo json_encode($query);
$res = $mysqli->query($query) or exit($mysqli->error);

if ($res) {
  //If there are no rows returned, then don't return any data
  if ($res->num_rows) {
    $lasty = -99999;
    foreach($res as $r)
    {
      // Convert from seconds to miliseconds for Flot
      $x = intval($r['idReadings']) * 1000;
      $y = floatval($r['PID T Output']);
      if ($lasty != $y) 
      {
        $data5[] = array ($x, $y);
      }
    }
    $res->close(); 
    //send our data values to $mergedData, add in your custom label and color
    $mergedData[] = array('label' => "PID_T_Output" , 'data' => $data5);
  }
}

$mergedData[] = array('lastReadings'=>  time());

echo json_encode($mergedData);

//now we can JSON encode our data
//echo json_encode($mergedData);
?>