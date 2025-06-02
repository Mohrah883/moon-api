<form action="edit_with_image.php" method="POST" enctype="multipart/form-data">
  <input name="id" placeholder="Post ID"><br><br>
  <input name="title" placeholder="Title"><br><br>
  <input name="description" placeholder="Description"><br><br>
  <input name="status" placeholder="Status"><br><br>
  <input type="file" name="thumbnail" accept="image/png, image/jpeg"><br><br>
  <button type="submit">Update Post</button>
</form>
