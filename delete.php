<?php
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? '';

if (!$id) {
  echo "id missing";
  exit;
}

$q = $conn->prepare("DELETE FROM posts WHERE id=?");
$q->bind_param("i", $id);
$q->execute();

echo $q->affected_rows > 0 ? "deleted" : "not deleted";

$q->close();
$conn->close();
