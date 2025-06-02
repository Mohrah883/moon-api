<?php
include "db.php";

$id = $_POST["id"] ?? '';
$title = $_POST["title"] ?? '';
$description = $_POST["description"] ?? '';
$status = $_POST["status"] ?? '';
$now = date("Y-m-d H:i:s");

if (!$id || !$title || !$description || !$status) {
  echo "missing";
  exit;
}

// check for duplicate title
$check = $conn->prepare("SELECT id FROM posts WHERE title = ? AND id != ?");
$check->bind_param("si", $title, $id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
  echo "title exists";
  exit;
}
$check->close();

// get old thumbnail
$oldThumb = null;
$get = $conn->prepare("SELECT thumbnail FROM posts WHERE id = ?");
$get->bind_param("i", $id);
$get->execute();
$get->bind_result($oldThumb);
$get->fetch();
$get->close();

// handle new image
$thumb = $oldThumb;
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['thumbnail']['tmp_name'];
  $name = basename($_FILES['thumbnail']['name']);
  $size = $_FILES['thumbnail']['size'];
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  $allowed = ['jpg', 'jpeg', 'png'];

  if (!in_array($ext, $allowed)) {
    echo "invalid file";
    exit;
  }

  if ($size > 2 * 1024 * 1024) {
    echo "file too big";
    exit;
  }

  $folder = __DIR__ . "/public/uploads/";
  if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
  }

  if ($oldThumb && file_exists(__DIR__ . "/" . $oldThumb)) {
    unlink(__DIR__ . "/" . $oldThumb);
  }

  $newName = uniqid() . "." . $ext;
  $thumb = "public/uploads/" . $newName;
  move_uploaded_file($tmp, $folder . $newName);
}

// update post
$update = $conn->prepare("UPDATE posts SET title=?, description=?, status=?, updated_at=?, thumbnail=? WHERE id=?");
$update->bind_param("sssssi", $title, $description, $status, $now, $thumb, $id);
$update->execute();

if ($update->affected_rows > 0) {
  echo "updated";
} else {
  echo "no change - current data: ";
  echo json_encode([$title, $description, $status, $thumb]);
}

$update->close();
$conn->close();
