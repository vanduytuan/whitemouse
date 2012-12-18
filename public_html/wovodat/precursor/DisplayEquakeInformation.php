<?php
include 'php/include/db_connect_view.php';
$volcanoId = $_GET['vdid'];
$numberOfEvents = $_GET['qty'];// 100 -> 500
$volcanoNameAndCavw = $_GET['vd_name'];// format: name_cavw
$volcanoName = substr($volcanoNameAndCavw,0,strrpos($volcanoNameAndCavw,"_"));
$divNum = $_GET['divnum'];// either 1 or 2


$startDate_d=$_GET['date_ss_d'];
$startDate_m=$_GET['date_ss_m'];
$startDate_y=$_GET['date_ss_y'];
if($startDate_y==''){
    $startDate = '1/1/1880';
}else{
	$startDate=$startDate_d.'/'.$startDate_m.'/'.$startDate_y;
}
$endDate_d=$_GET['date_se_d'];
$endDate_m=$_GET['date_se_m'];
$endDate_y=$_GET['date_se_y'];
if($endDate_y!=''){
	$endDate=$endDate_d.'/'.$endDate_m.'/'.$endDate_y;
}else{
	$endDate=date('d/m/y');
}

$startDepth = $_GET['dr_start'];
if(!is_numeric($startDepth) || $startDepth <= 0) $startDepth = 0;
$endDepth = $_GET['dr_end'];
if(!is_numeric($endDepth) || $endDepth >= 50) $endDepth = 50;
if($startDepth > $endDepth) {
    $startDepth = 0;
    $endDepth = 50;
}
$equakeType = $_GET['eqtype'];
if($equakeType != '') $equakeType = " and sd_evn_eqtype = '$equakeType' ";
$start = split('/', $startDate);
$end = split('/', $endDate);
$dates = " and sd_evn_time BETWEEN '$start[2]-$start[0]-$start[1]' AND '$end[2]-$end[0]-$end[1]' ";
$depth = " and sd_evn_edep BETWEEN $startDepth AND $endDepth ";
$mapWidth = 20;// map width is not set by user, hard coded value

//get lat, lon of volcano
$sqlVolcano = "select vd_inf_slat, vd_inf_slon from vd_inf where vd_id = '$volcanoId'";
$volcano = mysql_query($sqlVolcano) or die('<b>Can\'t connect to server now!</b>');
$volcanoInfo = mysql_fetch_array($volcano);
$volcanoLatitude = $volcanoInfo[0];
$volcanoLongitude = $volcanoInfo[1];
$sqlEvent = "(select sd_evn_elat, sd_evn_elon, sd_evn_edep, sd_evn_pmag, sd_evn_time, sd_evn_eqtype FROM sd_evn WHERE ($volcanoLatitude - sd_evn_elat)<1 and ($volcanoLongitude - sd_evn_elon)<5 $equakeType $dates $depth and (sqrt(pow(($volcanoLatitude - sd_evn_elat)*110, 2) + pow(($volcanoLongitude - sd_evn_elon)*111.32*cos(($volcanoLatitude)/57.32), 2)))<30 LIMIT $numberOfEvents)";
$getQuakes = mysql_query($sqlEvent) or die('<b>Can\'t connect to server now!</b>');
?>
<script type="text/javascript">
    $("#dp_min" + "<?php echo $divNum?>").val('<?php echo $startDepth?>');
    $("#dp_max" + "<?php echo $divNum?>").val('<?php echo $endDepth?>');
    $("#ss_start" + "<?php echo $divNum?>").val('<?php echo $startDate?>');
    $("#ss_end" + "<?php echo $divNum?>").val('<?php echo $endDate?>');
    // Store queried data to an array and return back to user
    // when ever user choose different options in leftEquakeList
    // we will process data again and get the necessary information
    // for each type of volcano.
    // This is a two dimension array.
    // to store each individual array element and push to 'data'
    // 0: latitude
    // 1: longitude
    // 2: depth
    // 3: time
    // 4: earthquake type
    //alert('starting');
    //var data<?php $divNum?> = [
