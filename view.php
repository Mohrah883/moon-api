<?php
include "db.php";

$all = mysqli_query($conn, "SELECT * FROM posts");

while ($one = mysqli_fetch_assoc($all)) {
  echo "Title: " . htmlspecialchars($one['title']) . "<br>";
  echo "Description: " . htmlspecialchars($one['description']) . "<br>";
  echo "Status: " . htmlspecialchars($one['status']) . "<br>";
  echo "Created: " . $one['created_at'] . "<br>";
  echo "Updated: " . $one['updated_at'] . "<br><br>";
}

mysqli_close($conn);
