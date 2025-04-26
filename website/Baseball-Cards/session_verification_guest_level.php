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
		//The admin account has logged in
		//Allow admin to act as a 'user'
		//Do nothing, user has been authenticated
		
	} else if( $user_role == 'user' ){
		//Not Admin account
		//Do nothing, user has been authenticated
		
	} else{
		//guest has logged in
		//Do nothing, guest has been authenticated
	}
}
?>