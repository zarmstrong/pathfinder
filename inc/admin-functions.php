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
        echo '<button type="submit" class="btn btn-default">Save Attendance</button></form></div></div>';
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
		echo '<div class="col-sm-offset-1 col-md-12"><h3>Encounter Builder - Player Inits</h3></div>';
	    while ($row = $result->fetch_assoc()) {
			echo '
			<div class="row">
			    <label for="inputinit" class="col-sm-2 control-label">' . $row["name"].'</label>
			  <div class="col-lg-2">
			    <div class="input-group">
			      <input type="text" pattern="[0-9]*" id="inputinit" class="form-control" aria-label="'.$row["playerid"].'" name="playerinit['.$row["playerid"].']" value="'.$row["init"].'" >
			    </div><!-- /input-group -->
			  </div><!-- /.col-lg-2 -->
			</div>
			'  ;
	    }
	    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2">';
        echo '<button type="submit" class="btn btn-default">Save Initiatives</button></form></div></div>';
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
					//showinitsalert('Saved','alert-success');
					console.log("saved inits, moving on");
					getData("combatzone","showroundtracker");
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

?>

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
		echo '<a data-creatureid="'.$row["creatureid"].'" class="list-group-item selectableCard" >'.$row["truename"].'      <span class="pull-right">
        <button class="btn btn-xs btn-warning" value="delete" data-toggle="modal"  data-objecttype="creature" data-record-id="'.$row["creatureid"].'" data-record-title="'.$row["truename"].'" data-target="#confirm-delete">
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

	$('.selectableCard').on('click touchstart',function(){
		var data = $(this).data();
		console.log(data.creatureid);
		loadMonsterEditor(data.creatureid);
	});
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
		      <input type="text" pattern="[0-9]*" class="form-control" name="initmod" id="initmod" value="'.$row['initmod'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexmod" class="col-sm-6 control-label">Dexterity Modifier</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[0-9]*" class="form-control" name="dexmod" id="dexmod" value="'.$row['dexmod'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexscore" class="col-sm-6 control-label">Dexterity Score</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[0-9]*" class="form-control" name="dexscore" id="dexscore" value="'.$row['dexscore'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="ac" class="col-sm-6 control-label">Armor Class</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[0-9]*" class="form-control" name="ac" id="ac" value="'.$row['ac'].'">
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
	echo '<div class="col-sm-offset-4 col-sm-3"><button type="submit" value="edit" class="btn btn-default">Save Changes</button></form></div>
	      <div class="col-sm-5"><form id="newmonstersform" class="form-inline"><button type="submit" value="new" class="btn btn-default">Save as New Monster</button></form></div>';
    echo '</div>';

    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-sm-offset-6 col-sm-4" id="monsteralertzone"></div></div>';
    echo '</div>';	
?>
	<script type="text/javascript">
	$("#editmonstersform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();
	    //alert(submitActor.value);
		submitEditMonsterForm();
	    getData("monsterlist");		
		
	});
	$("#newmonstersform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();
	    //alert(submitActor.value);
		//save new	
		submitCreateMonsterForm();
	    getData("newcreature");
	    getData("monsterlist");		

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

function combat_manager()
{
	echo '<div class="row"><div class="col-sm-offset-1 col-lg-4">
	<label>
	Encounter List (click to edit)</label>
	<div class="list-group" id="encounterlist">';
	create_encounter_list();

    echo '</div></div>';
    echo '<div class="col-lg-4" id="newencounter">';
	
	//encounter form
    create_new_encounter_form();

    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2" id="encounteralertzone"></div></div>';
    echo '</div>';

?>

<?php

}

function create_encounter_list()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from combats_name ");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
    while ($row = $result->fetch_assoc()) {
		echo '<a  data-combatid="'.$row["combatid"].'" class="list-group-item selectableCard" >'.$row["combats_name"].'      <span class="pull-right">
        <button class="btn btn-xs btn-warning" value="delete" data-toggle="modal" data-objecttype="encounter" data-record-id="'.$row["combatid"].'" data-record-title="'.$row["combats_name"].'" data-target="#confirm-delete">
          <span class="glyphicon glyphicon-trash"></span>
        </button></a>';
    }	

?>
	<script type="text/javascript">
	$(document).ready(function() {
	 	 $('#confirm-delete').modal(options) 
	});	
	function loadEncounterEditor(combatid){
		if ($(document.activeElement).val() == "delete")
		{
			//delete was triggered elsewhere
		}
		else
			getData("newencounter","edit",combatid)
	}

	$('.selectableCard').on('click touchstart',function(){
		var data = $(this).data();
		console.log(data.combatid);
		loadEncounterEditor(data.combatid);
	});

	</script>
<?php

}

