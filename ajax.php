<?php
require_once('inc/dbconn.php') ;
require_once('inc/admin-functions.php') ;
global $mysql;
if ($_GET['function']=='initform')
{
    add_inits_form();
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
    echo $mystring;
}
elseif ($_POST['function'] == "inits")
{
    $attending_players = $_POST['playerinit'];
    foreach($attending_players as $playerid => $init)  
    {
        if ($init)
        {
            $mystring .= "update players set init = $init where playerid=$playerid;\n";
            $mysqli->query("update players set init = $init where playerid=$playerid");
        }
        
    }
    echo $mystring;        
}else{
    //Do nothing
}

?>