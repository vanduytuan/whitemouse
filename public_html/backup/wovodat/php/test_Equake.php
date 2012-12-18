
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.button.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.draggable.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.resizable.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="/js/development-bundle/ui/jquery.ui.dialog.js"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/excanvas.min.js" language="javascript"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/jquery.flot.js" language="javascript"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/jquery.flot.selection.js" language="javascript"></script>
	<script type="text/javascript" src="/js/development-bundle/flot/jquery.flot.navigate.js" language="javascript"></script>
	
	<script type="text/javascript" src="/js/flot/jquery.flot.symbol.js"></script>	
	<script type="text/javascript" src="/js/scripts.js" language="javascript"></script>
	<script type="text/javascript" src="/js/Tooltip_v3.js" language="javascript"></script>
	<script type="text/javascript" src="/js/GPSCharts_devel.js" language="JavaScript"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			alert('start');
			showEquakeInformationPanel(1);
		});

	function showEquakeInformationPanel(divnum){
	    $.ajax({
		method: "get",
		url: "/precursor/DisplayEquakeInformation.php",
		data: "vdid="+$("#vd_name" + divnum).val()  +
		'&vd_name=' + $("#vd_name" + divnum + " option:selected").text() +
		'&divnum='+ divnum +
		'&qty=' + $("#events"+divnum+" option:selected").val() +
		//'&date_start=' + $("#ss_m"+divnum+'option:selected').val() +'/'+ $("#ss_d"+divnum+"option:selected").val() +'/'+ $("#ss_y"+divnum).val()+
		//'&date_end=' +$("#se_m"+divnum+"option:selected").val()+'/'+ $("#se_d"+divnum+"option:selected").val() +'/'+$("#se_y"+divnum).val()+
		'&date_ss_d='+$("#ss_d"+divnum+ " :selected").val()+
		'&date_ss_m='+$("#ss_m"+divnum+ " :selected").val()+
		'&date_ss_y='+$("#ss_y"+divnum).val()+
		'&date_se_d='+$("#se_d"+divnum+ " :selected").val()+
		'&date_se_m='+$("#se_m"+divnum+ " :selected").val()+
		'&date_se_y='+$("#se_y"+divnum).val()+
		'&dr_start=' + $("#dp_min"+divnum).val() +
		'&dr_end=' + $("#dp_max"+divnum).val() +
		'&eqtype=' + $("#eqtype"+divnum+" :selected").val(),
		beforeSend: function(){
		    $("#filLoading").show("fast");
		},
		complete: function(){
		    $("#filLoading").hide("fast");

		},
		success: function(html){
		alert("ajax complete");
		   $("#displayEquakeInformation" + divnum).html(html);
		}
	    });
	    $("#displayEquakeInformation" +divnum).show();
	}

	</script>
</head>
<body>
123
<div id="displayEquakeInformation1"></div>
</body>
</html>