function create_new_encounter_form()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from creatures ");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
	//encounter form
	echo '
		<div id="encounterbuilderlists" class="container">
		  <div class="col-sm-10">
		        <div class="col-md-12 text-center"><h3>Encounter Builder</h3></div>
		  		<div class="col-sm-4 col-sm-offset-1">
		          <div class="list-group" id="list1">
		          <a href="#" class="list-group-item active">All Available Creatures<input title="toggle all" type="checkbox" class="all pull-right"></a>';

	while ($row = $result->fetch_assoc()) {
		echo '	          <a href="#" class="list-group-item" >'.$row["truename"].' ('.$row["fakename"].')<input type="checkbox" data-record-id="'.$row["creatureid"].'" data-record-title="'.$row["truename"].'" class="pull-right"></a>';        
    }	


	echo '        </div>
		        </div>
		        <div class="col-md-2 v-center">
		     		<button title="Add to Encounter" class="btn btn-default center-block add"><i class="glyphicon glyphicon-chevron-right"></i></button>
		            <button title="Remove from Encounter" class="btn btn-default center-block remove"><i class="glyphicon glyphicon-trash"></i></button>
		        </div>
		        <div class="col-sm-4">
		    	  <div class="list-group form-group" id="list2">
		          <a href="#" class="list-group-item active">All Encounter Creatures<input title="toggle all" type="checkbox" class="all pull-right"></a>
		          </div>
		        </div>
		  </div>	 
		</div>		  		  
	';
	echo '<form id="encountersform" class="form-horizontal">';
	echo '<div class="form-group">
		    <label for="combats_name" class="col-sm-6 control-label">Encounter Name</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="combats_name" id="combats_name" value="'.$row['combats_name'].'">
		    </div>
		  </div>';	
	echo '<div class="col-md-10 text-center"><button type="submit" class="btn btn-default" name="save" id="save1" value="save2">Save Encounter</button></form></div>';
    echo '</div>';
    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-sm-offset-6 col-sm-2" id="encounteralertzone"></div></div>';
    echo '</div>';	
