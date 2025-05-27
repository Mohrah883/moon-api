<?php

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"];

$sql = "DELETE FROM posts WHERE id = $id";

mysqli_query($conn, $sql);

echo "deleted";
