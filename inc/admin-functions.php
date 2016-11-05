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

	echo '
		<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		                Confirm Delete of <b><i class="title"></i></b>
		            </div>
		            <div class="modal-body">
		                Are you sure you wish to delete this creature?
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		                <a class="btn btn-danger btn-ok">Delete</a>
		            </div>
		        </div>
		    </div>
		</div>';
	
	echo '<div class="row"><div class="col-sm-offset-1 col-lg-4">
	<label>
	Monster List (click to edit)</label>
	<div class="list-group" id="monsterlist">';
	create_monster_list();

    echo '</div></div>';
    echo '<div class="col-lg-4" id="newcreature">';
	
	//monster form
    create_new_monster_form();

    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2" id="monsteralertzone"></div></div>';
    echo '</div>';
/*
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
*/   
?>
<script type="text/javascript">

$('#confirm-delete').on('click', '.btn-ok', function(e) {

  var $modalDiv = $(e.delegateTarget);
  var id = $(this).data('recordId');

  $modalDiv.addClass('loading');
  $.post('ajax.php?function=deletecreature&creatureid=' + id, function(data)
  { 
  	console.log(data);
  }).then(function() {
     $modalDiv.modal('hide').removeClass('loading');
    getData("monsterlist");
  })});

// Bind to modal opening to set necessary data properties to be used to make request
$('#confirm-delete').on('show.bs.modal', function(e) {
  var data = $(e.relatedTarget).data();
  $('.title', this).text(data.recordTitle);
  $('.btn-ok', this).data('recordId', data.recordId);
});

</script
<?php

}

function create_monster_list()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from creatures ");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
    while ($row = $result->fetch_assoc()) {
		echo '<a href="#creatureid['.$row["creatureid"].']" data-record-id="'.$row["creatureid"].'" class="list-group-item " onclick="loadMonsterEditor('.$row["creatureid"].')">'.$row["truename"].'      <span class="pull-right">
        <button class="btn btn-xs btn-warning" value="delete" data-toggle="modal"  data-record-id="'.$row["creatureid"].'" data-record-title="'.$row["truename"].'" data-target="#confirm-delete">
          <span class="glyphicon glyphicon-trash"></span>
        </button></a>';
    }	
?>
<script type="text/javascript">
function loadMonsterEditor(creatureid){
	if ($(document.activeElement).val() == "delete")
	{
		//delete was triggered elsewhere
	}
	else
		getData("newcreature","edit",creatureid)
}

</script>
<?php


    
}
function create_new_monster_form()
{
	//monster form
	echo '<form id="monstersform" class="form-horizontal">';
	echo '
		  <div class="form-group">
		    <label for="truename" class="col-sm-6 control-label">Creature\'s True Name</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="truename" id="truename" placeholder="">
		    </div>
		  </div>
		  <div class="form-group">
		    <label for="fakename" class="col-sm-6 control-label">Creature\'s Display Name</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="fakename" id="fakename" placeholder="">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="initmod" class="col-sm-6 control-label">Initiative Modifier</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="initmod" id="initmod" placeholder="0">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexmod" class="col-sm-6 control-label">Dexterity Modifier</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="dexmod" id="dexmod" placeholder="0">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexscore" class="col-sm-6 control-label">Dexterity Score</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="dexscore" id="dexscore" placeholder="0">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="ac" class="col-sm-6 control-label">Armor Class</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="ac" id="ac" placeholder="0">
		    </div>
		  </div>	
		  <div class="form-group">
		    <div class="col-sm-offset-6 col-sm-10">
		      <div class="checkbox">
		        <label >
		          <input name="showtruename" id="showtruename" type="checkbox"> Show True Name
		        </label>
		      </div>
		    </div>
		  </div>	
		  <div class="form-group">
		    <div class="col-sm-offset-6 col-sm-10">
		      <div class="checkbox">
		        <label >
		          <input name="showac" id="showac" type="checkbox"> Show Armor Class
		        </label>
		      </div>
		    </div>
		  </div>				  		  
	';
	echo '<div class="col-sm-offset-6 col-sm-10"><button type="submit" class="btn btn-default">Submit</button></form></div>';
    echo '</div>';

    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-sm-offset-6 col-sm-2" id="monsteralertzone"></div></div>';
    echo '</div>';	
?>
<script type="text/javascript">
$("#monstersform").submit(function(event){
    // cancels the form submission
    event.preventDefault();
    $('#monstersform').serialize();
    console.log($('#monstersform').serialize());
    submitMonsterForm();
    getData("newcreature");
    getData("monsterlist");
}); 
function submitMonsterForm(){
    // Initiate Variables With Form Content
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "function=monster&" + $('#monstersform').serialize(),
        success : function(text){
            if (text == "success"){
				showmonsteralert('Saved','alert-success');
            }
            else 
            {
              console.log(text);
            }
        }
    });
}
  function showmonsteralert(message,alerttype) {
    $('#monsteralertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
    	$("#alertdiv").remove();
    }, 2000);
  }
