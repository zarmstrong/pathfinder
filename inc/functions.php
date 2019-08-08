<?php

function show_turn_data()
{
	global $mysqli;

    $result = $mysqli->query("SELECT count(*)  as count from turn"); 
    $row = $result->fetch_assoc();
    if ($row["count"] == "0" )
   		echo "<h1>Combat has not yet begun...</h1>";
   	else {
		//get the current thing's turn.
	    $result = $mysqli->query("SELECT * from turn"); 
	    $row = $result->fetch_assoc();
	    $round_number=$row['round_number'];
	    if (!$round_number) //not yet set, so make it round 1
	        $round_number=1;
	    $current_combatantid=$row['uid'];

		$result = $mysqli->query("SELECT rt.uid,rt.combatantid,COALESCE(npc.truename,pc.charname) as creaturename,npc.fakename,npc.image,rt.is_player,rt.init,rt.reveal_name,rt.turn_start,rt.reveal_ac,rt.show_in_tracker,rt.killed,tm.marker_desc 
									from round_tracker as rt 
									left join creatures as npc on npc.creatureid = rt.combatantid and rt.is_player !=1 
									left join players as pc on pc.playerid = rt.combatantid and rt.is_player = 1 
									left join tokenmarkers as tm on tm.tid = rt.tokenmarker and rt.is_player != 1 
									order by init desc, rt.uid asc");
		if (!$result) {
		    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
		    return;
		}	
		echo '<div class="row">
				<div class="col-xs-12"><!-- h1>Combat Tracker</h1 --></div>
				<div class="col-xs-12" id="combat_turn_div">';
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
	    	$image=$row["image"];
	    	$iskilled=$row['killed'];
	    	$tokenmarker = $row["marker_desc"];
	    	$tokeninfo=null;	
    		if ($tokenmarker)
    			$tokeninfo=" [$tokenmarker] ";								    	    	
	    	if ($current_combatantid == $uid)
	    	{
	    		$current_creature_uid=$uid;
		    	if ($show_in_tracker)
		    	{
		    		if ($is_player == '1')
		    		{
						echo '<h2>Current turn: '.$creaturename.'</h2>'
							 .'<span id="nextcreaturename"></span><br/>';
						$turntime=60;
						$timeleft=(strtotime($turn_start)+$turntime)-time();
						if ($timeleft <1)
						{
							if ($timeleft == 0)
								echo '<script type="text/javascript">play_turnend_sound();</script>';							
							$timeleft=0;
							$timeclass='col-xs-5 alert-danger';
							$timewords="<h1>Time's up!</h1>";
						}
						elseif ($timeleft <10)
						{

							$timeclass='col-xs-7 alert-warning';
							$timewords="<h2>$timeleft SECONDS LEFT.</h2>";
						}
						else
						{
							if ($timeleft == ($turntime-1))
								echo '<script type="text/javascript">play_turnstart_sound();</script>';
							$timeclass='col-xs-5 alert-success';
							$timewords="<h3>$timeleft seconds left.</h3>";
						}

						echo '<div class=" alert '.$timeclass.'" role="alert">'.$timewords.'</div>';
								
					}
					else
					{
						//not a player, but show this monster data
						echo '<h2>Current turn: '.($reveal_name ? "$creaturename" : $fakename ).(isset($tokeninfo) ? $tokeninfo : ""). '</h2>'
							 .'<span id="nextcreaturename"></span><br/>';
						if (isset($image))
							echo '<span class="col-xs-7 "><img src="uploads/'.$image.'" class="img-thumbnail" alt="Responsive image"></span>';

					}
				}
				else
				{
					//don't show this creature in the tracker. 
						//not a player, but show this monster data
						echo '<h2>Current turn: Unknown</h2>'
							 .'<span id="nextcreaturename"></span><br/>';
				}
	    	}
	    	elseif (isset($current_creature_uid) and !(isset($next_creature_uid)))
	    	{
	    		if (!$iskilled)
	    		{
		    		$next_creature_uid=$uid;
			    	if ($show_in_tracker)
			    	{
			    		if ($is_player == '1')
			    		{	    
			    			$words="<h3>On Deck: $creaturename</h3>";
			    		}
			    		else
			    		{
			    			$words="<h3>On Deck: ".($reveal_name ? "$creaturename" : $fakename ).(isset($tokeninfo) ? $tokeninfo : "").'</h3>';
			    		}
			    	}		
			    	else
			    		$words="<h3>On Deck: unknown</h3>";
			    	?>
			    	<script type="text/javascript">
			    		$('#nextcreaturename').html('<?php echo addslashes($words); ?>');
			    	</script>
			    	<?php
			    }
	    	}
	    }	
	    if (isset($current_creature_uid) and !(isset($next_creature_uid)))
	    {
			$result = $mysqli->query("SELECT rt.uid,rt.combatantid,COALESCE(npc.truename,pc.charname) as creaturename,npc.fakename,rt.is_player,rt.init,rt.reveal_name,rt.turn_start,rt.reveal_ac,rt.show_in_tracker,tm.marker_desc 
										from round_tracker as rt 
										left join creatures as npc on npc.creatureid = rt.combatantid and rt.is_player !=1 
										left join players as pc on pc.playerid = rt.combatantid and rt.is_player = 1 
										left join tokenmarkers as tm on tm.tid = rt.tokenmarker and rt.is_player != 1 
										order by init desc, rt.uid asc limit 1");
			if (!$result) {
			    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
			    return;
			}	
			$row = $result->fetch_assoc();
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
	    	$tokenmarker = $row["marker_desc"];
	    	$tokeninfo=null;

	    	if ($show_in_tracker)
	    	{
	    		if ($is_player == '1')
	    		{	    
	    			$words="<h3>On Deck: $creaturename (New Round)</h3>";
	    		}
	    		else
	    		{
		    		if ($tokenmarker)
		    			$tokeninfo=" [$tokenmarker] ";		    			
	    			$words="<h3>On Deck: ".($reveal_name ? "$creaturename" : $fakename ).(isset($tokeninfo) ? $tokeninfo : "").'  (New Round)</h3>';
	    		}
	    	}		
	    	else
	    		$words="<h3>On Deck: Unknown (New Round)</h3>";    		
	    	?>
	    	<script type="text/javascript">
	    		$('#nextcreaturename').html('<?php echo addslashes($words); ?>');
	    	</script>
	    	<?php
	    }
	    echo '  </div>
	    	  </div>';
	}
?>

<?php           
}


function show_legend()
{
	echo '<div class="row">
		<div class="col-xs-12"><h1>Legend</h1></div>
';

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
	$arraySize=sizeof($statusEffects);
	echo "Number of items: $arraySize<br/>";
	$loopMax=8;
	$colcount=ceil($arraySize / $loopMax);
	echo "Number of columns: $colcount<br/>";
	$loopcount=1;
	switch ($colcount) {
    case 1:
        $colsize=12;
        break;
    case 2:
        $colsize=6;
        break;
    case 3:
        $colsize=4;
        break;
    case 4:
        $colsize=3;
        break;
	}
	echo '<div class="row">';
	foreach ($statusEffects as $effectName)
	{	
		if ($loopcount == 1)
			echo '<div class="col-xs-'.$colsize.'">';
		if (!is_array($effectName))
			echo '<i title="'.$effectName.'" class="glyphicon icon-legend-'.$effectName.'"></i> '.ucfirst("$effectName").'<br>';
	    else
	    {
	    	$name=$effectName[0];
	    	$capName=ucfirst($effectName[1]);
	    	echo '<i title="'.$name.'" class="glyphicon icon-legend-'.$name.'"></i> '.$capName.'<br>';
	    }
		if ($loopcount == $loopMax)
		{
			$loopcount=0;
			echo '</div>';	
		}
		$loopcount++;
	}	
	echo '  </div>
		  </div>';	
}

function show_round_info()
{
	global $mysqli;

    $result = $mysqli->query("SELECT count(*)  as count from turn"); 
    $row = $result->fetch_assoc();
    if ($row["count"] == "0" )
   		echo "<h1>Waiting for combat to start.</h1>";
   	else {
		//get the current thing's turn.
	    $result = $mysqli->query("SELECT * from turn"); 
	    $row = $result->fetch_assoc();
	    $round_number=$row['round_number'];
	    if (!$round_number) //not yet set, so make it round 1
	        $round_number=1;
	    $current_combatantid=$row['uid'];

		$result = $mysqli->query("SELECT rt.uid,rt.combatantid,COALESCE(npc.truename,pc.charname) as creaturename,npc.fakename,npc.image,rt.is_player,rt.init,rt.reveal_name,rt.turn_start,rt.reveal_ac,rt.show_in_tracker,rt.killed,tm.marker_desc,pc.heropoints,
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
		echo '<div class="row">
				<div class="col-xs-12"><h1>Combat Tracker - Round '.$round_number.'</h1></div>
				<div class="col-xs-12" id="combat_tracker_div">';
	    echo '<ul class="list-group">';
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
	    	$killed=$row["killed"];
	    	$tokenmarker = $row["marker_desc"];
	    	$tokeninfo=null;
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

	    	if ($show_in_tracker)
	    	{
	    		$badgestart='<span class="badge badge-info pull-right">';
	    		$badgetext="";

		    	foreach ($statusEffects as $effectName) {
		    		if (!is_array($effectName))
		    		{
				    	$badgetext.= (${$effectName} >= $round_number ? '<i title="'.$effectName.'" class="glyphicon icon-'.$effectName.'"></i>': ""); 
				    }
				    else
				    {
				    	$name=$effectName[0];
				    	$capName=ucfirst($effectName[1]);				    	
				    	$badgetext.= (${$name} >= $round_number ? '<i title="'.$name.'" class="glyphicon icon-'.$name.'"></i>': ""); 
				    }
				}	    		

	    		$badgeend='</span>';
	    		
	    		if ($is_player == '1')
	    		{
					echo '<li class="list-group-item list-group-item-success">'.$creaturename.$badgestart.$badgetext.$badgeend.'</li>';
				}
				else
				{
					$acwords="";
					$deathwords="";
		    		if ($reveal_ac)
		    		{
			    		$resultb = $mysqli->query("SELECT * from creatures where creatureid=$combatantid");
			    		$rowb = $resultb->fetch_assoc();
			    		$creatureAC=$rowb['ac'];
			    		$acwords='<strong>AC:</strong> '.$creatureAC.'';
		    		}
		    		if ($killed)
		    			$deathwords='<i class="glyphicon-skull"></i>';
		    		if ($tokenmarker)
		    			$tokeninfo=" [$tokenmarker] ";		    		
					//not a player, but show this monster data

					echo '<li class="list-group-item list-group-item-danger ">'.($current_combatantid == $uid ?  '<i title="arrow-right" class="glyphicon icon-right-arrow"></i>  ' : '').'<strong>'.($reveal_name ? "$creaturename" : $fakename ).'</strong>'.$deathwords.(isset($tokeninfo) ? $tokeninfo : "").$badgestart.$acwords.$badgetext.$badgeend.'</li>';

				}
			}
	    }
	    echo '</ul>';
	    echo '  </div>
	    	  </div>';	    
	}
}

?>