<?php
echo "var data$divNum = [";
$eqtypes = array();
while($row = mysql_fetch_array($getQuakes,MYSQL_NUM)) {
    array_push($eqtypes,$row[5]);
    echo "['" . $row[0] . "','" . $row[1] . "','" . $row[2] . "','" . $row[4] . "','" . $row[5] . "'],";
}
sort($eqtypes);
// reset the pointer of getQuakes resource
mysql_data_seek($getQuakes,0);
echo "];";
$row = mysql_fetch_array($getQuakes,MYSQL_NUM);

mysql_data_seek($getQuakes,0);
?>
    //];
    $(document).ready(function(){
    $('#equakeList<?php echo $divNum?>').bind('change',function(){
		  
<?php
echo "var equaketype$divNum = $('#equakeList$divNum').val();
    if (equaketype$divNum == ''){
	    return;
	  }else if(equaketype$divNum == 'Hid'){
	  	$('#plotntitle$divNum').hide();
	    return;
	  }else{
	  	$('#plotntitle$divNum').show();		  
	  }
";
?>
            if (typeof(Number.prototype.toRad) === "undefined") {
                Number.prototype.toRad = function() {
                    return this * Math.PI / 180;
                }
            }
            var previousPoint = null;
<?php
echo "      var latitudeOptions$divNum = {
                legend:{position:\"nw\"},
                series:{points:{show:true,radius: 1.0}},
                colors:[\"#3a4cb2\"],
                grid:{
                    backgroundColor:{colors:[\"#f3ffed\",\"#f3ffdc\"]},
                    // this option is for changing the color of the border
                    borderColor: [\"white\"],
                    clickable:true,
                    hoverable:true,
                    autoHighlight:true
                },
                yaxis:{
                    tickFormatter : kmFormatter
                },
                xaxis:{
                    tickFormatter : kmFormatter
                },
                zoom:{ interactive: false},
                pan: {interactive: true}
            };
            var longitudeOptions$divNum = {
                legend:{position:\"nw\"},
                series:{points:{show:true,radius: 1.0}},
                colors:[\"#3a4cb2\"],
                grid:{
                    backgroundColor:{colors:[\"#f3ffed\",\"#f3ffdc\"]},
                    // this option is for changing the color of the border
                    borderColor: [\"white\"],
                    clickable:true,
                    hoverable:true,
                    autoHighlight:true
                },
                yaxis:{
                    tickFormatter: kmFormatter
                },
                xaxis:{
                    tickFormatter : kmFormatter
                },
                zoom:{ interactive: false},
                pan: {interactive: true}
            };
            var timeOptions$divNum = {
                legend:{position:\"nw\"},
                series:{points:{show:true,radius: 1.0}},
                colors:[\"#3a4cb2\"],
                grid:{
                    backgroundColor:{colors:[\"#f3ffed\",\"#f3ffdc\"]},
                    // this option is for changing the color of the border
                    borderColor: [\"white\"],
                    clickable:true,
                    hoverable:true,
                    autoHighlight:true
                },
                yaxis:{
                    tickFormatter: kmFormatter
                },
                xaxis:{mode:\"time\",timeformat:\"%d/%m/%y\",ticks:4},
                zoom:{ interactive: false},
                pan: {interactive: true},
                selection: {mode: null}
            }
            var R = 6371; // km
            var volcanoLat$divNum = $volcanoLatitude;
            var volcanoLong$divNum = $volcanoLongitude;
            var tempData$divNum = new Array();
            for( var i = 0 ; i < data$divNum.length ; i++ ){
                if ( equaketype$divNum == 'All' || equaketype$divNum == data1[i][4]){
                    // calculate the distance between the earthquake and the
                    // volcano based solely on the latitude view
                    var dLat = 0;
                    var dLon = (data$divNum [i][1]-volcanoLong$divNum).toRad();
                    // because we are viewing in the latitude direction, we will use
                    // the latitude of the volcano for simplification matter
                    var lat1 = volcanoLat$divNum.toRad();
                    var lat2 = volcanoLat$divNum.toRad();
                    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                        Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
                    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    var d = R * c;
                    // checking to see if we should give the distance between the
                    // volcano and the earthquake positive or negative value
                    var dif = data$divNum [i][1] - volcanoLong$divNum;
                    if ( (dif > 0 && dif <90) || dif <-180){
                        // positive value for d
                    }else{
                        d = -d;
                    }

                    tempData$divNum.push([d,-data$divNum [i][2]]);
                    //tempData$divNum.push([data$divNum [i][1],-data$divNum [i][2]]);
                }
            }
            var latitudeData$divNum = [{
                    data: tempData$divNum,
                    label: \"W-E\"
                }];
            var latitudePlotarea$divNum =$(\"#equakeXGraph$divNum\");
            latitudePlotarea$divNum.css(\"height\", CONSTANTS.height);
            latitudePlotarea$divNum.css(\"width\",CONSTANTS.width);
            latitudePlotarea$divNum.css(\"font-size\", \"8px\");
            $.plot(latitudePlotarea$divNum,latitudeData$divNum,latitudeOptions$divNum);

            $('#equakeXGraph$divNum').bind(\"plothover\", function (event, pos, item) {
                if (item){
                    if( previousPoint != item.datapoint){
                        removeAllToolTip();
                        previousPoint = item.datapoint;
                        var x = item.datapoint[0].toFixed(2);
                        var y = item.datapoint[1].toFixed(2);
                        showTooltip(item.pageX, item.pageY,\"Distance from the volcano = \" + x + \", depth = \" + y,\"equakeXGraph$divNum\" +\"ToolTip\");
                    }
                }else{
                    removeAllToolTip();
                    previousPoint = null;
                }
            });


            tempData$divNum = new Array();
            for(i = 0 ; i < data$divNum.length ; i++){
                if ( equaketype$divNum == 'All' || equaketype$divNum == data$divNum [i][4]){
                    dLat = (data$divNum [i][0]-volcanoLat$divNum).toRad();
                    dLon = 0;
                    // because we are viewing in the latitude direction, we will use
                    // the latitude of the volcano for simplification matter
                    lat1 = volcanoLat$divNum.toRad();
                    lat2 = volcanoLat$divNum.toRad();
                    a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                        Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
                    c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    d = R * c;
                    // checking to see if we should give the distance between the
                    // volcano and the earthquake positive or negative value
                    dif = data$divNum [i][0] - volcanoLat$divNum;
                    if ( (dif > 0 && dif <90) || dif <-180){
                        // positive value for d
                    }else{
                        d = -d;
                    }

                    tempData$divNum.push([d,-data$divNum [i][2]]);
                    //tempData1.push([data$divNum [i][0],-data$divNum [i][2]]);
                }
            }
            var longitudeData$divNum = [{
                    data:tempData$divNum,
                    label: \"S-N\"
                }];
            var longitudePlotarea$divNum= $('#equakeYGraph$divNum');
            longitudePlotarea$divNum.css(\"height\", CONSTANTS.height);
            longitudePlotarea$divNum.css(\"width\",CONSTANTS.width);
            longitudePlotarea$divNum.css(\"font-size\", \"8px\");

            $.plot(longitudePlotarea$divNum,longitudeData$divNum,longitudeOptions$divNum);

            // show tool tip when the mouse hover over the point
            $('#equakeYGraph$divNum').bind(\"plothover\", function (event, pos, item) {
                if (item ){
                    if( previousPoint != item.datapoint){
                        removeAllToolTip();
                        previousPoint = item.datapoint;
                        var x = item.datapoint[0].toFixed(2);
                        var y = item.datapoint[1].toFixed(2);
                        showTooltip(item.pageX, item.pageY,\"Distance from the volcano = \" + x + \", depth = \" + y,\"equakeYGraph$divNum\" + \"ToolTip\");
                    }
                }else{
                    removeAllToolTip();
                    previousPoint = null;
                }
            });
            // getting time data
            tempData$divNum = new Array();
            for( i = 0 ; i < data$divNum.length; i++){
                if ( equaketype$divNum == 'All' || equaketype$divNum == data$divNum [i][4]){
                    var temp = 0;
                    temp = data$divNum [i][3]*1000;
                    if(isNaN(temp)){
                        temp = data$divNum [i][3];
                        // the data$divNum [i][3] variables can't be converted to epoch
                        // value because of the format, need to perform the
                        // convertion manually, the format is yyyy-mm-dd hh:mm:ss
                        var expression = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
                        if( temp.search(expression) == 0)
                            temp = new Date(temp.substring(0,4),parseInt(temp.substring(5,7))-1,temp.substring(8,10),temp.substring(11,13),temp.substring(14,16),temp.substring(17,19),0);
                        // Other format of the date and time will be processed here.
                        temp = temp.getTime();
                    }
                    tempData$divNum.push([temp,-data$divNum [i][2]]);
                    //tempData$divNum .push([data$divNum [i][3],-data$divNum [i][2]]);
                }
            }
            var timeData$divNum = [{
                    data: tempData$divNum
                }];
            var timePlotarea$divNum = $('#equakeTimeSeriesGraph$divNum');
            timePlotarea$divNum.css(\"height\", CONSTANTS.height);
            timePlotarea$divNum.css(\"width\",CONSTANTS.width);
            timePlotarea$divNum.css(\"font-size\", \"8px\");
            $.plot(timePlotarea$divNum,timeData$divNum,timeOptions$divNum);
            $('#equakeTimeSeriesGraph$divNum').bind(\"plothover\", function (event, pos, item) {
                if (item ){
                    if( previousPoint != item.datapoint){
                        removeAllToolTip();
                        previousPoint = item.datapoint;
                        var date = new Date(parseInt(item.datapoint[0].toFixed(2)));
                        var year = date.getFullYear();
                        var month = date.getMonth()+1;
                        if (month.toString().length == 1) month=\"0\" + month;
                        var day = date.getDate();
                        if (day.toString().length == 1) day=\"0\" + day;
                        var hour = date.getHours();
                        if (hour.toString().length == 1) hour=\"0\" + hour;
                        var minute = date.getMinutes();
                        if (minute.toString().length == 1) minute=\"0\" + minute;
                        var second = date.getSeconds();
                        if (second.toString().length == 1) second=\"0\" + second;
                        var x = year + \"-\" + month + \"-\" + day + \" \" + hour + \":\" + minute + \":\" + second;
                        var y = item.datapoint[1].toFixed(2);
                        showTooltip(item.pageX, item.pageY,\"Date = \" + x + \", depth = \" + y,\"equakeTimeSeriesGraph$divNum\" + \"ToolTip\");
                    }
                }else{
                    removeAllToolTip();
                    previousPoint = null;
                }
            });
";
?>
        });
    });
      	$('#plotntitle$divNum').hide();
</script>
<div id="displayEquakeInformation<?php echo $divNum?>" style="padding-top: 0px;padding-bottom: 20px;">
    <div id="chooseEquakeType<?php echo $divNum?>" style="display: table-row;">
        <div style="display: table-cell;font-weight: bold;font-size:11px;width:140px">Earthquake type:</div>
        <div style="display: table-cell;width:160px;">
            <select id="equakeList<?php echo $divNum?>" style="width:160px;font-size: 9px;">
                <option value="">Choose earthquake type</option>
                <?php
                for($i = 0 ; $i < count($eqtypes);$i++) {
                    if ($eqtypes[$i] != '')
                        if ($i == 0 || ($i > 0 && $eqtypes[$i] != $eqtypes[$i-1]))
                        //if ($i == 0 || ($i > 0 && $eqtypes[$i] != $eqtypes[$i-1]))
                            echo "<option value=\"$eqtypes[$i]\">$eqtypes[$i]</option>";
                }
                ?>
                <option value="All">Show All</option>
                <option value="Hid">Hide</option>
            </select>
        </div>
    </div>
    
		<div id="plotntitle<?php echo $divNum?>">
	    <div id="titlelatplot<?php echo $divNum?>" style="padding-top: 20px;padding-bottom: 10px;font-weight: bold;">Latitude View</div>
	    <div id="equakeXGraph<?php echo $divNum?>" style="text-align: center"></div>   
	
	    <div id="titlelonplot<?php echo $divNum?>" style="padding-top: 20px;padding-bottom: 10px;font-weight: bold;">Longitude View</div>
	    <div id="equakeYGraph<?php echo $divNum?>"></div>    
	
	    <div  id="titletimeplot<?php echo $divNum?>" style="padding-top: 20px;padding-bottom: 10px;font-weight: bold;">Across time</div>
	    <div id="equakeTimeSeriesGraph<?php echo $divNum?>"></div>
    </div>   
</div>
<?php
mysql_close($link);
?>
