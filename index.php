<?php

echo <<<_END
  <html><head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>Malware Detector</title></head>
  <body>
  <div class="container">
  <h1>Malware Detector</h1>
  <form method="post" action="index.php" enctype="multipart/form-data">
  Upload your file:
  <input type="file" name="filename">
  <input type="submit" class="btn btn-primary" value="Submit">
  </form>
_END;


  include_once 'db.php';

  if ($_FILES) { //check if user uploaded a file

      $name = strtolower(preg_replace("[^A-Za-z0-9.]", "", $_FILES['filename']['name']));  //sanitizing name of the file to work in any OS

      move_uploaded_file($_FILES['filename']['tmp_name'], $name);  //moving file from the temporary location to permanent one
      echo "File upload successful!<br>";
      $contents = getContents($conn, $name); //parse file and get the words in file as an array

      if ($contents) { //if file is not empty,
          $nameMalware = findMalware($conn, $contents);
          if ($nameMalware) {
              echo "Virus found! Name: ". $nameMalware . "<br>";
          } else {
              echo "Not an infected file. <br>";
          }
      }
  } else {
      echo "Please upload a file<br>";
  }



  function getContents($conn, $file)
  {
      if (!function_exists('file_get_contents') || !function_exists('preg_replace')) {  //making sure build-in functions exist
          echo "Some functions not found. Could not find numbers.";
          return;
      }

      $contents = file_get_contents($file);
      if (empty($contents)) { // if the file is empty display message
          echo "File is empty.";
          return;
      }
      return $contents;
  }

function findMalware($conn, $contents)
{
    $query = "SELECT contents from malwares";
    $result = $conn->query($query);
    if (!$result) {
        die($conn->error);
    }

    $flag= false;
    $rows = $result->num_rows;

    for ($j = 0 ; $j < $rows ; ++$j) {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $findme = $row['contents'];
        $pos  = strpos($findme, $contents);
        echo "$pos . <br>";
        if (strpos($findme, $contents) || strpos($contents, $findme)) {
            $flag = true;
            break;
        }
    }
    $mname = "";
    if ($flag) {
        $mname = getMalwareName($conn, $findme);
    }
    $result->close();
    $conn->close();
    return $mname;
}

function getMalwareName($conn, $str)
{
    $query = "SELECT name FROM malwares WHERE contents='$str'";
    $result = $conn->query($query);

    if (!$result) {
        die($conn->error);
    }
    $row = $result->fetch_array(MYSQLI_ASSOC);

    $result->close();
    return $row['name'];
}


  echo "</div>
</body></html>";