?>
	<script type="text/javascript">
	$("#encountersform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();

	    var this_master = $("#encounterbuilderlists");
	    var array_of_creatures="";
	   	this_master.find('#list2 input[type="checkbox"]').each( function () {
        var checkbox_this = $(this);
        //alert(checkbox_this.data('recordId'));
        if (checkbox_this.data('recordId') != undefined)
        	array_of_creatures=array_of_creatures.concat(checkbox_this.data('recordId'),",")
		}); 

	    $('#encountersform').serialize();
	    console.log($('#encountersform').serialize());
	    submitEncounterForm(array_of_creatures);
	    getData("newencounter");
	    getData("encounterlist");

	});
	function submitEncounterForm(array_of_creatures){
	    // Initiate Variables With Form Content
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=encounter&" + $('#encountersform').serialize() +"&creaturelist="+array_of_creatures,
	        success : function(text){
	            if (text == "success"){
					showencounteralert('Saved','alert-success');
	            }
	            else 
	            {
	              console.log(text);
	            }
	        }
	    });
	}
	  function showencounteralert(message,alerttype) {
	    $('#encounteralertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
	    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
	    	$("#alertdiv").remove();
	    }, 2000);
	  }

	$('.add').click(function(){
	    $('.all').prop("checked",false);
	    var items = $("#list1 input:checked:not('.all')");
	    var n = items.length;
	  	if (n > 0) {
	      items.each(function(idx,item){
	        var choice = $(item);
	        choice.prop("checked",false);
	        choice.parent().clone(true,true).appendTo("#list2");
	      });
	  	}
	    else {
	  		alert("Choose a creature from the left list first");
	    }
	});

	$('.remove').click(function(){
	    $('.all').prop("checked",false);
	    var items = $("#list2 input:checked:not('.all')");
		items.each(function(idx,item){
	      var choice = $(item);
	      choice.prop("checked",false);
	      choice.parent().remove();
	    });
	});

	/* toggle all checkboxes in group */
	$('.all').click(function(e){
		e.stopPropagation();
		var $this = $(this);
	    if($this.is(":checked")) {
	    	$this.parents('.list-group').find("[type=checkbox]").prop("checked",true);
	    }
	    else {
	    	$this.parents('.list-group').find("[type=checkbox]").prop("checked",false);
	        $this.prop("checked",false);
	    }
	});

	$('[type=checkbox]').click(function(e){
	  e.stopPropagation();
	});

	/* toggle checkbox when list group item is clicked */
	$('.list-group a').click(function(e){
	  
	    e.stopPropagation();
	  
	  	var $this = $(this).find("[type=checkbox]");
	    if($this.is(":checked")) {
	    	$this.prop("checked",false);
	    }
	    else {
	    	$this.prop("checked",true);
	    }
	  
	    if ($this.hasClass("all")) {
	    	$this.trigger('click');
	    }
	});

	</script>
<?php        
}




