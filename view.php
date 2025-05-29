<?php
include "db.php";

$all = mysqli_query($conn, "SELECT * FROM posts");

while ($one = mysqli_fetch_assoc($all)) {
  echo "Title: " . $one['title'] . "<br>";
  echo "Description: " . $one['description'] . "<br>";
  echo "Status: " . $one['status'] . "<br>";
  echo "Created: " . $one['created_at'] . "<br>";
  echo "Updated: " . $one['updated_at'] . "<br><br>";
}

mysqli_close($conn);
?>
