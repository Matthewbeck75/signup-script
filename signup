<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
session_start();
session_destroy();
session_unset();

$params = session_get_cookie_params();	
if ($params['httponly'] != TRUE) {
			session_set_cookie_params(NULL, NULL, NULL, TRUE, TRUE); //secure only (set to true) / http only
			}
session_start();


$postdata = array();
parse_str($_POST['data'], $postdata); //Puts all form data into $postdata array
$errors = array();
require('../mysqli.php');


if (isset($_SESSION['user_ID'])) { //if user is already logged in then exit
$data = array(
				'status' => 'Failure', 
				'reason' => "Already logged in - refresh the page"
				);	
echo json_encode($data);
exit();	
}
if (empty($postdata['signup_username'])) { 
	$errors[] = 'No username given';
} else { 
	if (!preg_match('/^[A-Za-z0-9_-]*$/', $postdata['signup_username'])) { //Username can only contain letters, numbers, - and _
		$errors[] = 'Username can only contain letters, numbers, - and _';
	} else { //if username characters are valid
		if (strlen($postdata['signup_username']) >= 31) { 
			$errors[] = 'Username too long';
		}	else {
		$username = $mysqli->real_escape_string(substr($postdata['signup_username'], 0, 30)); //check if username exists
		$q = "SELECT ID FROM users_table WHERE username = '$username' LIMIT 1"; 
		$r = $mysqli->query($q);
		$numrows = mysqli_num_rows($r);
		if ($numrows == 1){
			$errors[] = 'Username already taken';
		} 
	}
	}
}
if (empty($postdata['signup_email'])) {
	$errors[] = 'No email given';
} elseif  (filter_var($postdata['signup_email'], FILTER_VALIDATE_EMAIL) == FALSE)  {
	$errors[] = 'Email is not valid';
} elseif (strlen($postdata['signup_email']) >= 200) {
	$errors[] = 'Email too long, limit is 200 characters';
}

if (empty($postdata['signup_password'])) {
	$errors[] = 'No password';
}
if (empty($postdata['signup_password_re'])) { //as new password was required twice
	$errors[] = 'No password';
} else {
	if (strlen($postdata['signup_password_re']) <= 7 ) {
		$errors[] = 'Password too short, must be at least 8 characters';
	} else {
		if ($postdata['signup_password'] != $postdata['signup_password_re']) {
			$errors[] = 'Passwords do not match';
		}
	}
}
require_once "includes/recaptchalib.php";
$secret = "SECRET GOOGLE RECAPTCHA API KEY HERE";
$response = null;
$reCaptcha = new ReCaptcha($secret);

if ($postdata["g-recaptcha-response"]) {
	 	$response = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $postdata["g-recaptcha-response"]	
 );
 if ($response != null && $response->success) {
 	//success!
 } else {
 	$errors[] = 'Google has identified you as a spammer. If this is not the case, please reload the page and try again.';
 }
} else { //if recaptcha not filled out
	$errors[] = 'Please tick the box to show you are not a robot.';
}

if (empty($errors)) {
			
	$safeusername = $mysqli->real_escape_string($postdata['signup_username']);
	$safeemail = $mysqli->real_escape_string(substr($postdata['signup_email'], 0, 200));
	
	$salt = substr(base64_encode(openssl_random_pseudo_bytes(32)), 0, 32);//works on windows and linux
	$salted = $salt . $postdata['signup_password_re'];
	$password_hashed = hash('SHA256', $salted);
	
	
				
	$q= "INSERT INTO `users` (`ID`, `username`, `email`, `password`, `salt`) VALUES (NULL, '$safeusername', '$safeemail', '$password_hashed', '$salt');";
	$r = $mysqli->query($q);
	
	if ($mysqli->affected_rows == 1) {
	$r = $mysqli->query($u);
	$q = "SELECT ID, username, email FROM users WHERE username = '$safeusername' AND email = '$safeemail' LIMIT 1";
	$r = $mysqli->query($q);//As ID is automatically created by MySQL, we must select the row after creation to retrieve it
	$numrows = mysqli_num_rows($r);
	if ($numrows == 1) { //if row returned therefore username and password match those entered
	$auth = $r->fetch_assoc();
		$ID = $auth['ID'];
		
		$_SESSION['user_ID'] = $auth['ID'];
		$_SESSION['username'] = $auth['username'];
		$_SESSION['email'] = $auth['email'];
		$safeuserhtml = htmlentities($safeusername);
		
	
		
		
		$data = array(
				'status' => 'Success',
				'username' => "$safeuserhtml"
				);
		$IP = $_SERVER['REMOTE_ADDR']; //to keep record of logins
		$a = "INSERT INTO user_login (`user_ID`, `IP`) VALUES ('$auth[ID]', '$IP')";
		$r = $mysqli->query($a);			
	} else {
		exit();
	}			
	} else {
		$data = array(
				'status' => 'Failure',
				'reason' => 'Signup Failed - Possibly invalid details?'
				);
	}			
} else { //errors is not empty
		$data = array( //only shows first error, if JS is turned on then unlikely there would be more than one.
				'status' => 'Failure', 
				'reason' => "$errors[0]"
				);
}

}

?>
