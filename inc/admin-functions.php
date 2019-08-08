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
			      <input type="text" class="form-control" aria-label="'.$row["name"].'" name="playername" value="'.$row['name'].'" readonly>
			    </div><!-- /input-group -->
			  </div><!-- /.col-lg-6 -->
			</div>
			'  ;
	    }
	    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2">';
        echo '<button type="submit" class="btn btn-default">Save Attendance</button></form></div></div>';
        echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2" id="attenalertzone"></div></div>';
        //echo '</div>';
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
		echo '<a data-creatureid="'.$row["creatureid"].'" class="list-group-item selectableCardM" >'.$row["truename"].' ('.$row["fakename"].')      <span class="pull-right">
        <button class="btn btn-xs btn-warning delmon" value="delete" data-toggle="modal" data-function="deletecreature"  
        data-objecttype="creature" data-record-id="'.$row["creatureid"].'" data-record-title="'.$row["truename"].'" 
        data-target="#confirm-delete" id="monster_'.$row["creatureid"].'">
          <i class="glyphicon glyphicon-trash"></i>
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
  $('.selectableCardM').on('click touchstart',function(){
    var data = $(this).data();
    console.log("Boom!" + data.creatureid);
    loadMonsterEditor(data.creatureid);
  });

 $('.delmon').on('click touchstart',function(e){
    console.log("naps!" + $(this).data('recordId') + " -- " + $(this).data('function') );
    e.preventDefault();
     $('#confirm-delete').data("function",$(this).data('function'))
     $('#confirm-delete').data('recordId',$(this).data('recordId'))
     $('#confirm-delete').modal({
     	show: true,
     	backdrop: 'static',
     	keyboard: true});
  });	  
	</script>
<?php
}

