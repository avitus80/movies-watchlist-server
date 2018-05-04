<?php
  include 'connect.php';

  header('Content-Type: application/json'); // return JSON

  $isRegistered = false;
  $errorMessage = "";

  // sanitise user details
  function sanitise($user) {
    $sanitisedUser = array(
      "username" => filter_var($user['username'], FILTER_SANITIZE_STRING),
      "password" => filter_var($user['password'], FILTER_SANITIZE_STRING),
      "firstname" => filter_var($user['firstname'], FILTER_SANITIZE_STRING),
      "lastname" => filter_var($user['lastname'], FILTER_SANITIZE_STRING),
      "email" => filter_var($user['email'], FILTER_SANITIZE_EMAIL)
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

      if (strlen($user['firstname']) < 3) {
        throw new Exception("Invalid first name.");
      }

      if (strlen($user['lastname']) < 3) {
        throw new Exception("Invalid last name.");
      }

      if (strlen($user['email']) < 3) {
        throw new Exception("Invalid email.");
      }      
    } catch (Exception $e) {
      $errorMessage = $e->getMessage();
      return false;
    }

    return true;
  }

  // read incoming content (JSON)
  $signupJsonObj = file_get_contents('php://input');
  // decode content to array
  $signupData = json_decode($signupJsonObj, true);

  // sanitise user details
  $sanitisedData = sanitise($signupData);
 
  // validate user details
  $isValid = validate($sanitisedData);
  
  if ($isValid === true) {
    // insert user into database
    global $mysqli;
    $query = "INSERT INTO users (username, password, firstname, lastname, email) VALUES (?, ?, ?, ?, ?)";
    $statement = $mysqli->prepare($query);
    $statement->bind_param("sssss", $sanitisedData['username'], $sanitisedData['password'], $sanitisedData['firstname'], $sanitisedData['lastname'], $sanitisedData['email']);
    $dbResult = $statement->execute();
    $statement->close();

    // check result
    if ($dbResult === true) {
      $isRegistered = true;
    } else {
      $errorMessage = $mysqli->error;
    }
  }

  // create array
  $result = array('isRegistered'=>$isRegistered, 'errorMessage'=>$errorMessage);

  // encode into JSON object
  echo json_encode($result);
?>