var graphData = [],
    lastReadings = 0,
    graphDataSize = 500000,
    lastGraphHover = 0,
    lastPlot, 
    lastOverviewPlot,
    sqlRequestPending = 0;

var graphData = [
        { label: "IntTemp", color: "#297", lines: { show: true }, data: [] },
        { label: "ExtTemp", color: "#6c3", lines: { show: true }, data: [] },
        { label: "IntHumid", lines: { show: true }, yaxis: 2, data: [] },
        { label: "ExtHumid", lines: { show: true }, yaxis: 2, data: [] },
        { label: "PID_T_Output", color: "#6cf", lines: { lineWidth: 1, fill: true }, shadowSize: 0, yaxis: 2, data: [] } 
    ];

var graphOpts = {
    legend: { show: true, position: "nw" },
    canvas: false,
    series: {
        lines: { show: true, lineWidth: 2 },
        points: { show: false, symbol: "circle", radius: 1, fill: false },
        shadowSize: 4
    },
    xaxis: { show: true, mode: "time", timezone: "browser",
      font: { color: "#ccc" } },
    yaxis: { show: true, ticks: 10, minTickSize: 0.5, font: { color: "#ccc" } },
    yaxes: [ { }, { position: "right", min: 0, max: 100 } ],
    grid: { clickable: true, hoverable: true, color: "#ccc",
      borderColor: "#545454" }
};



var graphOpts2 = {
    legend: { show: false },
    canvas: false,
    series: {
        lines: { show: true, lineWidth: 1 },
        points: { show: false },
        shadowSize: 0,
        downsample: {threshold: 500 }
    },
    xaxis: graphOpts.xaxis,
    yaxis: { show: false },
    yaxes: graphOpts.yaxes,
    grid: { clickable: false, color: "#ccc", borderColor: "#545454" },
    selection: { mode: "x" }
};

