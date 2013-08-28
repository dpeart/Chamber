<?php

function doDB() {
//global $mysqli;
  //Connect to database
  $mysqli = new mysqli("p:localhost", "David", "", "Chamber");

  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
  }
  return $mysqli;
}

function readStatus($mysqli, &$idBatch, &$tempSetpoint, &$humiditySetpoint, &$storeReadings, &$HKp, &$HKi, &$HKd, &$TKp, &$TKi, &$TKd) {
  $query = "SELECT * FROM Status WHERE idStatus=0";
  $res = $mysqli->query($query) or exit($mysqli->error);

  // Get fields of row
  $recs = $res->fetch_array();
  $idBatch = $recs['idBatch'];
  $tempSetpoint = $recs['Temp Setpoint'];
  $humiditySetpoint = $recs['Humidity Setpoint'];
  $storeReadings = $recs['Store Readings'];
  $HKp = $recs['PID H Kp'];
  $HKi = $recs['PID H Ki'];
  $HKd = $recs['PID H Kd'];
  $TKp = $recs['PID T Kp'];
  $TKi = $recs['PID T Ki'];
  $TKd = $recs['PID T Kd'];
  $res->close();
}

function getReadings($start, $stop, $type) {
  //idReading, idBatch, Internal Humidity, External Humidity, Internal Temp, External Teamp
  //$start = start time = 1365388734
  //$stop = stop time
  $lastreading = -1;

  $mysqli = doDB();
  $dataset = array();
  $query = "SELECT * FROM readings WHERE idReadings > " . $start . " and idReadings < " . "$stop";
  $res = $mysqli->query($query) or exit($mysqli->error);
  while ($row = $res->fetch_array()) {
    if (($row[$type] === $lastreading) || ($row[$type] > 100)) {
      // Don't add value to the list if it is the same as the previous reading
      continue;
    } elseif ($row[$type] >= 0) {
      // Need time in milliseconds
      $dataset[] = array($row[0] * 1000, $row[$type]);
      $lastreading = $row[$type];
    }
  }
  $res->close();
  $mysqli->close();

  return $dataset;
}
?>