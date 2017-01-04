<?php
require_once('inc/dbconn.php') ;
require_once('inc/functions.php') ;
global $mysql;
if ($_GET['function']=='initform' or $_GET['function']=='leftcolumn')
{
    show_turn_data();
}
elseif ($_GET['function']=='middlecolumn')
{
    show_round_info();
}
else{
    //Do nothing
    //error_log($_GET['function']);
}

?>