$(function() { 
  //  graphOpts.legend.container = $("#graph_legend");
  $("#graphOverview").bind("plotselected", overviewSelected);
  $("#graphOverview").bind("plotunselected", overviewUnselected);
  $("#graphtt").click(graphttClicked);
  $("#rangeselect").change(getReadings);
  $("#graph").bind("plotclick", graphClicked);
  $("#graph").bind("plothover", graphHover);
  $("#graph").mouseout(function () { $("#graphtt").fadeOut(); });
//  $("div.legfill").click(legendClicked);
//    $(document).keydown(keyPressed);  
// 
  function pushArray(newData) {
    for (i in graphData) 
    {
      if (graphData[i].label) 
      {
        // find matchin label in new data
        for (j in newData) 
        {
          if (newData[j].label) 
          {
            if(newData[j].label === graphData[i].label) 
            {
              // found a match
              for(var x=0; x<newData[j].data.length; x++) 
              {
                // TODO: Don't append data if it is older than what we currently have
                // I don't know why this is happening, If I figure it out later
                // remove this piece.
                // Also don't add new data if it is the same as the old data
                var firstNewDataTime = newData[j].data[x][0];
                var lastGraphDataTime = graphData[i].data[graphData[i].data.length - 1][0];
                var newDataPoint = newData[j].data[x][1];
                var previousDataPoint = graphData[i].data[graphData[i].data.length - 1][1];
                if (lastGraphDataTime >= firstNewDataTime) continue;
                if ((previousDataPoint != newDataPoint) || (graphData[i].label == "PID_T_Output")) {
                  graphData[i].data.push(newData[j].data[x]);
                } 
              }
              //Break out of the loop after processing
            //  break;
            }
          } else {
            if (newData[j].lastReadings)
              lastReadings = newData[j].lastReadings * 1000;        
          }
        }
        j = 0;
      }
    }
  }

function doPlot()
{
  lastPlot = $.plot($("#graph"), graphData, graphOpts);
}

function updateGraphRanges(from, to)
{
  $("#graphtt").fadeOut();
  graphOpts.xaxis.min = from;
  graphOpts.xaxis.max = to;
  doPlot();
}

function overviewSelected(event, ranges)
{
  updateGraphRanges(ranges.xaxis.from, ranges.xaxis.to);
}

function overviewUnselected()
{
  updateGraphRanges(null, null);
}


function formatTime(date, includeSeconds)
{
    if (includeSeconds)
        return $.plot.formatDate(date, "%l:%M:%S %P");
    else
        return $.plot.formatDate(date, "%l:%M %P");
}

function formatDate(date)
{
    return $.plot.formatDate(date, "%y %b %d, %l:%M %P");
}

function formatTimer(secs, includeSeconds)
{
    var hours = Math.floor(secs / 3600);
    secs -= hours * 3600;
    var mins = Math.floor(secs / 60);
    secs -= mins * 60;

    var retVal = "";
    if (hours > 0)
        retVal = hours + "h";
    if (mins > 0)
        retVal += mins + "m";
    if (includeSeconds && secs > 0)
        retVal += secs + "s";

    return retVal;
}


  function lerp(n1, n2, frac)
  {
    return (n1 * (1.0 - frac)) + (n2 * frac);
  }

  function graphHover(event, pos, item)
  {
    var now = +new Date();
    if (now - lastGraphHover > 100)
    {
        graphClicked(event, pos, item);
        lastGraphHover = now;
    }
  }

function graphttClicked()
{
    // After clicking the tooltip to dismiss it, don't show again for 1s
    lastGraphHover = +new Date();
    lastGraphHover += 1000;
    $(this).fadeOut();
}

  function graphClicked(event, pos, item)
  {
    var sInfo = "";
    var srsClosestY = -1;
    var srsClosestYIdx;
    // Display on tooltip as IntTemp, ExtTemp, IntHumid, ExtHumid, PID_T_Output
    var SERIES_DISPLAY_ORDER = [ 0, 1, 2, 3, 4 ];
    for (var idx=0; idx<SERIES_DISPLAY_ORDER.length; ++idx)
    {
      var srs = SERIES_DISPLAY_ORDER[idx];
      var val = NaN;
      var firstPastIdx = -1;
      var srsData = graphData[srs].data;

      for (var i=0; i<srsData.length; ++i)
      {
          if (pos.x <= srsData[i][0] && !isNaN(srsData[i][1]))
          {
              firstPastIdx = i;
              break;
          }
      }

      if (firstPastIdx === -1)
          continue;

      // This must either be the point at the same timestamp as where you
      // clicked or you must click between two points. If you click off to
      // the left of the first point or to the right of the last, we don't
      // use that value because you've clicked outside the range of valid data.
      var ptR = srsData[firstPastIdx];
      if (item && item.datapoint[0] === ptR[0])
          val = ptR[1];
      else if (firstPastIdx !== 0)
      {
        var ptL = srsData[firstPastIdx-1];
        var timeDiff = ptR[0] - ptL[0];
        val = lerp(ptL[1], ptR[1], (pos.x - ptL[0]) / timeDiff);
      }

      if (!isNaN(val))
        sInfo = sInfo + graphData[srs].label + " " + val.toFixed(1) + "&deg;<br />";

        // Save the series with the closest Y point to where the mouse is
        if (srsClosestY === -1 ||
            Math.abs(srsData[firstPastIdx][1] - pos.y) <
            Math.abs(graphData[srsClosestY].data[srsClosestYIdx][1] - pos.y))
        {
            srsClosestY = srs;
            srsClosestYIdx = firstPastIdx;
        }

    } /* for srs */
    
    if (sInfo === "")
        $("#graphtt").fadeOut();
    else
    {
      if (srsClosestY !== -1)
      {
        var axisY = lastPlot.getYAxes()[0];
        var valY = graphData[srsClosestY].data[srsClosestYIdx][1];
        pos.pageY = axisY.p2c(valY) +
            lastPlot.offset().top + lastPlot.getPlotOffset().top;
      }

      var d = new Date(pos.x);
      $("#graphtt_title").html(formatTime(d, false));
      $("#graphtt_content").html(sInfo);
      var ttWidth = $("#graphtt").outerWidth();
      var ttHeight = $("#graphtt").outerHeight();
      $("#graphtt_arrow").css({left: ttWidth/2 - 7, top: ttHeight - 1});
      $("#graphtt")
          .css({left: pos.pageX - (ttWidth/2) - 1, top: pos.pageY - ttHeight - 15})
          .show();
    }
}

function getReadings(offset,numRows) {
  if (sqlRequestPending == 1) 
    return;
  var range = $("#rangeselect").val();

  $("#graphtt").fadeOut();
  $("#rangeselect").prop("disabled", true);
  $("#loadindic").show();

  graphLoaded = false;
    
  $.ajax({
    type: 'POST',
    url: 'getData.php',
    data: {offset: offset, numRows: numRows},
    dataType: 'json',
    success: readSuccess,
    error: function(data) {
      var test = data;
      alert('Error: getReadings Failed' + data);
    },
   async: true
  });
}

function readSuccess(data) {
      var j = 0;
      getReadings.count = ++getReadings.count || 1;
          
      //Save the last successful readings query
      for(var i in data) {
        if (getReadings.count === 1) {
          // First time through set data limit set of data to graphDataSize
          if (data[i].label) {
            graphData[j++].data = data[i].data.slice(-graphDataSize);
          }
          if (data[i].lastReadings) {
            lastReadings = data[i].lastReadings * 1000;
          }            
        } else {
          // push new data onto graphData
          if (data[i].label) {
            pushArray(data);             
          }
        }
      }
      sqlRequestPending = 0;
      graphLoaded = true;
      updateGraph();
    }

function updateGraph()
{
    // we want to save the selected area, and if the selection is to the
    // end of the graph, extend it to include the new point
    var selectedArea, sizeSelectedArea;
    var extendSelection = false;
    var shiftSelection = false;

    if (lastOverviewPlot)
        selectedArea = lastOverviewPlot.getSelection();
    if (selectedArea && selectedArea.xaxis.to == lastOverviewPlot.getAxes().xaxis.max)
        extendSelection = true;
      // if selected is at far left of overview, shift selected area right by the number of newly added datapoints
//    var lastDataPoint = graphData[0].data[0][0];
//    if (selectedArea && selectedArea.xaxis.from < lastDataPoint)
//      shiftSelection = true;
    if (selectedArea)
      lastOverviewPlot.clearSelection(false);
      
    lastOverviewPlot = $.plot($("#graphOverview"), graphData, graphOpts2);
    
    if (extendSelection)
        selectedArea.xaxis.to = lastOverviewPlot.getAxes().xaxis.max;
//    if (shiftSelection) {
      // Keep selection same size (in time)
//      sizeSelectedArea = selectedArea.xaxis.to - selectedArea.xaxis.from;
//      selectedArea.xaxis.from = lastDataPoint;
//      selectedArea.xaxis.to = lastDataPoint + sizeSelectedArea;
//    }
    if (selectedArea)
        lastOverviewPlot.setSelection(selectedArea);
    else
        doPlot();
}

// Set up the control widget

var updateInterval = 10000;
$("#updateInterval").val(updateInterval).change(function () {
    var v = $(this).val();
    if (v && !isNaN(+v)) {
        updateInterval = +v;
        if (updateInterval < 1) {
            updateInterval = 1;
        } else if (updateInterval > 20000) {
            updateInterval = 20000;
        }
        $(this).val("" + updateInterval);
    }
});

  function update() {
 //   if (!sqlRequestPending) {
      //Offset needs to be lastReadings
      if (lastReadings === 0) {
        getReadings(1378990000,1);
      } else {
        getReadings(lastReadings / 1000,1);
      }
        setTimeout(update, updateInterval);
 //   }
  }
    update();
});
