<?php

require_once 'env.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("DB Connection Error: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}