function create_new_monster_form()
{
	//monster form
	echo '<form id="monstersform" enctype="multipart/form-data" class="form-horizontal">';
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
		      <input type="text" pattern="[-]?[0-9]*"  class="form-control" name="initmod" id="initmod" placeholder="0">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexscore" class="col-sm-6 control-label">Dexterity Score</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[0-9]*" class="form-control" name="dexscore" id="dexscore" placeholder="0">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexmod" class="col-sm-6 control-label">Dexterity Modifier</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[-]?[0-9]*" class="form-control" name="dexmod" id="dexmod" placeholder="0" readonly>
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="ac" class="col-sm-6 control-label">Armor Class</label>
		    <div class="col-sm-6">
		      <input type="text" class="form-control" name="ac" id="ac" placeholder="0">
		    </div>
		  </div>
		  <div class="form-group">
		  	<span class="col-sm-6"/>
		    <div class="input-group col-sm-6">	
                <label class="input-group-btn">
                    <span class="btn btn-primary">
                        Browse&hellip; <input id="monsterimage" type="file" style="display: none;" >
                    </span>
                </label>
                <input type="text" class="form-control" readonly>
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
	$("#dexscore").on("input",function(e){
	 if($(this).data("lastval")!= $(this).val()){
	     $(this).data("lastval",$(this).val());
	     //change action
	     dexscore=$(this).val();
	     dexmod=Math.floor((dexscore-10)/2)
	     console.log(dexmod);  
	     $("#dexmod").val(dexmod);
	 };
	});		
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
// Loop through each of the selected files.
		var formData = new FormData($('#monstersform')[0]);
		formData.append("function","monster");
		//formData.append($('#monstersform').serialize());

		var file = $('#monsterimage')[0].files[0];
		// Check the file type.
		if (file)
		{
			if (!file.type.match('image.*'))
				return;
			// Add the file to the request.
			console.log("adding a pic");
			formData.append('file', $('#monsterimage')[0].files[0]); 
			console.log(formData);			
		}


	    // Initiate Variables With Form Content
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: formData,
	        processData: false, // Don't process the files
	        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
	        cache: false,
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
	$('#monsterimage').on('change',  function() {
		var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [numFiles, label]);
	});
      $('#monsterimage').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
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
	echo '<div class="form-group">
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
		      <input type="text" pattern="[-]?[0-9]*" class="form-control" name="initmod" id="initmod" value="'.$row['initmod'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexscore" class="col-sm-6 control-label">Dexterity Score</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[0-9]*" class="form-control" name="dexscore" id="dexscore" value="'.$row['dexscore'].'">
		    </div>
		  </div>	
		  <div class="form-group">
		    <label for="dexmod" class="col-sm-6 control-label">Dexterity Modifier</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[-]?[0-9]*" class="form-control" name="dexmod" id="dexmod" placeholder="0" readonly>
		    </div>
		  </div>			  
		  <div class="form-group">
		    <label for="ac" class="col-sm-6 control-label">Armor Class</label>
		    <div class="col-sm-6">
		      <input type="text" pattern="[0-9]*" class="form-control" name="ac" id="ac" value="'.$row['ac'].'">
		    </div>
		  </div>';

		  	$image=$row['image'];
		  	if ($image)
		  	{
		  		echo '
				  <div class="form-group">
				  	<span class="col-sm-6"/>		  		
					<div id="creatureimage" class="col-sm-offset-6 col-sm-6"><span class="col-sm-2 hovereffect"><img src="uploads/'.$row['image'].'" class="img-thumbnail" alt="Creature image">
					<input type="hidden" value="'.$row['image'].'" name="editmonsterimage" id="editmonsterimage">
						<div class="overlay col-sm-6 text-center">
		           			<h2>Remove this image?</h2>
		           			<a class="info" id="removeimage" data-creatureid="'.$creatureid.'"><span class="glyphicon glyphicon-trash text-center"></span></a>
		        		</div>
					</div>
				  </div>';
		  }		
		  echo '
		  <div class="form-group">
		  	<span class="col-sm-6"/>
		    <div class="input-group col-sm-6">	
                <label class="input-group-btn">
                    <span class="btn btn-primary">
                        Browse&hellip; <input id="monsterimage" type="file" style="display: none;" >
                    </span>
                </label>
                <input type="text" class="form-control" readonly>
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
	echo '<input type="hidden" id="creatureid" name="creatureid" value="'.$creatureid.'">';
	echo '<div class="col-sm-offset-5 col-sm-3"><button type="submit" value="edit" class="btn btn-default">Save Changes</button></form></div>
	      <div class="col-sm-3"><form id="newmonstersform" class="form-inline"><button type="submit" value="new" class="btn btn-default">Save as New Monster</button></form></div>';

	echo '<div class="row">&nbsp;</div><div class="row">&nbsp;</div>
			<div class="row">
				<div class="col-sm-offset-5 col-sm-3">
					<form id="clearmonstersform" class="form-inline">
						<button type="submit" value="clear" class="btn btn-default" >Clear Form</button>
					</form>
				</div>
			</div>';
    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-sm-offset-6 col-sm-4" id="monsteralertzone"></div></div>';
    echo '</div>';	
?>
	<script type="text/javascript">

	$("#dexscore").on("input",function(e){
	 if($(this).data("lastval")!= $(this).val()){
	     $(this).data("lastval",$(this).val());
	     //change action
	     dexscore=$(this).val();
	     dexmod=Math.floor((dexscore-10)/2)
	     console.log(dexmod);  
	     $("#dexmod").val(dexmod);
	 };
	});	
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
		var formData = new FormData($('#editmonstersform')[0]);
		formData.append("function","editmonster");

		var file = $('#monsterimage')[0].files[0];
		// Check the file type.
		if (file)
		{
			if (!file.type.match('image.*'))
				return;
			// Add the file to the request.
			formData.append('file', $('#monsterimage')[0].files[0]); 
		}

	    // Initiate Variables With Form Content
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: formData,
	        processData: false, // Don't process the files
	        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
	        cache: false,	        
	        success : function(text){
	            if (text == "success"){
					showmonsteralert('Saved','alert-success');
					getData("newcreature","edit",$("#creatureid").val());

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
	$("#removeimage").click(function(event){
	    var data = $(this).data();
		console.log(data.creatureid);

	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=removeimage&uid=" + data.creatureid,
	        success : function(text){
	            if (text == "success"){
					$("#creatureimage").fadeOut(500,function() { $("#creatureimage").remove(); });
	            }
	            else 
	            {
	              console.log(text);
	            }
	        }
	    });		
	});
	$('#monsterimage').on('change',  function() {
		var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [numFiles, label]);
	});
      $('#monsterimage').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });	
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
		echo '<a id="ae_'.$row["combatid"].'" data-combatid="'.$row["combatid"].'" class="list-group-item selectableCard" >'.$row["combats_name"].'      <span class="pull-right">';
	    echo '<button class="btn btn-xs btn-warning delbut" value="delete" data-function="deleteencounter" 
	    		data-objecttype="encounter" data-record-id="'.$row["combatid"].'" data-record-title="'.$row["combats_name"].'" 
	    		data-target="#deleteencountermodal" id="encounter_'.$row["combatid"].'" onclick="$(\'#deleteencountermodal\').modal(\'show\');">
	          	<span class="glyphicon glyphicon-trash"></span>
	          </button></a>';
    }	

