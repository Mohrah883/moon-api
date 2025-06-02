<?php

require_once 'env.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("DB Connection Error: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

$sql = "ALTER TABLE posts ADD COLUMN thumbnail VARCHAR(255) NULL";

if ($conn->query($sql) === TRUE) {
    echo "Thumbnail column added successfully.";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
