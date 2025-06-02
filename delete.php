<?php
include "db.php";

$id = $_GET["id"] ?? '';

if (!$id || !is_numeric($id)) {
  echo "id missing";
  exit;
}

$thumb = null;
$get = $conn->prepare("SELECT thumbnail FROM posts WHERE id=?");
$get->bind_param("i", $id);
$get->execute();
$get->bind_result($thumb);
$get->fetch();
$get->close();

if ($thumb && file_exists(__DIR__ . "/" . $thumb)) {
  unlink(__DIR__ . "/" . $thumb);
}

$del = $conn->prepare("DELETE FROM posts WHERE id=?");
$del->bind_param("i", $id);
$del->execute();

echo $del->affected_rows > 0 ? "deleted" : "not deleted";

$del->close();
$conn->close();
