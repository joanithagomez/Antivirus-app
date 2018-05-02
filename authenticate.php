<?php
echo <<<_END
<form action="login.php" method="post">
<pre>
<h2>Admin Login</h2>
Username <input type="text" name="user">
Password <input type="text" name="pass">
<input type="submit" value="Sign In">
</pre>
</form>
_END;

$conn = new mysqli('localhost', 'root', '', 'cs174');

if ($conn->connect_error)
{
    die($conn->connect_error);
}

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

        $salt1 = "qm&h*";
        $salt2 = "pg!@";
        $token = hash('ripemd128', "$salt1$pw_temp$salt2");
        if ($token == $row[3])
        {
            echo "$row[0] $row[1] : Hi $row[0], you are now logged in as '$row[2]' <br>";
            require 'malwareupload.php';
            require 'logout.php';

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
