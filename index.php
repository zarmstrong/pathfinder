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
	<?php 
  include 'head.php'; 
  include 'inc/functions.php';
  ?>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4" id="leftcolumn"><?php show_turn_data(); ?></div>
      <div class="col-md-4" id="middlecolumn"><?php show_round_info(); ?></div>
      <div class="col-md-4" id="rightcolumn"><?php show_legend(); ?></div>
    </div>
  </div>



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
}, 2500);

var audioElementStart = document.createElement('audio');
audioElementStart.setAttribute('src', '/sounds/ding.mp3');
audioElementStart.load();
var audioElementEnd = document.createElement('audio');
audioElementEnd.setAttribute('src', '/sounds/buzz.mp3');
audioElementEnd.load();
function play_turnstart_sound()
{
  audioElementStart.play();  
}

function play_turnend_sound()
{
  audioElementEnd.play();  
}
</script>
  </body>
</html>