function edit_encounter_form($encounterid)
{
	global $mysqli;

	$result = $mysqli->query("SELECT * from combats_name where combatid='$encounterid'");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
	$row = $result->fetch_assoc();
	$combats_name = $row["combats_name"];
	$result = $mysqli->query("SELECT * from creatures ");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
	//encounter form
	echo '
		<div id="encounterbuilderlists" class="container">
		  <div class="col-sm-10">
		        <div class="col-md-12 text-center"><h3>Encounter Builder</h3></div>
		  		<div class="col-sm-4 col-sm-offset-1">
		          <div class="list-group" id="list1">
		          <a href="#" class="list-group-item active">All Available Creatures<input title="toggle all" type="checkbox" class="all pull-right"></a>';

	while ($row = $result->fetch_assoc()) {
		echo '	          <a href="#" class="list-group-item" >'.$row["truename"].' ('.$row["fakename"].')<input type="checkbox" data-record-id="'.$row["creatureid"].'" data-record-title="'.$row["truename"].'" class="pull-right"></a>';        
    }	


	echo '        </div>
		        </div>
		        <div class="col-md-2 v-center">
		     		<button title="Add to Encounter" class="btn btn-default center-block add"><i class="glyphicon glyphicon-chevron-right"></i></button>
		            <button title="Remove from Encounter" class="btn btn-default center-block remove"><i class="glyphicon glyphicon-trash"></i></button>
		        </div>
		        <div class="col-sm-4">
		    	  <div class="list-group form-group" id="list2">
		          <a href="#" class="list-group-item active">All Encounter Creatures<input title="toggle all" type="checkbox" class="all pull-right"></a>';
	
	$result = $mysqli->query("SELECT * from combats left join creatures on combats.creatureid=creatures.creatureid where combatid='$encounterid'");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
	while ($row = $result->fetch_assoc()) {
		echo '	          <a href="#" class="list-group-item" >'.$row["truename"].' ('.$row["fakename"].')<input type="checkbox" data-record-id="'.$row["creatureid"].'" data-record-title="'.$row["truename"].'" class="pull-right"></a>';        
    }	

	echo '      </div>
		        </div>
		  </div>	 
		</div>		  		  
	';
	echo '<form id="encountersform" class="form-horizontal">';
	echo '<div class="form-group">
		    <label for="combats_name" class="col-sm-6 control-label">Encounter Name</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="combats_name" id="combats_name" value="'.$combats_name.'">
		      <input type="hidden" class="form-control" name="combatid" id="combatsid" value="'.$encounterid.'">
		    </div>
		  </div>';	
	echo '<div class="col-sm-offset-2 col-md-5 text-center"><button type="submit" class="btn btn-default" name="edit">Save Changes</button></form></div>
		  <div class="col-md-5 text-center"><form id="newencountersform" class="form-inline"><button type="submit" class="btn btn-default" name="save">Save as New Encounter</button></form></div>';
    echo '</div>';
    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-sm-offset-6 col-sm-2" id="encounteralertzone"></div></div>';
    echo '</div>';	
?>
	<script type="text/javascript">
	$("#encountersform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();

	    var this_master = $("#encounterbuilderlists");
	    var array_of_creatures="";
	   	this_master.find('#list2 input[type="checkbox"]').each( function () {
        var checkbox_this = $(this);
        //alert(checkbox_this.data('recordId'));
        if (checkbox_this.data('recordId') != undefined)
        	array_of_creatures=array_of_creatures.concat(checkbox_this.data('recordId'),",")
		}); 

	    $('#encountersform').serialize();
	    console.log("boo:");
	    console.log($('#encountersform').serialize() + " nums: " + array_of_creatures);
	    submitEncounterForm(array_of_creatures);
	    getData("newencounter");
	    getData("encounterlist");

	});
	$("#newencountersform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();

	    var this_master = $("#encounterbuilderlists");
	    var array_of_creatures="";
	   	this_master.find('#list2 input[type="checkbox"]').each( function () {
        var checkbox_this = $(this);
        //alert(checkbox_this.data('recordId'));
        if (checkbox_this.data('recordId') != undefined)
        	array_of_creatures=array_of_creatures.concat(checkbox_this.data('recordId'),",")
		}); 

	    $('#encountersform').serialize();
	    console.log($('#encountersform').serialize());
	    return;
	    submitNewEncounterForm(array_of_creatures);
	    getData("newencounter");
	    getData("encounterlist");

	});	
	function submitEncounterForm(array_of_creatures){
	    // Initiate Variables With Form Content
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=editencounter&" + $('#encountersform').serialize() +"&creaturelist="+array_of_creatures,
	        success : function(text){
	            if (text == "success"){
					showencounteralert('Saved','alert-success');
	            }
	            else 
	            {
	              console.log(text);
	            }
	        }
	    });
	}
	function submitNewEncounterForm(array_of_creatures){
	    // Initiate Variables With Form Content
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=encounter&" + $('#encountersform').serialize() +"&creaturelist="+array_of_creatures,
	        success : function(text){
	            if (text == "success"){
					showencounteralert('Saved','alert-success');
	            }
	            else 
	            {
	              console.log(text);
	            }
	        }
	    });
	}	
	  function showencounteralert(message,alerttype) {
	    $('#encounteralertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
	    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
	    	$("#alertdiv").remove();
	    }, 2000);
	  }

	$('.add').click(function(){
	    $('.all').prop("checked",false);
	    var items = $("#list1 input:checked:not('.all')");
	    var n = items.length;
	  	if (n > 0) {
	      items.each(function(idx,item){
	        var choice = $(item);
	        choice.prop("checked",false);
	        choice.parent().clone(true,true).appendTo("#list2");
	      });
	  	}
	    else {
	  		alert("Choose a creature from the left list first");
	    }
	});

	$('.remove').click(function(){
	    $('.all').prop("checked",false);
	    var items = $("#list2 input:checked:not('.all')");
		items.each(function(idx,item){
	      var choice = $(item);
	      choice.prop("checked",false);
	      choice.parent().remove();
	    });
	});

	/* toggle all checkboxes in group */
	$('.all').click(function(e){
		e.stopPropagation();
		var $this = $(this);
	    if($this.is(":checked")) {
	    	$this.parents('.list-group').find("[type=checkbox]").prop("checked",true);
	    }
	    else {
	    	$this.parents('.list-group').find("[type=checkbox]").prop("checked",false);
	        $this.prop("checked",false);
	    }
	});

	$('[type=checkbox]').click(function(e){
	  e.stopPropagation();
	});

	/* toggle checkbox when list group item is clicked */
	$('.list-group a').click(function(e){
	  
	    e.stopPropagation();
	  
	  	var $this = $(this).find("[type=checkbox]");
	    if($this.is(":checked")) {
	    	$this.prop("checked",false);
	    }
	    else {
	    	$this.prop("checked",true);
	    }
	  
	    if ($this.hasClass("all")) {
	    	$this.trigger('click');
	    }
	});

	</script>
