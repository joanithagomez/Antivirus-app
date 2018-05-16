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

  if (isset($_FILES['filename'])) { //check if user uploaded a file

      $name = strtolower(preg_replace("[^A-Za-z0-9.]", "", $_FILES['filename']['name']));  //sanitizing name of the file to work in any OS
      if (move_uploaded_file($_FILES['filename']['tmp_name'], $name)) {  //moving file from the temporary location to permanent one
          echo "File upload successful!<br>";
          $contents = getData($conn, $name);

          if ($contents) { //if file is not empty,
              $nameMalware = findMalware($conn, $contents);
              if ($nameMalware) {
                  echo "Virus found! Name: ". $nameMalware . "<br>";
              } else {
                  echo "Not an infected file. <br>";
              }
          } else {
              echo "File empty";
          }
      } else {
          echo "File upload failed.";
      }
  } else {
      echo "Please upload a file<br>";
  }



  function getData($conn, $file)
  {
      $handle = fopen($file, "rb");
      $fsize = filesize($file);
      if (!$fsize) {
          return "";
      }
      $data = fread($handle, $fsize);
      $byteArray = unpack("N*", $data);
      $binaryData = "";
      foreach ($byteArray as $key => $block) {
          $binaryData .= $block;
      }

      fclose($handle);
      return $binaryData;
  }

function findMalware($conn, $contents)
{
    $query = "SELECT contents from malwares;";
    $result = $conn->query($query);
    if (!$result) {
        die($conn->error);
    }

    $rows = $result->num_rows;
    $mname = "";

    for ($j = 0 ; $j < $rows ; ++$j) {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $str =$row['contents'];
        // echo "$str <br>";
        $findme = "/$str/";
        if (preg_match($findme, $contents) == 1) {
            $mname = getMalwareName($conn, $str);
            break;
        }
    }

    $result->close();
    return $mname;
    // return false;
}

function getMalwareName($conn, $str)
{
    $query = "SELECT name FROM malwares WHERE contents='$str';";
    $result = $conn->query($query);

    if (!$result) {
        die($conn->error);
    }
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    $conn->close();
    // echo "Name: " . $row['name'];
    return $row['name'];
}


  echo "</div>
</body></html>";
