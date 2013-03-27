<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<?php
// Start session
session_start();
// Regenerate session ID
session_regenerate_id(true);
$uname = "";

include_once 'check_session.php';

// if this code run on server then we need to cache the wovodat.js file on the 
// client code. Otherwise, we do not cach it for the purpose of development
$cache = time();
?> 
<html>
    <head>
        <title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
        <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
        <meta http-equiv="cache-control" content="no-cache, must-revalidate">
        <meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
        <meta name="keywords" content="Volcano, Vulcano, Volcanoes">
        <link href="/css/styles_beta.css" rel="stylesheet"> 
        <link href="/css/tooltip.css" rel="stylesheet">
        <link type="text/css" href="/js/jqueryui/css/custom-theme/jquery-ui-1.8.22.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="/js/jqueryui/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.tuan.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.navigate.tuan.js"></script> 
        <script type="text/javascript" src="/js/flot/jquery.flot.selection.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.marks.js"></script>
        <script type="text/javascript" src="/js/wovodat.js?<?php echo $cache; ?>"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ9kUvUtmawmFJ62hWVsigWFTh3CKUzzM&sensor=false"></script>
        <script type="text/javascript" src="/js/Tooltip_v3.js"></script>
        <script type="text/javascript">
            // this is the list of available station type for each volcano,
            // this list will be initialize when the volcano is selected and 
            // it will be deleted when this volcano is deselected.
            var stationTypeList = [];
            // this list of available time series list
            var timeSeriesList = [];
            // the google maps (for both Time Series view and Compare Volcano view
            var map=[];
            // the list of station for each station type
            var stationsDatabase = {};
            // the list of station for each station type - The second volcano - in Compare Volcano view
            var compStationsDatabase = {};
            // the markers and infowindows for volcano stations
            var markers = [],infoWindows = [];
            //the markers for volcanoes
            var volMarkers  = [];
            //the markers for neighbors
            var neighMarkers=[], neighInfoWindows = [];
            // this inforWindow is for the volcano
            var infowindowVolcano=[];
            //All information of the loaded volcano
            var volcanoInfo = {};
            // this link to all the plotted graph
            var graphs = [];
            // this link to all the plot data for each graph
            var graphData = [];	
            // the variable store the reference to the overview graph
            var overviewGraph;
            // these marks will show the eruption start time 
            // eruptions data
            var eruptionsData = {};
            eruptionsData.compEruptions = [];
            // reference data to since between various graphs
            var referenceTime = null;
            // full details scaled data
            var detailedData = [];
            $(document).ready(function(){
                
                // get the list of all volcano in our database and insert it into 
                // the dropdown list
                //insertVolcanoList is used as a callback function with 2 parameter: the first 
                //parameter is the list of Volcano, and the second parameter is the ID of the dropdown menu
                //to which data is populated
                Wovodat.getVolcanoList(insertVolcanoList,["VolcanoList","CompVolcanoList"]);
                
                // store the eruption data for later use
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
                // store the eruption data for the second volcano
                eruptionsData.compEruptions = {
                    marks: { 
                        show: true,
                        color: 'rgb(212,59,62)',
                        labelVAlign: 'top' ,
                        rows: 1
                    }, 
                    data: [], 
                    markdata: []
                };
                
                lat = 1.29;
                lon = 103.85;
                var myOptions = {
                    center: new google.maps.LatLng(lat, lon),
                    zoom: 7,
                    mapTypeControl:false,
                    mapTypeId: google.maps.MapTypeId.TERRAIN,
                    streetViewControl:false
                };
                map[1] = new google.maps.Map(document.getElementById("Map"),myOptions);		
                map[2] = new google.maps.Map(document.getElementById("Map2"),myOptions);	
                
                $("#gvp1").click(function() {
                    var locat= $("#VolcanoList :selected").text();
                    var locati=locat.split("_");
                    open("http://www.volcano.si.edu/world/volcano.cfm?vnum="+locati[1]);
                    return false;
                });
                $("#gvp2").click(function() {
                    var locat= $("#CompVolcanoList :selected").text();
                    var locati=locat.split("_");
                    open("http://www.volcano.si.edu/world/volcano.cfm?vnum="+locati[1]);
                    return false;
                });
                
                $("#HideStationButton1").click(function(){
                    hideStation(1);
                    $(this).hide();
                });
                $("#HideStationButton2").click(function(){
                    hideStation(2);
                    $(this).hide();
                });          
            
                Wovodat.showProcessingIcon($("#loading"));
                // when the volcano option is changed
                $("#VolcanoList").change(function(){
                    hideEarthquakeMarkerButton(1);
                    uncheckAllEquakeButton(1);
                    var volcano = $("#VolcanoList").val();
                    volcano = volcano.split("&");
                    var cavw = volcano[1];
                    Wovodat.getLatLon({cavw:cavw,handler:drawMap,mapUsed:1},"VolcanoList", "Map");
                    //initialise value for Number of Events textbox
                    var startDate = new Date(1900, 0, 1, 0, 0, 0, 0);
                    var endDate = new Date();
                    $("#Evn1").val(500);
                    $("#SDate1").datepicker({changeMonth:true,changeYear:true,yearRange:"1900:2100"});
                    $("#EDate1").datepicker({changeMonth:true,changeYear:true,yearRange:"1900:2100"});
                    $("#DateRange1").slider({
                        range: true,
                        max: Math.floor(endDate.getTime()-startDate.getTime())/86400000,
                        values : [0, Math.floor(endDate.getTime()-startDate.getTime())/86400000],
                        slide: function(event,ui){
                            var startDate = new Date(1900, 0, 1, 0, 0, 0, 0);
                            var date = new Date(startDate.getTime());
                            date.setDate(date.getDate() + ui.values[0]);
                            $("#SDate1").val($.datepicker.formatDate('mm/dd/yy',date));
                            date = new Date(startDate.getTime());
                            date.setDate(date.getDate() + ui.values[1]);
                            $("#EDate1").val($.datepicker.formatDate('mm/dd/yy',date));
                        }
                    });
                    $("#DepthLow1").val(0);
                    $("#DepthHigh1").val(40);
                    $("#SDate1").change(function(){adjustSlider("1");});
                    $("#EDate1").change(function(){adjustSlider("1");});
                    var today = new Date();
                    
                    $("#FormFilter1").hide();
                    $("#FilterSwitch1").html("Show Filter");
                    $("#FlotDisplayLat1").html("");
                    $("#FlotDisplayLon1").html("");
                    //get the list of neightbors
                    //and position them in the map
                    Wovodat.getNeighbors(cavw,1,insertMarkersForNeighbors);
                    // get the eruption list for that specific volcano
                    Wovodat.getEruptionList({
                        volcano: $("#VolcanoList").val(),
                        handler: insertEruptionList,
                        selectId:"EruptionList"
                    });
                    //get data owner of the volcano
                    Wovodat.getCcUrl("1",cavw,insertDataOwnerandStatus);
                    // get the location of that volcano and position to it in the
                    // google map
                    // update the list of available station
                    // time-series view
                    //compare volcano view
                    Wovodat.getAllStationsList({
                        cavw:cavw,
                        handler:updateAllStationsList,
                        tableId:"StationList",
                        mapId:"Map",
                        stationsDatabaseUsed: stationsDatabase,
                        mapUsed:1
                    });
                    // Insert the list of available data in available time series
                    // to the list in the comparison view
                    Wovodat.getListOfTimeSeriesForVolcano({
                        cavw:cavw,
                        handler:updateTimeSeriesList,
                        tableId:1
                    });
                    // delete all the drawn graphs and the time series list
                    if ($("#CompVolc").css("display")=="none"){
                        for(var i in graphs){
                            delete(graphs[i]);
                            var div = document.getElementById(i + 'Row');
                            div.parentNode.removeChild(div);
                        }
                        document.getElementById('overviewPanel').style.display = 'none';
                        document.getElementById('TimeSeriesList').innerHTML = '';
                    }else{
                        for(var i in graphs){
                            var j = side(i);
                            if(j == 1){
                                delete graphs[i];
                                var div = document.getElementById(i.substring(0,i.length-1) + 'Row' + j);
                                div.parentNode.removeChild(div);
                            }
                        }
                        document.getElementById('overviewPanel1').style.display = 'none';
                        document.getElementById('TimeSeriesList1').innerHTML = '';
                    }
                    // reset the local list of available stations for each data type
                    delete(stationsDatabase);
                    stationsDatabase = {};
                    
                    hideEquakePanel({mapUsed:1});
                    hideMarkers({mapUsed:1});
                    clearEquakedrawingData({mapUsed:1});
                });
                
                $("#HideVolcanoInformation1").click(function(){
                    $("#VolcanoPanel1").hide();
                    return false;
                });
                
                $("#VolcanoInformation1").click(function(){
                    $("#VolcanoPanel1").show();
                    return false;
                });
                $("#HideVolcanoInformation2").click(function(){
                    $("#VolcanoPanel2").hide();
                    return false;
                });
                $("#VolcanoInformation2").click(function(){
                    $("#VolcanoPanel2").show();
                    return false;
                });
                $("#CompVolcanoList").change(function(){
                    hideEarthquakeMarkerButton(2);
                    uncheckAllEquakeButton(2);
                    var volcano = $("#CompVolcanoList").val();
                    volcano = volcano.split("&");
                    var cavw = volcano[1];
                    Wovodat.getLatLon({cavw:cavw,handler:drawMap,mapUsed:2},"CompVolcanoList", "Map2");
                    
                    $("#FormFilter2").hide();
                    $("#FilterSwitch2").html("Show Filter");
                    $("#FlotDisplayLat2").html("");
                    $("#FlotDisplayLon2").html("");
                    //initialize Number of events textbox
                    $("#Evn2").val(500);
                    //create date picker for earthquake filter form
                    $("#SDate2").val("01/01/1900");
                    var today = new Date();
                    $("#EDate2").val($.datepicker.formatDate("m/d/yy", today));
                    $("#SDate2").datepicker({changeMonth:true,changeYear:true,yearRange:"1900:2100"});
                    $("#EDate2").datepicker({changeMonth:true,changeYear:true,yearRange:"1900:2100"});
                    //Create slider for earthquake filter form
                    $("#DepthLow2").val(0);
                    $("#DepthHigh2").val(40);
                    $("#SDate2").change(function(){adjustSlider("2");});
                    $("#EDate2").change(function(){adjustSlider("2");});
                    var startDate = new Date(1900, 0, 1, 0, 0, 0, 0);
                    var endDate = new Date();
                    $("#DateRange2").slider({
                        range: true,
                        max: Math.floor(endDate.getTime()-startDate.getTime())/86400000,
                        values : [0,Math.floor(endDate.getTime()-startDate.getTime())/86400000],
                        slide: function(event,ui){
                            var startDate = new Date(1900, 0, 1, 0, 0, 0, 0);
                            var date = new Date(startDate.getTime());
                            date.setDate(date.getDate() + ui.values[0]);
                            $("#SDate2").val($.datepicker.formatDate('mm/dd/yy',date));
                            date = new Date(startDate.getTime());
                            date.setDate(date.getDate() + ui.values[1]);
                            $("#EDate2").val($.datepicker.formatDate('mm/dd/yy',date));
                        }
                    });
                    Wovodat.getEruptionList({
                        volcano: $("#CompVolcanoList").val(),
                        handler: insertEruptionList,
                        selectId:"CompEruptionList"
                    });
                    //get list of neighbors
                    Wovodat.getNeighbors(cavw,2,insertMarkersForNeighbors);
                    //get data owner of the volcano
                    Wovodat.getCcUrl("2",cavw,insertDataOwnerandStatus);
                    Wovodat.getAllStationsList({
                        cavw: cavw,
                        handler: updateAllStationsList,
                        tableId:"CompStationList",
                        mapId:"Map2",
                        stationsDatabaseUsed:compStationsDatabase,
                        mapUsed:2
                    });
                    Wovodat.getListOfTimeSeriesForVolcano({
                        cavw:cavw,
                        handler:updateTimeSeriesList,
                        tableId:2
                    });
                    for(var i in graphs){
                        var j = side(i);
                        if(j == 2){
                            delete graphs[i];
                            var div = document.getElementById(i.substring(0,i.length-1) + 'Row' + j);
                            div.parentNode.removeChild(div);
                        }
                    }
                    document.getElementById('overviewPanel2').style.display = 'none';
                    document.getElementById('TimeSeriesList2').innerHTML = '';
                    // reset the local list of available stations for each data type
                    delete(compStationsDatabase);
                    compStationsDatabase = {};
                
                
                    hideEquakePanel({mapUsed:2});
                    hideMarkers({mapUsed:2});
                    clearEquakedrawingData({mapUsed:2});
                });
                
                // get all the available graph move to the eruption
                $("#EruptionList").change(function(){
                    moveGraphsToEruptionTime.apply(this);
                });
                $("#CompEruptionList").change(function(){
                    moveGraphsToEruptionTime.apply(this,[2]);
                });
                var buttons = document.getElementsByTagName('button');
                for(var i = 0 ; i < buttons.length ; i++){
                    var button = buttons[i];
                    button.style.fontSize = '10px';
                }
                $("#ShowMap1").click(function(){
                    $("#Map").show();
                    $("#map_legend1").show();
                });
                $("#ShowMap2").click(function(){
                    $("#Map2").show();
                    $("#map_legend2").show();
                });
                $("#HideMap1").click(function(){
                    $("#Map").hide();
                    $("#map_legend1").hide();
                });
                $("#HideMap2").click(function(){
                    $("#Map2").hide();
                    $("#map_legend2").hide();
                });
            });
            function getCavw(mapUsed){
                if(mapUsed == undefined || (mapUsed !=1 && mapUsed != 2))
                    return ;
                var dropdownList;
                if(mapUsed == 1){
                    dropdownList = document.getElementById('VolcanoList');
                }else{
                    dropdownList = document.getElementById('CompVolcanoList');
                }
                var value = dropdownList.value;
                if(value == undefined)
                    return;
                var list = value.split("&");
                if(list.length != 2)
                    return;
                var cavw = list[1];
                return cavw;
            }
            function insertMarkersForNeighbors(cavw, list, panelUsed){
                //remove all neighMarkers
                for (var i in neighMarkers[panelUsed])
                    neighMarkers[panelUsed][i].setMap(null);
                neighMarkers[panelUsed]=[];
                if (list[list.length]=="")
                    list.length--;
                for (var index in list){
                    // alert(list[index]);
                    var info_split = list[index].split(";");
                    var neighCavw = info_split[0];
                    var name = info_split[1];
                    var lat = info_split[2];
                    var lon = info_split[3];
                    var marker = new google.maps.Marker({
                        position:new google.maps.LatLng(lat,lon)
                    });
                    marker.setIcon("/img/pin.png");
                    marker.setMap(map[panelUsed]);
                    marker.setTitle(name+"_"+neighCavw);
                    neighMarkers[panelUsed].push(marker);
                    google.maps.event.addListener(marker,"click",function(){
                        var selectDom;
                        var selectj;
                        if (panelUsed==1){
                            selectDom = document.getElementById("VolcanoList");
                            selectj = $("#VolcanoList");
                        }
                        else{
                            selectDom = document.getElementById("CompVolcanoList");
                            selectj = $("#CompVolcanoList");
                        }
                        var title = this.getTitle();
                        title = title.split('_');
                        title = Wovodat.trim(title[1]);
                        var l = selectDom.options.length;
                        for (var i = 0 ; i < l ; i++){
                            if(selectDom.options[i].text == undefined) 
                                continue;
                            if ((selectDom.options[i].text).indexOf(title) > -1){
                                selectDom.selectedIndex = i;
                                selectj.change();
                                break;
                            }
                        }
						
                    });
                }
            }
            // when user select a specific eruption, all the graphs will move to 
            // the volcano in the time series
            function moveGraphsToEruptionTime(tableId){
                // get time of the eruption
                if(!tableId){
                    if ($("#Map2").css("display") != "none"){
                        tableId = 1;
                    }else{
                        tableId = '';
                    }
                }
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
                    var temp = side(i);
                    if(temp != tableId) continue;
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
                    var temp = side(i);
                    if(temp != tableId) continue;
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
                        yaxis:options.yaxis,
                        zoom:{
                            interactive: true
                        },
                        pan: {
                            interactive: true
                        }
                    };
                    newOptions.xaxis = options.xaxis;
                    newOptions.xaxis.max = maxRange;
                    newOptions.xaxis.min = minRange;
                    placeholder.empty();
                    delete(graphs[i]);
                    if(temp == '2')
                        graphs[i] = $.plot(placeholder,[data,eruptionsData.compEruptions],newOptions);
                    else
                        graphs[i] = $.plot(placeholder,[data,eruptionsData],newOptions);
                }
            }
            //synchronize slide with textbox
            function adjustSlider(id){
                $("#DepthRange"+id).slider("values",[$("#DepthLow"+id).val(),$("#DepthHigh"+id).val()]);
            }
			
            
            // when user select a specific eruption, all the graphs will move to 
            // the volcano in the time series
            function updateTimeSeriesList(data,tableId){
                var timeSeriesList;
                var optionList = document.getElementById('OptionList' + tableId + '-1');
                if(tableId == null){
                    timeSeriesList = document.getElementById('TimeSeriesList');
                }
                else{
                    data = data.split(';');
                    data.length = data.length - 1;
                    timeSeriesList = document.getElementById('TimeSeriesList' + tableId);
                    timeSeriesList.innerHTML = '';
                    // delete all the graph and the overview of tableId side
                    $('#overviewPanel' + tableId).css('display','none');
                    $('#overview' + tableId).html('');
                    $('#GraphList' + tableId).html('');
                    for(var k in graphs){
                        var m = side(k);
                        if(m == tableId){
                            delete graphs[m];
                        }
                    }
                }
                if(timeSeriesList == null) return;
                var count = 0;
                var t;
                var value;
                var display;
                for(var i in data){
                    count++;
                    value = Wovodat.trim(data[i]);
                    value = value.split('&');
                    //display = value[0] + '_' + value[1] + '_' + value[2];
                    display = value[1];
                    if(value[5] != undefined)
                        display = display + '-' + value[5];
                    display += " (" + value[2] + ")";
                    value = value[0] + "&" + value[1] + "&" + value[2] + '&' + value[5];
                    t = document.createElement('tr');
                    if(tableId == null)
                        t.id = value + 'Tr';
                    else
                        t.id = value + 'Tr' + tableId;
                    timeSeriesList.appendChild(t);
                    $("#" + Wovodat.fixJSelector(t.id)).html("<td><input type='checkbox' id='" +value +  "' value='" + value + "' onclick='drawTimeSeries(this," + tableId + ")'></td><td>" + display + "</td>");          
                
                }
                if(count == 0){
                    timeSeriesList.innerHTML = "<tr><td>No data is available yet.</td></tr>";
                    optionList.style.height = '30px';
                }else{
                    if(count > 3) count = 3;
                    optionList.style.height  = 40 + (count-1)*17 + 'px';
                }
            }
            function drawTimeSeries(obj,tableId){
                var value = obj.value;
                var index = value;
                value = value.split("&");
                var type = value[0];
                var table = value[1];
                var code = value[2];
                var component = value[3];
                if(obj.checked){
                    var count = 0; 
                    for(var i in graphs){
                        var s = side(i);
                        if(s == tableId)
                            count++;
                    }
                    if(count >= 3){
                        alert('Please choose at most 3 time series to draw');
                        obj.checked = false;
                        return;
                    }
                    if(graphData[index] != undefined){
                        drawGraph({
                            id: index,
                            data: graphData[index],
                            tableId:tableId
                        });
                    }else{
                        Wovodat.getStationData({
                            type:type,
                            table:table,
                            code:code,
                            component: component,
                            handler:drawGraph,
                            tableId:tableId
                        });
                        
                    }
                }else{
                    deleteGraph({id:obj.value,tableId:tableId});
                }
            }
            // get the list of owners based on the data         
            function getOwnerList(data){
                var mySet = {}, temp;
                var length = data[0].length;
                if(length == 0) 
                    return;
                var i,j;
                for( i = 0 ; i < length; i++){
                    for(j = 3; j <6; j++){
                        temp = data[0][i][j];
                        mySet[temp] = true;
                    }
                }
                var ownerList = [];
                j = 0;
                for(i in mySet){
                    if(i != '0' && i != 'undefined')
                        ownerList[j++] = parseInt(i);
                }
                return ownerList;
            }
            // main function to draw the time series graph
            // data has the format [[[x1,y1],[x2,y2],[x3,y3]]]
            // id is the string to specify the type of the data
            function drawGraph(args){
                var id = args.id;
                // the map used
                var tableId = args.tableId;
                if(!tableId)
                    tableId = "";
                
                // get the label from the list of available time series
                var label = document.getElementById(id + 'Tr' + tableId).getElementsByTagName('td')[1].innerHTML;
                
                // delete the link between data that are too long from each other
                var data = Wovodat.highlightNoDataRange(args.data);
                
                // delete the data thar are too big compare to its neighbor
                data = Wovodat.fixBigData(data);
                
                // set up the reference time
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
                    Wovodat.getDetailedStationData({
                        id: id,
                        handler: function(e){
                            detailedData[id] = e.data;
                            // set the graphs to appropriate dataset when 
                            if(graphs[id+tableId]){
                                    
                                graphs[id + tableId].getPlaceholder().bind('plotpan',function(event,plot){
                                    Wovodat.redraw(graphs[id + tableId],graphData[id],e.data,graphs);
                                });
                                graphs[id + tableId].getPlaceholder().bind('plotzoom',function(event,plot){
                                    Wovodat.redraw(graphs[id + tableId],graphData[id],e.data,graphs,true);
                                });
                            }
                            Wovodat.showNotification({message:"Updated detailed data for " + label + " graph.",duration: 10});
                        }
                    });
                }else{
                    isDetailedDataAvailable = true;
                }
                
                if(graphData[id] == undefined)
                    graphData[id] = data;
                    
                var minValue ,maxValue;
                var maxXValue = Number.MIN_VALUE;
                var sixMonths = 6*30*24*60*60*1000; // in milliseconds
                var minXValue,xRangeMin;
                var i;
                var length = data[0].length;
                maxXValue = data[0][0][0];
                
                minValue = data[0][0][1];
                maxValue = minValue;
                
                xRangeMin = data[0][length-1][0];
                
                // get the min and max of y for current graph
                for(i = 0 ; i < length; i++){
                    if(data[0][i][1] == null) 
                        continue;
                    if(data[0][i][1] > maxValue) maxValue = data[0][i][1];
                    if(data[0][i][1] < minValue) minValue = data[0][i][1];
                }
                
                // get the maxXValue of every graph that is currently displayed
                for(var b in graphs){
                    // do not consider the graph that is not in the same side
                    if(tableId != side(b)) continue;
                    
                    for(var a in graphData){
                        if (b.indexOf(a) >= 0){
                            var temp = graphData[a][0][0][0];
                            if(temp > maxXValue ) maxXValue = temp;
                            var l = graphData[a][0].length;
                            var t = graphData[a][0][l-1][0];
                            if(t < xRangeMin) xRangeMin = t;
                        }
                    }
                }
                minXValue = maxXValue - sixMonths;
                minXValue = minXValue > data[0][length-1][0]? minXValue : data[0][length-1][0];
                
                for(var a in graphData){
                    if(tableId != side(a)) continue;
                    
                    for(var b in graphs){
                        if( b.indexOf(a) >= 0){
                            var temp = graphData[a][0][graphData[a][0].length -1][0];
                            if( minXValue > temp) minXValue = temp;
                        }
                    }
                }
                // dynamically create the table row for drawing the time series graph
                var tr = document.createElement('tr');
                tr.id = id + "Row" + tableId;
                document.getElementById("GraphList" + tableId ).appendChild(tr);
                var display = id.split("&");
                if(display[3] != 'undefined'){
                    display = id;
                }else{
                    display = display[0]  + "&" + display[1] + "&" + display[2];
                }
                // create the dropdown list for selecting the owner the data
                var td = document.createElement('td');
                var ownerLists = document.createElement('select');
                ownerLists.style.display = 'none';
                ownerLists.id = id + 'OwnerList' + tableId;
                ownerLists.style.setProperty('float','right');
                ownerLists.style.setProperty('height','16px');
                var ownerOption = document.createElement('option');
                ownerOption.value = '';
                ownerOption.innerHTML = 'Select Owner';
                ownerLists.appendChild(ownerOption);
                ownerLists.style.width = '100px';
                td.appendChild(ownerLists);
                var ownerList = getOwnerList(data);
                Wovodat.getOwnerList(ownerList,function(obj){
                    var j;
                    ownerLists = document.getElementById(ownerLists.id);
                    for(j in obj){
                        ownerOption = document.createElement('option');
                        ownerOption.value = j;
                        ownerOption.innerHTML = obj[j];
                        ownerLists.appendChild(ownerOption);
                    }
                    ownerOption = document.createElement('option');
                    ownerOption.value = 'All';
                    ownerOption.innerHTML = 'All';
                    ownerLists.appendChild(ownerOption);
                    
                    // redraw the graph based on the owner that user has chosen
                    ownerLists.onchange = function(){
                        var graph = graphs[id+tableId];
                        var ccid = ownerLists.value;
                        var data = graph.getData();
                        var initialData = graphData[id];
                        var tempData = [];
                        tempData[0] = [];
                        var length = initialData[0].length;
                        var i = 0;
                        if(ccid == 'All'){
                            data = initialData;
                        }else if(ccid == ''){
                            data[0].data = null;
                        }else{
                            for(i = 0 ; i < length; i++){
                                if(initialData[0][i][2] == ccid || initialData[0][i][3] == ccid || initialData[0][i][4] == ccid)
                                    tempData.push(initialData[0][i]);
                            }
                            data[0].data = tempData;
                        }
                        var placeholder = graph.getPlaceholder();
                        placeholder = placeholder[0];
                        placeholder.innerHTML = '';
                        graphs[id+tableId] = $.plot(placeholder,data,graph.getOptions());
                    };
                });
                
                // the label is taken from the checked element
                if(tableId) td.innerHTML += label;
                // div element to draw the graph into
                var div = document.createElement('div');
                div.id = id + "Graph" + tableId;
                div.style.width = '440px';
                div.style.height = '150px';
                td.appendChild(div);
                tr.appendChild(td);
                
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
                        panRange:[xRangeMin,maxXValue],
                        zoomRange:[Wovodat.ONE_DAY,maxXValue - xRangeMin],
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
                        labelHeight: 40
                        //,tickDecimals: 1
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
                    data: data[0]
                };
                if(!tableId) data.label = label;
                if(tableId == 2)
                    graphs[id + tableId] = $.plot($("#" + Wovodat.fixJSelector(id) + "Graph" + tableId),[data,eruptionsData.compEruptions],options);
                else
                    graphs[id + tableId] = $.plot($("#" + Wovodat.fixJSelector(id) + "Graph" + tableId),[data,eruptionsData],options);
                graphs[id + tableId].getPlaceholder().bind('plotpan plotzoom',function(event,plot){
                    Wovodat.redraw(graphs[id + tableId],graphData[id],detailedData[id],graphs);
                });
                // redraw other graphs
                // need to consider the end character of the id
                var placeholder;
                var temp;
                for( i in graphs){
                    // do not redraw itself
                    if( i == (id + tableId)) continue;
                    var j = side(i);
                    // only redraw the graph that is on the similar side with this
                    // graph
                    if(j == tableId){
                        placeholder = graphs[i].getPlaceholder();
                        temp = graphs[i].getOptions();
                        options.yaxis.panRange = temp.yaxis.panRange;
                        options.yaxis.zoomRange = temp.yaxis.zoomRange;
                        options.yaxis.max = temp.yaxis.max;
                        options.yaxis.min = temp.yaxis.min;
                        options.series.bars = temp.series.bars;
                        options.series.lines = temp.series.lines;
                        data = graphs[i].getData();
                        data = {
                            data: data[0].data,
                            label: data[0].label
                        };
                        graphs[i] = $.plot(placeholder,[data],options);
                    }
                }
                
                // this part is for synchronize the pan and zoom of the graphs
                for( i in graphs){
                    var temp = side(i);
                    if(temp == tableId){
                        if(i != id + tableId)
                            synchronizeGraph(i,id + '' + tableId);
                    }
                }
                
                // showing the tooltip of information for the graphs when
                // user hovers mouse over a point on the graph.
                var previousPoint = null;
                $("#" + Wovodat.fixJSelector(id) + "Graph" + tableId).bind('plothover',function(event,pos,item){
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
                                if(tableId){
                                    var j = index.length;
                                    j = parseInt(index.substring(j-1,j));
                                    if(j != tableId){
                                        continue;
                                    }
                                }
                                graphs[index].unhighlight();
                            }
                            for(index in graphs){
                                if(tableId){
                                    var j = index.length;
                                    j = parseInt(index.substring(j-1,j));
                                    if(j != tableId){
                                        continue;
                                    }
                                }
                                var data = graphs[index].getData();
                                data = data[0].data;
                                var currentIndex = -1;
                                if(index == id){
                                    graphs[index].highlight(0,item.dataIndex);
                                    currentIndex = item.dataIndex;
                                }
                                else{
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
                                if(currentIndex > 0){
                                    var m = side(index);
                                    index = index.substring(0,index.length-1);
                                    var k = document.getElementById(index + 'Row' + m).getElementsByTagName('td')[0];
                                    // the text of the graph is the second child node of the row
                                    content += "<br/>" + k.childNodes[1].nodeValue + ": " + data[currentIndex][1];
                                }
                            }
                            Wovodat.showTooltip(item.pageX, item.pageY,content);
                        }
                    }else{
                        for(index in graphs){
                            if(tableId){
                                var j = index.length;
                                j = parseInt(index.substring(j-1,j));
                                if(j != tableId){
                                    continue;
                                }
                            }
                            graphs[index].unhighlight();
                        }
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });
                $("#overviewPanel" + tableId).css('display','block');
                drawOverviewGraph(tableId);
                // making the overview shown
                
                if(isDetailedDataAvailable){
                    graphs[id + tableId].getPlaceholder().bind('plotpan',function(event,plot){
                        Wovodat.redraw(graphs[id + tableId],graphData[id],detailedData[id],graphs);
                    });
                    graphs[id + tableId].getPlaceholder().bind('plotzoom',function(event,plot){
                        Wovodat.redraw(graphs[id + tableId],graphData[id],detailedData[id],graphs,true);
                    });
                }
                
            }
            
            // draw overview graph
            function drawOverviewGraph(tableId){
                if(!tableId) {
                    return; 
                }
                var placeholder= document.getElementById('overview' + tableId);
                placeholder.innerHTML = '';
                $(placeholder).show();
                var id;
                var data = [];
                
                // consider two case when we are in comparison view or in single view
                // get the correct id for the graph data, this is different with the graphs id
                for(id in graphs){
                    var j = id.length;
                    j = parseInt(id.substring(j-1,j));
                    if( j != tableId) continue;
                    else id = id.substring(0,id.length -1 );
                    data.push(graphData[id][0]);
                }
                var options = {
                    series: {
                        lines: { show: true},
                        shadowSize: 0
                    },
                    xaxis: { mode:'time'},
                    yaxis: { ticks: []}, // no tick for the yaxis
                    selection: { mode: "x", color: '#451A2B' }
                };
                $.plot(placeholder,data,options);
                /*
                 * This section of code allow the user to see the updated version
                 * of every graph below the overview graph when user selecs a 
                 * portion of the overview graph.
                 */
                // clear previous handler
                $("#overview" + tableId).unbind('plotselected');
                // draw other main graphs when user select a portion of this graph
                $("#overview" + tableId).bind('plotselected',function(event,ranges){
                    var id;
                    var plot;
                    var options,data,placeholder,newOptions;
                    var to = ranges.xaxis.to;
                    var from = ranges.xaxis.from;
                    for(id in graphs){
                        if(tableId){
                            var j = id.length;
                            j = parseInt(id.substring(j-1,j));
                            if(j != tableId) continue;
                            else id = id.substring(0,id.length-1);
                        }
                        plot = graphs[id + tableId];
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
                            yaxis:{
                                panRange: options.yaxis.panRange,
                                zoomRange: options.yaxis.zoomRange,
                                max: maxY,
                                min: minY,
                                color: 'rgb(123,1,100)',
                                labelWidth: 25,
                                labelHeigth: 40,
                                tickDecimals:1
                            },
                            zoom:{
                                interactive: true
                            },
                            pan: {
                                interactive: true
                            }
                        }
                        newOptions.xaxis = options.xaxis;
                        newOptions.xaxis.max = to;
                        newOptions.xaxis.min = from;
                        if(tableId == '2')
                            graphs[id + tableId] = $.plot(placeholder,[data,eruptionsData.compEruptions],newOptions);
                        else
                            graphs[id + tableId] = $.plot(placeholder,[data,eruptionsData],newOptions);
                        Wovodat.redraw(graphs[id + tableId],graphData[id],detailedData[id],graphs,true);
                    }
                });
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
            
            // get the map used: 1 or 2
            function side(a){
                var m = a.length;
                if(a.indexOf('Tilt') > 0) m = m - 1;
                var k = a.substring(m-1,m);
                if(k == '1' || k == '2'){
                    m = a.length;
                    k = a.substring(m-1,m);
                    return k;
                }else{
                    return '';
                }
            }
                
            // make the graph moves together when user manipulate one graph
            function synchronizeGraph(i,j){
                var temp = side(i);
                if(temp != side(j)) return;
                var i1 = i.replace(/&/g,"\\&").replace(/=/g,"\\=");
                var j1 = j.replace(/&/g,"\\&").replace(/=/g,"\\=");
                var i2,j2;
                if(temp == '1' || temp == '2'){
                    var t = i.length;
                    i2 = i.substring(0,t-1);
                    t = j.length;
                    j2 = j.substring(0,t-1);
                    var t = i1.length;
                    i1 = i1.substring(0,t-1);
                    t = j1.length;
                    j1 = j1.substring(0,t-1);
                }else{
                    i2 = i;
                    j2 = j;
                }
                $("#" + i1 + "Graph" + temp).bind('plotzoom',function(event,plot,args){
                    if(graphs[j] == undefined) return;
                    if(args[j] && args[j] == true)
                        return;
                    args[j] = true;
                    args.preventEvent = true;
                    graphs[j].zoom(args);
                    Wovodat.redraw(graphs[j],graphData[j2],detailedData[j2],graphs,true);
                });
                $("#" + i1 + "Graph"  + temp).bind('plotpan',function(event,plot,args){
                    if(graphs[j] == undefined) return;
                    if(args[j] && args[j] == true)
                        return;
                    args[j] = true;
                    args.preventEvent = true;
                    graphs[j].pan(args);
                    Wovodat.redraw(graphs[j],graphData[j2],detailedData[j2],graphs);
                });
                $("#" + j1 + "Graph" + temp).bind('plotzoom',function(event,plot,args){
                    if(graphs[i] == undefined) return;
                    if(args[i] && args[i] == true)
                        return;
                    args[i] = true;
                    graphs[i].zoom(args);
                    Wovodat.redraw(graphs[i],graphData[i2],detailedData[i2],graphs,true);
                });
                $("#" + j1 + "Graph" + temp).bind('plotpan',function(event,plot,args){
                    if(graphs[i] == undefined) return;
                    if(args[i] && args[i] == true)
                        return;
                    args[i] = true;
                    args.preventEvent = true;
                    graphs[i].pan(args);
                    Wovodat.redraw(graphs[i],graphData[i2],detailedData[i2],graphs);
                });
            }
            function deleteGraph(args){
                var id = args.id;
                var tableId = args.tableId;
                if(tableId == undefined) tableId = "";
                delete(graphs[id + tableId]);
                var tr = document.getElementById(id +'Row'  + tableId);
                if(tr)
                    tr.parentNode.removeChild(tr);
                var hideOverview = true;
                for(id in graphs){
                    if(tableId){
                        var j = id.length;
                        j = parseInt(id.substring(j-1,j));
                        if(j!= tableId) continue;
                    }
                    hideOverview = false;
                    break;
                }
                if(hideOverview){
                    $("#overviewPanel" + tableId).css('display','none');
                }else{
                    drawOverviewGraph(tableId);
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
            function insertMarkersForStations(data,mapUsed){
                var value;
                var index;
                for(var i in data){
                    index = data[i];
                    value = index.split("&");
                    markers[index] = new google.maps.Marker({
                        position: new google.maps.LatLng(value[3], value[4]), 
                        map: map[mapUsed],
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
                            
                        infoWindows[this.index].open(map[mapUsed],markers[this.index]);
                    });
                    value = index.substr(0,1);
                    value = value.toLowerCase();
                    if(value == 'f'){
                        markers[index].setIcon('');
                    }else{
                        markers[index].setIcon('/img/pin_' + value + 's_s.png');
                    }
                }
            }
        
            function toRad(number){
                return number * Math.PI /180;
            }
            function openInfoWindow(){
                var marker = openInfoWindow.marker;
                var infoWindow = openInfoWindow.infoWindow;
                infoWindow.open(marker.getMap(),marker);
            }
			
            function ImageExist(url) {
                var img = new Image();
                img.src = url;
                return img.height != 0;
            }
            function updateTimeSeriesandStations(args,stationsDatabaseUsed,mapUsed){
                var action = args.action;
                switch(action){
                    case 'delete':
                        var type = args.type;
                        var index = '';// delete the available markers for this specific type
                        for(var i in stationsDatabaseUsed[type]){
                            index = stationsDatabaseUsed[type][i];
                            markers[index].setMap(null);
                        }
                        deleteTimeSeriesList(type);
                        break;
                    case 'updateNewData':
                        var type =args.type;
                        var data = args.data;
                        data = data.split(";");
                        data.length--;
                        stationsDatabaseUsed[type] = data;
                        // udpate the list of station nad the markers on the custom google map 
                        updateTimeSeriesList(data);
                        insertMarkersForStations(data,mapUsed);
                        break;
                    case 'updateOldData':
                        var type = args.type;
                        var data = stationsDatabaseUsed[type];
                        // update the list of station and the markers on the google map
                        updateTimeSeriesList(data);
                        insertMarkersForStations(data,mapUsed);
                        break;
                    default:
                        break;
                }
            }
            
            
            function randomSelectVolcano(selectedId){
                var list = document.getElementById(selectedId);
                var length = list.options.length;
                var i = Math.floor(Math.random()*length);
                list.options[i].selected = 'selected';
                $("#"+selectedId).change();
            }
            function updateStationsWithDataList(args, tableId){
                stationTypeList.length = 0;
                stationTypeList = args.list;
                var stationsTable = $("#"+tableId);
                var html="";
                if(stationTypeList.length == 0){
                    html="<tr><td></td><td>No data</td></tr>";
                    stationsTable.html(html);
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
                                }, stationsDatabase,1);
                            }else{
                                var volcano = $("#VolcanoList").val();
                                volcano = volcano.split("&");
                                var cavw = volcano[1];
                                Wovodat.getStations({
                                    cavw: cavw,
                                    type: this.value,
                                    handler: updateTimeSeriesandStations,
                                    stationsDatabaseUsed: stationsDatabase
                                });
                            }
                        }else{
                            updateTimeSeriesandStations({action:'delete',type:this.value}, stationsDatabase,1);
                        }
                    }
                }
            }
            function updateAllStationsList(args,tableId,mapId,stationsDatabaseUsed,mapUsed){
                
                stationTypeList.length = 0;
                stationTypeList = args.list.split(";");
                var stationsTable = $("#"+tableId);
                if (stationTypeList[stationTypeList.length-1] =="")
                    stationTypeList.length--;
                //count number of stations of each type
                var typeList={};
                var dataList={};
                for (var i in stationTypeList){
                    stationTypeList[i] = Wovodat.trim(stationTypeList[i]);
                    if(stationTypeList[i] == "") continue;
                    nextEntry = stationTypeList[i].split("&");
                    stationType = nextEntry[0];
                    if (typeList[stationType]){
                        typeList[stationType]++;
                        dataList[stationType]+=stationTypeList[i]+";";
                    }
                    else{
                        typeList[stationType]=1;
                        dataList[stationType] = stationTypeList[i]+";";
                    }
                }
                var html="";
                got_station = false;
                for (var i in typeList){
                    got_station = true;
                    var checkBoxId = tableId + i +"Checkbox";
                    html += "<tr><td><input type='checkbox' value='" + i + "' id='" + checkBoxId + "'/></td><td>" + formatReturnStations(i) +  ":" + typeList[i]+ "</td></tr>";
                }
                stationsTable.html(html);
                if(stationTypeList.length == 0 || !got_station){
                    html="<tr><td></td><td>No data</td></tr>";
                    stationsTable.html(html);
                }
                var id;
                if(tableId.indexOf('Comp') != -1) id = 2;
                else id = 1;
                for(var i in typeList){
                    document.getElementById(tableId + i +"Checkbox").onclick = function(){
                        if(this.checked){
                            if(stationsDatabaseUsed[this.value]){
                                updateTimeSeriesandStations({
                                    type:   this.value,
                                    action: 'updateOldData'
                                }, stationsDatabaseUsed, mapUsed);
                            }else{
                                updateTimeSeriesandStations({data:dataList[this.value],type:this.value,action:'updateNewData'}, stationsDatabaseUsed,mapUsed);
                            }
                            var j = '';
                            if(tableId.indexOf('Comp') == -1) j = 1;
                            else j = 2;
                            map[j].panTo(new google.maps.LatLng(volcanoInfo[j].lat,volcanoInfo[j].lon));
                            if(map[j].getZoom() <= 10)
                                map[j].setZoom(10);
                            $('#HideStationButton' + id).show();
                        }else{
                            updateTimeSeriesandStations({action:'delete',type:this.value},stationsDatabaseUsed,mapUsed);
                            var j = checkStationChecked(id);
                            if(!j) $('#HideStationButton' + id).hide();
                        }
                    }
                }
            }
            function checkStationChecked(tableId){
                var stationListName;
                // get the 'id of table contains the list of station type'
                if(tableId == 1){
                    stationListName = '';
                }else{
                    stationListName = 'Comp';
                }
                stationListName += 'StationList';
                var stationList = document.getElementById(stationListName).getElementsByTagName('tbody')[0].childNodes;
                for(var i= 0; i <  stationList.length ;i++){
                    var input = stationList[i].getElementsByTagName('input')[0];
                    if(input.checked) return true;
                }
                return false;
            }
            function hideStation(tableId){
                var stationListName ;
                // get the 'id of table contains the list of station type'
                if(tableId == 1){
                    stationListName = '';
                }else{
                    stationListName = 'Comp';
                }
                stationListName += 'StationList';
                var stationList = document.getElementById(stationListName).getElementsByTagName('tbody')[0].childNodes;
                for(var i= 0; i <  stationList.length ;i++){
                    var input = stationList[i].getElementsByTagName('input')[0];
                    if(input.checked) input.click();
                }
                //map[tableId].setZoom(7);
            }
            function formatReturnStations(name){
                name = name + " stations";
                name = name.substr(0,1).toUpperCase() + name.substr(1);
                return name;
            }
            function drawLatLonOnMaps(pos,mapId,value){
                separate = pos.split(",");
                if (separate[separate.length-1]=="") separate.length--;
                for (var i in separate){
                    info = separate[i].split("X");
                    lat = info[0];
                    lon = info[1];
                    
                    // lat and long of singapore
                    if(!lat || !lon){
                        lat = 1.29;
                        lon = 103.85;
                    }
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(lat, lon), 
                        map: map,
                        animation: google.maps.Animation.DROP
                    });  
                    value = value.substr(0,1);
                    value = value.toLowerCase();
                    if(value == 't' || value == 'f'){
                        marker.setIcon('');
                    }else{
                        marker.setIcon('/img/pin_' + value + 's_s.png');
                    }
                    var contentString = "Volcano: " ;
                    infowindow = new google.maps.InfoWindow({
                        content: contentString
                    });
                    google.maps.event.addListener(marker, 'click', function() {
                        for(var i in infoWindows)
                            infoWindows[i].close();
                        infowindow.open(map,marker);
                    });
                }
            }
            function drawMap(args, volcId, mapId, mapUsed){
                //remove volcano marker
                if (volMarkers[mapUsed])
                    volMarkers[mapUsed].setMap(null);
                volMarkers[mapUsed] = null;	
                var volcano = $("#"+volcId).val();
                volcano = volcano.split("&");
                if(args == undefined){
                    args = 0;
                }
                var lat = args.lat;
                var lon = args.lon;
                var elev = args.elev;
                var cavw = volcano[1];
                var volName=volcano[0];  //Nang added 
                var cavw = volcano[1];
                //Nang added volName				
                volcanoInfo[mapUsed] = {lat:args.lat,lon:args.lon,elev:args.elev,cavw:cavw,volName:volName}; 				
				
                //    volcanoInfo[mapUsed] = {lat:args.lat,lon:args.lon,elev:args.elev,cavw:cavw};
                // location of singapore
                if(!lat || !lon){
                    lat = 1.29;
                    lon = 103.85;
                }
                map[mapUsed].setCenter(new google.maps.LatLng(lat,lon));
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lon), 
                    map: map[mapUsed],
                    animation: google.maps.Animation.DROP
                });   
                volMarkers[mapUsed]= marker;
                var contentString = "Volcano: " + volcano[0] + "<br/>CAVW: " + volcano[1] +"<br/>Lat: " + parseFloat(lat).toFixed(3) + " N<br/>Lon: " + parseFloat(lon).toFixed(3)
                    + " E<br/>Elev: " + elev + "(meters)";
                infowindowVolcano[mapUsed] = new google.maps.InfoWindow({
                    content: contentString
                });
                google.maps.event.addListener(marker, 'click', function() {
                    for(var i in infoWindows)
                        infoWindows[i].close();
                    infowindowVolcano[mapUsed].open(map[mapUsed],marker);
                });
            }
            function insertEruptionList(obj,selectId){
                var data = [];
                var list = obj.list;
                list = list.split(";");
                var eruptions = document.getElementById(selectId);
                eruptions.options.length = 0;
                eruptions.options = [];
                var i = 0;
                var t;
                for(;i < list.length;i++){
                    list[i] = Wovodat.trim(list[i]);
                    if(list[i].length == 0) continue;
                    t = list[i].split("&");
                    if(t[2] != undefined){
                        var temp = t[1];
                        t[1] = t[1] + " " + t[2];
                        data.push({label:'<div style="text-align:center"><img src="/img/SmallEruptionIcon.png"/><br/><b >' + temp + '</b></div>',position:Wovodat.toDate(t[1]).getTime()});
                    }
                    eruptions.options[eruptions.options.length] = new Option(temp,t[1]);
                }
                if(selectId == 'CompEruptionList')
                    eruptionsData.compEruptions.markdata = data;
                else
                    eruptionsData.markdata = data;
            }
            //changed by Nam
            //insert parameter: selectId - id of the "select" option where the list of Volcano is inserted into
            function insertVolcanoList(obj, selectId){
                var ids = selectId;
                for(var j = 0 ; j < ids.length ;j++){
                    selectId = ids[j];
                    // a list of volcanos and their cavw separated by: ;
                    var list = obj.list;
                    list = list.split(";");
                    // get the volcano select list tag
                    var volcanos = document.getElementById(selectId);
                    // reset the volcano list
                    volcanos.options = [];
                    // assign new list
                    var i = 0;
                    volcanos.options[0] = new Option("Select...","");
                    for(;i < list.length;i++){
                        if (list[i].indexOf("Unnamed")==-1)
                            volcanos.options[volcanos.options.length] = new Option(list[i].replace('&','_'),list[i]);
                    }                   
                    //	randomSelectVolcano(selectId);
                    //  }
                    // }
                 
<?php
if ($dev) {
    ?>
                    if(ids[j] == 'VolcanoList'){
                        var a = document.getElementById('VolcanoList');
                        a.value = "Akutan&1101-32-";
                        $(a).change();
                        $("#DisplayEquake1").click();
                        $("#TimeSeriesHeader1").click();
                    }else{
                        var a = document.getElementById('CompVolcanoList');
                        a.value = "Miyake-jima&0804-04=";
                        $(a).change();
                        $("#DisplayEquake2").click();
                        $("#TimeSeriesHeader2").click();
                    }       
    <?php
} else {
    ?>     
                    randomSelectVolcano(selectId);
    <?php
}
?>
        }
    }
    /*
     * Volcano information module
     */                           
    function insertDataOwnerandStatus(o){
        var mapUsed = o.mapUsed;
        function getDataOwnerPanel(mapUsed){
            var holder = document.getElementById('VolcanoPanel' + mapUsed);
            var panel = $("#dataOwnerPanel",holder);
            panel = panel[0];
            return panel;
        }
        function createDataOwnerLink(url){
            url = url + "";
            if(url == undefined || url.length == 0)
                return;
            var a = document.createElement('a');
            a.className = 'dataOwner';
            a.target = '_blank';
            a.innerHTML = format(url);
            a.href = fixUrl(url);
            return a;
        }
        function fixUrl(url){
            if(url.substring(0,7) != "http://");
            url = "http://" + url;
            return url;
        }
        function format(url){
            var text = url;
            var i = text.indexOf('//');
            if(i == -1)
                i = 0;
            else 
                i = i + 2;
            var i1 = text.indexOf('www',i);
            if(i1 != -1) i = i + 3;
            var j = text.indexOf('/',i);
            if(j == -1) j = text.length;
            if(text[i] == '.')
                return text.substring(i+1,j);
            else return text.substring(i,j);
        }
        function insertDataOwners(o){
            var panel = getDataOwnerPanel(mapUsed);
            $(panel).empty();
            var a = createDataOwnerLink(o['owner1']);
            if(a == undefined)
                return;
            panel.appendChild(a);
            var a = createDataOwnerLink(o['owner2']);
            if(a == undefined)
                return;
            $(panel).html($(panel).html() + " - ");
            panel.appendChild(a);
        }
        function insertStatus(o){
            $("#volcstatus" + mapUsed).html(o['status']);
        }
        insertDataOwners(o);
        insertStatus(o);
    }
    
    /*
     * Equake module
     * Handle the event related to the equake panels
     * Provide the function to work with various equake data
     */
            
    /*
     * 
     * Object to store the list of earthquake values that user has requested
     * from the server. These values are organized according to the volcano 
     * that they are close to. That means some data will be duplicated 
     * at the client side this is the problem that the script in the future 
     * needs to address. This data will be used in the future when we 
     * need to filter the equake and redraw the data using Flot. This
     * object is also used to store the information about the GMT output
     * value.
     * First level of this object is the CAVW of the volcano
     * {cavw1,cavw2,cavw3,...}
     * To retrieve: earthquakes[cavw]
     * 
     * The earthquakes[cavw] object contains the following attributes:
     * - vlat: the latitude of the volcano
     * - vlon: the longitude of the volcano
     * - many objects that represent specific earthquakes that happened
     * 
     * Each earthquake event object has the followoing attributes:
     * - marker: show the position of the event in Google Map
     * - infoWindow: showed when mouse hovers over the positon of the marker
     * - eqtype: the type of the earthquake, please refer to the documentation of 
     * WOVOdat to see different type of earthquakes
     * - lat: latitude of the event
     * - lon: longitude of the event
     * - available: to mark if we should display this event on the graph and the map
     * - mag: magnitude of the event
     * - depth: the depth of the event
     * - latDistance: the distance from the event to the volcano projected in the latitude axis
     * - lonDistance: the distance from the event to the volcano projected in the longitude axis
     * - time: the happended time of the event in the standard format
     * - timestamp: the number of milliseconds starting from 1/1/1970 that 
     * this earthquake happens
     */ 
    var earthquakes = {};
    /*
     * Object to store the queried array of data for the 3D display
     */
    var gmt3DData = {};
    var gmt2DData = {};
    // store reference to the plotted graphs in the equake section
    // this variable will help us when we need to do the printing
    var equakeGraphs = [];
    // left graphs
    equakeGraphs[1] = {};
    // right graphs
    equakeGraphs[2] = {};
    $(document).ready(function(){
        /*
         * Drop down the display equake panel
         * Draw the Flot equake graph of current volcano if no display type is selected 
             
         */
        $("#DisplayEquake1").click(function(){
            $('#EquakePanel1').show();
            $("#twoDEquakeFlotGraph1").hide();
            $("#2DGMTEquakeGraph1").hide();
            showHideEquakeButton(1);
        });
        $("#DisplayEquake2").click(function(){
            $("#EquakePanel2").show();
            $("#twoDEquakeFlotGraph2").hide();
            $("#2DGMTEquakeGraph2").hide();
            showHideEquakeButton(2);
        }); 
        
        /*
         * Hide the entire earthquake panel when the x button is click
         */
        $("#HideEquake1").click(function(){
            hideEquakePanel({mapUsed:1});
            var a = document.getElementById('showHideMarkers1');
            if($(a).html() == "Hide earthquake"){
                $(a).click();
            }
            $('#showHideMarkers1').hide();
            uncheckAllEquakeButton(1);
            return false;
        });
        $("#HideEquake2").click(function(){
            hideEquakePanel({mapUsed:2});
            var a = document.getElementById('showHideMarkers2');
            if($(a).html() == "Hide earthquake"){
                $(a).click();
            }
            $('#showHideMarkers2').hide();
            uncheckAllEquakeButton(2);
            return false;
        });
                
        // hide the earth quake map during initialization
        hideEquakePanel({mapUsed:1});
        hideEquakePanel({mapUsed:2});
                
        /*
         * Javascript to handle button click of 2D, 2D using GMT and 3D using GMT
         */
        $(".equakeDisplayBox1").click(function() {
            $(".equakeDisplayBox1").closest("label").removeClass("equakeDisplayButtonChecked");
            $(this).closest("label").addClass("equakeDisplayButtonChecked");
        });
        $(".equakeDisplayBox2").click(function() {
            $(".equakeDisplayBox2").closest("label").removeClass("equakeDisplayButtonChecked");
            $(this).closest("label").addClass("equakeDisplayButtonChecked");
                    
        });
        $("#3DGMTEquakeGraph1, #3DGMTEquakeGraph2").hide();
            
        //handle the Filter buttons
        $("#FilterBtn1").click(function(){
            registerFilter({mapUsed:1});
        });
        $("#FilterBtn2").click(function(){
            registerFilter({mapUsed:2});
        });
        
        // this function is trigger when the filter button is clicked.
        function registerFilter(o){
            var mapUsed = o.mapUsed;
            if (volcanoInfo[mapUsed]){
                var cavw = volcanoInfo[mapUsed].cavw;
                // depend on the graph that is shown when filter button
                // is clicked, that graph will be redraw according to the the
                // parameter set by filter value
                if( document.getElementById('twoDEquakeFlotGraph' + mapUsed).style.display == 'block'){
                    // if the earthquakes list for for this volcano haven't been
                    // retrieve from the server, don't do anything
                    if(!earthquakes[cavw]){
                        return;
                    }
                    filterData(cavw,mapUsed);
                    drawEquake({mapUsed:mapUsed,source:document.getElementById('equakeDisplayType' + mapUsed + '2D')});
                }else if(document.getElementById('3DGMTEquakeGraph' + mapUsed).style.display == 'block'){
                    gmt3DData[cavw] = undefined;
                    drawEquake({mapUsed:mapUsed,
                        source:document.getElementById('equakeDisplayType' + mapUsed + '3D')
                    });
                }else if(document.getElementById('2DGMTEquakeGraph' + mapUsed).style.display == 'block'){
                    gmt2DData[cavw] = undefined;
                    drawEquake({mapUsed:mapUsed,
                        source:document.getElementById('equakeDisplayType' + mapUsed + '2DGMT')
                    });
                }else{
                    Wovodat.showNotification({message:'Please click to one of the buttons to retrieve the data.'});
                }
                //document.getElementById('FilterSwitch' + mapUsed).click();
                document.getElementById('DepthLow' + o.mapUsed).scrollIntoView(true);
            }
        }
        (function(list){
            var l = list.length;
            var i = 0;
            for(i = 0 ; i < l ; i++){
                $("#FilterSwitch" + list[i]).click([list[i]],function(e){
                    var j = e.data[0];
                    if ($("#FormFilter" + j).css("display")!="none"){
                        $("#FormFilter" + j).hide();
                        $("#FilterSwitch" + j).html("Show Filter");
                    }
                    else{
                        $("#FormFilter" + j).show();
                        $("#FilterSwitch" + j).html("Hide Filter");
                        if ($("#SDate" + j).val() =="" || $("#SDate" + j).val() =="undefined"){
                            $("#SDate" + j).val("01/01/1900");
                        }
                        if ($("#EDate" + j).val() =="" || $("#EDate" + j).val() =="undefined"){
                            var today = new Date();
                            $("#EDate" + j).val($.datepicker.formatDate("m/d/yy",today));
                        }
                    }
                });
            }
        })([1,2]);
        (function(list){
            var l = list.length;
            var i = 0;
            for(i = 0; i < l;  i++){
                $("#showHideMarkers" + list[i]).click([list[i]],function(e){
                    var j = e.data[0];
                    hideMarkers({mapUsed:j,button:this});
                });
            }
        })([1,2]);         
    });
    function hideEquakePanel(o){
        $("#EquakePanel" + o.mapUsed).hide();
    }
    function hideEarthquakeMarkerButton(mapUsed){
        // parameter checking
        if(mapUsed == undefined || (mapUsed != 1 && mapUsed != 2))
            return;
        var button = $("#showHideMarkers" + mapUsed);
        // if the button does not exist, return
        if(button == undefined)
            return;
        button.css('display','none');
        
    }
    // show the button to hide/show earthquake markers in the graph
    // the function only show the button when we have any marker
    function showHideEquakeButton(mapUsed){
        var cavw = getCavw(mapUsed);
        if(cavw == undefined)
            return;
        // parameter checking
        if(mapUsed == undefined || (mapUsed != 1 && mapUsed != 2))
            return;
        var button = $("#showHideMarkers" + mapUsed);
        // if the button does not exist, return
        if(button == undefined)
            return;
        // if the button is already shown, return
        if(button.css('display') == 'block')
            return;
        // go through all the earthquakes available for the current vocano
        for (var i in earthquakes[cavw])
            if (typeof earthquakes[cavw][i]['marker' + mapUsed] != "undefined"){
                // if there is earthquakes that are shown for on the map, show the 
                // button and return
                if(earthquakes[cavw][i]['marker' + mapUsed].getMap() != null){
                    button.css('display','block');
                    return;
                }
        }
            
        button.css('display','none');
    }
    /*
     * This function is used for the filtering of the equake events based on the
     * list of parameter in the filter.
     * All of the event that are filtered will be marked unavailable by
     * setting the available variable to false.
     */
    function filterData(cavw,panelUsed){
        // parse the data in the start-date and end-date box
        function parseDateVal(date){
            var result = Date.parse(date);
            // the function parse of the class Date 
            if (isNaN(result)){
                // remove all the whitespace character in the date string
                date = date.replace(/\s+/g,'');
                if(date.match(/^\d{2}\/\d{2}\/\d{4}$/)){
                    // date should be in the format MM/DD/YYYY now
                    result = new Date(date.substring(0,4),parseInt(date.substring(5,7))-1,date.substring(8,10),date.substring(11,13),date.substring(14,16),date.substring(17,19),0);
                    result = result.getTime();
                }else
                    result = "";
            }
            return result;
        }
        // data is not available for filtering
        if(!earthquakes[cavw]) 
            return;
        var nEvent = $("#Evn"+panelUsed).val();
        var sDate = parseDateVal($("#SDate"+panelUsed).val());
        if(sDate == undefined || sDate == "")
            sDate = 0;
        var eDate = parseDateVal($("#EDate"+panelUsed).val());
        if(eDate == undefined || eDate == "")
            eDate = new Date().getTime();
        var dhigh = parseFloat($("#DepthHigh"+panelUsed).val());
        var dlow = parseFloat($("#DepthLow"+panelUsed).val());
        var type = document.getElementById("EqType"+panelUsed);
        var wkm = parseFloat(document.getElementById("wkm"+panelUsed).value) / 2.0 ;		
        
        type = type.options[type.selectedIndex].value;
        var count = 0;
        var vlat = earthquakes[cavw]['vlat'], vlon = earthquakes[cavw]['vlon'];
        // some error here, what if i is 'vlat' or 'vlon'
        for (var i in earthquakes[cavw]){
            
            if(i == 'vlat' || i == 'vlon')
                continue;
            if (count > nEvent){
                earthquakes[cavw][i]['available'] = false;
                continue;
            }
            if (earthquakes[cavw][i]['time'] != "" && typeof earthquakes[cavw][i]['time'] != "undefined"){
                var eType = earthquakes[cavw][i]['eqtype'];
                var eDepth = parseFloat(earthquakes[cavw][i]['depth']);
                var eTime = Wovodat.convertDate(earthquakes[cavw][i]['time']);
				
                var elat = earthquakes[cavw][i]['lat'], elon = earthquakes[cavw][i]['lon'];
                var distanceFromVolcano = Wovodat.calculateD(vlat,vlon,elat,elon,2);
                
                if(distanceFromVolcano > wkm + 0.1){
                    earthquakes[cavw][i]['available'] = false;	
                    continue;
                }
                eTime = eTime.getTime();
                earthquakes[cavw][i]['available'] = false;
                if(eType != type && type != "")
                    continue;
                if(eDepth < dlow)
                    continue;
                if(eDepth > dhigh)
                    continue;
                // event happened after the end date
                if(eTime > eDate + Wovodat.ONE_DAY)
                    continue;
                // event happned before the start date
                if (eTime < sDate - Wovodat.ONE_DAY)
                    continue;
                count++;
                earthquakes[cavw][i]['available'] = true;
            }
        }
    }
    /*
     * Insert markers for earthquakes
     * This function will also show the marker of earthquake event in the
     * Google map of either mapUsed
     * Author: Tran Thien Nam
     * 2012-07-19
     */
    function insertMarkersForEarthquakes(data,cavw, mapUsed){
        // the function will initialize the earthquake variable when 
        // there is no equake data stored at the client side for a
        // specific volcano. 
        if (!earthquakes[cavw]){
            earthquakes[cavw]={};
            // the latitude and longitude of the volcano that this earthquake surround
            earthquakes[cavw]['vlat']=volcanoInfo[mapUsed]['lat'];
            earthquakes[cavw]['vlon']=volcanoInfo[mapUsed]['lon'];
            
            var equakeSet = {},vlat = volcanoInfo[mapUsed]['lat'],
            vlon = volcanoInfo[mapUsed]['lon'];
            equakeSet = data.split(";");
            // eliminate the empty elements at the end of the ajax data
            while (equakeSet[equakeSet.length-1] == "")
                equakeSet.length--;
            for (var i in equakeSet){
                var index = Wovodat.trim(equakeSet[i]);
                var nextQuake = index.split(",");
                var lat = nextQuake[0];
                var lon = nextQuake[1];
                var depth = nextQuake[2];
                var mag = nextQuake[3];
                var time = nextQuake[4];
                var type = nextQuake[5];
						
                // ignore earthquakes that have no information on depth and/or magnitude
                if (depth == "" || typeof depth=="undefined" || mag=="" || typeof mag=="undefined")
                    continue;
                
                // choose icon size base on magnitude of the equake event
                size = Math.round(4+((mag)*2)/4);
                        
                if (size<4) 
                    size = 4;
                if (size>14) 
                    size = 14;
                        
                // choose icon image base on depth of the equake event
                if (depth <= 2.5) 
                    color = '../img/blankCircles/pin_re.png'; // Red
                else if (depth >2.5 && depth <= 5) 
                    color = '../img/blankCircles/pin_org.png'; // Orange
                else if (depth >5 && depth <= 10) 
                    color = '../img/blankCircles/pin_ye.png'; // Yellow
                else if (depth >10 && depth <= 15) 
                    color ='../img/blankCircles/pin_ge.png'; // Dark Green
                else if (depth >15 && depth <= 25) 
                    color ='../img/blankCircles/pin_lbe.png'; // Light BLUE
                else if (depth >25 && depth <= 40) 
                    color ='../img/blankCircles/pin_be.png'; // BLUE
                else 
                    color = '../img/blankCircles/pin_dbe.png'; // Dark Blue
                if(i < parseInt(document.getElementById('Evn' + mapUsed).value)){
                    				
                    // set icon
                    var icon = new google.maps.MarkerImage(color,null,null,null,new google.maps.Size(size,size));

                    // set marker
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(lat,lon),
                        icon:icon
                    });

                    var marker2 = new google.maps.Marker({
                        position: new google.maps.LatLng(lat,lon),
                        icon:icon
                    });
                    
                    // the content for the popup window when the mouse hovers
                    // over this equake event
                    var contentText = "<table><tr><td><b>Lat</b> </td><td>" + 
                        lat + "</td></tr><tr><td><b>Lon</b></td><td>" + 
                        lon + "</td></tr><tr><td><b>Time</b></td><td>" + 
                        time + "</td></tr><tr><td><b>Depth</b></td><td>" + 
                        depth + "</td></tr><tr><td><b>Magnitude</b></td><td>" 
                        + mag+"</td></tr></table>";
                    
                    var infoWindow = new google.maps.InfoWindow({content:contentText});
                    
                    new Tooltip({marker:marker,content:contentText,cssClass:"earthquakeTooltip"});
                }	
                // infoWindow.open(map[mapUsed],marker);
                // store the quake data in the earthquakes[cavw] object
                
                earthquakes[cavw][index]=[];
                earthquakes[cavw][index]['marker' + mapUsed]= marker;
                earthquakes[cavw][index]['marker' + (3 - mapUsed)]= marker2;
                earthquakes[cavw][index]['infoWindow']= infoWindow;
                earthquakes[cavw][index]['eqtype'] = type;
                earthquakes[cavw][index]['lat']=lat;
                earthquakes[cavw][index]['lon']=lon;
                earthquakes[cavw][index]['time']=time;
                earthquakes[cavw][index]['available'] = true;
                earthquakes[cavw][index]['mag']=mag;
                earthquakes[cavw][index]['depth']=depth;
                earthquakes[cavw][index]['latDistance'] = Wovodat.calculateD(lat,lon,vlat,vlon,0);
                earthquakes[cavw][index]['lonDistance'] = Wovodat.calculateD(lat,lon,vlat,vlon,1);
                earthquakes[cavw][index]['timestamp'] = Wovodat.convertDate(time);
            }
            filterData(cavw,mapUsed);
            insertMarkersForEarthquakes(null,cavw,mapUsed);
        }
        else{
            // if we already had the cached data, just display it in the specific 
            // map
            console.log('considering showing marker');
            for (var i in earthquakes[cavw]){
                if (typeof earthquakes[cavw][i]['marker' + mapUsed] != "undefined"){
                    if (earthquakes[cavw][i]['available']){
                        console.log('show marker');
                        earthquakes[cavw][i]['marker' + mapUsed].setMap(map[mapUsed]);
                    }
                    else
                        earthquakes[cavw][i]['marker' + mapUsed].setMap(null);
                }	
            }
        }
        var a = document.getElementById('showHideMarkers' + mapUsed);
        $(a).unbind('click');
        $(a).html("Hide earthquake");
        $(a).click(function(){
            hideMarkers({mapUsed:mapUsed,button:a}); 
        });
    }
    /*
     * Plot the equake data
     * The volcano to draw the earthquake data is determined using the cavw and
     * mapUsed parameter
     */
    function plotEarthquakeData(cavw, eqtype, mapUsed){
        
        var numberOfEarthquakes = parseInt(document.getElementById('Evn' + mapUsed).value);
        // skip this function if we can not find the data to draw
        if(!earthquakes[cavw]) 
            return;
        
        // skip this function if we can not find the side of the map
        if(!mapUsed)
            return;
        
        
        function initializeFilter(data,mapUsed){
            var i, item, startTime, endTime, timestamp;
            for(i in data){
                item = data[i];
                timestamp = item['timestamp'];
                if(startTime == undefined) startTime = timestamp;
                else{
                    if(startTime > timestamp) startTime = timestamp;
                }
                if(endTime == undefined) endTime = timestamp;
                else{
                    if(endTime < timestamp) endTime = timestamp;
                }
            }
            if(startTime == undefined || endTime == undefined)
                return;
            // no need to reset
            if($("#SDate" + mapUsed ).datepicker( "option", "yearRange" ) == (startTime.getUTCFullYear() + ":" + endTime.getUTCFullYear()))
                return;
            var maxValue = Math.floor(endTime.getTime()-startTime.getTime())/86400000;
            
            
            var startTimeString = startTime.getUTCMonth() + 1 + "/" + startTime.getUTCDate() + "/" + startTime.getUTCFullYear();
            $("#SDate" + mapUsed).val(startTimeString);
            var endTimeString = endTime.getUTCMonth() + 1 + "/" + endTime.getUTCDate() + "/" + endTime.getUTCFullYear();
            $("#EDate" + mapUsed).val(endTimeString);
            
            $("#SDate" + mapUsed).datepicker("option", "yearRange", startTime.getUTCFullYear() + ":" + endTime.getUTCFullYear());
            $("#EDate" + mapUsed).datepicker("option", "yearRange", startTime.getUTCFullYear() + ":" + endTime.getUTCFullYear());
            
            $("#DateRange" + mapUsed).slider({
                range: true,
                max: maxValue,
                values : [0, maxValue],
                slide: function(event,ui){
                    var date = new Date(startTime.getTime());
                    date.setDate(date.getDate() + ui.values[0]);
                    $("#SDate" + mapUsed).val($.datepicker.formatDate('mm/dd/yy',date));
                    date = new Date(startTime.getTime());
                    date.setDate(date.getDate() + ui.values[1]);
                    $("#EDate" + mapUsed).val($.datepicker.formatDate('mm/dd/yy',date));
                }
            });
        }
        
        initializeFilter(earthquakes[cavw],mapUsed);
        filterData(cavw,mapUsed);
        // This is the height and width for the 
        // flot graph. Flot is for 2D javascript drawing
        var CONSTANTS = {
            fontSize: '9px',
            labelHeight: '70px',
            labelWidth: '45px',
            labelFontSize: '14px',
            labelPaddingTop: '60px',
            marginTop: '15px'
        };
                
        // Options for drawing lat-lon plot. Please refer to the documentation
        // of Flot to see the meaning of the each value
        var plotOptions = {
            series:{
                points:{
                    show: true,
                    lineWidth: 1,
                    symbol: drawMagnitude,
                    fill: false
                }
            },
            grid:{
                // this option is for changing the color of the border
                borderColor: "#9C9C9C",
                clickable:true,
                hoverable:true,
                autoHighlight:true
            },
            yaxis:{
                tickFormatter : kmFormatter,
                tickDecimals: 0,
                min: -20,
                labelWidth: 25
            },
            xaxis:{
                position:"top",
                tickDecimals:0,
                max: 10,
                min: -10,
                tickFormatter : kmFormatter
            },
            zoom:{ interactive: true},
            pan: {interactive: true},
            shadowSize: 0
        };
        function drawMagnitude(ctx, x, y, radius, shadow, realRadius, color){
            ctx.strokeStyle = color;
            ctx.arc(x,y,realRadius,0,shadow ? Math.PI : Math.PI * 2);
        }
        // Options for drawing time view. 
        var timeOptions = {
            series:{
                points:{
                    show:true,
                    lineWidth: 0,
                    symbol: drawMagnitude,
                    fill: false
                }
            },
            colors:["#3a4cb2"],
            grid:{
                // this option is for changing the color of the border
                borderColor: "#9C9C9C",
                clickable:true,
                hoverable:true,
                autoHighlight:true
            },
            yaxis:{
                tickFormatter: kmFormatter,
                tickDecimals:0,
                min: -40,
                labelWidth:25
            },
            xaxis:{position:"top", mode:"time",timeformat:"%d/%m/%y",ticks:4},
            zoom:{ interactive: true},
            pan: {interactive: true},
            shadowSize: 0
        }
        // Arrays that store data for the 3 graphs that we are about to draw.
        var latArray = new Array(), lonArray = new Array(), timeArray = new Array();
        // The latitude and longitude of the volcano
        var time, latPlot, lonPlot, timePlot;
                
        // get the data for each earthquakes, put them into arrays for 
        // flot library to draw
        var counter = 0;
        var sizeOfEquakeDot, color, depth;
        for (var i in earthquakes[cavw]){
            if(counter > numberOfEarthquakes) break;
            // skip this value when there is no latitude or longitude value 
            // for them
            if(typeof lat == 'undefined' || typeof lon == 'undefined')
                continue;
            
            // skip this event when it is not supposed to be displayed
            if(earthquakes[cavw][i]['available'] == 'undefined' || earthquakes[cavw][i]['available'] == false)
                continue;
            
            // skip this event when it does not have the earthquake type required
            if(earthquakes[cavw][i]['eqtype'] != eqtype && eqtype != "")
                continue;
            
            // the timestampe of the event
            time = earthquakes[cavw][i]['timestamp'];
            if(time == undefined || isNaN(time)) 
                continue;
            
            // count the number of even to display
            counter++;
            sizeOfEquakeDot = earthquakes[cavw][i]['mag'];
            if(sizeOfEquakeDot < 2) sizeOfEquakeDot = 2;
            if(sizeOfEquakeDot > 6) sizeOfEquakeDot = 6;
            sizeOfEquakeDot *= 1.2;
            
            
            depth = earthquakes[cavw][i]['depth'];
            if (depth <= 0) depth = 0;
            if (depth > 40) depth = 40;
            
            function generateColorCode(depth){
                if(depth > 20) depth = 20;
                var r,g,b;
                if(depth <= 10){
                    r = 255;
                    b = 0;
                }else{
                    r = 0;
                    depth = 20 - depth;
                    b = 255;
                }
                g = depth / 10.0 * 255;
                g = parseInt(g);
                return 'rgb(' + r + ',' + g + ',' + b + ')';
            }
            
            color = generateColorCode(depth);
            // set lat, lon coordination
            latArray.push([earthquakes[cavw][i]['latDistance'],-earthquakes[cavw][i]['depth'],,,,,sizeOfEquakeDot,color]);
            lonArray.push([earthquakes[cavw][i]['lonDistance'],-earthquakes[cavw][i]['depth'],,,,,sizeOfEquakeDot,color]);
            
            // set time coordination
            //if time is not convertible by javascript native functions
            //then use own-created function
            if(isNaN(time)){
                time = earthquakes[cavw][i]['timestamp'];
                time = new Date(time.substring(0,4),parseInt(time.substring(5,7))-1,time.substring(8,10),time.substring(11,13),time.substring(14,16),time.substring(17,19),0);
                time = time.getTime();
            }
            
            timeArray.push([time,-earthquakes[cavw][i]['depth'],,,,,sizeOfEquakeDot,color]);
            
        }
        
        // prepare the data object for the plot functions
        latPlot = [{
                data:latArray
            }];
        lonPlot = [{
                data:lonArray
            }];
        timePlot = [{
                data:timeArray
            }];
        // draw the latitude map
        if(getSelectedEquakesButton(mapUsed) == 1)
            $('#twoDEquakeFlotGraph' + mapUsed).show();
        var latitudePlotArea = document.getElementById("FlotDisplayLat"+mapUsed);
        equakeGraphs[mapUsed].latGraph = $.plot(latitudePlotArea,latPlot,plotOptions);
        Wovodat.enableTooltip({type:'single',
            id:"FlotDisplayLat"+mapUsed,
            firstValueFront:'Distance from volcano',
            firstValueBack:'km',
            secondValueFront:'Depth',
            secondValueBack:'km'
        });
        // draw the longitude map
        var longitudePlotArea =$("#FlotDisplayLon"+mapUsed);
        equakeGraphs[mapUsed].lonGraph = $.plot(longitudePlotArea,lonPlot,plotOptions);
        Wovodat.enableTooltip({type:'single',
            id:"FlotDisplayLon"+mapUsed,
            firstValueFront:'Distance from volcano',
            firstValueBack:'km',
            secondValueFront:'Depth',
            secondValueBack:'km'
        });
        // draw the time series map        
        var timePlotArea =$("#FlotDisplayTime"+mapUsed);
        equakeGraphs[mapUsed].timeGraph = $.plot(timePlotArea,timePlot,timeOptions);
        Wovodat.enableTooltip({type:'single',id:"FlotDisplayTime"+mapUsed,
            firstValueFront:'Time',
            firstValueBack:'UTC',
            secondValueFront:'Depth',
            secondValueBack:'km',
            xValueType: 'time'
        });
                
        // adjust the flot label for all the graph ('E-W','N-S','Time')
        $('.plot-label').css({
            'float': 'right',
            'display': 'block-inline',
            'height': CONSTANTS.labelHeight,
            'width': CONSTANTS.labelWidth,
            'font-size': CONSTANTS.labelFontSize,
            'vertical-align': 'middle',
            'padding-top': CONSTANTS.labelPaddingTop
        });
        showHideEquakeButton(mapUsed);
    }  
    /*
     * hide all the markers when user closes the Earthquakes panel section
     * This function is accomplished by setting the map of each pointer
     * to null value
     */
    function hideMarkers(o){
        var mapUsed = o.mapUsed;
        if(volcanoInfo[mapUsed] == undefined) return;
        var cavw = volcanoInfo[mapUsed].cavw;
        var currentEquakeSet = [];
        for (var i in earthquakes[cavw])
            if (typeof earthquakes[cavw][i]['marker' + mapUsed] != "undefined"){
                if(earthquakes[cavw][i]['marker' + mapUsed].getMap() != null){
                    currentEquakeSet.push(earthquakes[cavw][i]['marker' + mapUsed]);
                    earthquakes[cavw][i]['marker' + mapUsed].setMap(null);
                }
        }
        var button = o.button;
        $(button).unbind('click');
        $(button).html("Show earthquake");
        $(button).click(function(){
            _showMarkers(this);
        });
        function _showMarkers(o){
            $(o).html('Hide earthquake');
            var l = currentEquakeSet.length;
            for(var i = 0 ; i < l ; i++){
                currentEquakeSet[i].setMap(map[mapUsed]);
            }
            $(o).unbind('click');
            $(o).click(function(){
                _hideMarkers(o);
            });
        }
        function _hideMarkers(o){
            $(o).html('Show earthquake');
            var l = currentEquakeSet.length;
            for(var i = 0 ; i < l ; i++){
                currentEquakeSet[i].setMap(null);
            }
            $(o).unbind('click');
            $(o).click(function(){
                _showMarkers(o);
            });
        }
    }
    /*
     * Function to format the X-axis for Latitude and Longtitude axises
     * This function will set axis.tickDecimals number after the '.' and
     * append the ' km' part to each tick label.
     */
    function kmFormatter(v, axis){
        return v.toFixed(axis.tickDecimals) + " km";
    }
    
    
    /*
     * Draw the equake graphs under the equake panels
     */
    function drawEquake(o){
        var source = o.source;
        var id=source.id;
        $("#twoDEquakeFlotGraph" + o.mapUsed).hide();
        $("#2DGMTEquakeGraph" + o.mapUsed).hide();
        $("#3DGMTEquakeGraph" + o.mapUsed).hide();
        if(id.indexOf('3D') >0)
            drawEquake3DGMT(o);
        else if(id.indexOf('2DGMT') >0)
            drawEquake2DGMT(o);
        else drawEquake2D(o);
        map[o.mapUsed].setZoom(10);
    }
    /*
     * Help function to draw equake in 2 dimensions using Flot
     */
    function drawEquake2D(o){
        var cavw = volcanoInfo[o.mapUsed].cavw;
        if (!earthquakes[cavw]){
            Wovodat.loadEarthquakes({
                numberOfEvents: 500,
                mapUsed: o.mapUsed,
                volInfo: volcanoInfo[o.mapUsed],
                handlers: [insertMarkersForEarthquakes,plotEarthquakeData]
            });
        }
        else{
            insertMarkersForEarthquakes("",cavw,o.mapUsed);
            plotEarthquakeData(cavw,"",o.mapUsed);
        }
    }
    function uncheckAllEquakeButton(mapUsed){
        function removeClassName(element){
            if(element == null)
                return;
            var parent = element.parentNode;
            if(parent == null)
                return;
            parent.className = parent.className.replace('equakeDisplayButtonChecked','');
        }
        var element;
        var ids = ["2D","2DGMT","3D"];
        var length = ids.length;
        var i = 0;
        for(i = 0 ; i < length; i++){
            ids[i] = "equakeDisplayType" + mapUsed + ids[i];
        }
        for(i = 0 ; i < length; i++){
            element = document.getElementById(ids[i]);
            if(element == null) continue;
            removeClassName(element);
        }
        
    }
    /*
     * Get the current selected button in the list of button for earth quakes
     * 1: 2D
     * 2: 2DGMT
     * 3: 3DGMT
     * 4: No button is selected
     */
    function getSelectedEquakesButton(mapUsed){
        var tempElement = document.getElementById('EquakePanel' + mapUsed);
        if(tempElement == undefined) return;
        tempElement = $(".equakeButtonsRow",tempElement);
        if(tempElement == undefined) return;
        tempElement = tempElement[0];
        if(tempElement == undefined) return;
        tempElement = $("label",tempElement);
        if(tempElement == undefined) return;
        var tempElements = tempElement;
        var i = 0, length = tempElements.length;
        var className;
        for(i = 0 ; i < length; i++){
            tempElement = tempElements[i];
            className = tempElement.className;
            if(className.match(/equakeDisplayButtonChecked/))
                break;
        }
        return i + 1;
    }
    /*
     * Draw the earthquakes around the volcano displayed in the
     * map in two dimensions. This function is using GMT to draw the map in case
     * the user don't have access to googel map
     */
    function drawEquake2DGMT(o){
        var mapUsed = o.mapUsed;
		
        var cavw = volcanoInfo[mapUsed].cavw;
        var volName = volcanoInfo[mapUsed].volName;                  //Nang added   
        var vlat = volcanoInfo[mapUsed].lat;                         //Nang added
        var vlon = volcanoInfo[mapUsed].lon;                         //Nang added				

        var id = o.source.id;
        var placeholder = document.getElementById('2DGMTEquakeGraph' + mapUsed);   
 
        if(gmt2DData[cavw] == undefined){
            Wovodat.get2DGMTMap({
                cavw: cavw,
                qty: document.getElementById('Evn' + mapUsed).value,
                date_start: document.getElementById('SDate' + mapUsed).value,
                date_end: document.getElementById('EDate' + mapUsed).value,
                dr_start: document.getElementById('DepthLow' + mapUsed).value,
                dr_end: document.getElementById('DepthHigh' + mapUsed).value,
                eqtype: document.getElementById('EqType' + mapUsed).value,
                wkm: document.getElementById('wkm' + mapUsed).value,   // Nang added
                vname:volName,                         // Nang added
                vlat:vlat,                             //Nang added
                vlon:vlon,                             //Nang added					
                //Only 3D GMT needs these two degree & init_azim.				
                //  degree: document.getElementById('degree' + mapUsed).value, 
                //  init_azim: document.getElementById('azim' + mapUsed).value,			

                handler: function(ar){
                    gmt2DData[cavw] = ar; 
                    show2DGMT(ar);
                }
            });
                
        }else{
            show2DGMT(gmt2DData[cavw]);
        }
        function show2DGMT(ar){
            var directory = ar['directory'];
            $("#imageLink",placeholder).attr('href',directory + "/" + ar['imageSrc']);
            $("#image",placeholder).attr('src',directory + "/" + ar['imageSrc']);
            $("#gifImage",placeholder).attr('href',directory + "/" + ar['imageSrc']);
            $("#gmtScriptFile",placeholder).attr('href',ar['gmtScriptFile']);
            if(getSelectedEquakesButton(mapUsed) == 2)
                placeholder.style.display = 'block';
        }
        if (!earthquakes[cavw]){
            Wovodat.loadEarthquakes({
                numberOfEvents: document.getElementById('Evn' + mapUsed).value,
                mapUsed: o.mapUsed,
                volInfo: volcanoInfo[o.mapUsed],
                handlers: [insertMarkersForEarthquakes]
            });
        }
        else{
            insertMarkersForEarthquakes("",cavw,o.mapUsed);
        }
    }
            
    /*
     * Draw the earthquakes around the volcano displayed in the
     * map in three dimensions. This function is using GMT to draw the map in case
     * the user don't have access to googel map.
     * 
     */
    function drawEquake3DGMT(o){
        var mapUsed = o.mapUsed;
        var cavw = volcanoInfo[mapUsed].cavw;
		
        var volName = volcanoInfo[mapUsed].volName;         //Nang added 
        var vlat = volcanoInfo[mapUsed].lat;                //Nang added
        var vlon = volcanoInfo[mapUsed].lon;                //Nang added				
		
        if(gmt3DData[cavw] == undefined){
            Wovodat.get3DMap({
                cavw: cavw,
                qty: document.getElementById('Evn' + mapUsed).value,                    
                date_start: document.getElementById('SDate' + mapUsed).value,
                date_end: document.getElementById('EDate' + mapUsed).value,
                dr_start: document.getElementById('DepthLow' + mapUsed).value,
                dr_end: document.getElementById('DepthHigh' + mapUsed).value,
                eqtype: document.getElementById('EqType' + mapUsed).value,
                wkm: document.getElementById('wkm' + mapUsed).value,   // Nang added	
                vname:volName,                // Nang added
                vlat:vlat,                    //Nang added
                vlon:vlon,                    //Nang added									
                degree: document.getElementById('degree' + mapUsed).value,
                init_azim: document.getElementById('azim' + mapUsed).value,
                handler: function(ar){
                    gmt3DData[cavw] = ar; 
                    show3DGMT(ar);
                }
            });
        }else{
            show3DGMT(gmt3DData[cavw]);
        }
        /*
         * Private function to help putting the 3D images and information
         * on the equake panel
         * This function will set the image , add the function for the 
         */
        function show3DGMT(ar){
            function padding(value){
                value = value + "";
                var l = value.length;
                l = 6 - l;
                while(l > 0){
                    value = "0" + value;
                    l--;
                }
                return "/frame_" + value + ".jpg";
            }
            var placeholder = $('#3DGMTEquakeGraph' + mapUsed);
            var numberOfImages = ar['numberOfImages'];
            var imageLink = ar['directory'] + '/frame_000000.jpg';
            var currentLink = 0;
            $("#3DImage #title",placeholder).html(ar['title']);
            $("#3DImage #image",placeholder).attr('src',imageLink);
            $("#3DImage #imageLink",placeholder).attr('href',imageLink);
                    
            // clear previous registered handlers
            $("#showAnimation",placeholder).unbind('click');
            $("#previousButton",placeholder).unbind('click');
            $("#nextButton",placeholder).unbind('click');
                    
            // add handlers for navigation button
            $("#showAnimation",placeholder).click(function(){
                $("#3DImage #image",placeholder).attr('src',ar['animationImage']);
                $("#3DImage #imageLink",placeholder).attr('href',ar['animationImage']);
            });
            $("#previousButton",placeholder).click(function(){
                currentLink = (currentLink - 1 + numberOfImages) % numberOfImages;
                $("#3DImage #image",placeholder).attr('src',ar['directory'] + padding(currentLink));
                $("#3DImage #imageLink",placeholder).attr('href',ar['directory'] + padding(currentLink));
            });
            $("#nextButton",placeholder).click(function(){
                currentLink = (currentLink + 1 + numberOfImages) % numberOfImages;
                $("#3DImage #image",placeholder).attr('src',ar['directory'] + padding(currentLink));
                $("#3DImage #imageLink",placeholder).attr('href',ar['directory'] + padding(currentLink));
            });
                    
                    
            $("#gifImage",placeholder).attr('href',ar['animationImage']);
            $("#gmtScriptFile",placeholder).attr('href',ar['gmtScriptFile']);
            if(getSelectedEquakesButton(mapUsed) == 3)
                placeholder.show();
        }
        if (!earthquakes[cavw]){
            Wovodat.loadEarthquakes({
                numberOfEvents: document.getElementById('Evn' + mapUsed).value,
                mapUsed: o.mapUsed,
                volInfo: volcanoInfo[o.mapUsed],
                handlers: [insertMarkersForEarthquakes]
            });
        }
        else{
            insertMarkersForEarthquakes("",cavw,o.mapUsed);
        }
    }
    /*
     */
    function clearEquakedrawingData(o){
        var mapUsed = o.mapUsed;
        var placeholder = $("#equakeGraphs" + mapUsed);
        var tmp = $("#twoDEquakeFlotGraph" + mapUsed,placeholder)
        tmp.hide();
        $("#FlotDisplayLat" + mapUsed).html('');
        $("#FlotDisplayLon" + mapUsed).html('');
        $("#FlotDisplayTime" + mapUsed).html('');
        tmp = $("#2DGMTEquakeGraph" + mapUsed,placeholder);
        tmp.hide()
        $("#imageLink",tmp).attr('href','');
        $("#image",tmp).attr('src','');
        $("#gifImage",tmp).attr('href','');
        $("#gmtScriptFile",tmp).attr('href','');
        tmp = $("#3DGMTEquakeGraph" + mapUsed,placeholder);
        tmp.hide()
        $("#imageLink",tmp).attr('href','');
        $("#image",tmp).attr('src','');
        $("#gifImage",tmp).attr('href','');
        $("#gmtScriptFile",tmp).attr('href','');
    } 
            
    /*
     * Time series module
     * 
     */
    $(function(){
        // show/hide the time series panel
        (function(list){
            var l = list.length;
            var i = 0;
            for(i = 0 ; i < l ; i++){
                var j = list[i];
                $("#TimeSeriesHeader" + j).click([j],function(e){
                    var mapUsed = e.data[0];
                    $("#TimeSeriesView" + mapUsed).show();
                    $(".TimeSeriesGraphPanel" + mapUsed).show();
                });
                $("#HideTimeSeriesPanel" + j).click([j],function(e){
                    var mapUsed = e.data[0];
                    $("#TimeSeriesView" + mapUsed).hide();
                    $(".TimeSeriesGraphPanel" + mapUsed).hide();
                    return false;
                });
            }
        })([1,2]);
    });
            
        </script>
        <style type="text/css">
            #contentrview_x #StationList tr td:first-child{
                width: 50px;
                text-align: right;
            }
            #contentrview_x #CompStationList td{
                height: 20px;
            }
            #contentrview_x #CompStationList tr td:first-child{
                width: 50px;
                text-align: right;
            }
            #Evn1, #SDate1, #EDate1, #DepthLow1, #DepthHigh1, #EqType1, #Evn2, #SDate2, #EDate2, #DepthLow2, #DepthHigh2, #EqType2{
                font-size:13px;
            }
            #GraphList{
                text-align: right;
            }
            .EquakePanel1, .EquakePanel2{
                margin: 10px;
            }
            #EquakePanel1 table,#EquakePanel2 table{
                width: 100%;
            }
            #OptionList1 , #OptionList2{
                width: 340px;
                display: block;
            }
            #OptionList1-1, #OptionList2-1{
                width: 330px;
                height: 110px;
                overflow:auto;
                margin-top: 5px;
                margin-left: 2px;
                margin-right: 2px;
                background-color:white;
                border: 1px solid #b0a9a9;
            }
            #TimeSeriesView1, #TimeSeriesView2{
                margin-top: 5px;
                display: none;
                margin-left: 10px;
            }
            .button {
                width:460px;
                display: inline-block;
                outline: none;
                text-align: left;
                text-decoration: none;
                padding: .2em 2em .2em;
                -webkit-border-radius: .4em; 
                -moz-border-radius: .4em;
                border-radius: .4em;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                box-shadow: 0 1px 2px rgba(0,0,0,.2);
                margin-top: 8px;
                height: 16px;
            }
            .button a:visited,.button a:link{
                color: rgb(58,89,184);
            }
            .button a:hover{
                text-decoration: underline;
            }
            /* white */
            .white {
                color: #606060;
                border: solid 1px #b7b7b7;
                background: #fff;
                /*background: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ededed));
                background: -moz-linear-gradient(top,  #fff,  #ededed);
                filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#ededed');*/
            }
            .HideTimeSeriesPanel1, .HideTimeSeriesPanel2{
                color:black;
            }
            .HideTimeSeriesPanel1:hover,.HideTimeSeriesPanel1:active,.HideTimeSeriesPanel2:hover,.HideTimeSeriesPanel2:active{
                cursor: pointer;
                color: red;
            }
            .TimeSeriesHeader{
                font-weight: bold;
            }
            .VolcanoComparisonHeader{
                font-weight: bold;
            }
            .map_legend img{
                width:16px;
                height:16px;
            }
            .CloseButton{
                float:right;
                background-image: url('/img/inactiveCloseButton.png');
                width:16px;
                height:16px;
                font-weight: bold;
            }
            .CloseButton:hover, .CloseButton:active{
                cursor:pointer;
                background-image: url('/img/activeCloseButton.png');
            }
            #VolcanoList, #CompVolcanoList{
                background: transparent;
                border: 1px solid #ccc;
                width:220px;
                font-size:11px;
            }
            #Map, #Map2{
                width:100%;
                height:210px;
                float:left;
                margin-top:10px;
            }
            .Gvp{
                height: 20px;
            }
            .Gvp:hover{
                cursor: pointer;
            }
            html, body{
                margin: 0;
                height: 100%;
            }
            #wrapborder_x {
                min-height: 100%;
                height: auto !important;
                height: 100%;
                margin: 0 auto -34px;
            }
            .footer, .reservedSpace {
                height: 38px;
                text-align:center;
            }
            #DateRange1, #DateRange2{
                width: 200px;
                padding-left: 100px;
            }
            .PrintButton{
                margin-top: 20px;
                padding: 2px;
                width: 65px;
                float: right;
                border-radius: 3px;
                border: 1px solid #cccccc;
                cursor: pointer;
                font-weight: bold;
                /* IE10 Consumer Preview */ 
                background-image: -ms-linear-gradient(bottom, #D6D0D0 0%, #FFFFFF 100%);

                /* Mozilla Firefox */ 
                background-image: -moz-linear-gradient(bottom, #D6D0D0 0%, #FFFFFF 100%);

                /* Opera */ 
                background-image: -o-linear-gradient(bottom, #D6D0D0 0%, #FFFFFF 100%);

                /* Webkit (Safari/Chrome 10) */ 
                background-image: -webkit-gradient(linear, left bottom, left top, color-stop(0, #D6D0D0), color-stop(1, #FFFFFF));

                /* Webkit (Chrome 11+) */ 
                background-image: -webkit-linear-gradient(bottom, #D6D0D0 0%, #FFFFFF 100%);

                /* W3C Markup, IE10 Release Preview */ 
                background-image: linear-gradient(to top, #D6D0D0 0%, #FFFFFF 100%);
            }
            .PrintButton:hover{
                /* IE10 Consumer Preview */ 
                background-image: -ms-linear-gradient(bottom, #BFBABA 0%, #E6E6E6 100%);

                /* Mozilla Firefox */ 
                background-image: -moz-linear-gradient(bottom, #BFBABA 0%, #E6E6E6 100%);

                /* Opera */ 
                background-image: -o-linear-gradient(bottom, #BFBABA 0%, #E6E6E6 100%);

                /* Webkit (Safari/Chrome 10) */ 
                background-image: -webkit-gradient(linear, left bottom, left top, color-stop(0, #BFBABA), color-stop(1, #E6E6E6));

                /* Webkit (Chrome 11+) */ 
                background-image: -webkit-linear-gradient(bottom, #BFBABA 0%, #E6E6E6 100%);

                /* W3C Markup, IE10 Release Preview */ 
                background-image: linear-gradient(to top, #BFBABA 0%, #E6E6E6 100%);
            }
            .PrintButton a:visited{
                text-decoration: none;
                color: black;
            }
            .PrintButtonFloat{
                float: right;
            }
            .print-icon{
                background-image: url('/img/print_icon.png');
                margin-right: 5px;
            }
            .app-icon{
                height: 16px;
                width: 30px;
                display:block;
                float: left;
                background-size: auto auto;
                position: relative;
                background-repeat: no-repeat;
            }
            .app-button-text{
                display: block;
                line-height: 16px;
                text-align: center;
            }
            .eruptionList{
                width: 100px;
            }
        </style>
    </head>
    <body>

        <div id="wrapborder_x">
            <div id="loading" class="loadingPanel">Loading ...</div>

            <div id="wrap_x">
                <?php include 'php/include/header_beta.php'; ?>

                <!-- Google map -->
                <table style="border-collapse: collapse;width: 1028px;">
                    <tr>
                        <td style="width:514px" valign="top">
                            <table>
                                <tr>
                                    <td>
                                        <div class="button white" style="margin-top:0px">
                                            <div class="CloseButton" id="HideMap1"></div>
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span class="MapsHeader" id="ShowMap1">
                                                            <a href="" onclick="return false;"><b>View Map</b></a>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                        <div id="map_legend1" class="map_legend" style="font-size:8px;display:inline">
                                            <div style="float:right">
                                                <button id="showHideMarkers1" class="showHideMarkerButton">
                                                    Hide earthquake
                                                </button>
                                            </div>
                                            <div>
                                                <img src="/img/pin_ds.png" alt=""/> Deformation
                                                <img src="/img/pin_gs.png" alt=""/> Gas
                                                <img src="/img/pin_hs.png" alt=""/> Hydrologic
                                                <img src="/img/pin_ss.png" alt=""/> Seismic
                                                <img src="/img/pin_ts.png" alt=""/> Thermal
                                                <img src="/img/pin_ms.png" alt=""/> Meteo
                                            </div>
                                        </div>
                                        <div style="" id="Map" style="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="button white">
                                            <div class="CloseButton" id="HideVolcanoInformation1"></div>
                                            <div style="float:right;padding-right: 10px;">
                                                <select id="VolcanoList" class="">
                                                    <option value="">Select...</option>
                                                </select>
                                            </div>   
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span id="VolcanoInformation1" class="VolcanoComparisonHeader">
                                                            <a href="" onclick="return false;">Volcano Info:</a>
                                                        </span>
                                                    </td>
                                                <tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <!-- The section under Volcano Info of the left volcano-->
                                <tr id="VolcanoPanel1">
                                    <td align="center" style="vertical-align:top">
                                        <table id="MainVolc" style="border-collapse: collapse;width:400px;">
                                            <tr>
                                                <td rowspan="2">
                                                    <button class="Gvp" id="gvp1">
                                                        Go to GVP
                                                    </button>
                                                </td>
                                                <td style="text-align:right" id="dataOwnerPanel">

                                                </td>
                                            </tr>
                                            <tr style="height:5px">
                                                <td style="text-align:right" ><span id="volcstatus1"></span></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;height:5px" colspan="2"><div style="height:5px;"></div></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top"style="text-align:left;height:5px;width:180px;"><b>Eruption:</b><br/>
                                                                <select id="EruptionList" class="eruptionList" >
                                                                </select>
                                                            </td>
                                                            <td colspan="2" style="height:20px;width:250px">
                                                                <div class="viewStationPanel">
                                                                    <button id="HideStationButton1" style="float:right;display:none">Hide Stations</button>
                                                                    <b>View stations:</b>
                                                                </div>
                                                                <table id="StationList" class="stationList"></table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td >
                                </tr>
                                <tr>
                                    <td>
                                        <div class="button white">
                                            <div class="CloseButton" id="HideEquake1"></div>
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span id="DisplayEquake1" class="VolcanoComparisonHeader">
                                                            <a href="" onclick="return false;">Earthquakes</a>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="EquakePanel1">
                                    <td style="vertical-align:top">
                                        <div class="FilterButton" id="FilterSwitch1"></div>
                                        <form id="FormFilter1" class="FormFilter" onSubmit="return false;" style="display:none">
                                            <div class="pointer"></div>
                                            <div class="row">
                                                <div class="leftPanel">No of events:</div>
                                                <div class="rightPanel">
                                                    <select id="Evn1">
                                                        <option value="100">100</option>
                                                        <option value="200">200</option>
                                                        <option value="300">300</option>
                                                        <option value="400">400</option>
                                                        <option value="500">500</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Period:</div>
                                                <div class="rightPanel">
                                                    <div class="subrow">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    Start: <input type="text" id="SDate1" class="dateInput" size=10/> 
                                                                </td>
                                                                <td>
                                                                    End: <input type="text" id="EDate1" class="dateInput" size=10/>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div>
                                                        <div id="DateRange1"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Depth (km):</div>
                                                <div class="rightPanel">
                                                    <div class="subrow">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    Start: <input type="text" id="DepthLow1" class="numberInput" value="0" size=4/>
                                                                </td>
                                                                <td>
                                                                    End: <input type="text" id="DepthHigh1" class="numberInput" value="40" size=4/>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div>
                                                        <div id="DepthRange1"></div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Type:</div>
                                                <div class="rightPanel">
                                                    <select id="EqType1">
                                                        <option value="">All earthquake type</option>
                                                        <option value="R">Regional</option>
                                                        <option value="Q">Quary Blast</option>
                                                        <option value="VT">Volcano Tectonic</option>
                                                        <option value="H">Hybrid</option>
                                                        <option value="LF">Low Frequency</option>
                                                        <option value="VLP">Very Long Period</option>
                                                        <option value="E">Explosion</option>
                                                        <option value="T">Tremor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Map Width (km):</div>
                                                <div class="rightPanel">
                                                    <select id="wkm1">
                                                        <option value="20">20</option>
                                                        <option value="30">30</option>
                                                        <option value="40">40</option>
                                                        <option value="50">50</option>
                                                        <option value="60">60</option>
                                                    </select>
                                                </div>
                                            </div>											
                                            <div class="threeDGMTFilter">
                                                For drawing in 3D-GMT only:
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Azimuth:</div>
                                                <div class="rightPanel">
                                                    <input type="text" id="azim1" class="numberInput" value="175" size="10"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Rotation Degree:</div>
                                                <div class="rightPanel">
                                                    <input type="text" id="degree1" class="numberInput" value="30" size="10"/>
                                                </div>
                                            </div>
                                            <div class="FilterBtnHolder">
                                                <button id="FilterBtn1" class="FilterBtn">Filter</button>
                                            </div>
                                        </form>
                                        <div class="equakeButtonsRow">
                                            <label for="equakeDisplayType12D" class="equakeDisplayBox equakeDisplayBox1">
                                                <input type="radio" name="equakeDisplayType1" id="equakeDisplayType12D" value="2D" onclick="drawEquake({mapUsed:1,source:this})"/>
                                                2D
                                            </label>
                                            <label for="equakeDisplayType12DGMT" class="equakeDisplayBox equakeDisplayBox1">
                                                <input type="radio" name="equakeDisplayType1" id="equakeDisplayType12DGMT" value="2D(GMT)" onclick="drawEquake({mapUsed:1,source:this})"/>2D(GMT)
                                            </label>
                                            <label for="equakeDisplayType13D" class="equakeDisplayBox equakeDisplayBox1">
                                                <input type="radio" name="equakeDisplayType1" id="equakeDisplayType13D" value="3D(GMT)" onclick="drawEquake({mapUsed:1,source:this})"/>3D(GMT)
                                            </label>
                                        </div>
                                        <!-- place holders for the Flot graphs and GMT images-->

                                        <div id="equakeGraphs1">
                                            <div  id="twoDEquakeFlotGraph1" class="twoDEquakeFlotGraph">
                                                <b class="pointer"></b>
                                                <div class="plot-label">
                                                    <b>E-W</b>
                                                </div>
                                                <div id="FlotDisplayLat1" class="equakeGraphPlaceholder">

                                                </div>
                                                <div class="plot-label">
                                                    <b>N-S</b>
                                                </div>
                                                <div id="FlotDisplayLon1" class="equakeGraphPlaceholder">
                                                </div>
                                                <div class="plot-label">
                                                    <b>Time</b>
                                                </div>
                                                <div id="FlotDisplayTime1" class="equakeGraphPlaceholder">
                                                </div>
                                                <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.TWOD_EQUAKE,element:document.getElementById('equakeGraphs1'),mapUsed:1,equakeGraph:equakeGraphs[1],info:document.getElementById('VolcanoList').value})" >
                                                    <a title="Print this graphs" href="#" >
                                                        <span class="app-icon light print-icon"></span>
                                                        <span class="app-button-text">Print</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div id="2DGMTEquakeGraph1" class="twoDGMTEquakeFlotGraph">
                                                <b class="pointer"></b>
                                                <div id="2DImage" class="TwoDImage">
                                                    <a href="" id="imageLink" target="_blank"><img height="707" width="495" src="" id="image"/></a>
                                                </div>
                                                <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.TWOD_GMT_EQUAKE,link:$('#image',document.getElementById('equakeGraphs1'))[0].src,info:document.getElementById('VolcanoList').value})">
                                                    <a title="Print this graphs" href="#" >
                                                        <span class="app-icon light print-icon"></span>
                                                        <span class="app-button-text">Print</span>
                                                    </a>
                                                </div>
                                                <div id="additionalInfomation">
                                                    Additional data:
                                                    <a id="gifImage" href="" target="_blank">Image file</a>, 
                                                    <a id="gmtScriptFile" href="" target="_blank">GMT script file</a><br/> 
                                                </div>
                                            </div>
                                            <div id="3DGMTEquakeGraph1" class="threeDGMTEquakeFlotGraph">
                                                <b class="pointer"></b>
                                                <div id="3DImage" class="ThreeDImage">
                                                    <div id="navigationBar" class="threeDNavigationBar">

                                                        <div id="previousButton"></div>
                                                        <div id="showAnimation"></div>
                                                        <div id="nextButton"></div>

                                                    </div>
                                                    <div id="title"></div>
                                                    <a href="" id="imageLink" target="_blank"><img height="500" width="495" src="" id="image"/></a>
                                                </div>

                                                <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.THREED_GMT_EQUAKE,link:$('#image',document.getElementById('3DGMTEquakeGraph1'))[0].src,info:document.getElementById('VolcanoList').value})">
                                                    <a title="Print this graphs" href="#" >
                                                        <span class="app-icon light print-icon"></span>
                                                        <span class="app-button-text">Print</span>
                                                    </a>
                                                </div>
                                                <div id="additionalInfomation">
                                                    Additional data:
                                                    <a id="gifImage" href=""  target="_blank">GIF image file</a>, 
                                                    <a id="gmtScriptFile" href="" target="_blank">GMT script file</a><br/> 
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="button white" >

                                            <div class="CloseButton" id="HideTimeSeriesPanel1"></div>
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span id="TimeSeriesHeader1" class="TimeSeriesHeader">
                                                            <a href="" onclick="return false;">Data Plots</a>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="vertical-align:top">
                                        <div id="TimeSeriesView1">
                                            <div id="OptionList1">
                                                <b>Available time series data (max. 3):</b> 
                                                <div id="OptionList1-1">
                                                    <table id="TimeSeriesList1" class="timeSeriesList">
                                                    </table>
                                                </div>
                                            </div>
                                            <br/>
                                            <div id="overviewPanel1" class="overviewPanel">
                                                <b>Overview (select a range to redraw the graph): </b>
                                                <div id="overview1" class="overviewGraph">

                                                </div>
                                                <br/>
                                            </div>
                                            <div style="clear:both;" id="PlotArea1">
                                                <table id="GraphList1">
                                                </table>
                                            </div>
                                            <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.TIME_SERIES,graphsTable: document.getElementById('GraphList1'),graphsPlot:graphs,info:document.getElementById('VolcanoList').value})">
                                                <a title="Print this graphs" href="#" >
                                                    <span class="app-icon light print-icon"></span>
                                                    <span class="app-button-text">Print</span>
                                                </a>
                                            </div>
                                        </div>

                                    </td>
                                    <td style="vertical-align:top">
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:514px" valign="top">
                            <table>
                                <tr>
                                    <td>
                                        <div class="button white" style="margin-top:0px">
                                            <div class="CloseButton" id="HideMap2"></div>
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span class="MapsHeader" id="ShowMap2">
                                                            <a href="" onclick="return false;"><b>View Map</b></a>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                        <div id="map_legend2" class="map_legend" style="font-size:8px;display:inline">
                                            <div style="float:right">
                                                <button id="showHideMarkers2" class="showHideMarkerButton">
                                                    Hide earthquake
                                                </button>
                                            </div>
                                            <div>
                                                <img src="/img/pin_ds.png" alt=""/> Deformation
                                                <img src="/img/pin_gs.png" alt=""/> Gas
                                                <img src="/img/pin_hs.png" alt=""/> Hydrologic
                                                <img src="/img/pin_ss.png" alt=""/> Seismic
                                                <img src="/img/pin_ts.png" alt=""/> Thermal
                                                <img src="/img/pin_ms.png" alt=""/> Meteo
                                            </div>
                                        </div>
                                        <div id="Map2">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="button white">
                                            <div class="CloseButton" id="HideVolcanoInformation2"></div>
                                            <div style="float:right;padding-right: 10px">
                                                <select id="CompVolcanoList">
                                                    <option value="">Select...</option>
                                                </select>
                                            </div>    
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span id="VolcanoInformation2" class="VolcanoComparisonHeader">
                                                            <a href="" onclick="return false;">Volcano Info:</a>
                                                    </td>
                                                <tr>
                                            </table>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <!-- HTML section of the region below Volcano Info tab of the second volcano -->
                                <tr id="VolcanoPanel2" >
                                    <td align="center"  style="vertical-align:top">
                                        <table id="CompVolc" style="border-collapse:collapse;width:400px">

                                            <tr>
                                                <td rowspan="2">
                                                    <button class ="Gvp" id="gvp2">
                                                        Go to GVP
                                                    </button>
                                                </td>
                                                <td style="text-align:right" id="dataOwnerPanel">

                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;height:5px"><span id="volcstatus2"></span></td>
                                            </tr>

                                            <tr>
                                                <td style="text-align:right" colspan="2"><div style="height:5px"></div></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top"style="text-align:left;height:5px;width:180px;"><b>Eruption:</b><br/>

                                                                <select id="CompEruptionList"  class="eruptionList">
                                                                </select>
                                                            </td>
                                                            <td colspan="2" style="height:20px;width:250px">
                                                                <div class="viewStationPanel">
                                                                    <button id="HideStationButton2" style="float:right;display:none">Hide Stations</button>
                                                                    <b>View stations:</b>
                                                                </div>
                                                                <table id="CompStationList" class="stationList"></table>
                                                            </td>

                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Earthquake Tab (this section is not including the body of the tab, just the tab only)-->

                                <tr>
                                    <td>
                                        <div class="button white">
                                            <div class="CloseButton" id="HideEquake2"></div>
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span id="DisplayEquake2" class="VolcanoComparisonHeader">
                                                            <a href="" onclick="return false;">Earthquakes</a>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>


                                <tr id="EquakePanel2">
                                    <td style="vertical-align:top">
                                        <div class="FilterButton" id="FilterSwitch2"></div>
                                        <form id="FormFilter2" class="FormFilter" onSubmit="return false;" style="display:none">
                                            <div class="pointer"></div>
                                            <div class="row">
                                                <div class="leftPanel">No of events:</div>
                                                <div class="rightPanel">
                                                    <select id="Evn2">
                                                        <option value="100">100</option>
                                                        <option value="200">200</option>
                                                        <option value="300">300</option>
                                                        <option value="400">400</option>
                                                        <option value="500">500</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Period:</div>
                                                <div class="rightPanel">
                                                    <div class="subrow">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    Start: <input type="text" id="SDate2" class="dateInput" size=10/> 
                                                                </td>
                                                                <td>
                                                                    End: <input type="text" id="EDate2" class="dateInput" size=10/>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div>
                                                        <div id="DateRange2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Depth (km):</div>
                                                <div class="rightPanel">
                                                    <div class="subrow">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    Start: <input type="text" id="DepthLow2" class="numberInput" value="0" size=4/>
                                                                </td>
                                                                <td>
                                                                    End: <input type="text" id="DepthHigh2" class="numberInput" value="40" size=4/>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div>
                                                        <div id="DepthRange2"></div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Type:</div>
                                                <div class="rightPanel">
                                                    <select id="EqType2">
                                                        <option value="">All earthquake type</option>
                                                        <option value="R">Regional</option>
                                                        <option value="Q">Quary Blast</option>
                                                        <option value="VT">Volcano Tectonic</option>
                                                        <option value="H">Hybrid</option>
                                                        <option value="LF">Low Frequency</option>
                                                        <option value="VLP">Very Long Period</option>
                                                        <option value="E">Explosion</option>
                                                        <option value="T">Tremor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Map Width (km):</div>
                                                <div class="rightPanel">
                                                    <select id="wkm2">
                                                        <option value="20">20</option>
                                                        <option value="30">30</option>
                                                        <option value="40">40</option>
                                                        <option value="50">50</option>
                                                        <option value="60">60</option>
                                                    </select>                                               
                                                </div>
                                            </div>												
                                            <div class="threeDGMTFilter">
                                                For drawing in 3D-GMT only:
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Azimuth:</div>
                                                <div class="rightPanel">
                                                    <input type="text" id="azim2" class="numberInput" value="175" size="10"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="leftPanel">Rotation Degree:</div>
                                                <div class="rightPanel">
                                                    <input type="text" id="degree2" class="numberInput" value="30" size="10"/>
                                                </div>
                                            </div>
                                            <div class="FilterBtnHolder">
                                                <button id="FilterBtn2" class="FilterBtn">Filter</button>
                                            </div>
                                        </form>
                                        <div class="equakeButtonsRow">
                                            <label for="equakeDisplayType22D" class="equakeDisplayBox equakeDisplayBox2">
                                                <input type="radio" name="equakeDisplayType2" id="equakeDisplayType22D" value="2D" onclick="drawEquake({mapUsed:2,source:this})"/>
                                                2D
                                            </label>
                                            <label for="equakeDisplayType22DGMT" class="equakeDisplayBox equakeDisplayBox2">
                                                <input type="radio" name="equakeDisplayType2" id="equakeDisplayType22DGMT" value="2D(GMT)" onclick="drawEquake({mapUsed:2,source:this})"/>2D(GMT)
                                            </label>
                                            <label for="equakeDisplayType23D" class="equakeDisplayBox equakeDisplayBox2">
                                                <input type="radio" name="equakeDisplayType2" id="equakeDisplayType23D" value="3D(GMT)" onclick="drawEquake({mapUsed:2,source:this})"/>3D(GMT)
                                            </label>
                                        </div>
                                        <!-- place holders for the 2D Flot graph-->
                                        <div id="equakeGraphs2">
                                            <div id="twoDEquakeFlotGraph2" class="twoDEquakeFlotGraph">
                                                <b class="pointer"></b>
                                                <div class="plot-label">
                                                    <b>E-W</b>
                                                </div>
                                                <div id="FlotDisplayLat2" class="equakeGraphPlaceholder">

                                                </div>
                                                <div class="plot-label">
                                                    <b>N-S</b>
                                                </div>
                                                <div id="FlotDisplayLon2" class="equakeGraphPlaceholder">
                                                </div>
                                                <div class="plot-label">
                                                    <b>Time</b>
                                                </div>
                                                <div id="FlotDisplayTime2" class="equakeGraphPlaceholder">
                                                </div>
                                                <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.TWOD_EQUAKE,element:document.getElementById('equakeGraphs2'),mapUsed:2,equakeGraph:equakeGraphs[2],info:document.getElementById('CompVolcanoList').value})">
                                                    <a title="Print this graphs" href="#" >
                                                        <span class="app-icon light print-icon"></span>
                                                        <span class="app-button-text">Print</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div id="2DGMTEquakeGraph2" class="twoDGMTEquakeFlotGraph">
                                                <b class="pointer"></b>
                                                <div id="2DImage" class="TwoDImage">
                                                    <a href="" id="imageLink" target="_blank"><img height="707" width="495" src="" id="image"/></a>
                                                </div>
                                                <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.TWOD_GMT_EQUAKE,link:$('#image',document.getElementById('equakeGraphs2'))[0].src,info:document.getElementById('CompVolcanoList').value})">
                                                    <a title="Print this graphs" href="#" >
                                                        <span class="app-icon light print-icon"></span>
                                                        <span class="app-button-text">Print</span>
                                                    </a>
                                                </div>
                                                <div id="additionalInfomation">
                                                    Additional data:
                                                    <a id="gifImage" href="" target="_blank">Image file</a>, 
                                                    <a id="gmtScriptFile" href="" target="_blank">GMT script file</a><br/> 
                                                </div>
                                            </div>
                                            <div id="3DGMTEquakeGraph2" class="threeDGMTEquakeFlotGraph">
                                                <b class="pointer"></b>
                                                <div id="3DImage" class="ThreeDImage">
                                                    <div id="navigationBar" class="threeDNavigationBar">

                                                        <div id="previousButton"></div>
                                                        <div id="showAnimation"></div>
                                                        <div id="nextButton"></div>

                                                    </div>
                                                    <div id="title"></div>
                                                    <a href="" id="imageLink" target="_blank"><img height="500" width="495" src="" id="image"/></a>
                                                </div>
                                                <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.THREED_GMT_EQUAKE,link:$('#image',document.getElementById('3DGMTEquakeGraph2'))[0].src,info:document.getElementById('CompVolcanoList').value})">
                                                    <a title="Print this graphs" href="#" >
                                                        <span class="app-icon light print-icon"></span>
                                                        <span class="app-button-text">Print</span>
                                                    </a>
                                                </div>
                                                <div id="additionalInfomation">
                                                    Additional data:
                                                    <a id="gifImage" href="" target="_blank">GIF image file</a>, 
                                                    <a id="gmtScriptFile" href="" target="_blank">GMT script file</a><br/> 
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="button white" >
                                            <div class="CloseButton" id="HideTimeSeriesPanel2"></div>
                                            <table>
                                                <tr>
                                                    <td valign="middle">
                                                        <span id="TimeSeriesHeader2" class="TimeSeriesHeader">
                                                            <a href="" onclick="return false;">Data Plots</a>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="TimeSeriesView2">
                                            <div id="OptionList2">
                                                <b>Available time series data (max. 3):</b>
                                                <div id="OptionList2-1">
                                                    <table id="TimeSeriesList2" class="timeSeriesList">

                                                    </table>
                                                </div>
                                            </div>
                                            <br/>
                                            <div id="overviewPanel2" class="overviewPanel">
                                                <b>Overview (select a range to redraw the graph): </b>
                                                <div id="overview2"  class="overviewGraph">

                                                </div>
                                                <br/>
                                            </div>
                                            <div style="clear:both;" id="PlotArea2">
                                                <table id="GraphList2">
                                                </table>
                                            </div>
                                            <div class="PrintButton" onclick="javascript:Wovodat.Printer.print({type:Wovodat.Printer.Printing.Type.TIME_SERIES,graphsTable: document.getElementById('GraphList2'),graphsPlot:graphs,info:document.getElementById('CompVolcanoList').value})">
                                                <a title="Print this graphs" href="#" >
                                                    <span class="app-icon light print-icon"></span>
                                                    <span class="app-button-text">Print</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                </table>

            </div>
            <div style="height: 20px"></div>
            <div class="reservedSpace">
            </div>
        </div>
        <div class="wrapborder_x">
            <?php include 'php/include/footer_main_beta.php'; ?>
        </div>
    </body>
</html>