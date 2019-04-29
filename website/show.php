<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <title>Airbnb x Jana</title>
</head>
<body>
    <?php
        $id = $_SESSION['id'];
        echo "<a href='https://www.airbnb.com/rooms/$id'>Go to Airbnb.com</a>";
        session_destroy();
    ?>
</body>