</script>
<?php        
}





function edit_monster_form($creatureid)
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from creatures where creatureid = $creatureid");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
    $row = $result->fetch_assoc();
    $showtruename = $row['showtruename']==1 ? "checked" : "";
    $showac = $row['showac']==1 ? "checked" : "";

	//monster form
	echo '<form id="editmonstersform" class="form-horizontal">';
	echo '
		  <div class="form-group">
		    <label for="truename" class="col-sm-6 control-label">Creature\'s True Name</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="truename" id="truename" value="'.$row['truename'].'">
		    </div>
		  </div>
		  <div class="form-group">
		    <label for="fakename" class="col-sm-6 control-label">Creature\'s Display Name</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="fakename" id="fakename" value="'.$row['fakename'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="initmod" class="col-sm-6 control-label">Initiative Modifier</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="initmod" id="initmod" value="'.$row['initmod'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexmod" class="col-sm-6 control-label">Dexterity Modifier</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="dexmod" id="dexmod" value="'.$row['dexmod'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexscore" class="col-sm-6 control-label">Dexterity Score</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="dexscore" id="dexscore" value="'.$row['dexscore'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="ac" class="col-sm-6 control-label">Armor Class</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="ac" id="ac" value="'.$row['ac'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <div class="col-sm-offset-6 col-sm-10">
		      <div class="checkbox">
		        <label >
		          <input name="showtruename" id="showtruename" type="checkbox" ' . $showtruename. ' > Show True Name
		        </label>
		      </div>
		    </div>
		  </div>	
		  <div class="form-group">
		    <div class="col-sm-offset-6 col-sm-10">
		      <div class="checkbox">
		        <label >
		          <input name="showac" id="showac" type="checkbox" ' . $showac . '> Show Armor Class
		        </label>
		      </div>
		    </div>
		  </div>				  		  
	';
	echo '<input type="hidden" name="creatureid" value="'.$creatureid.'">';
	echo '<div class="col-sm-offset-6 col-sm-10"><button type="submit" value="edit" class="btn btn-default">Save Changes</button>&nbsp;<button type="submit" value="new" class="btn btn-default">Save as New Monster</button></form></div>';
    echo '</div>';

    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-sm-offset-6 col-sm-4" id="monsteralertzone"></div></div>';
    echo '</div>';	
?>
<script type="text/javascript">
$("#editmonstersform").submit(function(event){
    // cancels the form submission
    event.preventDefault();
    if ($(document.activeElement).val() == "edit")
	{
		//edit
		//console.log("edit button pressed");
		submitEditMonsterForm();
	    getData("monsterlist");		
	}
	else if ($(document.activeElement).val() == "new")
	{
		//save new	
		submitCreateMonsterForm();
	    getData("newcreature");
	    getData("monsterlist");		
	}     	
}); 
function submitEditMonsterForm(){
    // Initiate Variables With Form Content
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "function=editmonster&" + $('#editmonstersform').serialize(),
        success : function(text){
            if (text == "success"){
				showmonsteralert('Saved','alert-success');
            }
            else 
            {
              console.log(text);
            }
        }
    });
}
function submitCreateMonsterForm(){
    // Initiate Variables With Form Content
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "function=monster&" + $('#editmonstersform').serialize(),
        success : function(text){
            if (text == "success"){
				showmonsteralert('Saved','alert-success');
            }
            else 
            {
              console.log(text);
            }
        }
    });
}
  function showmonsteralert(message,alerttype) {
    $('#monsteralertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
    	$("#alertdiv").remove();
    }, 2000);
  }
</script>
<?php        
}

?>