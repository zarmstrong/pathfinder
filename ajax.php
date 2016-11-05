<?php
require_once('inc/dbconn.php') ;
require_once('inc/admin-functions.php') ;
global $mysql;
error_log($_GET['function']);
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
    $query = "delete from creatures where `creatureid`='$creatureid'";
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
    error_log("IN THE DELETE");
    $combatid=$_GET["combatid"];
    $query = "delete from combats_name where `combatid`='$combatid'";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $query = "delete from combats where `combatid`='$combatid'";
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
elseif ($_POST['function'] == "attendance")
{
    $attending_players = $_POST['players'];
    //var_dump($attending_players);
    $mysqli->query("update players set present = 0");
    foreach($attending_players as $player)  
    {
        //$mystring .= "update players set present = 1 where playerid=" . $player;
        $mysqli->query("update players set present = 1 where playerid=" . $player);
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
            //$mystring .= "update players set init = $init where playerid=$playerid;\n";
            $mysqli->query("update players set init = $init where playerid=$playerid");
        }
        
    }
    $query = "select playerid, charname, LPAD(init, 2, '0') as init,LPAD(dexmod, 2, '0') as dexmod,LPAD(dex, 2, '0') as dex  from players where present=1";
    $result = $mysqli->query($query);   
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {   
        while ($row = $result->fetch_assoc()) {
            $initval = $row["init"];
            $initval="$initval.".$row["initmod"].$row["dexmod"].$row["dexscore"];
            $query = "insert into round_tracker (`combatantid`, `is_player`,`init`) VALUES ('".$row['playerid']."', '1', '$initval')";
            $resultb = $mysqli->query($query);

            if (!$resultb) {
                throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
            }
        }
    }    
    $mystring='success';
    echo $mystring;     
}    
elseif ($_POST['function'] == "monster")
{       
    //var_dump($_POST);
    //$mysqli->query("update players set init = $init where playerid=$playerid");
    $truename=$_POST["truename"];
    $fakename=$_POST["fakename"];
    $initmod=$_POST["initmod"];
    $dexmod=$_POST["dexmod"];
    $dexscore=$_POST["dexscore"];
    $ac=$_POST["ac"];
    $showtruename=$_POST["showtruename"] == "on" ? '1' : '0';
    $showac=$_POST["showac"] == "on" ? '1' : '0';

    $query = "INSERT INTO creatures (`truename`, `fakename`, `showtruename`, `initmod`, `dexmod`, `dexscore`, `ac`, `showac`) VALUES ('$truename', '$fakename', '$showtruename', '$initmod', '$dexmod', '$dexscore', '$ac', '$showac')";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        echo "success"; 
    }   
}
elseif ($_POST['function'] == "editmonster")
{
    //var_dump($_POST);
    //$mysqli->query("update players set init = $init where playerid=$playerid");
    $truename=$_POST["truename"];
    $fakename=$_POST["fakename"] ? $_POST["fakename"] : 'null';
    $initmod=$_POST["initmod"];
    $dexmod=$_POST["dexmod"] ? $_POST["dexmod"] : 'null';
    $dexscore=$_POST["dexscore"] ? $_POST["dexscore"] : 'null';
    $ac=$_POST["ac"] ? $_POST["ac"] : 'null';
    $showtruename=$_POST["showtruename"] == "on" ? '1' : '0';
    $showac=$_POST["showac"] == "on" ? '1' : '0';
    
    $creatureid=$_POST["creatureid"];
    
    $query = "update creatures set `truename`='$truename', `fakename`='$fakename', `showtruename`='$showtruename', `initmod`='$initmod', `dexmod`='$dexmod', `dexscore`='$dexscore', `ac`='$ac', `showac`='$showac' where `creatureid`='$creatureid'";
    //echo $query;
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
    $query = "INSERT INTO combats_name (`combats_name`) VALUES ('$encountername')";
    $result = $mysqli->query($query);
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {
        $combatid=$mysqli->insert_id;
        foreach (explode(",",$creature_list) as $creatureid)  
        {
            $query = "INSERT INTO combats (`combatid`, `creatureid`) VALUES ('$combatid', '$creatureid')"; 
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
    $query = "update combats_name set combats_name='$encountername' where combatid=$encounterid";
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
    $encounterid=$_POST['encounterid'];
    $query = "truncate round_tracker";
    $result = $mysqli->query($query);
    $query = "select combats.creatureid, creatures.showtruename, LPAD(creatures.initmod, 2, '0') as initmod,LPAD(creatures.dexmod, 2, '0') as dexmod,LPAD(creatures.dexscore, 2, '0') as dexscore,creatures.showac from combats left join creatures on creatures.creatureid=combats.creatureid where combatid=$encounterid";
    $result = $mysqli->query($query);

    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {   
        while ($row = $result->fetch_assoc()) {
            $d20roll = crypto_rand_secure ( 1,20 );
            $initval = ($d20roll + $row["initmod"]);
            $initval="$initval.".$row["initmod"].$row["dexmod"].$row["dexscore"];
            $query = "insert into round_tracker (`combatantid`, `init`,`reveal_name`,`reveal_ac`) VALUES ('".$row['creatureid']."', '$initval','".$row["showtruename"]."','".$row["showac"]."')";
            $resultb = $mysqli->query($query);

            if (!$resultb) {
                throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
            }
        }
    }
    echo "success";
    //add players
    /*
    $query = "select playerid, charname, LPAD(init, 2, '0') as init,LPAD(dexmod, 2, '0') as dexmod,LPAD(dex, 2, '0') as dex  from players where present=1";
    $result = $mysqli->query($query);   
    if (!$result) {
        throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
    }
    else
    {   
        while ($row = $result->fetch_assoc()) {
            $initval = $row["init"];
            echo "first init: $initval";
            $initval="$initval.".$row["initmod"].$row["dexmod"].$row["dexscore"];
            echo "last init: $initval";
            $query = "insert into round_tracker (`combatantid`, `is_player`,`init`) VALUES ('".$row['playerid']."', '1', '$initval')";
            var_dump($row);
            echo $query;
            $resultb = $mysqli->query($query);

            if (!$resultb) {
                throw new Exception("Database Error [{$mysqli->errno}] {$mysqli->error}");
            }
        }
    }
    */

}else{
    //Do nothing
}

?>