<?php  
}

function create_combat_tracker()
{
	echo '<div id="combatzone">';
	echo '<div class="row"><div class="col-sm-offset-1 col-lg-4">
	<label>
	Encounter List</label>
';
	show_encounter_picker();

    echo '</div>';
    echo '<div class="col-lg-4" id="encountertrackingspot">';
	
	//encounter form
    //create_new_encounter_form();

    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2" id="encounteralertzone"></div></div>';
    echo '</div>';

    echo '</div>'; //combatzone

}

function show_encounter_picker()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from combats_name ");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
	echo '<div class="dropdown">
		  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuEncounters" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		    Encounters
		    <span class="caret"></span>
		  </button>';
    echo '<ul class="dropdown-menu" id="dropdownMenuEncounters" aria-labelledby="dropdownMenuEncounters">';
    while ($row = $result->fetch_assoc()) {
		echo '<li data-recordid="'.$row["combatid"].'""><a href="#" id="encounter['.$row["combatid"].']" data-recordid="'.$row["combatid"].'">'.$row["combats_name"].'</a></li>';
    }	
    echo '  </ul>
		  </div>';
	echo '<div class="row"> &nbsp;</div><div class=""><form id="loadcurentencounterform" class="form-inline"><button type="submit" class="btn btn-default" name="loadenc">Load Current Encounter</button></form></div>';
?>	
	<script type="text/javascript">
	$("#loadcurentencounterform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();

		getData("combatzone","showroundtracker");

	});	

	$("#dropdownMenuEncounters li a").click(function(){
	  	var recordid = $(this).parent().data("recordid");
	  //getData("newencounter");
	  pickEncounter(recordid)
	});

	

	function pickEncounter(encounterID){
	    // Initiate Variables With Form Content
	    console.log("s");
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=createandloadcombat&encounterid="+encounterID,
	        success : function(text){
	            if (text == "success"){
					//showencounteralert('Saved','alert-success');
					getData("combatzone","playerinits");
	            }
	            else 
	            {
	              console.log(text);
	            }
	        }
	    });
	}

	</script>
<?php
}

