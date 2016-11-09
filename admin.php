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
    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Confirm Delete of <b><i class="title"></i></b>
                </div>
                <div class="modal-body">
                    Are you sure you wish to delete this <span class="objecttype"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger btn-ok">Delete</a>
                </div>
            </div>
        </div>
    </div>    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	
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
<div class="content" id="monsters" style='display:none;'>
  <div class="row">
     <div class="col-md-1"> &nbsp;</div>
  </div>  
  <span id="monsterform">
    <?php  ?>
  </span>
</div>
<div class="content" id="combat" style='display:none;'>
  <div class="row">
     <div class="col-md-1"> &nbsp;</div>
  </div>  
  <span id="encounterform">
    <?php  ?>
  </span>
</div>  
<div class="content" id="turntracker" style='display:none;'>
  <div class="row">
     <div class="col-md-1"> &nbsp;</div>
  </div>  
  <span id="combattracker">
    <?php  ?>
  </span>
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

function getData(id,param1,param2)
{
  var param1 = (typeof param1 !== 'undefined') ?  param1 : '0';
  var param2 = (typeof param2 !== 'undefined') ?  param2 : '0';
   $.ajax({
     type: "GET",
     url: 'ajax.php',
     data: "function=" + id + "&param1="+param1 + "&param2="+param2, // appears as $_GET['id'] @ your backend side
     success: function(data) {
           // data is ur summary
          $('#'+id).html(data);
     }
   });
}

</script>  

  <script type="text/javascript">

  $('#confirm-delete').on('click', '.btn-ok', function(e) {
    var $modalDiv = $(e.delegateTarget);
    var id = $(this).data('recordId');
    $modalDiv.addClass('loading');
    $.post('ajax.php?function=deleteencounter&combatid=' + id, function(data)
    { 
      console.log(data);
    }).then(function() {
       $modalDiv.modal('hide').removeClass('loading');
      getData("encounterlist");
    })});

  // Bind to modal opening to set necessary data properties to be used to make request
  $('#confirm-delete').on('show.bs.modal', function(e) {
    var data = $(e.relatedTarget).data();
    $('.title', this).text(data.recordTitle);
    $('.objecttype', this).text(data.objecttype);
    $('.btn-ok', this).data('recordId', data.recordId);
  });

  $(document).ready(function() {
     $('#confirm-delete').modal(options) 
  }); 
  </script>
</html>