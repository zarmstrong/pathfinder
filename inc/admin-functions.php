<?php

function list_some_inits()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from players");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	}
	else
	{
	echo '<ul class="list-group">';

	    while ($row = $result->fetch_assoc()) {
	      echo '  <li class="list-group-item">';
	      echo '    <span class="badge">' . $row['init'] . "." . $row['dexmod'] . '</span>';
	      echo $row['charname'] ? $row['charname'] : $row['name'] ;
	      echo "</li>";
	    }
	    echo '  </li>';
	    echo '</ul>  ';    
	}
}

function create_newsession_form()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from players");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	}
	else
	{
		echo '<form id="attendanceform" class="form-horizontal">';		
	    while ($row = $result->fetch_assoc()) {
			echo '
			<div class="row">
			  <div class="col-lg-2">
			    <div class="input-group">
			      <span class="input-group-addon">
			        <input type="checkbox" aria-label="'.$row["playerid"].'" id="players[]" name="players[]"  value="'.$row["playerid"].'" ' . ($row["present"]==1 ? 'checked="checked"' : '') . '>
			      </span>
			      <input type="text" class="form-control" aria-label="'.$row["name"].'" name="playername" value="'.$row['name'].'" >
			    </div><!-- /input-group -->
			  </div><!-- /.col-lg-6 -->
			</div>
			'  ;
	    }
	    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2">';
        echo '<button type="submit" class="btn btn-default">Submit</button></form></div></div>';
        echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2" id="attenalertzone"></div></div>';
        echo '</div>';
        ?>
<script type="text/javascript">

$("#attendanceform").submit(function(event){
    // cancels the form submission
    event.preventDefault();
 //var formVals = $('#attendanceform').serializeArray();
 //console.log(formVals);
    submitAttendanceForm();
}); 
function submitAttendanceForm(){
    // Initiate Variables With Form Content
$('#attendanceform').serialize();
 
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "function=attendance&" + $('#attendanceform').serialize(),
        success : function(text){
            if (text == "success"){
				showattenalert('Saved','alert-success');
            }
            else 
            {
              console.log(text);
            }
        }
    });
}
  function showattenalert(message,alerttype) {
    $('#attenalertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
    	$("#alertdiv").remove();
    }, 2000);
  }
</script>
<?php        
	}
}

function add_inits_form()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from players where present=1");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	}
	else
	{
		echo '<form id="initsform" class="form-horizontal">';		
	    while ($row = $result->fetch_assoc()) {
			echo '
			<div class="row">
			    <label for="inputinit" class="col-sm-2 control-label">' . $row["name"].'</label>
			  <div class="col-lg-2">
			    <div class="input-group">
			      <input type="text" id="inputinit" class="form-control" aria-label="'.$row["playerid"].'" name="playerinit['.$row["playerid"].']" value="'.$row["init"].'" >
			    </div><!-- /input-group -->
			  </div><!-- /.col-lg-2 -->
			</div>
			'  ;
	    }
	    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2">';
        echo '<button type="submit" class="btn btn-default">Submit</button></form></div></div>';
        echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2" id="initsalertzone"></div></div>';
        echo '</div>';
?>
<script type="text/javascript">
$("#initsform").submit(function(event){
    // cancels the form submission
    event.preventDefault();
 //var formVals = $('#initsform').serializeArray();
 //console.log(formVals);
    submitInitsForm();
}); 
function submitInitsForm(){
    // Initiate Variables With Form Content
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "function=inits&" + $('#initsform').serialize(),
        success : function(text){
            if (text == "success"){
				showinitsalert('Saved','alert-success');
            }
            else 
            {
              console.log(text);
            }
        }
    });
}
  function showinitsalert(message,alerttype) {
    $('#initsalertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
    	$("#alertdiv").remove();
    }, 2000);
  }
</script>
<?php
	}
}















function add_monster_form()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from creatures ");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	}
	else
	{
		echo '<form id="initsform" class="form-horizontal">';		
	    while ($row = $result->fetch_assoc()) {
			echo '
			<div class="row">
			    <label for="inputinit" class="col-sm-2 control-label">' . $row["name"].'</label>
			  <div class="col-lg-2">
			    <div class="input-group">
			      <input type="text" id="inputinit" class="form-control" aria-label="'.$row["playerid"].'" name="playerinit['.$row["playerid"].']" value="'.$row["init"].'" >
			    </div><!-- /input-group -->
			  </div><!-- /.col-lg-2 -->
			</div>
			'  ;
	    }
	    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2">';
        echo '<button type="submit" class="btn btn-default">Submit</button></form></div></div>';
        echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2" id="initsalertzone"></div></div>';
        echo '</div>';
?>
<script type="text/javascript">
$("#initsform").submit(function(event){
    // cancels the form submission
    event.preventDefault();
 //var formVals = $('#initsform').serializeArray();
 //console.log(formVals);
    submitInitsForm();
}); 
function submitInitsForm(){
    // Initiate Variables With Form Content
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "function=inits&" + $('#initsform').serialize(),
        success : function(text){
            if (text == "success"){
				showinitsalert('Saved','alert-success');
            }
            else 
            {
              console.log(text);
            }
        }
    });
}
  function showinitsalert(message,alerttype) {
    $('#initsalertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
    	$("#alertdiv").remove();
    }, 2000);
  }
</script>
<?php
	}
}




?>