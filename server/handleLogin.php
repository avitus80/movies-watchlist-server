<?php
  include 'connect.php';

  // header('Content-Type: text/plain'); // return text
  header('Content-Type: application/json'); // return JSON
  
  $loggedIn = false;
  $errorMessage = "";

  // sanitise user details
  function sanitise($user) {
    $sanitisedUser = array(
      "username" => filter_var($user['username'], FILTER_SANITIZE_STRING),
      "password" => filter_var($user['password'], FILTER_SANITIZE_STRING)
    );

    return $sanitisedUser;
  }

  // validate user details
  function validate($user) {
    global $errorMessage;

    try {
      if (strlen($user['username']) < 3) {
        throw new Exception("Invalid username.");
      }

      if (strlen($user['password']) < 3) {
        throw new Exception("Invalid password.");
      }

    } catch (Exception $e) {
      $errorMessage = $e->getMessage();
      return false;
    }

    return true;
  }

  // read incoming content (JSON)
  $loginJsonObj = file_get_contents('php://input');
  // decode content (true = to array)
  $loginData = json_decode($loginJsonObj, true);

  // sanitise login details
  $sanitisedData = sanitise($loginData);

  // validate login details
  $isValid = validate($sanitisedData);

  if ($isValid === true) {
    // get user from database
    global $mysqli;
    $query = "SELECT username FROM users WHERE username=? AND password=?";
    $statement = $mysqli->prepare($query);
    $statement->bind_param("ss", $sanitisedData['username'], $sanitisedData['password']);
    $statement->execute();
    $dbResult = $statement->get_result();
    $statement->close();
    
    // check result
    if ($dbResult->num_rows > 0) {
      $loggedIn = true;
    } else {
      $errorMessage = "Invalid username/password";
    }
  }

  // create array
  $result = array('username'=>$sanitisedData['username'], 'loggedIn'=>$loggedIn, 'errorMessage'=>$errorMessage);

  // encode into JSON object
  echo json_encode($result);
?>