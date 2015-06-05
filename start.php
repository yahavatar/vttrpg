<?php
    if (session_id() == "") session_start();        //Initializes the use of sessions in PHP.
    require_once("..\php\lib_php.php");             //Additional PHP basic functions
    require_once("..\php\h_php_mysql.php");         //Additional PHP MySQL functions

	if (isset($_POST["fn"])){                       //Only process any PHP in here if 'fn' is posted
		$fn = $_POST["fn"];
		$ret = "";
		$session_id = session_id();

		//error_log("fn is posted: $fn");
		switch ($fn){
            case "current_location_display":            //Return the text of the current room for session_id
            	$location = location_get($session_id);
                $ret = location_get_description($location);
                break;
			case "user_input":
				$txt = $_POST["txt"];
				switch ($txt){
					case "e":
					case "n":
					case "s":
					case "w":
						//See if you can move in the appropriate direction
						break;
					
					case "l":
						$ret .= "look: ";
						$location = location_get($session_id);
						$ret .= location_get_description($location);
						break;
				}
				break;
		}
		echo $ret;
		die;	                                    //Prevent anything after PHP, i.e. HTML, from being processed
	}

	function location_get($session_id){
		$query = "SELECT location FROM session WHERE session_id='$session_id'";
	   	include "../php/conn_adv_localhost.php";
	   	if ($num == 0){
			mysql_close();
			//Add a new session record
			$query = "INSERT INTO session (session_id,location) VALUES ('$session_id','SE Room')";
			include "../php/conn_adv_localhost.php";
		} else {
			$location = mysql_result($result,$i,'location');
		}
		mysql_close();
		return $location;		
		
	}

	function location_get_description($location){
	    //error_log("php: current_location_get_description($location)");
	   	$query = "SELECT description_long FROM room WHERE room_key='$location'";
	   	include "../php/conn_adv_localhost.php";
		$ret = mysql_result($result,$i,'description_long');
		mysql_close();
		return $ret;
	}

?>

<html>
	<head>
		<title>Adv</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		
        <style>
			#user_input{
				width:50%;
		}
        </style>
	</head>
	<body>
        <div id='div_main'>
            Welcome!<br>
            <br>
            This is a basic screen to navigate a map.<br>
            <br>
            The map is persistent until your session changes like closing and re-opening your browser.<br>
            
        </div>
        <hr>
        <div id='div_user_input' width=100%>
            <input id='user_input' type='text' onchange='user_input_change();'>
        </div>

	</body>
	<script>
	    $(document).ready(function(){                // This is the first javascript routine that gets run after the page loads.
	        //Display current room
	        current_location_display();
        });
	
	    function current_location_display(){
			$.post( i(), {fn: "current_location_display"}, function(ret){
				$('#div_main').append("<br>" + ret);
			});
	    }

		function i(){                                // Just a short routine to get the URL of the current page
			return window.location.pathname;
		}

        function user_input_change(){
            var txt = $('#user_input').val();
            $.post( i(), {fn: "user_input", txt: txt}, function(ret){
				$('#div_main').append("<br>" + ret);
				$('#user_input').val("");
			});
        }

	</script>
</html>
