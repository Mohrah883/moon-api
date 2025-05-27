<?php

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"];
$t = $data["title"];
$d = $data["description"];
$s = $data["status"];
$u = date("Y-m-d H:i:s");

$sql = "update posts set title='$t', description='$d', status='$s', updated_at='$u' where id=$id";

mysqli_query($conn, $sql);

echo "done";