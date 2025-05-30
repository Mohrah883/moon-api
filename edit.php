<?php
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? '';
$title = $data["title"] ?? '';
$description = $data["description"] ?? '';
$status = $data["status"] ?? '';

if (!$id || !$title || !$description || !$status) {
  echo "missing fields";
  exit;
}

$stmt = $conn->prepare("UPDATE posts SET title=?, description=?, status=?, updated_at=NOW() WHERE id=?");
$stmt->bind_param("sssi", $title, $description, $status, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  echo "updated";
} else {
  echo "not updated";
}

$stmt->close();
$conn->close();
