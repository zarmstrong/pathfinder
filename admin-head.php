    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Pathfinder Tools</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#attendance" onclick="getData('presentform')">Attendance</a></li>
            <!-- li><a href="#init" onclick="getData('initform')">Initiative Tracker</a></li -->
            <li><a href="#monsters" onclick="getData('monsterform')">Monster Manager</a></li>
            <li><a href="#combat" onclick="getData('encounterform')">Combat Manager</a></li>
            <li><a href="#markers" onclick="getData('creaturemarkers')">Creature Markers</a></li>
            <li><a href="#turntracker" onclick="getData('combattracker')">Combat Tracker</a></li>            
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('inc/dbconn.php') ;

?>

  <script type="text/javascript">

  </script>