?>
	<script type="text/javascript">
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
  $('.delbut').on('click touchstart',function(e){
    console.log("nips!" + $(this).data('recordId') + " -- " + $(this).data('function') );
    e.preventDefault();
     $('#deleteencountermodal-title').html($(this).data('recordTitle'));
     $('#deleteencountermodal').data("function",$(this).data('function'))
     $('#deleteencountermodal').data('recordId',$(this).data('recordId'))
     $('#deleteencountermodal').modal({
     	show: true,
     	backdrop: 'static',
     	keyboard: true});
  });	  

  $('#deleteencountermodal').on('click', '.btn-ok', function(e) {
    var $modalDiv = $(e.delegateTarget);
    var id = $('#deleteencountermodal').data('recordId');
    var func = $('#deleteencountermodal').data('function');
    $modalDiv.addClass('loading');
    $.post('ajax.php?function='+func+'&combatid=' + id, function(data)
    {       
    }).then(function() {
	    $('#deleteencountermodal').data('recordId','');
	    $('#deleteencountermodal').data('function','');
	    $modalDiv.modal('hide').removeClass('loading');
      getData("encounterlist");
    })
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
					getData("encounterlist");
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
	Encounter List</label>';
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
    $result = $mysqli->query("SELECT * FROM turn LIMIT 1"); 
    $row = $result->fetch_assoc();
    $round_number=$row['round_number'];
    if (!$round_number) //not yet set, so make it round 1
        $round_number=1;

	$result = $mysqli->query("SELECT rt.uid,rt.combatantid,COALESCE(npc.truename,pc.charname) as creaturename,npc.fakename,rt.is_player,rt.init,rt.reveal_name,rt.turn_start,rt.reveal_ac,rt.show_in_tracker,rt.killed,tm.marker_desc,pc.heropoints,
								rt.deaf,rt.blind,rt.mute,rt.burn,rt.sleep,rt.stone,rt.slow,rt.haste,rt.unconcious,rt.stuck,rt.invisible,rt.prone,rt.enlarge,rt.shrink,rt.bleeding,rt.fear,rt.confused,rt.burning,rt.paralysed
								from round_tracker as rt 
								left join creatures as npc on npc.creatureid = rt.combatantid and rt.is_player !=1 
								left join players as pc on pc.playerid = rt.combatantid and rt.is_player = 1 
								left join tokenmarkers as tm on tm.tid = rt.tokenmarker and rt.is_player != 1 
								order by init desc, rt.uid asc");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
	echo '<div class="row"><div class="list-group col-sm-offset-1 col-lg-6"><h4>Round <span name="roundnum" id="roundnum">'.$round_number.'</h4></div></div>';
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
    	$killed=$row["killed"];
    	$show_in_tracker=$row["show_in_tracker"];
    	$tokenmarker = $row["marker_desc"];
    	$tokeninfo=null;
		$heropoints=$row['heropoints'];
    	$statusEffects=array(
			['deaf','Deaf'],
			['blind','Blind'],
			['mute','Mute'],
			['burn','Burning'],
			['sleep','Sleeping'],
			['stone','Petrified'],
			['slow','Slowed'],
			['haste','Hastened'],
			['unconcious','Unconcious'],
			['stuck','Entangled/Stuck'],
			['invisible','Invisible'],
			['prone','Prone'],
			['enlarge','Enlarged'],
			['shrink','Shrunken'],
			['bleeding','Bleeding'],
			['fear','Afraid'],
			['confused','Confused'],
			['burning','Burning'],
			['paralysed','Paralysed']
		);
    	foreach ($statusEffects as $effectName) {
    		if (!is_array($effectName))
		    	$$effectName=$row["$effectName"];
		    else
		    {
		    	$name=$effectName[0];
		    	${$name}=$row["$name"];
		    }
		    	
		}		
    	if (!$is_player)
    	{
    		$resultb = $mysqli->query("SELECT * from creatures where creatureid=$combatantid");
    		$rowb = $resultb->fetch_assoc();
    		$creatureAC=$rowb['ac'];
    		$acwords=" <strong>AC:</strong> $creatureAC";
    		if ($tokenmarker)
    			$tokeninfo=" [$tokenmarker] ";
    	}
		echo '<li class="list-group-item" data-count="'.$count.'" data-recordid="'.$combatantid.'" data-uid="'.$uid.'"  data-isplayer="'.($is_player ? $is_player : "0").'">
				<span id="combatantid['.$combatantid.']" data-isplayer="'.($is_player ? $is_player : "0").'" data-recordid="'.$uid.'">
				Init: <input type="text" maxlength="12" size="10" style="background-color:#adadad;" id="'.$uid.'-'.$combatantid.'-'.($is_player ? $is_player : "0").'" name="init-'.$combatantid.'" value="'.$init.'" > -- '. ($is_player ? $creaturename : 
				( $reveal_name ? "Displaying $creaturename" : $fakename . " [$creaturename]" )).(isset($tokeninfo) ? $tokeninfo : "").$acwords.' </span>';
		echo '<span class="badge">';
    	if ($is_player)
    	{
			for ($points = 1; $points <= $heropoints; $points++) 
			{
				echo '<i class="glyphicon glyphicon-star"></i>';
			}
		}
		echo '</span>';				
		echo '<script type="text/javascript">
				$("input[name=init-'.$combatantid.']").change(function() {
					newinit=$(this).val();
					combatant=$(this).attr("id");
					//console.log("new init: " + newinit + " combatant id: " + combatant);
					//$("#startnextturnform").submit();
				    $.ajax({
				        type: "POST",
				        url: "ajax.php",
				        data: "function=changeinit&combatantinfo="+combatant+"&newinit="+newinit,
				        success : function(text){
				            if (text == "success"){
				            	console.log("I GOT--: " + text);
								getData("combatzone","showroundtracker");
				            }
				            else 
				            {
				              console.log("I GOT: " + text);
				            }
				        }
				    });	
		  
				});
			  </script>
		';
		echo '  <p class="list-group-item-text">';
		if (!$is_player)
		{
		echo '
					<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_1" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="reveal_ac"'.($reveal_ac ? " checked" : "").'>Reveal AC</label>
					<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_2" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="reveal_name"'.($reveal_name ? " checked" : "").'>Reveal Name</label>
					<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_3" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="show_in_tracker"'.($show_in_tracker ? " checked" : "").'>Show in Tracker</label>
					<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_4" id="checkbox_'.$row["uid"].'_4" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="killed"'.($killed ? " checked" : "").'>Killed</label>';
		
		}
		echo '		<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_5" type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="make_it_my_turn">Change to My Turn</label>';

		echo '  </p>';

		echo '  <p class="list-group-item-text">';		  
		echo '	<label>Status Effects:</label>  ';

		$incrementer=1;
		$arraySize=count($statusEffects);
    	foreach ($statusEffects as $effectName) {
    		if (!is_array($effectName))
    		{
		    	$name=$effectName;
		    	$capName=ucfirst($name);
		    }
		    else
		    {
		    	$name=$effectName[0];
		    	$capName=ucfirst($effectName[1]);				    	
		    }
			echo '<label><input onchange="changedvalforcreature(this)" name="checkbox_'.$row["uid"].'_'.(5+$incrementer).'" id="'.$name.'_'.$row["uid"].'_'."checkbox".'"  type="checkbox" data-uid="'.$row["uid"].'" data-dbaction="'.$name.'" '. (${$name} >0 ? 'checked' : '').'>'.$capName.'</label> <input  onchange="changedvalforcreature(this)" type="text" maxlength="2" data-uid="'.$row["uid"].'" data-dbaction="'.$name.'" size="1" id="'.$name.'_'.$row["uid"].'_round" name="'.$name.'_'.$row["uid"].'_round" value="'.(${$name} >0 ? ${$name} : $round_number+2).'">';
			if ($incrementer % 6 == 0)
				echo "<br/>";
			else
				if ($incrementer != $arraySize)
				echo ' <strong>|</strong>  ';
		    $incrementer++;
		}	 		

		echo '  </p>
		  </li>';
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
		//console.log("which : " + $(which).is(':checked') + " uid: " + uid + " action: " + dbaction)
		if ($(which).is(':checkbox'))
		{
			var setVal=0;
			if ($(which).is(':checked') == true)
				setVal=1;
		}
		if (dbaction == "make_it_my_turn")
		{
			func="changewhoseturn"
			which.checked = false;
		}
		else
			func="changecreatureval"
		//console.log ("Thing: " + "input#"+dbaction+"_"+uid+"_round")
		if ( $("input#"+dbaction+"_"+uid+"_round").val())
		{
			//console.log( "VALUE: " + $("input#"+dbaction+"_"+uid+"_round").val())
			//console.log("is object input#"+dbaction+"_"+uid+"_checkbox" + " checked?  " +$("input#"+dbaction+"_"+uid+"_checkbox").val());
			if ($("input#"+dbaction+"_"+uid+"_checkbox").is(':checked') )
				setVal=$("input#"+dbaction+"_"+uid+"_round").val();
			else
				setVal=0;

		}
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function="+func+"&action="+dbaction+"&uid="+uid+"&value="+setVal,
	        success : function(text){
	            if (text == "success"){
	            	console.log("saved");
	            }
	            if (text == "successchangewhoseturn"){
            		getData("encounter_controls");
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
    	echo "<br/>";
    	echo '<form id="changeroundform" class="form-inline">
    	    	<input type="text" maxlength="3" size="3" id="new_round_number" name="new_round_number" value="'.$round_number.'">
    	    	<button type="submit" class="btn btn-default" name="changeround">Set Round</button>
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
	$("#changeroundform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();
	    newroundnum=$("input#new_round_number").val();
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=changeroundnum&newround="+newroundnum,
	        success : function(text){
	            if (text == "success"){
	            	console.log("round changed to " + newroundnum);
	            	$("span#roundnum").html(newroundnum);
	            }
	            else 
	            {
	              console.log("changeroundform: " +text);
	            }
	        }
	    });		    
	});
	$("#startnextturnform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();

	    var getnextitem=false;
	    var setnextitem=false;
	    var round_number=0;
	    var activeid;
	    var this_master = $("#combat_tracker_list");
	   	this_master.find('li').each( function () {
	        var listitem = $(this);
	        //console.log(listitem);
	        if ($(listitem).hasClass('active'))
	        {	
	        	getnextitem=true;
	        	$(listitem).removeClass("active");
	        }
	        else if (getnextitem==true)
	        {
	        	activeid=$(listitem).data("uid");
	        	console.log("for activeid #"+activeid);
	        	//console.log($("input#checkbox_"+activeid+"_4").is(':checked'))
	        	if ($("input#checkbox_"+activeid+"_4").is(':checked') == false )
	        	{
	        		console.log("not checked");
		        	nextcreature=$(listitem).data('recordid');
		        	nextcreatureisplayer=$(listitem).data('isplayer');
			        nextcreatureuid=$(listitem).data('uid');
		        	$(listitem).addClass("active");
		        	getnextitem=false;
		        	setnextitem=true;
		        	console.log(nextcreature + " " + nextcreatureisplayer + " " +nextcreatureuid + " " +getnextitem  + " " + setnextitem)
		        }
	        	else
		    		console.log("checked");
	        }
	        if (setnextitem)
	        	return;
		}); 
		if (setnextitem==false)
		{
			console.log("didnt find next. starting at the top");
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
		console.log ("do the post with the data: function=startencounter&nextcreature="+nextcreature+"&nextcreatureuid="+nextcreatureuid+"&is_player="+nextcreatureisplayer+"&roundnum="+round_number);
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

function create_creature_markers()
{
	echo '<div class="row"><div class="col-sm-offset-1 col-lg-4">
	<label>
	Creature Marker List (click to edit)</label>
	<div class="list-group" id="markerlist">';
	list_markers();

    echo '</div></div>';
    echo '<div class="col-lg-4" id="newmarker">';
	
	//encounter form
    create_new_marker_form();

    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2"></div><div class="col-lg-2" id="encounteralertzone"></div></div>';
    echo '</div>';
}

function list_markers()
{
	global $mysqli;
	$result = $mysqli->query("SELECT * from tokenmarkers ");
	if (!$result) {
	    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
	    return;
	}	
    while ($row = $result->fetch_assoc()) {
		echo '<a data-tokenid="'.$row["tid"].'" class="list-group-item selectableCardT" >'.$row["marker_desc"].'<span class="pull-right">
        <button class="btn btn-xs btn-warning " value="delete" data-toggle="modal" data-function="deletetoken"  data-title="'.$row["marker_desc"].'" data-objecttype="token" data-record-id="'.$row["tokenid"].'" data-target="#confirm-delete" id="tid_'.$row["tokenid"].'">
          <i class="glyphicon glyphicon-trash"></i>
        </button></a>';
    }	
?>
	<script type="text/javascript">
	function loadTokenEditor(tokenid){
		if ($(document.activeElement).val() == "delete")
		{
			//delete was triggered elsewhere
		}
		else
			getData("newtoken","edit",tokenid)
	}
  $('.selectableCardT').on('click touchstart',function(){
    var data = $(this).data();
    loadTokenEditor(data.tokenid);
  });

	</script>
<?php
}

function create_new_marker_form()
{
	echo '<form id="newtokenform" class="form-horizontal">';		
	echo '
	<div class="row">
	  <div class="col-lg-6">
	    <div class="input-group">
	      <input type="text" class="form-control" aria-label="marker_desc" name="marker_desc" value="" >
	    </div><!-- /input-group -->
	  </div><!-- /.col-lg-6 -->
	</div>
	'  ;
    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2">';
    echo '<button type="submit" class="btn btn-default">Create Token</button></form></div></div>';
    echo '<div class="row">&nbsp;</div><div class="row"><div class="col-lg-2" id="tokenalertzone"></div></div>';
?>
	<script type="text/javascript">

	$("#newtokenform").submit(function(event){
	    // cancels the form submission
	    event.preventDefault();
	    submitTokenForm();
	}); 
	function submitTokenForm(){
	    // Initiate Variables With Form Content
	$('#newtokenform').serialize();
	 
	    $.ajax({
	        type: "POST",
	        url: "ajax.php",
	        data: "function=createtoken&" + $('#newtokenform').serialize(),
	        success : function(text){
	            if (text == "success"){
					showtokenalert('Saved','alert-success');
	            }
	            else 
	            {
	            	showtokenalert(text,'alert-warning',6000);
	            }
	        }
	    });
	    getData("markerlist");
	    getData("newmarker");	    
	}
	  function showtokenalert(message,alerttype,fadedelay=2000) {
	    $('#tokenalertzone').append('<div id="alertdiv" class="alert .out ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
	    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
	    	$("#alertdiv").remove();
	    }, fadedelay);
	  }
	</script>
<?php        
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