<?php

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"];
$t = $data["title"];
$d = $data["description"];
$s = $data["status"];
$u = date("Y-m-d H:i:s");

if (!$id || !$t || !$d || !$s) {
    echo "missing data";
    exit;
}

$sql = "UPDATE posts SET title='$t', description='$d', status='$s', updated_at='$u' WHERE id=$id";

mysqli_query($conn, $sql);

echo "done";