function show_round_tracker()
{
	global $mysqli;
	$result = $mysqli->query("SELECT rt.uid,rt.combatantid,COALESCE(npc.truename,pc.charname) as creaturename,npc.fakename,rt.is_player,rt.init,rt.reveal_name,rt.turn_start,rt.reveal_ac,rt.show_in_tracker 
								from round_tracker as rt 
								left join creatures as npc on npc.creatureid = rt.combatantid and rt.is_player !=1 
								left join players as pc on pc.playerid = rt.combatantid and rt.is_player = 1 
								order by init desc, rt.uid asc");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
	echo '<div class="row"><div class="list-group col-sm-offset-1 col-lg-6" id="combat_tracker_div">
       
            <ul class="list-group" id="combat_tracker_list" >';
    $count=1;
    while ($row = $result->fetch_assoc()) {
    	$acwords='';
    	$uid = $row["uid"];
    	$combatantid=$row["combatantid"];
    	$creaturename=$row["creaturename"];
    	$fakename=$row["fakename"];
    	$is_player=$row["is_player"];
    	$init=$row["init"];
    	$reveal_name=$row["reveal_name"];
    	$turn_start=$row["turn_start"];
    	$reveal_ac=$row["reveal_ac"];
    	$show_in_tracker=$row["show_in_tracker"];
    	if (!$is_player)
    	{
    		$resultb = $mysqli->query("SELECT * from creatures where creatureid=$combatantid");
    		$rowb = $resultb->fetch_assoc();
    		$creatureAC=$rowb['ac'];
    		$acwords=" <strong>AC:</strong> $creatureAC";
    	}
		echo '<li class="list-group-item" data-count="'.$count.'" data-recordid="'.$combatantid.'" data-uid="'.$uid.'"  data-isplayer="'.($is_player ? $is_player : "0").'">
				<span id="combatantid['.$combatantid.']" data-isplayer="'.($is_player ? $is_player : "0").'" data-recordid="'.$uid.'">
				Init: '.$init.' -- '. ($is_player ? $creaturename : 
				( $reveal_name ? "Displaying $creaturename" : $fakename . " [$creaturename]" )).$acwords.'</span>';
		if (!$is_player)
		{
		echo '  <span class="pull-right">
					<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_1" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="reveal_ac"'.($reveal_ac ? " checked" : "").'>Reveal AC</label>
					<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_2" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="reveal_name"'.($reveal_name ? " checked" : "").'>Reveal Name</label>
					<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_3" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="show_in_tracker"'.($show_in_tracker ? " checked" : "").'>Show in Tracker</label>
				</span>';
		}
		echo '  </li>';
				$count++;
    }	
    echo '  </ul>
        </div></div>';
    echo '<div class="row"><div class="col-sm-offset-1 col-lg-6" id="encounter_controls">
    </div></div>';

?>
	<script type="text/javascript">
		getData("encounter_controls");
	function changedvalforcreature(which)
	{
		var dbaction = $(which).data("dbaction");
		var uid = $(which).data("uid");

		var setVal=0;
		if (which.checked)
			setVal=1;

	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=changecreatureval&action="+dbaction+"&uid="+uid+"&value="+setVal,
	        success : function(text){
	            if (text == "success"){
	            	console.log("saved");
	            }
	            else 
	            {
	              console.log(text);
	            }
	        }
	    });		
	}
		
	</script>
<?php
}

function encounter_controls()
{
	global $mysqli;

    $result = $mysqli->query("SELECT count(*) as count from turn"); 
    $row = $result->fetch_assoc();
    if ($row["count"] == "0" )
    {
    	//start combat button here
    	echo '<form id="startcurrentencounterform" class="form-inline">
    			<button type="submit" class="btn btn-default" name="startenc">Start This Encounter</button>
    		  </form>';
?>
	<script type="text/javascript">
	$("#startcurrentencounterform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();

	    var this_master = $("#combat_tracker_list");
	   	this_master.find('li').each( function () {
	        var listitem = $(this);
	        if (listitem.data('count') == 1)
	        {
	        	startingcreature=listitem.data('recordid');
	        	startingcreatureuid=listitem.data('uid');
	        	startingcreatureisplayer=listitem.data('isplayer');
	        	$(listitem).addClass("active");
	        }
	        else
	        {
	        	$(listitem).removeClass("active");
	        }
		}); 
		console.log("starting creature: "+startingcreature);
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=startencounter&nextcreature="+startingcreature+"&nextcreatureuid="+startingcreatureuid+"&is_player="+startingcreatureisplayer,
	        success : function(text){
	            if (text == "success"){
					getData("encounter_controls");

	            }
	            else 
	            {
	              console.log(text);
	            }
	        }
	    });	    

	});	

	</script>
