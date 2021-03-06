<?php
require_once('inc/dbconn.php') ;
require_once('inc/admin-functions.php') ;
global $mysql;
error_log("Function: get: ".$_GET['function']. " post: ". $_POST['function']);
if ($_GET['function']=='initform')
{
    add_inits_form();
}
elseif ($_GET['function']=='presentform')
{
    create_newsession_form();
}
elseif ($_GET['function']=='monsterform')
{
        add_monster_form();
}
elseif ($_GET['function']=='newcreature')
{
    if ($_GET['param1'] == 'edit')
    {
        edit_monster_form($_GET['param2']);
    }
    else
    {
        create_new_monster_form();
    }    
}
elseif ($_GET['function']=='editcreature')
{
    edit_monster_form($_GET['creatureid']);
}
elseif ($_GET['function']=='monsterlist')
{
    create_monster_list();
}
elseif ($_GET['function'] == "deletecreature")
{
    $creatureid=$_GET["creatureid"];
    $query = "DELETE from creatures where `creatureid`='$creatureid'";
    error_log($query);
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        echo "success"; 
    } 
}
elseif ($_GET['function']=='encounterform')
{
    combat_manager();
}
elseif ($_GET['function']=='newencounter')
{
    if ($_GET['param1'] == 'edit')
    {
        edit_encounter_form($_GET['param2']);
    }
    else
    {
        create_new_encounter_form();
    }    
}
elseif ($_GET['function']=='editencounter')
{
    edit_encounter_form($_GET['encounterid']);
}
elseif ($_GET['function']=='encounterlist')
{
    create_encounter_list();
}
elseif ($_GET['function']=='deleteencounter')
{
    $combatid=$_GET["combatid"];
    $query = "DELETE from combats_name where `combatid`='$combatid'";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $query = "DELETE from combats where `combatid`='$combatid'";
        $result = $mysqli->query($query);
        if (!$result) {
            throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
        }
        else
        {
            echo "success"; 
        }  
    }     
}
elseif ($_GET['function']=='combattracker')
{
    create_combat_tracker();
}
elseif ($_GET['function']=='combatzone')
{
    if ($_GET['param1'] == 'playerinits')
    {
        add_inits_form();
    }
    elseif ($_GET['param1'] == 'showroundtracker')
    {
        show_round_tracker();
    }
}
elseif ($_GET['function']=='encounter_controls')
{
    encounter_controls();
}
elseif ($_GET['function']=='creaturemarkers')
{
    create_creature_markers();
}
elseif ($_GET['function']=='newmarker')
{
    create_new_marker_form();
}
elseif ($_GET['function']=='markerlist')
{
    list_markers();
}
elseif ($_POST['function'] == "attendance")
{
    $attending_players = $_POST['players'];
    //var_dump($attending_players);
    $mysqli->query("UPDATE players set present = 0");
    foreach($attending_players as $player)  
    {
        //$mystring .= "UPDATE players set present = 1 where playerid=" . $player;
        $mysqli->query("UPDATE players set present = 1 where playerid=" . $player);
    }
    $mystring='success';
    echo $mystring;
}
elseif ($_POST['function'] == "inits")
{
    $attending_players = $_POST['playerinit'];
    foreach($attending_players as $playerid => $init)  
    {
        if ($init)
        {
            //$mystring .= "UPDATE players set init = $init where playerid=$playerid;\n";
            $mysqli->query("UPDATE players set init = $init where playerid=$playerid");
        }
        
    }
    $query = "SELECT  playerid, charname, init, LPAD(initmod, 2, '0') as initmod,LPAD(dexmod, 2, '0') as dexmod,LPAD(dex, 2, '0') as dex  from players where present=1";
    $result = $mysqli->query($query);   
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {   
        while ($row = $result->fetch_assoc()) {
            $initval = $row["init"];
            $initval="$initval.{$row['initmod']}{$row['dexmod']}{$row['dex']}";
            error_log("{$row['charname']} -- $initval");
            $query = "INSERT into round_tracker (`combatantid`, `is_player`,`init`) VALUES ('".$row['playerid']."', '1', '$initval')";
            error_log($query);
            $resultb = $mysqli->query($query);

            if (!$resultb) {
                throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
            }
        }
    }    
    $mystring='success';
    echo $mystring;     
}
elseif ($_POST['function'] == "changeroundnum")
{
    $round_number=$_POST["newround"];
    $query = "UPDATE turn SET round_number = $round_number"; 
    error_log($query);
    $result = $mysqli->query($query); 
    echo "success";
    return;         
}

