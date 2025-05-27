<?php

include "db.php";

$t = "mimi, first post";
$d = "Lets gooo to the moon";
$s = "draft";

$c = date("Y-m-d H:i:s");
$u = date("Y-m-d H:i:s");

$sql = "INSERT INTO posts (title, description, status, created_at, updated_at)
VALUES ('$t', '$d', '$s', '$c', '$u')";

mysqli_query($conn, $sql);

echo "done";

mysqli_close($conn);
