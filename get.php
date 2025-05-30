<?php
include "db.php";

$q = $conn->query("SELECT id, title, description, status, created_at, updated_at FROM posts");

if (!$q) {
  echo "SQL Error: " . $conn->error;
  exit;
}

$rows = [];

while ($r = $q->fetch_assoc()) {
  $rows[] = $r;
}

echo json_encode($rows);

$conn->close();
