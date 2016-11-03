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


  </head>
  <body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="../timer/jquery.countdown.min.js">  crossorigin="anonymous"></script>
	<?php include 'admin-head.php'; ?>


    <div class="container">

<?php
include("inc/admin-functions.php");
?>

<div class="content" id="attendance">
  <div class="row">
     <div class="col-md-1"> &nbsp;</div>
  </div>
    <span id="presentform">
    <?php create_newsession_form(); ?>
  </span>

</div>
<div class="content" id="init" style='display:none;'>
  <div class="row">
     <div class="col-md-1"> &nbsp;</div>
  </div>  
  <span id="initform">
    <?php  ?>
  </span>
</div>
<div class="content" id="combat" style='display:none;'>
  <div class="row">
     <div class="col-md-1"> &nbsp;</div>
  </div>  
    <?php  ?>
</div>
<div class="content" id="monsters" style='display:none;'>
  <div class="row">
     <div class="col-md-1"> &nbsp;</div>
  </div>  
    <?php  ?>
</div>
  

    </div><!-- /.container -->
  </body>
<script type="text/javascript">
  $(".navbar-nav a").on('click',function(e) {
   e.preventDefault(); // stops link form loading
   $(".nav").find(".active").removeClass("active");
   $(this).parent().addClass("active");
   $('.content').hide(); // hides all content divs
   $($(this).attr('href') ).show(); //get the href and use it find which div to show
});

</script>

  <script type="text/javascript">
function formSuccess(){
    $( "#msgSubmit" ).removeClass( "hidden" );
}

function getData(id)
{
   $.ajax({

     type: "GET",
     url: 'ajax.php',
     data: "function=" + id, // appears as $_GET['id'] @ your backend side
     success: function(data) {
           // data is ur summary
          $('#'+id).html(data);
     }

   });

}

</script>  
</html>