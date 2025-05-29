<?php
include "db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "Missing or invalid ID";
  exit;
}

$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM posts WHERE id = $id");

if ($row = mysqli_fetch_assoc($result)) {
  echo "Title: " . htmlspecialchars($row['title']) . "<br>";
  echo "Description: " . htmlspecialchars($row['description']) . "<br>";
  echo "Status: " . htmlspecialchars($row['status']) . "<br>";
  echo "Created at: " . $row['created_at'] . "<br>";
  echo "Updated at: " . $row['updated_at'] . "<br>";
} else {
  echo "Post not found";
}

mysqli_close($conn);
?>
