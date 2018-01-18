<?php
	session_start();
	require_once 'dbconfig.php';

	if(isset($_POST['btn-login']))
	{
		//$user_name = $_POST['user_name'];
		$user_email = trim($_POST['nombre_usuario']);
		$user_password = trim($_POST['password']);
		
		$password = ($user_password);
		// $password = ($user_password);
		// echo 'clave '.$password;
		
		try
		{	
			$sql = "SELECT * FROM fr_pass WHERE alias=:email and password = sha1(:password)";
			$stmt = $db_con->prepare($sql);
			$stmt->execute(array(":email"=>$user_email, ":password"=>$password));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$count = $stmt->rowCount();
			if($count > 0){

				echo "ok"; // log in
				$_SESSION['user_session'] = $row['alias'];
				$_SESSION['institucion']='CAPPOUCLA_';
			}
			else{
				
				echo "Nombre de usuario o clave inv&aacute;lida"; // wrong details 
			}
				
		}
		catch(PDOException $e){
			echo $e->getMessage();
			// echo 'Algo ha fallado!';
		}
	}

?>