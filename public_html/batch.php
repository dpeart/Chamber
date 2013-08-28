<?php

function batch() {
  if (($_POST) && ($_POST['submit'] == 'batch')) {
    //Make sure we got values
    if ($_POST['BatchName'] == "") {
      header("Location: batch.php");
      exit;
    }

    //connect to database
    $mysqli = doDB();

    //clean up strings
    $safe_BatchName = $mysqli->real_escape_string($_POST['BatchName']);

    //Add to batch table
    $query = "INSERT INTO Batch (Name) VALUES ('$safe_BatchName')";
    $res = $mysqli->query($query) or exit($mysqli->errno);
    //$res->close();
    $mysqli->close();
  }
  $display_block = <<<END_OF_TEXT
  <form method = post action = "index.php">
  <p><label for = "BatchName">Name:</label>
  <input type = "text" id = "BatchName" name = "BatchName"></p>
  <button type = submit name = submit value = batch>Submit</button>
  </form>
END_OF_TEXT;
  return $display_block;
}
?>
