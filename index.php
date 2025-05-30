<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo "Hi from index.php<br><br>";

?>

<form method="POST" action="add.php">
  Title: <input name="title"><br><br>
  Description: <input name="description"><br><br>
  Status: <input name="status"><br><br>
  
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

  <button type="submit">Add</button>
</form>
