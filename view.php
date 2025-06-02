<?php
include "db.php";

$all = mysqli_query($conn, "SELECT * FROM posts ORDER BY created_at DESC");

while ($one = mysqli_fetch_assoc($all)) {
  echo "Title: " . $one["title"] . "<br>";
  echo "Description: " . $one["description"] . "<br>";
  echo "Status: " . $one["status"] . "<br>";
  echo "Created: " . $one["created_at"] . "<br>";
  echo "Updated: " . $one["updated_at"] . "<br>";
  echo "ID: " . $one["id"] . "<br>";

  if ($one["thumbnail"]) {
    echo "<img src='" . $one["thumbnail"] . "' width='200'><br>";
  }

  echo "<a href='get_1.php?id=" . $one["id"] . "'>View</a><br>";
  echo "<br><hr><br>";
}

mysqli_close($conn);
