<?php

// According to data type selected, get stations of interest (stored in session)

// Get data type selected
$datatype=$_REQUEST['dataType'];

if ($datatype=="") {
	// No data type selected
	exit();
}

// Open list of stations
echo <<<STRING
					<p>Select station:</p>
					<select id="station_id" style="width:100%; margin-top:2px">
STRING;

// According to data type
session_start();
// List stations related to data type
$stacode=substr($datatype, 0, 1).'s';
foreach ($_SESSION[$stacode.'_obj'] as $station) {
	echo "<option value='".$station->{$stacode.'_id'}."'>".$station->{$stacode.'_name'};
	if (!empty($station->{$stacode.'_stime'}) || !empty($station->{$stacode.'_stime'})) {
		echo " [";
		if (empty($station->{$stacode.'_stime'})) {
			echo "1970";
		}
		else {
			echo $station->{$stacode.'_stime'};
		}
		echo " - ";
		if (empty($station->{$stacode.'_etime'})) {
			echo "Present";
		}
		else {
			echo $station->{$stacode.'_etime'};
		}
		echo "]";
	}
	echo "</option>";
}

// Close list of stations
echo <<<STRING
					</select>
STRING;
?>
	<!-- Graph button -->
	<div id="gbutton" align=center><button id="graphBtnCreate">Plot data</button></div>
	<script type="text/javascript">
	$("#graphBtnCreate").button();
	$("#graphBtnCreate").click(
		function() {
			$("#viewcontent").show();
			$("#east").html("");
			$("#north").html("");
			$("#elevation").html("");
			$("#fourth").html("");
			
			// Here will be the script for flot
			$.ajax({
				method: "get", url: "/php/plot_data.php", 
				data: "station_id=" + $("#station_id :selected").val() + "&datatype=" + $("#dataType :selected").val() + "&startdate=" + $("#startdate").val() + "&enddate=" + $("#enddate").val(), 
				beforeSend: function(){$("#filLoading").show("fast");},
				complete: function(){$("#filLoading").hide("fast");},
				success: function(html){
					$("#east").html(html);
				}
			});
		}
	);
	</script>