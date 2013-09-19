var graphData = [],
    lastReadings = 0,
    graphDataSize = 1000,
    lastGraphHover = 0,
    plotGraph,
    plotOverview;

var graphData = [
        { label: "IntTemp", color: "#297", lines: { show: true }, data: [] },
        { label: "ExtTemp", color: "#6c3", lines: { show: true }, data: [] },
        { label: "IntHumid", lines: { show: true }, yaxis: 2, data: [] },
        { label: "ExtHumid", lines: { show: true }, yaxis: 2, data: [] },
        { label: "PID_T_Output", color: "#6cf", lines: { lineWidth: 1, fill: true }, shadowSize: 0, yaxis: 2, data: [] } 
    ];

  function pushArray(newData) {
    for (i in graphData) {
     if (graphData[i].label) {
       // find matchin label in new data
       for (j in newData) {
         if (newData[j].label) {
           if(newData[j].label === graphData[i].label) {
             // found a match
             // remove data to make room for new data
             if (graphData[i].data.length <= graphDataSize) {
               graphData[i].data = graphData[i].data.slice(newData[j].data.length);
             }
             for(var x=0; x<newData[j].data.length; x++) {
               var d = newData[j].data[x];
               graphData[i].data.push(d);
             }
           }
         } else {
           if (newData[j].lastReadings) {
             lastReadings = newData[j].lastReadings * 1000;
           }
         }
       }
       j = 0;
     }
    }
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
        var axisY = plotGraph.getYAxes()[0];
        var valY = graphData[srsClosestY].data[srsClosestYIdx][1];
        pos.pageY = axisY.p2c(valY) +
            plotGraph.offset().top + plotGraph.getPlotOffset().top;
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

$(function() { 
function getReadings(offset,numRows) {
//  getReadings.count = 2;
  $.ajax({
    type: 'POST',
    url: 'getData.php',
    data: {offset: offset, numRows: numRows},
    dataType: 'json',
    success: function(data) {
      var j = 0;
      getReadings.count = ++getReadings.count || 1;

  //    if (graphData.length === 5) {
  //      initial = 1;
  //    }            
      //Save the last successful readings query
      for(var i in data) {
        if (getReadings.count === 1) {
          // First time through set data
          if (data[i].label) {
            graphData[j++].data = data[i].data;
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
      
/*          plotOverview = $.plot("#graph_overview", graphData , graphOpts2);
      plotGraph = $.plot("#graph", graphData , graphOpts);
*/
      plotOverview.setData(graphData);
      plotGraph.setData(graphData);

      plotOverview.setupGrid();
      plotGraph.setupGrid();

      plotOverview.draw();
      plotGraph.draw();

    },
    error: function(data) {
      var test = data;
      alert('Error: getReadings Failed' + data);
    },
   async: true
  });
}

// Set up the control widget

var updateInterval = 5000;
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

/*
        { label: "Set", color: "rgba(255,0,0,0.8)", lines: { lineWidth: 1 }, shadowSize: 0, data: [] },
        { label: "Amb", color: "#789", lines: { show: true }, data: [] },
 */

    $("#graph").bind("plothover", graphHover);
    $("#graph").mouseout(function () { $("#graphtt").fadeOut(); });


var graphOpts = {
    legend: { show: true },
    canvas: false,
    series: {
        lines: { show: true, lineWidth: 2 },
        points: { show: false, symbol: "circle", radius: 1, fill: false },
        shadowSize: 4,
        downsample: {threshold: 0 }
    },
    xaxes: 
    [{ 
        show: true, 
        mode: "time", 
        timezone: "browser",
        font: { color: "#ccc" } 
    }],
    yaxes: 
    [{ 
      show: true, 
      ticks: 10, 
      font: { color: "#ccc" } 
      },{ 
      show: true,
      position: "right", 
      min: 0, 
      max: 100 
    }],
    grid: { 
      clickable: true, 
      hoverable: true, 
      color: "#ccc",
      borderColor: "#545454" },
    selection: {mode: "x"},
    legend: { position: "nw" }
};

var graphOpts2 = {
    legend: { show: false },
    canvas: false,
    series: {
        lines: { show: true, lineWidth: 1 },
        points: { show: false },
        shadowSize: 0,
        downsample: {threshold: 2100 }
    },
    xaxis: { mode: "time", timezone: "browser" },
    yaxis: { show: false },
    grid: { clickable: false, color: "#ccc", borderColor: "#545454" },
    selection: { mode: "x" }
};

   plotOverview = $.plot("#graph_overview", graphData , graphOpts2);
   plotGraph = $.plot("#graph", graphData , graphOpts);

    $("#graph").bind("plotselected", function (event, ranges) {

      // do the zooming

      plotGraph = $.plot("#graph", graphData , $.extend(true, {}, graphOpts, {
          xaxis: {
              min: ranges.xaxis.from,
              max: ranges.xaxis.to
          }
      }));
      //don't fire event on the overview to prevent eternal loop
      plotOverview.setSelection(ranges,true);
    });

    $("#graph_overview").bind("plotselected", function (event, ranges) {
      plotGraph.setSelection(ranges);
    });

   function update() {
     //Offset needs to be lastReadings
      if (lastReadings === 0) {
        getReadings(1379610000,1);
      } else {
        getReadings(lastReadings / 1000,1);
      }
        setTimeout(update, updateInterval);
    }

    update();
});
