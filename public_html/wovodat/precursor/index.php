<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
        <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
        <meta http-equiv="cache-control" content="no-cache, must-revalidate">
        <meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
        <meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, 
              forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, 
              database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, 
              earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, 
              stratovulcano">
        <link href="/css/styles_beta.css" rel="stylesheet"> 
        <link type="text/css" href="/js/jqueryui/css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="/js/jqueryui/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.js"></script>
        <script type="text/javascript" src="/js/jquery.flot.navigate.tuan.js"></script> 
        <script type="text/javascript" src="/js/flot/jquery.flot.selection.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.marks.js"></script>
        <!-- this is to prevent caching of js file, will need to amend when produce for production -->
        <script type="text/javascript" src="/js/wovodat.js?<?php echo time(); ?>"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ9kUvUtmawmFJ62hWVsigWFTh3CKUzzM&sensor=false"></script>
        <script type="text/javascript">
            // this is the list of available station type for each volcano,
            // this list will be initialize when the volcano is selected and 
            // it will be deleted when this volcano is deselected.
            var stationTypeList = [];
            // this list of available time series list
            var timeSeriesList = [];
            // the google map
            var map;
            // the list of station for each station type
            var stationsDatabase = {};
            // the markers and infowindows for google maps
            var markers = [],infoWindows = [];
            // this inforWindow is for the volcano
            var infowindow;
            // this link to all the plot graph
            var graphs = [];
            // this link to all the plot data for each graph
            var graphData = [];
            // the variable store the reference to the overview graph
            var overviewGraph;
            // these marks will show the eruption start time 
            // eruptions data
            var eruptionsData = {};
            // reference data to since between various graphs
            var referenceTime = null;
            // full details scaled data
            var detailedData = [];
            
            $(document).ready(function(){
                $("button").button();
                // initialize
                eruptionsData = {
                    marks: { 
                        show: true,
                        color: 'rgb(212,59,62)',
                        labelVAlign: 'top' ,
                        rows: 1
                    }, 
                    data: [], 
                    markdata: []
                };
                
                Wovodat.showProcessingIcon($("#loading"));
                
                $("#OptionList").fadeIn(3000);
                // make the list of available time series have the style of jquery ui tabs
                $("#OptionList" ).tabs({
                    collapsible: true
                });
                // get the list of all volcano in our database and insert it into 
                // the dropdown list
                Wovodat.getVolcanoList(insertVolcanoList);
                
                // when the volcano option is changed
                document.getElementById('VolcanoList').onchange = function(){
                    // get the eruption list for that specific volcano
                    Wovodat.getEruptionList({
                        volcano: $("#VolcanoList").val(),
                        handler: insertEruptionList
                    });
                    // get the location of that volcano and position to it in the
                    // google map
                    var volcano = $("#VolcanoList").val();
                    volcano = volcano.split("&");
                    var cavw = volcano[1];
                    Wovodat.getLatLon({cavw:cavw,handler:drawMap});
                    // update the list of available station
                    Wovodat.getAvailableStations({
                        cavw: cavw,
                        handler: updateStationList,
                        volcano: volcano[0]
                    });
                    
                    // delete all the drawn graphs and the time series list
                    for(var i in graphs){
                        delete(graphs[i]);
                        var div = document.getElementById(i + 'Row');
                        div.parentNode.removeChild(div);
                    }
                    document.getElementById('overviewPanel').style.display = 'none';
                    document.getElementById('TimeSeriesList').innerHTML = '';
                    
                    // reset the local list of available stations for each data type
                    delete(stationsDatabase);
                    stationsDatabase = {};
                };
                // get all the available graph move to the eruption
                $("#EruptionList").change(moveGraphsToEruptionTime);
                
            });
            
            // prints the available graphs
            function printGraphs(){
                var graphsArea = document.getElementById("PlotArea");
                graphsArea = graphsArea.cloneNode(true);
                var newWindow = window.open('');
                var doc = newWindow.document;
                var t;
                t = doc.createElement('div');
                t.style.margin = '0px 0px 0px 20px';
                t.innerHTML = '<b>www.WOVOdat.org</b>: Database of Volcanic Unrest<br/> Brought to You by the Earth Observatory of Singapore <br/><br/>';
                var volcano = $("#VolcanoList").val();
                volcano = volcano.split('&');
                var cavw = volcano[1];
                volcano = volcano[0];
                t.innerHTML += '<table><tr><td>Vocano: </td><td>' + volcano + '</td></tr><tr><td>CAVW: </td><td>' + cavw + '</td></tr></table>';
                doc.body.appendChild(t);
                t = doc.createElement('table');
                doc.body.appendChild(t);
                var tr,td,div;
                for(var i in graphs){
                    div = doc.createElement('div');
                    div.id = i + 'Graph';
                    div.style.width = '700px';
                    div.style.height = '200px';
                    td = doc.createElement('td');
                    tr = doc.createElement('tr');
                    tr.appendChild(td);
                    td.appendChild(div);
                    t.appendChild(tr);
                    $.plot(div,graphs[i].getData(),graphs[i].getOptions());
                }
                newWindow.print();
                newWindow.close();
            }
            // draw overview graph
            function drawOverviewGraph(){
                if(overviewGraph != undefined){
                    $("#overview").empty();
                    delete(overviewGraph);
                }
                var id;
                var data = [];
                for(id in graphs){
                    data.push(graphData[id][0]);
                }
                var options = {
                    series: {
                        lines: { show: true},
                        shadowSize: 0
                    },
                    xaxis: { mode:'time'},
                    yaxis: { ticks: []},
                    selection: { mode: "x", color: '#451A2B' }
                };
                overviewGraph = $.plot($("#overview"),data,options);
                // clear previous handler
                $("#overview").unbind('plotselected');
                
                // draw other main graphs when user select a portion of this graph
                $("#overview").bind('plotselected',function(event,ranges){
                    var id;
                    var plot;
                    var options,data,placeholder,newOptions;
                    var to = ranges.xaxis.to;
                    var from = ranges.xaxis.from;
                    for(id in graphs){
                        plot = graphs[id];
                        if(plot == undefined) continue;
                        placeholder = plot.getPlaceholder();
                        placeholder.empty();
                        // this is for the label
                        var data = plot.getData();
                        // when user select a section on the overview graph, the data
                        // will reset to the initial data which is 12 hours re-sampling data
                        data = {
                            data: graphData[id][0],
                            label: data[0].label
                        };
                        var o = Wovodat.getLocalMaxMin(data.data,from,to);
                        var maxY,minY;
                        maxY = o.max;
                        minY = o.min;
                        options = plot.getOptions();
                        newOptions = {
                            series: options.series,
                            grid: options.grid,
                            xaxis:{
                                max: to,
                                min: from,
                                ticks: tickGenerator,
                                labelWidth: 50,
                                show: true
                            },
                            yaxis:{
                                panRange: options.yaxis.panRange,
                                zoomRange: options.yaxis.zoomRange,
                                max: maxY,
                                min: minY,
                                color: 'rgb(123,1,100)',
                                labelWidth: 25,
                                tickDecimals:1
                            },
                            zoom:{
                                interactive: true
                            },
                            pan: {
                                interactive: true
                            }
                        }
                        graphs[id] = $.plot(placeholder,[data,eruptionsData],newOptions);
                        Wovodat.redraw(graphs[id],graphData[id],detailedData[id],graphs,true);
                    }
                });
            }
            // when user select a specific eruption, all the graphs will move to 
            // the volcano in the time series
            function moveGraphsToEruptionTime(){
                // get time of the eruption
                var value = this.value;
                if(value == "") return;
                value = value.split(' ');
                if(value.length <= 1) {
                    alert('No available data for this eruption');
                    return;
                }
                // convert the time to javascript data object
                var time = Wovodat.toDate(this.value).getTime();
                var range = 0;
                // since we have synchronized all the graphs, all of them must have
                // the same range
                for(var i in graphs){
                    var temp = graphs[i].getAxes().xaxis;
                    range = temp.max - temp.min;
                    break;
                }
                // get the duration of the displayed graph
                if(range == '0' ) {
                    alert('Please select at least one type of time series.')
                    return;
                }
                // the eruption will be displayed at the center of the time series.
                var minRange;
                var maxRange;
                range = range / 2;
                minRange = time - range;
                maxRange = time + range;
                var data;
                var placeholder;
                var options,newOptions;
                var tempGraph;
                for(var i in graphs){
                    tempGraph = graphs[i];
                    data = tempGraph.getData();
                    placeholder = tempGraph.getPlaceholder();
                    data = {
                        data: data[0].data,
                        label: data[0].label,
                        xaxis:{
                            max: maxRange,
                            min: minRange
                        }
                    };
                    options = tempGraph.getOptions();
                    newOptions = {
                        series: options.series,
                        grid: options.grid,
                        xaxis:{
                            max: maxRange,
                            min: minRange,
                            ticks: tickGenerator
                            ,
                            labelWidth: 50
                            ,
                            show: true
                        },
                        yaxis:options.yaxis,
                        zoom:{
                            interactive: true
                        },
                        pan: {
                            interactive: true
                        }
                    };
                    placeholder.empty();
                    delete(graphs[i]);
                    graphs[i] = $.plot(placeholder,[data,eruptionsData],newOptions);
                }
            }
            function updateTimeSeriesList(data){
                var timeSeriesList = document.getElementById('TimeSeriesList');
                var t;
                var value;
                var display;
                for(var i in data){
                    value = data[i];
                    value = value.split('&');
                    //display = value[0] + '_' + value[1] + '_' + value[2];
                    display = value[1];
                    if(value[5] != undefined)
                        display = display + '-' + value[5];
                    display += " (" + value[2] + ")";
                    value = value[0] + "&" + value[1] + "&" + value[2] + '&' + value[5];
                    t = document.createElement('tr');
                    t.id = value + 'Tr';
                    timeSeriesList.appendChild(t);
                    $("#" + value.replace(/&/g,"\\&").replace(/=/g,"\\=") + 'Tr').html("<td><input type='checkbox' id='" +value +  "' value='" + value + "' onclick='drawTimeSeries(this)'></td><td>" + display + "</td>");          
                }
            }
            function drawTimeSeries(obj){
                var value = obj.value;
                var index = value;
                value = value.split("&");
                var type = value[0];
                var table = value[1];
                var code = value[2];
                var component = value[3];
                if(obj.checked ){
                    var count = 0;
                    for(var i in graphs){
                        count++;
                    }
                    if(count >= 5){
                        alert('Please choose at most 5 time series to draw');
                        obj.checked = false;
                        return;
                    }
                    if(graphData[index] != undefined){
                        drawGraph({
                            id: index,
                            data: graphData[index]
                        });
                    }else{
                        Wovodat.getStationData({
                            type:type,
                            table:table,
                            code:code,
                            component: component,
                            referenceTime: referenceTime,
                            handler:drawGraph
                        });
                        
                    }
                }else{
                    deleteGraph({id:obj.value});
                }
            }
            // data has the format [[[x1,y1],[x2,y2],[x3,y3]]]
            // id is the string to specify the type of the data
            function drawGraph(args){
                var id = args.id;
                // get the label from the list of available time series
                var label = document.getElementById(id + 'Tr').getElementsByTagName('td')[1].innerHTML;
                var data = Wovodat.highlightNoDataRange(args.data);
                
                // set up the reference time if that 
                if(referenceTime == null){
                    referenceTime = data[0][0][0];
                }
                
                
                // get detailed scaled data for the graph 
                // the data is divied into two scale: minimized scale and full scale
                // full scale contains resampling version of entire data in 12 hours period
                // minimized scale contains all data without any resampling.
                // the minimized scale will be used when the graph have to draw data 
                // with a range of less than month. This will efficiently improve then
                // running time of the javascript when perform drawing.
                var isDetailedDataAvailable = false;
                if(detailedData[id] == null){
                    try{
                        var worker = new Worker('/js/GetStationDataWorker.js');
                        worker.postMessage({id:id});
                        worker.onmessage = function(e){
                            detailedData[id] = e.data.data;
                            // set the graphs to appropriate dataset when 
                            graphs[id].getPlaceholder().bind('plotpan',function(event,plot){
                                Wovodat.redraw(graphs[id],graphData[id],e.data.data,graphs);
                            });
                            graphs[id].getPlaceholder().bind('plotzoom',function(event,plot){
                                Wovodat.redraw(graphs[id],graphData[id],e.data.data,graphs,true);
                            });
                        };
                    }catch(e){
                        Wovodat.getDetailedStationData({
                            id: id,
                            handler: function(e){
                                detailedData[id] = e.data;
                                // set the graphs to appropriate dataset when 
                                graphs[id].getPlaceholder().bind('plotpan',function(event,plot){
                                    Wovodat.redraw(graphs[id],graphData[id],e.data,graphs);
                                });
                                graphs[id].getPlaceholder().bind('plotzoom',function(event,plot){
                                    Wovodat.redraw(graphs[id],graphData[id],e.data,graphs,true);
                                });
                            }
                        });
                    }
                }else{
                    isDetailedDataAvailable = true;
                }
                //var data = args.data;
                if(graphData[id] == undefined)
                    graphData[id] = data;
                var minValue ,maxValue;
                var maxXValue = Number.MIN_VALUE;
                var sixMonths = 6*30*24*60*60*1000; // in milliseconds
                var minXValue;
                var i;
                var length = data[0].length;
                maxXValue = data[0][0][0];
                minValue = data[0][0][1];
                maxValue = minValue;
                for(i = 0 ; i < length; i++){
                    if(data[0][i][1] > maxValue) maxValue = data[0][i][1];
                    if(data[0][i][1] < minValue) minValue = data[0][i][1];
                }
                // get the maxXValue of every graph
                for(var a in graphData){
                    for(var b in graphs){
                        if (b == a){
                            var temp = graphData[a][0][0][0];
                            if(temp > maxXValue ) maxXValue = temp;
                        }
                    }
                }
                minXValue = maxXValue - sixMonths;
                minXValue = minXValue > data[0][length-1][0]? minXValue : data[0][length-1][0];
                for(var a in graphData){
                    for(var b in graphs){
                        if( b == a){
                            var temp = graphData[a][0][graphData[a][0].length -1][0];
                            if( minXValue < temp) minXValue = temp;
                        }
                    }
                }
                var tr = document.createElement('tr');
                tr.id = id + "Row";
                document.getElementById("GraphList").appendChild(tr);
                var display = id.split("&");
                if(display[3] != 'undefined'){
                    display = id;
                }else{
                    display = display[0]  + "&" + display[1] + "&" + display[2];
                }
                var td = document.createElement('td');
                var div = document.createElement('div');
                div.id = id + "Graph";
                div.style.width = '700px';
                div.style.height = '200px';
                td.appendChild(div);
                tr.appendChild(td);
                // temporarily hide the graph
                $(tr).css('display','none');
                // these marks will show the eruption start time 
                
                if(maxValue == minValue){
                    minValue = minValue - 1;
                    maxValue = maxValue + 1;
                }
                var options = {
                    series:{
                        points: {show:false},
                        color: 'rgb(60, 10, 255)'
                    },
                    grid:{
                        hoverable: true,
                        clickable: true,
                        backgroundColor:{
                            colors: ['#fff','#eee']
                        }
                    },
                    xaxis:{
                        max: maxXValue,
                        min: minXValue,
                        ticks: tickGenerator,
                        labelWidth: 50,
                        show: true
                    },
                    yaxis:{
                        panRange:[minValue,maxValue],  
                        zoomRange:[maxValue-minValue,maxValue-minValue],
                        max: maxValue,
                        min: minValue,
                        color: 'rgb(123,1,100)',
                        labelWidth: 25,
                        tickDecimals: 1
                    },
                    zoom:{
                        interactive: true
                    },
                    pan: {
                        interactive: true
                    }
                };
                if(id.indexOf('Seismic') > -1){
                    options.series.bars = {};
                    options.series.bars.show = true;
                    options.series.bars.barType = 'continued';
                }else{
                    options.series.lines = {};
                    options.series.lines.show = true;
                }
                data = {
                    data: data[0],
                    label: label
                };
                graphs[id] = $.plot($("#" + id.replace(/&/g,"\\&").replace(/=/g,"\\=") + "Graph"),[data,eruptionsData],options);
                graphs[id].getPlaceholder().bind('plotpan plotzoom',function(event,plot){
                    Wovodat.redraw(graphs[id],graphData[id],detailedData[id],graphs);
                });
                // redraw other graphs
                var placeholder;
                var temp;
                for( i in graphs){
                    if( i == id) continue;
                    placeholder = graphs[i].getPlaceholder();
                    temp = graphs[i].getOptions();
                    options.yaxis.panRange = temp.yaxis.panRange;
                    options.yaxis.zoomRange = temp.yaxis.zoomRange;
                    options.yaxis.max = temp.yaxis.max;
                    options.yaxis.min = temp.yaxis.min;
                    data = graphs[i].getData();
                    data = {
                        data: data[0].data,
                        label: data[0].label
                    };
                    graphs[i] = $.plot(placeholder,[data,eruptionsData],options);
                }
                // this part is for synchronize the pan and zoom of the graphs
                for( i in graphs){
                    if(i != id){
                        synchronizeGraph(i,id);
                    }
                }
                
                $("#GraphList").sortable();
                
                // showing the tooltip of information for the graphs when
                // user hovers mouse over a point on the graph.
                var previousPoint = null;
                graphs[id].getPlaceholder().bind('plothover',function(event,pos,item){
                    if(item){
                        if(previousPoint != item.dataIndex){
                            previousPoint = item.dataIndex;
                            $("#tooltip").remove();
                            var x = new Date(item.datapoint[0]);
                            var currentTime = item.datapoint[0];
                            var index = 0;
                            x = x.getUTCDate() + "/" + (x.getUTCMonth() + 1) + "/" + x.getUTCFullYear() + " " + x.getUTCHours() + ":" + x.getUTCMinutes() + ":" + x.getUTCSeconds();
                            var content = "Time: " + x + " UTC";
                            var id = this.id;
                            index = id.indexOf("Graph");
                            id = id.substr(0,index);
                            for(index in graphs){
                                graphs[index].unhighlight();
                            }
                            for(index in graphs){
                                var data = graphs[index].getData();
                                data = data[0].data;
                                var currentIndex = -1;
                                if(index == id){
                                    graphs[index].highlight(0,item.dataIndex);
                                    currentIndex = item.dataIndex;
                                }else{
                                    // searching for the value at the position x of 
                                    // graphs[index] using binary search
                                    var start = 0, end = data.length - 1;
                                    var mid = Math.floor((start + end) / 2);
                                    if(currentTime < data[end][0] || currentTime > data[start][0]){
                                        end = start - 1;
                                        currentIndex = -1;
                                    }
                                    while(start <= end){
                                        if(currentTime == data[mid][0]){
                                            graphs[index].highlight(0,mid);
                                            currentIndex = mid;
                                            break;
                                        }else{
                                            if(currentTime > data[mid][0]){
                                                end = mid - 1;
                                            }else{
                                                start = mid + 1;
                                            }
                                        }
                                        mid = Math.floor((start + end) / 2);
                                        if(end < start){
                                            currentIndex = -1;
                                        }
                                    }
                                }
                                if(currentIndex >= 0 ){
                                    content += "<br/>" + graphs[index].getData()[0].label + ": " + data[currentIndex][1];
                                }
                            }
                            Wovodat.showTooltip(item.pageX, item.pageY,content);
                        }
                    }else{
                        for(index in graphs){
                            graphs[index].unhighlight();
                        }
                        $("#tooltip").remove();
                        previousPoint = -1;
                    }
                });
                drawOverviewGraph();
                // making the overview shown
                $(tr).slideDown('slow');
                
                $("#overviewPanel").css('display','block');
                if(isDetailedDataAvailable){
                    graphs[id].getPlaceholder().bind('plotpan',function(event,plot){
                        Wovodat.redraw(graphs[id],graphData[id],detailedData[id],graphs);
                    });
                    graphs[id].getPlaceholder().bind('plotzoom',function(event,plot){
                        Wovodat.redraw(graphs[id],graphData[id],detailedData[id],graphs,true);
                    });
                }
            }
            function tickGenerator(axis){
                var ticks = 6;
                var size = axis.max - axis.min;
                size = size/ticks;
                var start = size * Math.floor(axis.min / size);
                var value = Number.Nan;
                var da;
                var res = [];
                for(var i = 0 ; i < ticks + 1 ; i++){
                    value = start + size * i;
                    value = value.toFixed(0);
                    value = Math.round(value);
                    da = new Date(value);
                    res.push([value, da.getUTCDate() + "/" + (da.getUTCMonth() + 1) + "/" + ("" + da.getUTCFullYear()).substr(0) + " " + da.getUTCHours() + ":" + da.getUTCMinutes() + ":" + da.getUTCSeconds()]);
                } 
                return res.sort();
            }
            // make the graph moves together
            function synchronizeGraph(i,j){
                var i1 = i.replace(/&/g,"\\&").replace(/=/g,"\\=");
                var j1 = j.replace(/&/g,"\\&").replace(/=/g,"\\=");
                $("#" + i1 + "Graph").bind('plotzoom',function(event,plot,args){
                    if(graphs[j] == undefined) return;
                    if(args[j] && args[j] == true)
                        return;
                    args[j] = true;
                    args.preventEvent = true;
                    graphs[j].zoom(args);
                    Wovodat.redraw(graphs[j],graphData[j],detailedData[j],graphs,true);
                });
                $("#" + i1 + "Graph").bind('plotpan',function(event,plot,args){
                    if(graphs[j] == undefined) return;
                    if(args[j] && args[j] == true)
                        return;
                    args[j] = true;
                    args.preventEvent = true;
                    graphs[j].pan(args);
                    Wovodat.redraw(graphs[j],graphData[j],detailedData[j],graphs);
                });
                $("#" + j1 + "Graph").bind('plotzoom',function(event,plot,args){
                    if(graphs[i] == undefined) return;
                    if(args[i] && args[i] == true)
                        return;
                    args[i] = true;
                    graphs[i].zoom(args);
                    Wovodat.redraw(graphs[i],graphData[i],detailedData[i],graphs,true);
                });
                $("#" + j1 + "Graph").bind('plotpan',function(event,plot,args){
                    if(graphs[i] == undefined) return;
                    if(args[i] && args[i] == true)
                        return;
                    args[i] = true;
                    args.preventEvent = true;
                    graphs[i].pan(args);
                    Wovodat.redraw(graphs[i],graphData[i],detailedData[i],graphs);
                });
            }
            function deleteGraph(args){
                var id = args.id;
                delete(graphs[id]);
                var tr = document.getElementById(id +'Row');
                if(tr)
                    tr.parentNode.removeChild(tr);
                var hideOverview = true;
                for(id in graphs){
                    hideOverview = false;
                    break;
                }
                if(hideOverview){
                    $("#overviewPanel").css('display','none');
                }else{
                    drawOverviewGraph();
                }
            }
            function deleteTimeSeriesList(type){
                var value;
                var id;
                var element;
                for(var t in stationsDatabase[type]){
                    value = stationsDatabase[type][t];
                    value = value.split("&");
                    id = value[0] + '&' + value[1] + '&' + value[2];
                    id = id + '&' + value[5];
                    deleteGraph({id:id});
                    element = document.getElementById(id + 'Tr');
                    element.parentNode.removeChild(element);
                }
            }
            function insertMarkersForStations(data){
                var value;
                var index;
                for(var i in data){
                    index = data[i];
                    value = index.split("&");
                    markers[index] = new google.maps.Marker({
                        position: new google.maps.LatLng(value[3], value[4]), 
                        map: map,
                        animation: google.maps.Animation.DROP
                    });
                    markers[index].index = index;
                    value = "Station type: " + value[0] + "<br/>Station code: " + value[2] + "<br/>Latitude: " + parseFloat(value[3]).toFixed(4)
                        + "<br/>Longitude: " + parseFloat(value[4]).toFixed(3);
                    infoWindows[index] = new google.maps.InfoWindow({
                        content:value 
                    });
                    google.maps.event.addListener(markers[index], 'click', function() {
                        for(var i in infoWindows){
                            infoWindows[i].close();
                            if(typeof infoWindow != 'undefined'){
                                if(typeof infoWindow.close != 'undefined'){
                                    infoWindow.close();
                                }
                            }
                        }
                            
                        infoWindows[this.index].open(map,markers[this.index]);
                    });
                    value = index.substr(0,1);
                    value = value.toLowerCase();
                    if(value == 't' || value == 'f'){
                        markers[index].setIcon('');
                    }else{
                        markers[index].setIcon('/img/pin_' + value + 's_s.png');
                    }
                }
            }
            function ImageExist(url) 
            {
                var img = new Image();
                img.src = url;
                return img.height != 0;
            }
            function updateTimeSeriesandStations(args){
                var action = args.action;
                switch(action){
                    case 'delete':
                        var type = args.type;
                        var index = '';// delete the available markers for this specific type
                        for(var i in stationsDatabase[type]){
                            index = stationsDatabase[type][i];
                            markers[index].setMap(null);
                        }
                        deleteTimeSeriesList(type);
                        break;
                    case 'updateNewData':
                        var type =args.type;
                        var data = args.data;
                        data = data.split(";");
                        data.length--;
                        stationsDatabase[type] = data;
                        // udpate the list of station and the markers on the custom google map 
                        updateTimeSeriesList(data);
                        insertMarkersForStations(data);
                        break;
                    case 'updateOldData':
                        var type = args.type;
                        var data = stationsDatabase[type];
                        // update the list of station and the markers on the google map
                        updateTimeSeriesList(data);
                        insertMarkersForStations(data);
                        break;
                    default:
                        break;
                }
            }
            function selectAllAvailableVolcanoWithStationData(){
                var list = document.getElementById('VolcanoList');
                var length = list.options.length;
                var t;
                var i = -1;
                var f = function(){
                    var list = document.getElementById('VolcanoList');
                    i++;
                    if(i == length) return;
                    list.options[i].selected = 'selected';
                    t = $("#VolcanoList").val().split('&')
                    Wovodat.getAvailableStations({
                        cavw: t[1],
                        handler: function(o){
                            list = o.list;
                            if(list != undefined && list.length >= 1) {
                                console.log(list);
                                console.log(o.volcano);
                            }
                        },
                        volcano: t[0]
                    });
                    setTimeout(function(){
                        f();
                    },200);    
                };
                f();
            }
            function randomSelectVolcano(){
                var list = document.getElementById('VolcanoList');
                var length = list.options.length;
                var i = Math.floor(Math.random()*length);
                list.options[i].selected = 'selected';
                $("#VolcanoList").change();
            }
            function updateStationList(args){
                stationTypeList.length = 0;
                stationTypeList = args.list;
                var stationsTable = $("#StationList");
                var html = '';
                if(stationTypeList.length == 0){
                    stationsTable.html("<tr><td></td><td>No station that has data near this volcano.</td></tr>");
                    return;
                }
                for (var i in stationTypeList){
                    html += "<tr><td><input type='checkbox' value='" + stationTypeList[i] + "' id='" + stationTypeList[i]+"Checkbox'/></td><td>" 
                        + formatReturnStations(stationTypeList[i])+  "</td></tr>";
                }
                stationsTable.html(html);
                for(var i in stationTypeList){
                    document.getElementById(stationTypeList[i] + "Checkbox").onclick = function(){
                        if(this.checked){
                            if(stationsDatabase[this.value]){
                                updateTimeSeriesandStations({
                                    type:   this.value,
                                    action: 'updateOldData'
                                });
                            }else{
                                var volcano = $("#VolcanoList").val();
                                volcano = volcano.split("&");
                                var cavw = volcano[1];
                                Wovodat.getStations({
                                    cavw: cavw,
                                    type: this.value,
                                    handler: updateTimeSeriesandStations
                                });
                            }
                        }else{
                            updateTimeSeriesandStations({action:'delete',type:this.value});
                        }
                    }
                }
            }
            function formatReturnStations(name){
                name = name + " stations";
                name = name.substr(0,1).toUpperCase() + name.substr(1);
                return name;
            }
            function drawMap(args){
                var volcano = $("#VolcanoList").val();
                volcano = volcano.split("&");
                if(args == undefined){
                    args = 0;
                }
                var lat = args.lat;
                var lon = args.lon;
                var elev = args.elev;
                // location of singapore
                if(!lat || !lon){
                    lat = 1.29;
                    lon = 103.85;
                }
                var myOptions = {
                    center: new google.maps.LatLng(lat, lon),
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.TERRAIN
                };
                map = new google.maps.Map(document.getElementById("Map"),myOptions);
                
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lon), 
                    map: map,
                    animation: google.maps.Animation.DROP
                });   
                var contentString = "Volcano: " + volcano[0] + "<br/>CAVW: " + volcano[1] +"<br/>Lat: " + parseFloat(lat).toFixed(3) + " N<br/>Lon: " + parseFloat(lon).toFixed(3)
                    + " E<br/>Elev: " + elev + "(meters)";
                infowindow = new google.maps.InfoWindow({
                    content: contentString
                });
                google.maps.event.addListener(marker, 'click', function() {
                    for(var i in infoWindows)
                        infoWindows[i].close();
                    infowindow.open(map,marker);
                });
            }
            function insertEruptionList(obj){
                var data = [];
                var list = obj.list;
                list = list.split(";");
                var eruptions = document.getElementById("EruptionList");
                eruptions.options.length = 0;
                eruptions.options = [];
                var i = 0;
                var t;
                eruptions.options[0] = new Option("Select...","");
                for(;i < list.length;i++){
                    if(list[i].length == 0) continue;
                    t = list[i].split("&");
                    if(t[2] != undefined){
                        t[1] = t[1] + " " + t[2];
                        data.push({label:'<img src="/img/EruptionIcon.png"/><br/><b>Eruption<br/>' + t[1] + '</b>',position:Wovodat.toDate(t[1]).getTime()});
                    }
                    eruptions.options[eruptions.options.length] = new Option(t[1],t[1]);
                }
                eruptionsData.markdata = data;
            }
            function insertVolcanoList(obj){
                // a list of volcanos and their cawv separated by: ;
                var list = obj.list;
                list = list.split(";");
                // get the volcano select list tag
                var volcanos = document.getElementById('VolcanoList');
                // reset the volcano list
                volcanos.options = [];
                // assign new list
                var i = 0;
                volcanos.options[0] = new Option("Select...","");
                for(;i < list.length;i++){
                    volcanos.options[volcanos.options.length] = new Option(list[i].replace('&','_'),list[i]);
                }
                randomSelectVolcano();
            }
        </script>
        <style type="text/css">
            #contentrview_x td{
                height: 30px;
            }
            #contentrview_x #StationList td{
                height: 20px;
            }
            #contentrview_x #StationList tr td:first-child{
                width: 50px;
                text-align: right;
            }
            #GraphList{
                text-align: center;
            }
            #overviewPanel{
                display: none;
            }
        </style>
    </head>
    <body>
        <div id="wrapborder_x">
            <div id="wrap_x">
                <?php include 'php/include/header_beta.php'; ?>
                <div id="content_x">
                    <div id="contentrview_x">
                        <div style="height:25px;font-size:9px;">
                            <table id="loading" style="display:none">
                                <tr>
                                    <td style="width:100px;" align="right"><img src="/gif2/loadinfo.net.gif"/></td>
                                    <td>Loading ...</td>
                                </tr>
                            </table>
                        </div>
                        <!-- Data input -->
                        <table >
                            <tr>
                                <td style="width:100px"><b>Volcano:</b> </td>
                                <td>
                                    <select id="VolcanoList" style="width:140px">
                                        <option value="">Select...</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Eruption:</b></td>
                                <td>
                                    <select id="EruptionList" style="width:140px">
                                        <option value="">Select...</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>View stations:</b></td>
                            </tr>
                        </table>
                        <table id="StationList">

                        </table>
                    </div>
                    <div id="contentlview_x" style="">
                        <br/>
                        <!-- Google map -->
                        <table>
                            <tr>
                                <td>
                                    <div style="width:400px;height:300px;float:left" id="Map">

                                    </div>
                                </td>
                                <td>

                                    <!-- Available options -->
                                    <div id="OptionList"  style="width:280px;height:300px;float:left;display:none">
                                        <ul style="background-color: #2042c1;border: #2042c1; ">
                                            <li style="color: white">Available time series data (max. 5)</li>
                                        </ul>
                                        <div style="
                                             overflow:auto;height:250px;
                                             margin-top:15px;
                                             margin-left: 2px;
                                             margin-right:2px;
                                             background-color:white;
                                             border: 1px solid #b0a9a9;
                                             " id="OptionList-1">
                                            <table id="TimeSeriesList" style="">

                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <!-- plotting area -->
                        <br/>
                        <div id="overviewPanel" style="padding-left: 50px;padding-bottom: 10px;float:left;width:610px">
                            <div style="text-align: right;float: right">
                                <button id="printGraphs" style="height:40px"onclick="printGraphs()">Print Graphs</button>
                            </div>
                            <b>Overview (select a range to redraw the graph): </b>
                            <div id="overview" style="width:400px;height:40px;">

                            </div>
                            <br/>

                        </div>
                        <div style="clear:both;" id="PlotArea">
                            <table id="GraphList">

                            </table>
                        </div>
                    </div>

                </div>

            </div>
            <?php include 'php/include/footer_beta.php'; ?>
        </div>
    </body>
</html>