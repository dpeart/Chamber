<?php

function home() {
//connect to database
  $mysqli = doDB();

//haven't seen the form, so show it
  readStatus($mysqli, $idBatch, $tempSetpoint, $humiditySetpoint, $storeReadings, $HKp, $HKi, $HKd, $TKp, $TKi, $TKd);

// If the storeReadings check box has been changed
  if ($_POST) {
    if (!empty($_POST['frm_storeReadings']) && ($storeReadings == 0)) {
      $query = 'UPDATE Status SET `Store Readings`=1 where idStatus=0';
      $res = $mysqli->query($query) or exit($mysqli->error);
    } else if (empty($_POST['frm_storeReadings']) && ($storeReadings != 0)) {
      $query = 'UPDATE Status SET `Store Readings`=0 where `idStatus`=0';
      $res = $mysqli->query($query) or exit($mysqli->error);
    }

    // Sanitize all inputs

    $safe_idBatch = (empty($_POST['frm_idBatch'])) ? $idBatch : $mysqli->real_escape_string($_POST['frm_idBatch']);
    $safe_tempSetpoint = (empty($_POST['frm_tempSetpoint'])) ? $tempSetpoint : $mysqli->real_escape_string($_POST['frm_tempSetpoint']);
    $safe_humiditySetpoint = (empty($_POST['frm_humiditySetpoint'])) ? $humiditySetpoint : $mysqli->real_escape_string($_POST['frm_humiditySetpoint']);
    $safe_TKp = (empty($_POST['frm_TKp'])) ? $TKp : $mysqli->real_escape_string($_POST['frm_TKp']);
    $safe_TKi = (empty($_POST['frm_TKi'])) ? $TKi : $mysqli->real_escape_string($_POST['frm_TKi']);
    $safe_TKd = (empty($_POST['frm_TKd'])) ? $TKd : $mysqli->real_escape_string($_POST['frm_TKd']);
    $safe_HKp = (empty($_POST['frm_HKp'])) ? $HKp : $mysqli->real_escape_string($_POST['frm_HKp']);
    $safe_HKi = (empty($_POST['frm_HKi'])) ? $HKi : $mysqli->real_escape_string($_POST['frm_HKi']);
    $safe_HKd = (empty($_POST['frm_HKd'])) ? $HKd : $mysqli->real_escape_string($_POST['frm_HKd']);

    // Update values
    $query = "UPDATE Status SET `idBatch`=$safe_idBatch, ";
    $query .= "`Temp Setpoint`=$safe_tempSetpoint, `Humidity Setpoint`=$safe_humiditySetpoint, ";
    $query .= "`PID H Kp`=$safe_HKp, `PID H Ki`=$safe_HKi, `PID H Kd`=$safe_HKd, ";
    $query .= "`PID T Kp`=$safe_TKp, `PID T Ki`=$safe_TKi, `PID T Kd`=$safe_TKd ";
    $query .= "where `idStatus`=0";
    $res = $mysqli->query($query) or exit($mysqli->error);

    readStatus($mysqli, $idBatch, $tempSetpoint, $humiditySetpoint, $storeReadings, $HKp, $HKi, $HKd, $TKp, $TKi, $TKd);
  }
  $display_block = <<<END_OF_TEXT
    <form name=status method=post action=$_SERVER[PHP_SELF]>
    
      <table border = '1' width = '100%'>
        <tr>
          <th rowspan = '2'>Batch ID</th>
          <th colspan = '2'>Setpoint</th>
          <th colspan = '3'>Temp PID Parameters</th>
          <th colspan = '3'>Humidity PID Parameters</th>
        </tr>
        <tr>
          <th>Temp C</th>
          <th>Humidity</th>
          <th>Kp</th>
          <th>Ki</th>
          <th>Kd</th>
          <th>Kp</th>
          <th>Ki</th>
          <th>Kd</th>
        </tr>
        <tr>
          <td><input type='text' size=5 name='frm_idBatch' value=$idBatch></td>
          <td><input type='text' size=8 name='frm_tempSetpoint' value=$tempSetpoint></td>
          <td><input type='text' size=8 name='frm_humiditySetpoint' value=$humiditySetpoint></td>
          <td><input type='text' size=5 name='frm_TKp' value=$TKp></td>
          <td><input type='text' size=5 name='frm_TKi' value=$TKi></td>
          <td><input type='text' size=5 name='frm_TKd' value=$TKd></td>
          <td><input type='text' size=5 name='frm_HKp' value=$HKp></td>
          <td><input type='text' size=5 name='frm_HKi' value=$HKi></td>
          <td><input type='text' size=5 name='frm_HKd' value=$HKd></td>
        </tr>
        </table></ul >
END_OF_TEXT;

  if ($storeReadings) {
    $display_block .= <<<END_OF_TEXT
      <p>Store Readings: <input type='checkbox' name='frm_storeReadings' value=1 checked></p>
END_OF_TEXT;
  } else {
    $display_block .= <<<END_OF_TEXT
      <p>Store Readings: <input type='checkbox' name='frm_storeReadings' value=1></p>
END_OF_TEXT;
  }

  $display_block .= <<<END_OF_TEXT
     <button type = submit name = submit value = send>Submit</button>
     </form>
END_OF_TEXT;
  $mysqli->close();

  return $display_block;
}

?>
