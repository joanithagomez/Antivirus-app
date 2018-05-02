<?php
echo <<<_END
<html><head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<title>Admin Login</title></head>
<body>
<div class="container">
<form action="login.php" method="post">
<pre>
<h2>Admin Login</h2>
Username <input type="text" name="user">
Password <input type="text" name="pass">
<input type="submit" value="Sign In">
</pre>
</form>
_END;

include_once 'db.php';

if (isset($_POST['user']) && isset($_POST['pass']))
{
    $un_temp = mysql_entities_fix_string($conn, $_POST['user']);
    $pw_temp = mysql_entities_fix_string($conn, $_POST['pass']);

    $query = "SELECT * FROM users WHERE username='$un_temp'";
    $result = $conn->query($query);

    if (!$result)
    {
        die($conn->error);
    }
    elseif ($result->num_rows)
    {
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();

        $salt1 = $row[5];
        $salt2 = $row[4];
        $token = hash('ripemd128', "$salt1$pw_temp$salt2");
        if ($token == $row[3])
        {
          session_start();
          $_SESSION['username'] = $un_temp;
          $_SESSION['name'] = $row[0];
            header("Location: malwareupload.php");
            // die();
        }
        else
        {
            die("Invalid username/password combination");
        }
    }
    else
    {
        die("Invalid username/password combination");
    }
}
else
{ // if username and password are not set
    die("Please enter your username and password");
}

$conn->close();


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

echo "</div></body></html>";
