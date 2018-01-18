<?php
// include("head.php");
@session_start();
	
	// PHP5 Implementation - uses MySQLi.
	// mysqli('localhost', 'yourUsername', 'yourPassword', 'yourDatabase');
	require("dbconfig.php");
//	$db = new mysqli($Servidor,$Usuario, $Password, $_SESSION['empresa']);
	
	if(!$db_con) {
		// Show error if we cannot connect.
		echo 'ERROR: Could not connect to the database.';
	} else {
		// Is there a posted query string?
		if(isset($_POST['queryString'])) {
			$queryString = $_POST['queryString'];
			
			// Is the string length greater than 0?
			
			if(strlen($queryString) >0) {
				// Run the query: We use LIKE '$queryString%'
				// The percentage sign is a wild-card, in my example of countries it works like this...
				// $queryString = 'Uni';
				// Returned data = 'United States, United Kindom';
				
				// YOU NEED TO ALTER THE QUERY TO MATCH YOUR DATABASE.
				// eg: SELECT yourColumnName FROM yourTable WHERE yourColumnName LIKE '$queryString%' LIMIT 10

				$niveles=7; // $_SESSION['maxnivel'];
				$filtro="SELECT cue_codigo, cue_nombre FROM cuentas WHERE ((cue_codigo LIKE '$queryString%') or (cue_nombre LIKE '$queryString%')) and (cue_nivel = '".$niveles ."') order by cue_codigo LIMIT 10";
				
				$query = $db_con->prepare($filtro);
				$query->execute();
				if($query) {
					// While there are results loop through them - fetching an Object (i like PHP5 btw!).
					while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
						// Format the results, im using <li> for the list, you can change it.
						// The onClick function fills the textbox with the result.
						
						// YOU MUST CHANGE: $result->value to $result->your_colum
	         			echo '<li onClick="fill2(\''.$result['cue_codigo'].'\');">'.$result['cue_codigo'].' - '.$result['cue_nombre'].'</li>';
	         		}
				} else {
					echo 'ERROR: There was a problem with the query.'.$filtro;
				}
			} else {
				// Dont do anything.
			} // There is a queryString.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
?>