<?php    		  
    }
    else //not starting a new encounter; continue
    {
	    $result = $mysqli->query("SELECT * from turn"); 
	    $row = $result->fetch_assoc();
	    $round_number=$row['round_number'];
	    $creatureid=$row['creatureid'];
	    $uid=$row['uid'];
	    $is_player=$row['is_player'];

	    //now find out who is next
	    $get_next=false;
	    $got_next=false;
	    $result = $mysqli->query("SELECT rt.uid,rt.combatantid,COALESCE(npc.truename,pc.charname) as creaturename,npc.fakename,rt.is_player,rt.init,rt.reveal_name,rt.turn_start,rt.reveal_ac 
								from round_tracker as rt 
								left join creatures as npc on npc.creatureid = rt.combatantid and rt.is_player !=1 
								left join players as pc on pc.playerid = rt.combatantid and rt.is_player = 1 
								order by init desc, rt.uid asc"); 
		while ($row = $result->fetch_assoc()) {
			if ($get_next)
			{
				$get_next=false;
				$got_next=true;
			    $nextcreatureid=$row['creatureid'];
			    $nextuid=$row['uid'];
			    $nextis_player=$row['is_player'];
			}
			else
			{
				if ($row['uid']==$uid)
				{
					$get_next=true;
				}
			}
		}
		$nextround=$round_number;
		if (!$got_next) //didn't find a player after, the round must be over.
		{
			$nextround=$round_number+1;
		    $result = $mysqli->query("SELECT rt.uid,rt.combatantid,COALESCE(npc.truename,pc.charname) as creaturename,npc.fakename,rt.is_player,rt.init,rt.reveal_name,rt.turn_start,rt.reveal_ac 
									from round_tracker as rt 
									left join creatures as npc on npc.creatureid = rt.combatantid and rt.is_player !=1 
									left join players as pc on pc.playerid = rt.combatantid and rt.is_player = 1 
									order by init desc, rt.uid asc limit 1");	
	 	   	$row = $result->fetch_assoc();
		    $nextcreatureid=$row['creatureid'];
		    $nextuid=$row['uid'];
		    $nextis_player=$row['is_player'];											

		}

    	//continue combat stuff here
    	echo '<form id="startnextturnform" class="form-inline">
    			<button type="submit" class="btn btn-default" name="startenc">Start Next Turn</button>
    			<input type="hidden" id="creatureid" name="creatureid" value="'.$creatureid.'">
    			<input type="hidden" id="uid" name="uid" value="'.$uid.'">
    			<input type="hidden" id="is_player" name="is_player" value="'.$is_player.'">
    			<input type="hidden" id="round_number" name="round_number" value="'.$round_number.'">
    		  </form>';
?>
	<script type="text/javascript">
    var creatureid = $("#uid").val();
    console.log(creatureid);
    var this_master = $("#combat_tracker_list");
   	this_master.find('li').each( function () {
        var listitem = $(this);
        if (listitem.data('uid') == creatureid)
        {
        	$(listitem).addClass("active");
        }
        else
        {
        	$(listitem).removeClass("active");
        }
	}); 

	$("#startnextturnform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();

	    var getnextitem=false;
	    var setnextitem=false;
	    var round_number=0;
	    var this_master = $("#combat_tracker_list");
	   	this_master.find('li').each( function () {
	        var listitem = $(this);
	        if ($(listitem).hasClass('active'))
	        {	
	        	getnextitem=true;
	        	$(listitem).removeClass("active");

	        }
	        else if (getnextitem==true)
	        {
	        	nextcreature=listitem.data('recordid');
	        	nextcreatureisplayer=listitem.data('isplayer');
		        nextcreatureuid=listitem.data('uid');
	        	$(listitem).addClass("active");
	        	getnextitem=false;
	        	setnextitem=true;
	        }
		}); 
		if (setnextitem==false)
		{
		   	this_master.find('li').each( function () {
		        var listitem = $(this);
		        if (listitem.data('count') == 1)
		        {
		        	nextcreature=listitem.data('recordid');
		        	nextcreatureuid=listitem.data('uid');
		        	nextcreatureisplayer=listitem.data('isplayer');
		        	$(listitem).addClass("active");
		        }
		        else
		        {
		        	$(listitem).removeClass("active");
		        }
			}); 
		}
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=startencounter&nextcreature="+nextcreature+"&nextcreatureuid="+nextcreatureuid+"&is_player="+nextcreatureisplayer+"&roundnum="+round_number,
	        success : function(text){
	            if (text == "success"){
					getData("encounter_controls");

	            }
	            else 
	            {
	              console.log("startnextturnform: " +text);
	            }
	        }
	    });	    

	});	

	</script>
<?php   
    }	
}
function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range == 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes, $s)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}


?>