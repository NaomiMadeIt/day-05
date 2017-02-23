<?php
//begin or resume the session
session_start();
//hide notices because they are ugly
error_reporting( E_ALL & ~ E_NOTICE );
//begin parsing the form if the user submitted it
if( $_POST['submitted-login']){
  //TEMPORARY: the correct credentials for logging in; TODO: replace with DB stuff later
  $correct_username = 'gabe';
  $correct_password = 'phprules1010';

  //clean the values the user typed in:
  $username = trim(strip_tags($_POST['username']));
  $password = trim(strip_tags($_POST['password']));

  if ( strlen($username) >=5 and strlen($username) <= 25 and strlen($password) >= 5 ) {
    //check to see if they matched both the UN and PW
    if ( $username === $correct_username and $password === $correct_password ) {
      //remember the user for one week
      setcookie('logged-in-cookie', true, time() + 60 * 60 * 24 * 7 );
      $_SESSION['logged-in-session'] = true;
      //send to secret page
      header('location:secret-page.php');
    }else{
      //show error message
      $feedback = 'The information that was entered is incorrect. Please try again.';
    }
  }else{
    //show correct message
    $feedback = 'Username or password is not the correct length.';
  } //end of validation

} //end of form parser

//is the user trying to log out?
if ( $_GET['action'] == 'logout' ) {
  // Unset all of the session variables.
  $_SESSION = array();

  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
    $params["path"], $params["domain"],
    $params["secure"], $params["httponly"]
  );
}
//destroy the session
session_destroy();
//force cookies to expire
setcookie('logged-in-cookie', '', time() - 9999999);
} //end of logout logic

//do not close php when the document only contains php
