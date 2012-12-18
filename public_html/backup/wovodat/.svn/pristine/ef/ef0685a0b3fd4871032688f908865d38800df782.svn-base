<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>WOVOdat :: The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat), by IAVCEI</title>
        <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
        <meta name="description" content="The World Organization of Volcano Observatories (WOVO): Database of Volcanic Unrest (WOVOdat)">
        <meta name="keywords" content="Volcano, Vulcano, Volcanoes, Vulcanoes, Volcan, Vulkan, eruption, 
              forecasting, forecast, predict, prediction, hazard, desaster, disaster, desasters, disasters, 
              database, data warehouse, format, formats, WOVO, WOVOdat, IAVCEI, sharing, streaming, earthquake, 
              earthquakes, seismic, seismicity, seismology, deformation, INSar, GPS, uplift, caldera, stratovolcano, 
              stratovulcano">
        <link href="/css/styles_beta.css" rel="stylesheet">
        <link type="text/css" href="/js/jqueryui/css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="/js/jqueryui/js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.flot.js"></script>
        <script type="text/javascript" src="/js/jquery.flot.navigate.tuan.js"></script> 
        <script type="text/javascript" src="/js/wovodat.js"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ9kUvUtmawmFJ62hWVsigWFTh3CKUzzM&sensor=false"></script>
        <script type="text/javascript">
            // this is the list of available station type for each volcano,
            // this list will be initialize when the volcano is selected and 
            // it will be deleted when this volcano is deselected.
            var stationTypeList = [];
            // this list of available time series list
            var timeSeriesList  = [];
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
            
            $(document).ready(function(){
                // make the list of available time series have the style of jquery ui tabs
                $( "#OptionList" ).tabs({
                    collapsible: true
                });
                // get the list of all volcano in our database and insert it into 
                // the dropdown list
                Wovodat.getVolcanoList(insertVolcanoList);
                
                // when the volcano option is changed
                $("#VolcanoList").change(function(){
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
                        handler: updateStationList
                    });
                    // delete all the drawn graphs and the time series list
                    for( var i in stationsDatabase){
                        deleteTimeSeriesList(i);
                    }
                    // reset the local list of available stations for each data type
                    delete(stationsDatabase);
                    stationsDatabase = {};
                });
                
                // show the loading icon when ajax is executing
                $("#loading").ajaxStart(function(){
                    $(this).show();
                });
                // hide the loading icon when ajax is completed
                $("#loading").ajaxComplete(function(){
                    $(this).hide();
                });
            
                // get all the available graph move to the eruption
                $("#EruptionList").change(moveGraphsToEruptionTime);
            });
            
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
                    graphs[i] = $.plot(placeholder,[data],newOptions);
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
                    document.getElementById(value + 'Tr').innerHTML = "<td><input type='checkbox' id='" +value +  "' value='" + value + "' onclick='drawTimeSeries(this)'></td><td>" + display + "</td>";
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
                var data =args.data;
                if(graphData[id] == undefined)
                    graphData[id] = data;
                var minValue = Number.MAX_VALUE,maxValue = Number.MIN_VALUE;
                var maxXValue = Number.MIN_VALUE;
                var sixMonths = 6*30*24*60*60*1000;
                var minXValue;
                var i;
                var length = data[0].length;
                maxXValue = data[0][0][0];
                for(i = 0 ; i < length; i++){
                    if(data[0][i][1] > maxValue) maxValue = data[0][i][1];
                    if(data[0][i][1] < minValue) minValue = data[0][i][1];
                }
                // get the maxXValue of every graph
                for(var a in graphData){
                    var temp = graphData[a][0][0][0];
                    if(temp > maxXValue ) maxXValue = temp;
                }
                minXValue = maxXValue - sixMonths;
                minXValue = minXValue > data[0][length-1][0]? minXValue : data[0][length-1][0];
                for(var a in graphData){
                    var temp = graphData[a][0][graphData[a][0].length -1][0];
                    if( minXValue < temp) minXValue = temp;
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
                div.style.width = '650px';
                div.style.height = '200px';
                td.appendChild(div);
                tr.appendChild(td);
                
                // these marks will show the eruption start time 
                var markings = [];
                var eruptionList = document.getElementById("EruptionList").options;
                var l = eruptionList.length;
                var t;
                for(i = 0; i < l; i++){
                    t = eruptionList[i].value;
                    t = t.split(" ");
                    if(t.length <= 1)
                        continue;
                    t = eruptionList[i].value;
                    t = Wovodat.toDate(t).getTime();
                    markings[markings.length] = {
                        color: '#800080',
                        lineWidth: 1,
                        xaxis:{
                            from: t,
                            to: t
                        }
                    }
                }
                var options = {
                    series:{
                        lines: {
                            show:true
                        },
                        points: {show:false},
                        color: 'rgb(60, 10, 255)'
                    },
                    grid:{
                        hoverable: true,
                        clickable: true,
                        backgroundColor:{
                            colors: ['#fff','#eee']
                        },
                        markings: markings
                    },
                    xaxis:{
                        max: maxXValue,
                        min: minXValue,
                        ticks: tickGenerator
                        ,
                        labelWidth: 50
                        ,
                        show: true
                    },
                    yaxis:{
                        panRange:[minValue,maxValue],  
                        zoomRange:[minValue,maxValue],
                        max: maxValue,
                        min: minValue,
                        color: 'rgb(123,1,100)',
                        labelWidth: 15
                    },
                    zoom:{
                        interactive: true
                    },
                    pan: {
                        interactive: true
                    }
                };
                data = {
                    data: data[0],
                    label: display.replace(/&/g, "_")
                };
                graphs[id] = $.plot($("#" + id.replace(/&/g,"\\&") + "Graph"),[data],options);
                
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
                    graphs[i] = $.plot(placeholder,[data],options);
                }
                // this part is for synchronize the pan and zoom of the graphs
                for( i in graphs){
                    if(i != id){
                        synchronizeGraph(i,id);
                    }
                }
                
                $("#GraphList").sortable();
            }
            function tickGenerator(axis){
                var ticks = 6;
                var size = axis.max - axis.min;
                size = size/ticks;
                var start = size * Math.floor( axis.min / size);
                var value = Number.Nan;
                var da;
                var res = [];
                for(var i = 0 ; i < ticks + 1 ; i++){
                    value = start + size * i;
                    value = value.toFixed(0);
                    value = Math.round(value);
                    da = new Date(value);
                    res.push([value, da.getUTCDate() + "/" + da.getUTCMonth() + "/" + ("" + da.getUTCFullYear()).substr(2) + " " + da.getUTCHours()]);
                    //res.push([value," "]);
                } 
                
                // adding the labels for the start time of eruption list
                var eruptionList = document.getElementById("EruptionList").options;
                var l = eruptionList.length;
                var t,k;
                for(i = 0; i < l; i++){
                    t = eruptionList[i].value;
                    t = t.split(" ");
                    if(t.length <= 1)
                        continue;
                    t = eruptionList[i].value;
                    t = Wovodat.toDate(t);
                    k = t.getTime();
                    if(k> axis.min && k < axis.max)
                        res.push([k,"<b><img src='/img/eruption.png'><br/>Eruption<br/>" + t.getUTCDate() + "/" + t.getUTCMonth() + "/" + t.getUTCFullYear() 
                                + " " + t.getUTCHours()+":" + t.getUTCMinutes() + ":" + t.getUTCSeconds() + "</b>"]);
                }
                return res.sort();
            }
            // make the graph moves together
            function synchronizeGraph(i,j){
                var i1 = i.replace(/&/g,"\\&");
                var j1 = j.replace(/&/g,"\\&");
                $("#" + i1 + "Graph").bind('plotzoom',function(event,plot,args){
                    if(graphs[j] == undefined) return;
                    if(args[j] && args[j] == true)
                        return;
                    args[j] = true;
                    args.preventEvent = true;
                    graphs[j].zoom(args);
                });
                $("#" + i1 + "Graph").bind('plotpan',function(event,plot,args){
                    if(graphs[j] == undefined) return;
                    if(args[j] && args[j] == true)
                        return;
                    args[j] = true;
                    args.preventEvent = true;
                    graphs[j].pan(args);
                });
                $("#" + j1 + "Graph").bind('plotzoom',function(event,plot,args){
                    if(graphs[i] == undefined) return;
                    if(args[i] && args[i] == true)
                        return;
                    args[i] = true;
                    graphs[i].zoom(args);
                });
                $("#" + j1 + "Graph").bind('plotpan',function(event,plot,args){
                    if(graphs[i] == undefined) return;
                    if(args[i] && args[i] == true)
                        return;
                    args[i] = true;
                    args.preventEvent = true;
                    graphs[i].pan(args);
                });
            }
            function deleteGraph(args){
                var id = args.id;
                delete(graphs[id]);
                var tr = document.getElementById(id +'Row');
                if(tr)
                    tr.parentNode.removeChild(tr);
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
                        markers[index].setIcon('/img/pin_' + value + 's_small.png');
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
                        // udpate the list of station nad the markers on the custom google map 
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
                var list = obj.list;
                list = list.split(";");
                var eruptions = document.getElementById("EruptionList");
                eruptions.options.length = 0;
                eruptions.options = [];
                var i = 0;
                var t;
                eruptions.options[0] = new Option("Select...","");
                for(;i < list.length;i++){
                    t = list[i].split("&");
                    if(t[2] != undefined)
                        t[1] = t[1] + " " + t[2];
                    eruptions.options[eruptions.options.length] = new Option(t[1],t[1]);
                }
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
            #GraphList tr td div .tickLabels .xAxis{
                display: none;
            }
            #GraphList tr:last-child td div .tickLabels .xAxis{
                display: block;
            }
            #GraphList{
                text-align: center;
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
                                    <div id="OptionList"  style="width:280px;height:300px;float:left">
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
                        <div id="testing">

                        </div>
                        <!-- plotting area -->
                        <br/>
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