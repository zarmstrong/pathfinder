<?php
require_once('inc/dbconn.php') ;
require_once('inc/functions.php') ;
global $mysql;
error_log($_GET['function']);
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
}

?>