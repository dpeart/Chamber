function selectBatchInit() {
  $.post('select_mysql.php', {cmd: 'list'}, function(data) {
    //Set first option as empty
    $("select#selectBatchName").html('<option value="null"></option>');
    for (row in data) {
      var batch = data[row];
      $("select#selectBatchName").append('<option value="' + batch.idBatch + '">' + batch.Name + '</option>');
    }
  }, "json")
  .error(function(data) {
    alert('Error: select_mysql.php - init');
  });
} // end init

function getBatch() {
  var idBatch = $('select#selectBatchName').val();
  if (idBatch !== 'null') {
    $.post('select_mysql.php', {cmd: 'id', val: idBatch}, function(data) {
      var batch = data[0];
      $("#selectBatch_output").html('<h2> Batch Information </h2>');
      $("#selectBatch_output").append('<dl>');
      $("#selectBatch_output").append('<dt> Name: ' + batch['Name'] + '</dt>');
       $("#selectBatch_output").append('<dd> Fermentaion Start: ' + batch['Fermentation Start'] + '</dt>');
      $("#selectBatch_output").append('<dd> Fermentation Stop ' + batch['Drying Stop'] + '</dt>');
     $("#selectBatch_output").append('<dd> Drying Start: ' + batch['Drying Start'] + '</dt>');
      $("#selectBatch_output").append('<dd> Drying Stop ' + batch['Drying Stop'] + '</dt>');
      getMeat(batch['idBatch']);
      $('h1:contains(Current Batch:)').html('Current Batch: ' + batch['Name']);
    }, "json")
      .error(function(data) {
        alert('Error: displayBatch');
    });
  } else {
    // Clear out contents
    $("div#selectBatch_output").html('');
  }
} //end getBatch

function getMeat(idBatch) {
  $.post('select_mysql.php', {cmd: 'meat', id: idBatch}, function(data) {
    $("div#selectBatch_output").append('<h2> Products for this Batch</h2>');
    for (row in data) {
      var meat = data[row];
      $("div#selectBatch_output").append('<dt> Name: ' + meat['Name'] + '</dt>');
      $("div#selectBatch_output").append('<dd> Recipe: ' + meat['Recipe'] + '</dd>');
      $("div#selectBatch_output").append('<dd> Culture: ' + meat['Culture'] + '</dd>');
      $("div#selectBatch_output").append('<dd> Mold: ' + meat['Mold'] + '</dd>');
    }
    $("div#selectBatch_output").append('</dl>');
  }, "json")
          .error(function(data) {
    $("div#selectBatch_output").append('<h2> No products in this batch</h2>');
  });
}

function createBatch() {
  var batchName = $("input#createBatchName").val();

  $.ajax({
    type: 'POST',
    url: 'insert_mysql.php',
    data: {cmd: 'createBatch', val: batchName},
    success: function(data) {
//      var test = data;
//      alert('Success: ' + data);
      $("div#batch_output").html("Success");
      selectBatchInit();
    },
    error: function(data) {
      var test = data;
      alert('Error: createBatch Failed');
      $("div#batch_output").html("createBatch: batchName unsuccessful");
    },
    async: true
  });
}

//TODO: when we delete a batch make sure we also delete all meats attached to the batch
function deleteBatch() {
  var idBatch = $('select#selectBatchName').val();
  $.ajax({
    type: 'POST',
    url: 'insert_mysql.php',
    data: {cmd: 'deleteBatch', val: idBatch}, 
    success: function(data) {
      // Update batch pulldown
      selectBatchInit();
      // Clear out Batch information
      $("div#selectBatch_output").html("");
    },
    error: function(data) {
      var test = data;
      alert('Error: deleteBatch Failed');
    },
    async: true
  });
} 

function updateBatch() {
  var data = "('" + $('select#selectBatchName').val() + "', '" +
             $('input#Name').val() +  "', '" +
             $('input#Recipe').val() +  "', '" +
             $('input#Culture').val() +  "', '" +
             $('input#Mold').val() + "');";
  $.ajax({
    type: 'POST',
    url: 'insert_mysql.php',
    data: {cmd: 'updateBatch', val: data},
    success: function(data) {
      var test = data;
      alert('Success:' + test);
    },
    error: function(data) {
      var test = data;
      alert('Error: updateBatch Failed');
    },
    async: true
  });

}

