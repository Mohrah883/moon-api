<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  include "db.php";

  $t = $_POST["title"] ?? '';
  $d = $_POST["description"] ?? '';
  $s = $_POST["status"] ?? '';
  $now = date("Y-m-d H:i:s");

  if (!$t || !$d || !$s) {
    echo "missing";
    exit;
  }

  $q = $conn->prepare("INSERT INTO posts (title, description, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
  $q->bind_param("sssss", $t, $d, $s, $now, $now);
  $q->execute();

  echo "added";
  $q->close();
  $conn->close();
  exit;
}
?>

<form method="POST">
  <input name="title" placeholder="title"><br><br>
  <input name="description" placeholder="desc"><br><br>
  <input name="status" placeholder="status"><br><br>
  <button type="submit">add</button>
</form>