elseif ($_POST['function'] == "changeinit")
{
    $combataninfo=explode('-',$_POST["combatantinfo"]);
    $combatantUID=$combataninfo[0];
    $combatantID=$combataninfo[1];
    $combatantIsPlayer=$combataninfo[2];
    $newinit=$_POST["newinit"];    
    $checkturn = "SELECT *, count(*) as count FROM turn WHERE uid = $combatantUID and creatureid = $combatantID and is_player = $combatantIsPlayer";
    error_log("Query: $checkturn");
    $checkresult = $mysqli->query($checkturn);    

    if (!$checkresult) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
        echo "fail";
        return;
    }
    else
    {   
        $row = $checkresult->fetch_assoc();
        error_log("count is: " . $row['count']);
        if ($row['count'] == "0"){
            $updatequery = "UPDATE round_tracker SET init='$newinit' WHERE uid = $combatantUID and combatantid = $combatantID and is_player = $combatantIsPlayer";
            error_log("not current turn, just updating where needed. $updatequery");
            $updateresult = $mysqli->query($updatequery);    
            echo "success";  
            return;          
        }
        else // it's this guys turn... this is gonna suuuuck.
        {
            error_log("current turn, lots of shit to do");
            $combatantinfo = "SELECT init FROM round_tracker WHERE uid = $combatantUID AND combatantid = $combatantID AND is_player = $combatantIsPlayer";
            error_log($combatantinfo);
            $combatantresult = $mysqli->query($combatantinfo);    
            $combatantrow = $combatantresult->fetch_assoc();
            $currentInit = $combatantrow['init'];
            if ($newinit == $currentInit)
            {
                //init didn't change, just return ok.
                echo "success";
                return;
            }
            else
            {   
                $query = "SELECT * FROM round_tracker WHERE init < $currentInit AND killed = 0 ORDER BY init DESC LIMIT 1";
                error_log($query);
                $whoisnext = $mysqli->query($query);
                $whoisnextRes = $whoisnext->fetch_assoc();
                $nextUID=$whoisnextRes['uid'];
                $nextID=$whoisnextRes['combatantid'];
                $nextIsPlayer=$whoisnextRes['is_player'];

                $result = $mysqli->query("SELECT * FROM turn LIMIT 1"); 
                $row = $result->fetch_assoc();
                $round_number=$row['round_number'];
                if (!$round_number) //not yet set, so make it round 1
                    $round_number=1;

                //find the last combatant in the turn
                $query = "SELECT  * FROM round_tracker ORDER BY init ASC, uid DESC LIMIT 1";
                $result = $mysqli->query($query); 
                $row = $result->fetch_assoc();
                if ($combatantUID == $row['uid'])
                    $round_number++;

                if ($combatantIsPlayer)
                    $mysqli->query("UPDATE players SET init = $newinit WHERE playerid=$combatantID");
                $updatequery = "UPDATE round_tracker SET init='$newinit', turn_start=now() WHERE uid = $combatantUID and combatantid = $combatantID and is_player = $combatantIsPlayer";
                error_log($updatequery);
                $updateresult = $mysqli->query($updatequery);                    

                if ($nextIsPlayer)
                {
                    $updatequery = "UPDATE round_tracker SET turn_start=now() WHERE uid = $nextUID and combatantid = $nextID and is_player = $nextIsPlayer";
                    error_log($updatequery);
                    $updateresult = $mysqli->query($updatequery);                    
                }


                $query = "truncate turn";
                $result = $mysqli->query($query);   
                $query = "INSERT INTO turn (`uid`,`round_number`, `creatureid`, `is_player`) VALUES ('$nextUID','$round_number', '$nextID','$nextIsPlayer')"; 
                error_log($query);
                $result = $mysqli->query($query); 
                echo "success";
                return;                                 
            }
        }
    }
    echo "fail";
}
elseif ($_POST['function'] == "monster")
{
    $target_dir = realpath(dirname(__FILE__))."/uploads/";
    $truename=$_POST["truename"];
    $fakename=$_POST["fakename"];
    $initmod=$_POST["initmod"];
    $dexmod=$_POST["dexmod"];
    $dexscore=$_POST["dexscore"];
    $ac=$_POST["ac"];
    $showtruename=$_POST["showtruename"] == "on" ? '1' : '0';
    $showac=$_POST["showac"] == "on" ? '1' : '0';
    $editmonsterimage=$_POST["editmonsterimage"];
    if ($editmonsterimage)
        $imageFileType = pathinfo(basename($editmonsterimage),PATHINFO_EXTENSION);    
    else
        $imageFileType = pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION);    

    $query = "INSERT into creatures (`truename`, `fakename`, `showtruename`, `initmod`, `dexmod`, `dexscore`, `ac`, `showac`) VALUES ('$truename', '$fakename', '$showtruename', '$initmod', '$dexmod', '$dexscore', '$ac', '$showac')";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $creatureUID=$mysqli->insert_id;
        $new_file_name="creature_". $creatureUID.".$imageFileType";
        $target_file = ($target_dir . $new_file_name);
        if ($_FILES["file"]){
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
            {
                $query = "UPDATE creatures set `image`='".$new_file_name."' where creatureid=$creatureUID";
                $result = $mysqli->query($query);
                if (!$result)
                {
                    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
                }
                else
                {
                    echo "success"; 
                } 
            }
            else
                echo "File upload failed. See php logs.";
        }
        else
        {
            if ($editmonsterimage)
            {
                copy($target_dir.$editmonsterimage,$target_file);
            }
            $query = "UPDATE creatures set `image`='".$new_file_name."' where creatureid=$creatureUID";
            $result = $mysqli->query($query);
            if (!$result)
            {
                throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
            }
            else
            {
                echo "success"; 
            }             
        }
    }   
}
elseif ($_POST['function'] == "editmonster")
{
    $target_dir = realpath(dirname(__FILE__))."/uploads/";
    $truename=$_POST["truename"];
    $fakename=$_POST["fakename"] ? $_POST["fakename"] : 'null';
    $initmod=$_POST["initmod"];
    $dexmod=$_POST["dexmod"] ? $_POST["dexmod"] : 'null';
    $dexscore=$_POST["dexscore"] ? $_POST["dexscore"] : 'null';
    $ac=$_POST["ac"] ? $_POST["ac"] : 'null';
    $showtruename=$_POST["showtruename"] == "on" ? '1' : '0';
    $showac=$_POST["showac"] == "on" ? '1' : '0';
    $creatureid=$_POST["creatureid"];
    $new_file_name=null;
    if (isset($_FILES["file"]))
    {
        $imageFileType = pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION);
        $new_file_name="creature_". $creatureid.".$imageFileType";
        $target_file = ($target_dir . $new_file_name); 
        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
    }
    $query = "UPDATE creatures set `truename`='$truename', `fakename`='$fakename', `showtruename`='$showtruename', `initmod`='$initmod', `dexmod`='$dexmod', `dexscore`='$dexscore', `ac`='$ac', `showac`='$showac', `image`=".($new_file_name ? "'$new_file_name'" : "null")." where `creatureid`='$creatureid'";
    error_log( $query);
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        echo "success"; 
    } 
}
elseif ($_POST['function'] == "encounter")
{
    $encountername = $_POST['combats_name'];
    $creature_list=trim($_POST['creaturelist'],",");
    $query = "INSERT into combats_name (`combats_name`) VALUES ('$encountername')";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $combatid=$mysqli->insert_id;
        foreach (explode(",",$creature_list) as $creatureid)  
        {
            $query = "INSERT into combats (`combatid`, `creatureid`) VALUES ('$combatid', '$creatureid')"; 
            $result = $mysqli->query($query);
            if (!$result) {
                throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
            }           
        }  
        echo "success";
    }
}
elseif ($_POST['function'] == "editencounter")
{
    $encountername = $_POST['combats_name'];
    $creature_list=trim($_POST['creaturelist'],",");
    $encounterid=$_POST['combatid'];
    $query = "UPDATE combats_name set combats_name='$encountername' where combatid=$encounterid";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $query = "delete from combats where combatid=$encounterid";
        $result = $mysqli->query($query);
        if (!$result) {
            throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
        }
        else
        {
            foreach (explode(",",$creature_list) as $creatureid)  
            {
                $query = "INSERT INTO combats (`combatid`, `creatureid`) VALUES ('$encounterid', '$creatureid')"; 
                error_log( $query);
                $result = $mysqli->query($query);
                if (!$result) {
                    throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
                }           
            }  
        echo "success";
        }
    }   
}
elseif ($_POST['function'] == "createandloadcombat")
{
    $query = "TRUNCATE turn";
    $result = $mysqli->query($query);       
    $encounterid=$_POST['encounterid'];
    $query = "truncate round_tracker";
    $result = $mysqli->query($query);
    $query = "SELECT  combats.creatureid, creatures.showtruename, LPAD(creatures.initmod, 2, '0') AS initmod,LPAD(creatures.dexmod, 2, '0') AS dexmod,LPAD(creatures.dexscore, 2, '0') AS dexscore,creatures.showac FROM combats LEFT JOIN creatures ON creatures.creatureid=combats.creatureid WHERE combatid=$encounterid";
    $result = $mysqli->query($query);

    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {   
        while ($row = $result->fetch_assoc()) {
            $d20roll = crypto_rand_secure ( 1,20 );
            $initval = ($d20roll + $row["initmod"]);
            error_log("$initval.".$row["initmod"].$row["dexmod"].$row["dexscore"]);
            $initval="$initval.".$row["initmod"].$row["dexmod"].$row["dexscore"];
            $query = "INSERT into round_tracker (`combatantid`, `init`,`reveal_name`,`reveal_ac`) VALUES ('".$row['creatureid']."', '$initval','".$row["showtruename"]."','".$row["showac"]."')";
            $resultb = $mysqli->query($query);

            if (!$resultb) {
                throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
            }
        }
    }
    //populate an array of all the tokenmarkers
    $query = "SELECT GROUP_CONCAT(tid) AS TIDs FROM tokenmarkers";
    $resultMarkers = $mysqli->query($query);
    $resultMarkersRow = $resultMarkers->fetch_assoc();
    $resultMarkersTIDs=array_reverse(explode(",",$resultMarkersRow['TIDs']));

    $query = "SELECT GROUP_CONCAT(uid) AS UIDs, combatantid, COUNT(*) c FROM round_tracker GROUP BY combatantid HAVING c > 1";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {   
 
        while ($row = $result->fetch_assoc()) 
        {
            $rownum=1;            
            foreach (explode(",",$row['UIDs']) as $uid)
            {
                if ($rownum>1)
                {
                    $thistid=array_pop($resultMarkersTIDs);
                    $updatequery = "UPDATE round_tracker set tokenmarker='$thistid' where uid='$uid'";
                    $updateresult = $mysqli->query($updatequery);
                }
                $rownum++;
            }
        }
    }
    echo "success";
}
elseif ($_POST['function'] == "startencounter")
{
    $nextcreature=$_POST['nextcreature'];
    $is_player=$_POST['is_player'];
    $uid=$_POST['nextcreatureuid'];

    // find out/set/increment round number
    $result = $mysqli->query("SELECT  * from turn"); 
    $row = $result->fetch_assoc();
    $round_number=$row['round_number'];
    if (!$round_number) //not yet set, so make it round 1
        $round_number=1;
    $current_combatantid=$row['uid'];

    //find the last combatant in the turn
    $query = "SELECT  * from round_tracker order by init asc, uid desc limit 1";
    $result = $mysqli->query($query); 
    $row = $result->fetch_assoc();
    if ($current_combatantid == $row['uid'])
        $round_number++;

    $query = "select uid,combatantid,is_player,killed from round_tracker order by init asc, uid desc";
    $result = $mysqli->query($query); 
    $getnext=0;
    while ($row = $result->fetch_assoc()) 
    {
        if (($row['uid'] == $uid) and ($row['killed']==1))
        {
            $getnext=1;
            echo "getnext";
        }
        elseif (($row['uid'] == $uid) and ($row['killed']==0))
        {
            echo "not dead, use this guy.";
            $uid=$row['uid'];
            $nextcreature=$row['combatantid'];
            $is_player=$row['is_player'];    
            break;        
        }
        elseif ($getnext)
        {
            echo "getting next";
            if ($row['killed']==0)
            {
                echo "not dead - use this one";
                $getnext=0;
                $uid=$row['uid'];
                $nextcreature=$row['combatantid'];
                $is_player=$row['is_player'];
            }
            else
                echo "dead, trying next";
        }
    }

    if ($is_player)
    {  
        $query="UPDATE round_tracker set turn_start = now() where uid = $uid";
        $result = $mysqli->query($query);  
    }
    $query = "truncate turn";
    $result = $mysqli->query($query);    
    $query = "INSERT into turn (`uid`,`round_number`, `creatureid`, `is_player`) VALUES ('$uid','$round_number', '$nextcreature','$is_player')"; 
    $result = $mysqli->query($query);  

    echo "success";
}
elseif ($_POST['function'] == "changecreatureval")
{
    $uid=$_POST['uid'];
    $value=$_POST['value'];    
    $action=$_POST['action'];
    switch ($action)
    {
        case "show_in_tracker":
            $field="";
        break;
        case "reveal_name":
            $field="";
        break;
        case "reveal_ac":
            $field="";        
        break;
        case "killed":
            $field="";        
        break;        
    }
    $query="UPDATE round_tracker set $action = $value where uid = $uid";
    error_log($query);
    $result = $mysqli->query($query);  
}
elseif ($_POST['function'] == "changewhoseturn")
{
    $uid=$_POST['uid'];
    $value=$_POST['value'];    
    $action=$_POST['action'];


    $query = "SELECT * FROM round_tracker WHERE uid = $uid";
    $whoisnext = $mysqli->query($query);
    $whoisnextRes = $whoisnext->fetch_assoc();
    $nextUID=$whoisnextRes['uid'];
    $nextID=$whoisnextRes['combatantid'];
    $nextIsPlayer=$whoisnextRes['is_player'];

    $result = $mysqli->query("SELECT * FROM turn LIMIT 1"); 
    $row = $result->fetch_assoc();
    $round_number=$row['round_number'];
    if (!$round_number) //not yet set, so make it round 1
        $round_number=1;

    error_log("is player?  $nextIsPlayer -- q UPDATE round_tracker SET turn_start=now() WHERE uid = $nextUID and combatantid = $nextID and is_player = $nextIsPlayer");
    if ($nextIsPlayer)
        $mysqli->query("UPDATE round_tracker SET turn_start=now() WHERE uid = $nextUID and combatantid = $nextID and is_player = $nextIsPlayer");

    $query = "truncate turn";
    $result = $mysqli->query($query);   
    $query = "INSERT INTO turn (`uid`,`round_number`, `creatureid`, `is_player`) VALUES ('$nextUID','$round_number', '$nextID','$nextIsPlayer')"; 
    error_log($query);
    $result = $mysqli->query($query); 
    echo "successchangewhoseturn";
    return;             
}
elseif ($_POST['function'] == "removeimage")
{
    $uid=$_POST['uid'];
    $query = "select image from creatures where creatureid=$uid";
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    if (!$result)
    {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $filename=$row['image'];
        $query = "UPDATE creatures set `image`=null where creatureid=$uid";
        $result = $mysqli->query($query);
        if (!$result)
        {
            throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
        }
        else
        {
            $target_dir = realpath(dirname(__FILE__))."/uploads/";
            $target_file = ($target_dir . $filename);
            unlink($target_file);
            echo "success"; 
        } 
    }
}
elseif ($_POST['function'] == "createtoken")
{
    $marker_desc=$_POST['marker_desc'];
    $query = "select count(*) as count from tokenmarkers where marker_desc='$marker_desc'";
    $result = $mysqli->query($query);
    if (!$result)
    {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $row = $result->fetch_assoc();
        if ($row['count']>0)
        {
            echo "Already Exists";
            return;
        }
        $query = "INSERT into tokenmarkers (`marker_desc`) VALUES ('$marker_desc')"; 
        $result = $mysqli->query($query);  
        if (!$result)
        {
            throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
        }
        else
        {
            echo "success"; 
        } 
    }

}else{
    //Do nothing
}

?>