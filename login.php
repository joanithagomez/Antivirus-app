<?php

session_start();

if ($_SESSION['username']) {
    header("Location: malwareupload.php");
}

$username = $password = "";
echo <<<_END
<html><head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>Admin Login</title></head>
_END;

if (isset($_POST['user'])) {
    $username = fix_string($_POST['user']);
}

if (isset($_POST['pass'])) {
    $password = fix_string($_POST['pass']);
}

$fail = validate_username($username);
$fail .= validate_password($password);

if ($fail == "") {
    loggingin($username, $password);
}
// else {
//   echo $fail;
// }

echo <<<_END
<script>
function validate(form){
  fail = validateUsername(form.user.value)
  fail += validatePassword(form.pass.value)

  if (fail == "") {
    return true}
  else {
    document.getElementById("error").innerHTML = fail;
    return false
  }
}

function validateUsername(field) {
  if(field == ""  || field.trim() == "") return "No Username was entered. <br>"
  else return ""
}

function validatePassword(field) {
  if (field == "") return "No Password was entered.<br>"
  return ""
}
</script>
<body>
<div class="container">
<h2>Admin Login</h2>
<form action="login.php" method="post" onsubmit="return validate(this)">
<pre>
Username <input type="text" name="user" value="$username">
Password <input type="text" name="pass" value="$password">
<input type="submit" value="Sign In">
</pre>
</form>
<div id="error"></div>
</div></body>
</html>
_END;



function validate_username($field)
{
    if ($field == "" || trim($field) == "")
        return "No Username was entered<br>";
    return "";

}

function validate_password($field)
{
    if ($field == "") {
        return "No Password was entered<br>";
    }
    return "";
}


function loggingin(){
  include_once 'db.php';

  if (!empty($_SESSION['message'])) {
      $message = $_SESSION['message'];
      echo "<br> $message";
  }

  if (isset($_POST['user']) && isset($_POST['pass'])) {
      $un_temp = mysql_entities_fix_string($conn, $_POST['user']);
      $pw_temp = mysql_entities_fix_string($conn, $_POST['pass']);

      $query = "SELECT * FROM users WHERE username='$un_temp'";
      $result = $conn->query($query);

      if (!$result) {
          die($conn->error);
      } elseif ($result->num_rows) {
          $row = $result->fetch_array(MYSQLI_NUM);
          $result->close();

          $salt1 = $row[4];
          $salt2 = $row[5];
          $token = hash('ripemd128', "$salt1$pw_temp$salt2");
          if ($token == $row[3]) {
              session_start();
              $_SESSION['username'] = $un_temp;
              $_SESSION['name'] = $row[0];
              $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] .
            $_SERVER['HTTP_USER_AGENT']);
            setcookie('username',$un_temp, time() + 60 * 60 * 24 * 7, '/');
              header("Location: malwareupload.php");
          // die();
          } else {

              die("Invalid username/password combination");
          }
      } else {
          die("Invalid username/password combination");
      }
  } else { // if username and password are not set
      die("Please enter your username and password");
  }

  $conn->close();
}

function fix_string($string){
    if(get_magic_quotes_gpc()) $string = stripslashes($string);
    return htmlentities($string);
  }

function mysql_entities_fix_string($connection, $string)
{
    return htmlentities(mysql_fix_string($connection, $string));
}

function mysql_fix_string($connection, $string)
{
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }
    return $connection->real_escape_string($string);
}
