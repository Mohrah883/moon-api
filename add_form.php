<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  include "db.php";

  $t = $_POST["title"] ?? '';
  $d = $_POST["description"] ?? '';
  $s = $_POST["status"] ?? '';
  $now = date("Y-m-d H:i:s");

  if (!$t || !$d || !$s) {
    echo "missing";
    exit;
  }

  // check for unique title
  $check = $conn->prepare("SELECT id FROM posts WHERE title = ?");
  $check->bind_param("s", $t);
  $check->execute();
  $check->store_result();
  if ($check->num_rows > 0) {
    echo "title exists";
    exit;
  }
  $check->close();

  // handle image
  $thumb = null;
  if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['thumbnail']['tmp_name'];
    $name = basename($_FILES['thumbnail']['name']);
    $size = $_FILES['thumbnail']['size'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
      echo "invalid file type";
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

    $newName = uniqid() . "." . $ext;
    $thumb = "public/uploads/" . $newName;
    move_uploaded_file($tmp, $folder . $newName);
  }

  // insert
  $q = $conn->prepare("INSERT INTO posts (title, description, status, created_at, updated_at, thumbnail) VALUES (?, ?, ?, ?, ?, ?)");
  $q->bind_param("ssssss", $t, $d, $s, $now, $now, $thumb);
  $q->execute();

  echo "added";
  $q->close();
  $conn->close();
  exit;
}
?>

<form method="POST" enctype="multipart/form-data">
  <input name="title" placeholder="title"><br><br>
  <input name="description" placeholder="desc"><br><br>
  <input name="status" placeholder="status"><br><br>
  <input type="file" name="thumbnail" accept="image/png, image/jpeg"><br><br>
  <button type="submit">add</button>
</form>
