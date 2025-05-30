<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']) {
    echo "csrf error";
    exit;
  }

  $title = $_POST['title'] ?? '';
  $description = $_POST['description'] ?? '';
  $status = $_POST['status'] ?? '';

  if ($title == '' || $description == '' || $status == '') {
    echo "please fill all fields";
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO posts (title, description, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
  $stmt->bind_param("sss", $title, $description, $status);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    echo "post added";
  } else {
    echo "error happened";
  }

  $stmt->close();
  $conn->close();
}
