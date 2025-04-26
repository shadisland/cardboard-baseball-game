<?php
session_start();
if (!isset($_SESSION['user_role'])) {
	header('Location: login.php');
	exit();
} else {
	$user_role = $_SESSION['user_role'];
	//Check which login/access level
	//Levels:
	//admin
	//user
	//guest
	//The admin has all super powers.
	//The user is a specific user with a unique login who has been added by the admin.
	//The guest is a welcomed guest who has used a generic single login which is used for all guests.
	if( $user_role == 'admin' ){
		//Admin has logged in
		//Do nothing, admin has been authenticated
		
	} else {
		//Logged in but not with the admin account
		//Redirect to login	
		header('Location: login.php');
		exit();
	}
}
?>