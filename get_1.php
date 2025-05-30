<?php
include "db.php";

$id = $_GET["id"] ?? '';

if (!$id || !is_numeric($id)) {
  echo "id missing";
  exit;
}

$stmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if ($row) {
  echo "Title: " . $row["title"] . "<br>";
  echo "Description: " . $row["description"] . "<br>";
  echo "Status: " . $row["status"] . "<br>";
} else {
  echo "not found";
}

$stmt->close();
$conn->close();
