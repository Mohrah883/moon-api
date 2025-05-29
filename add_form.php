<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  include "db.php";

  $t = $_POST["title"];
  $d = $_POST["description"];
  $s = $_POST["status"];
  $now = date("Y-m-d H:i:s");

  $q = "insert into posts (title, description, status, created_at, updated_at)
        values ('$t', '$d', '$s', '$now', '$now')";

  mysqli_query($conn, $q);
  echo "added";
  exit;
}
?>

<form method="POST">
  <input name="title" placeholder="title"><br><br>
  <input name="description" placeholder="desc"><br><br>
  <input name="status" placeholder="status"><br><br>
  <button type="submit">add</button>
</form>
