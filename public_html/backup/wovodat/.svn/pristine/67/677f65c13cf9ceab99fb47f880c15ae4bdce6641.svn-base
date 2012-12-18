<?php
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
?>
