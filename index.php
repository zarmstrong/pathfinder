<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  <!-- Custom styles for this template -->
  <link href="style.css" rel="stylesheet">
  <link href="css/bootstrap.icon-large.min.css" rel="stylesheet">    


  </head>
  <body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="../timer/jquery.countdown.min.js">  crossorigin="anonymous"></script>
	<?php 
  include 'head.php'; 
  include 'inc/functions.php';
  ?>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4" id="leftcolumn"><?php show_turn_data(); ?></div>
      <div class="col-md-4" id="middlecolumn"><?php show_round_info(); ?></div>
      <div class="col-md-4" id="rightcolumn">.col-md-4</div>
    </div>


<?php 

$useold=0;
if ($useold)
{?>
      <div class="starter-template">
        <h1>Pathfinder Turn Timer</h1>
        <p class="lead">Magic is here...</p>
	    <div class="panel panel-default" data-toggle="tooltip" data-placement="top" title="Beautiful, isn't it?" >
	      <div class="panel-body">
	        <div class="lead" id="clock"></div>
	      </div>
	    </div>


    <button type="button" class="btn btn-primary" id="btn-reset">
      <i class="glyphicon glyphicon-repeat"></i>
      Reset
    </button>

    <div class="btn-group" data-toggle="buttons">
      <label class="btn btn-default active" id="btn-stop">
        <input type="radio" name="options" id="option2" autocomplete="off" >
        <i class="glyphicon glyphicon-stop"></i>
        Stop
      </label>

      <label class="btn btn-default" id="btn-resume">
        <input type="radio" name="options3" id="option3" autocomplete="off" >
        <i class="glyphicon glyphicon-play"></i>
        Start
      </label>

      </div>

    </div><!-- /.container -->
    <script type="text/javascript">
		$(document).keydown(function(e) {
			//alert(e.which);
		    switch(e.which) {
		    	case 90: //z
		    	$('#btn-reset').click();
		    	break;
		    	case 88: //x
		    	$('#btn-stop').click();
		    	break;
		    	case 67: //c
		    	$('#btn-resume').click();
		    	break;

		        case 37: // left
		        break;

		        case 38: // up
		        break;

		        case 39: // right
		        break;

		        case 40: // down
		        break;

		        default: return; // exit this handler for other keys
		    }
		    e.preventDefault(); // prevent the default action (scroll / move caret)
		});

    </script>

<script type="text/javascript">
  // Turn on Bootstrap
  $('[data-toggle="tooltip"]').tooltip();

  // 15 days from now!
  function getTurnTimeFromNow() {
    return new Date(new Date().valueOf() +  6 * 1000);
  }

  var $clock = $('#clock');

  $clock.countdown(getTurnTimeFromNow(), function(event) {
    $(this).html(event.strftime('%M:%S'));
  }).on('finish.countdown', function() {
  		$(this).addClass("timesup");
	    $("#btn-stop").addClass("active");
	    $("#btn-resume").removeClass("active");
  });

  $('#btn-reset').click(function() {
    $('div#clock').removeClass("lead timesup").addClass("lead");    
    $clock.countdown(getTurnTimeFromNow());
    $('div#clock').countdown('stop');
    $("#btn-stop").addClass("active");
    $("#btn-resume").removeClass("active");  

  });

  $('#btn-stop').click(function() {
    $clock.countdown('stop');
    $("#btn-stop").addClass(" active");
    $("#btn-resume").removeClass("active");
  });

  $('#btn-resume').click(function() {
    $clock.countdown('resume');
    $("#btn-stop").removeClass("active");
    $("#btn-resume").addClass("active");
  });

  $('div#clock').countdown('stop');
</script>
<?php
}
?>


<script type="text/javascript">
function getData(id,param1,param2)
{
  var param1 = (typeof param1 !== 'undefined') ?  param1 : '0';
  var param2 = (typeof param2 !== 'undefined') ?  param2 : '0';
   $.ajax({
     type: "GET",
     url: 'feajax.php',
     data: "function=" + id + "&param1="+param1 + "&param2="+param2, // appears as $_GET['id'] @ your backend side
     success: function(data) {
           // data is ur summary
          $('#'+id).html(data);
     }
   });
}

setInterval(function() {
  getData("leftcolumn");
}, 1000);
setInterval(function() {
  getData("middlecolumn");
}, 5000);
</script>
  </body>
</html>

