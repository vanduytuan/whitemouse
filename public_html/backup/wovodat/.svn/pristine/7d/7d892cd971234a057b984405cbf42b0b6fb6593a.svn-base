
  function changedDataType()
  {
	$.ajax({
	  method: "get", url: "getStations.php", 
	  data: "vd_id=" + $("#vd_name :selected").val() + '&dataType=' + $("#dataType :selected").text(),
	  beforeSend: function(){$("#filLoading").show("fast");},
	  complete: function(){$("#filLoading").hide("fast");},
	  success: function(html){		
	  $("#selectStationDiv").html(html);
	  }
	});
  }
  function showSpatial()
  {
	window.maporgraph = 0; 
	$('#viewcontent').hide(); 
	$('#bigmap').height('80%'); 
	$('#bigmap').width('90%'); 
	$('#temporal').hide(); 
	$('#spatial').show();
	$('#bigmap').show(); 
	return false;
  }
  
  function showTemporal()
  {
	window.maporgraph = 1; 
	$('#viewcontent').show(); 
	$('#temporal').show(); 
	$('#spatial').hide();
	$('#bigmap').hide(); 
	return false;
  }

	function setupMap(){
		if ($("#filter").val() && $("#dp_max").val() >= $("#dp_min").val()) {
			$.ajax({
			  method: "get", url: "getvd_info.php", 
			  data: "vd_id=" + $("#vd_name").val() + 
				'&vd_name=' + $("#vd_name :selected").text() + '&date_start=' + $("#ss_start").val() + 
				'&date_end=' + $("#ss_end").val() + '&dr_start=' + $("#dp_min").val() + 
				'&dr_end=' + $("#dp_max").val() + '&filter=' + $("#filter").val() + 
				'&qty=' + $("#events :selected").text() + '&eqtype=' + $("#eqtype :selected").val(),
				
			  beforeSend: function(){$("#filLoading").show("fast");},
			  complete: function(){$("#filLoading").hide("fast");},
			  success: function(html){		
			  $("#dialog").dialog("destroy");
			  $("#dialog").html(html);
			  $("#dataType").val(1);
			  $("#selectStationDiv").html('');
			  }
			});
		}
		else alert("Invalid Depth Range");
	}
	function setupNeighborMap(vid, vname){
		if ($("#filter").val() && $("#dp_max").val() >= $("#dp_min").val()) {
			$.ajax({
			  method: "get", url: "getvd_info.php", 
			  data: "vd_id=" + vid + '&vd_name=' + vname + '&date_start=' + $("#ss_start").val() + 
				'&date_end=' + $("#ss_end").val() + '&dr_start=' + $("#dp_min").val() + 
				'&dr_end=' + $("#dp_max").val() + '&filter=' + $("#filter").val() + 
				'&qty=' + $("#events :selected").text() + '&eqtype=' + $("#eqtype :selected").val(),
			  
			  beforeSend: function(){$("#filLoading").show("fast");},
			  complete: function(){$("#filLoading").hide("fast");},
			  success: function(html){		
			  $("#dialog").dialog("destroy");
			  $("#dialog").html(html);
			  $("#dataType").val(0);
			  $("#selectStationDiv").html('');
			  }
			});
		}
		else alert("Invalid Depth Range");
	}
  function getDS()
  {
   var temp1 = $('#ds_id :selected').text();
   if (temp1 == "Select ds_id:") return;
    $("#ds_id_ref1").slideUp("slow");
	$("#gbutton").hide();
	var ds_id = $('#ds_id :selected').text();
    $.ajax({
      method: "get", url: "getDS.php", data: "ds_id=" + ds_id,
      beforeSend: function(){$("#filLoading").show("fast");},
      complete: function(){$("#filLoading").hide("fast");},
      success: function(html){
      $("#ds_id_ref1").show("fast");
      $("#ds_id_ref1").html(html);
      }
    });
  }
  $(document).ready(function(){
	// hide elements that are not part of the map module on load
	$("#filLoading").hide();
	$("#temporal").hide();
	$("#filterSS").hide();
	$("#filterSSG").hide();
	$("#minfil").hide();
	$("#minfilG").hide();
	$("#filterBtn").hide();
	$("#viewcontent").hide();
	
	$("#startdate").datepicker({
		changeMonth: true,
		changeYear: true
	});
	$("#enddate").datepicker({
		changeMonth: true,
		changeYear: true
	});
	$("#ss_start").datepicker({
		changeMonth: true,
		changeYear: true
	});
	$("#ss_end").datepicker({
		changeMonth: true,
		changeYear: true
	});
	
	// render as buttons and tabs
	$("button", "#dateBtns").button();
	$("button", "#gbutton").button();
	$("#filterBtn").button();
	$("#reloadBtn").button();
	$("#viewcontrol").tabs();
	$("#viewcontent").tabs();
	
	$("#minfil").click(
		function() {
			$("#filterSS").hide("fast");
			$(this).hide();
			$("#filter").val("0");
			$("#plfil").show();
			$("#filterBtn").hide();
			$("#reloadBtn").show();
		}
	);
	$("#minfilG").click(
		function() {
			$("#filterSSG").hide("fast");
			$(this).hide();
			$("#filterG").val("0");
			$("#plfilG").show();
		}
	);
	$("#filterBtn").click(
		function() {
		setupMap();
		}
	);
	$("#reloadBtn").click(
		function() {
		setupMap();
		}
	);
	$("#plfil").click(
		function() {
			$("#filterSS").show("fast");
			$("#filterBtn").show();
			$("#reloadBtn").hide();
			$(this).hide();
			$("#filter").val("1");
			$("#minfil").show();
		}
	);
	$("#plfilG").click(
		function() {
			$("#filterSSG").show("fast");
			$(this).hide();
			$("#filterG").val("1");
			$("#minfilG").show();
		}
	);
	window.mapcreated = false;
});