
  function setup() {
	//hide message_body after the first one
	$("div# .message_list .message_body:gt(0)").hide();
	
	//hide message li after the 5th
	$(".message_list li:gt(4)").hide();
	
  //hide button on load
  $(".decollapse_all_message").hide();
  
	//toggle message_body
	$(".message_head").click(function(){
		$(this).next(".message_body").slideToggle(500)
		return false;
	});

	//collapse all messages
	$(".collapse_all_message").click(function(){
		$(this).hide()
		$(".decollapse_all_message").show()
		$(".message_body").slideUp(500)
		return false;
	});

	//decollapse all messages
	$(".decollapse_all_message").click(function(){
		$(this).hide()
		$(".collapse_all_message").show()
		$(".message_body").slideDown(500)
		return false;
	});

	//show all messages
	$(".show_all_message").click(function(){
		$(this).hide()
		$(".show_recent_only").show()
		$(".message_list li:gt(4)").slideDown()
		return false;
	});

	//show recent messages only
	$(".show_recent_only").click(function(){
		$(this).hide()
		$(".show_all_message").show()
		$(".message_list li:gt(4)").slideUp()
		return false;
	});
}

  function refresh(){
    $("#batchOnly").show();
    $("#seeAll").hide();
    $.ajax({
      method: "get", url: "announcements_get.php", data: "type=0",
      beforeSend: function(){$("#filLoading").show("fast");},
      complete: function(){$("#filLoading").hide("fast");},
      success: function(html){
      $("#comments").show("slow");
      $("#comments").html(html);
      setup();
      }
    });
    
   }

  $(document).ready(function(){
  setInterval("refresh()", 60000);
  refresh();
  $('#menu a').click(function() { //start function when any link is clicked
      $(".content").slideUp("slow");
       var content_show = $(this).attr("title"); //retrieve title of link so we can compare with php file
        $.ajax({
          method: "get",url: "boo.php",data: "page="+content_show,
          beforeSend: function(){$("#loading").show("fast");}, //show loading just when link is clicked
          complete: function(){ $("#loading").hide("fast");}, //stop showing loading when the process is complete
          success: function(html){ //so, if data is retrieved, store it in html
          $(".content").show("slow"); //animation
          $(".content").html(html); //show the html inside .content div
          }
        }); //close $.ajax(
   }); //close click(
   
  $("#seeAll").hide();
  $('#batchOnly').click(function() {
    $(this).hide();
    $("#seeAll").show();
    $("#comments").slideUp("slow");
    $.ajax({
      method: "get", url: "announcements_get.php", data: "type=1",
      beforeSend: function(){$("#filLoading").show("fast");},
      complete: function(){$("#filLoading").hide("fast");},
      success: function(html){
      $("#comments").show("fast");
      $("#comments").html(html);
      setup();
      }
    });
  });
  
  $('#seeAll').click(function() {
    $(this).hide();
    $("#batchOnly").show();
    $("#comments").slideUp("slow");
    $.ajax({
      method: "get", url: "announcements_get.php", data: "type=0",
      beforeSend: function(){$("#filLoading").show("fast");},
      complete: function(){$("#filLoading").hide("fast");},
      success: function(html){
      $("#comments").show("fast");
      $("#comments").html(html);
      setup();
      }
    });
  });
  $('#submitA').click(function() {
    var msg = document.getElementById('msg').value; 
    $.ajax({
      method: "get", url: "announcements_add.php", data: "type=0&msg="+msg,
      beforeSend: function(){$("#loading").show("fast");},
      complete: function(){$("#loading").hide("fast");},
      success: function(html){
        $("#comments").show();
        document.getElementById('result').className = 'error';
        document.getElementById('msg').value = "";
        if (html == "01")
          $("#result").html("Error: Please login first.");
        else if (html == "02")
          $("#result").html("Error: Blank Message.");
        else if (html == "03 0") {
          document.getElementById('result').className = 'success';
          $("#result").html("Your message was posted for all to see.");
          refresh();
        }
        else if (html == "03 1") {
          document.getElementById('result').className = 'success';
          $("#result").html("Your message was posted for batchmates only.");
          refresh();
        } else {
          document.getElementById('result').innerHTML = html;
        }
      }
    })
  });
  $('#submitB').click(function() {
    var msg = document.getElementById('msg').value; 
    $.ajax({
      method: "get", url: "announcements_add.php", data: "type=1&msg="+msg,
      beforeSend: function(){$("#loading").show("fast");},
      complete: function(){$("#loading").hide("fast");},
      success: function(html){
        $("#comments").show();
        document.getElementById('result').className = 'error';
        document.getElementById('msg').value = "";
        if (html == "01")
          $("#result").html("Error: Please login first.");
        else if (html == "02")
          $("#result").html("Error: Blank Message.");
        else if (html == "03 0") {
          document.getElementById('result').className = 'success';
          $("#result").html("Your message was posted for all to see.");
          refresh();
        }
        else if (html == "03 1") {
          document.getElementById('result').className = 'success';
          $("#result").html("Your message was posted for batchmates only.");
          refresh();
        } else {
          document.getElementById('result').innerHTML = html;
        }
      }
    })
  });
  setup();
});