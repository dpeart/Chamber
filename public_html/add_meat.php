<?php

//require_once "include.php";

function add_meat() {
  if ((!$_POST) || ($_POST['submit'] != 'add_meat')) {
    //Connect to database
    $mysqli = doDB();

    //get master_info
    $query = "SELECT * FROM Status WHERE idStatus=0";
    $res = $mysqli->query($query) or exit($mysqli->error);
    $name_info = $res->fetch_array();
    $idBatch = $name_info['idBatch'];

    $query = "select Name from Batch where idBatch=$idBatch";
    $res = $mysqli->query($query) or exit($mysqli->error);
    $name_info = $res->fetch_array();
    $name = stripslashes($name_info['Name']);

    //haven't seen the form, so show it
    $display_block = <<<END_OF_TEXT
  <form method = post action = "index.php">
  <p><h1>Current Batch: $name </h1></p>
  <p><label for = "Name">Product Name:</label>
  <input type = "text" id = "Name" name = "Name"></p>
  <p><label for = "Recipe">Recipe Name:</label>
  <input type = "text" id = "Recipe" name = "Recipe"></p>
  <p><label for = "Culture">Starting Culture Name:</label>
  <input type = "text" id = "Culture" name = "Culture"></p>
  <p><label for = "Mold">Mold Name:</label>
  <input type = "text" id = "Mold" name = "Mold"></p>
  <button type = submit name = submit value = "add_meat">Submit</button>
  </form>
END_OF_TEXT;
    $res->close();
    $mysqli->close();
    return $display_block;
  } else if ($_POST['submit'] == 'meat') {
    //Make sure we got values
    if ($_POST['Name'] == "") {
      header("Location: add_meat.php");
      exit;
    }
    //Connect to database
    $mysqli = doDB();

    // Sanitize all inputs
    $safe_Name = $mysqli->real_escape_string($_POST['Name']);
    $safe_Recipe = $mysqli->real_escape_string($_POST['Recipe']);
    $safe_Mold = $mysqli->real_escape_string($_POST['Mold']);
    $safe_Culture = $mysqli->real_escape_string($_POST['Culture']);

    // Get idBatch from Status
    if ($res = $mysqli->query("SELECT * FROM Status limit 0, 1")) {
      //printf("Select returned %d rows. \n", $res->num_rows);
      // Get objects of the row so we can use the idBatch later
      $obj = $res->fetch_object();
    } else {
      printf("\nError: %s\n", $mysqli->sqlstate);
    }

    //Add to meat table
    if (!$mysqli->query("INSERT INTO Meat (idBatch, Name, Recipe, Culture, Mold) VALUES 
    ($obj->idBatch, '$safe_Name', '$safe_Recipe', '$safe_Mold', '$safe_Culture')")) {
      printf("\nError: %s\n", $mysqli->sqlstate);
    } else {
      $display_block = "<p>Meat $safe_Name has been added.</p>";
    }
    $res->close();
    $mysqli->close();
    return $display_block;
  }
}
?>
