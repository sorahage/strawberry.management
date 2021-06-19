<?php
require_once(dirname(__FILE__).'/_common_include.php');	// 設定情報読込
?>

<form action="file_upload2.php" method="post" enctype="multipart/form-data">
  file：
  <input type="file" name="upfile" size="30" /><br />
  <br />
  <input type="submit" value="upload" />
</form>
</body>
</html>