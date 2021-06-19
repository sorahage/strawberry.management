<?php
require_once(dirname(__FILE__).'/_common_include.php');	// 設定情報読込

if (is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
  if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "files/" . $_FILES["upfile"]["name"])) {
    chmod("files/" . $_FILES["upfile"]["name"], 0644);
    echo $_FILES["upfile"]["name"] . "をアップロードしました。<br>";
    echo '<a href="add.php">成績登録へ</a>';
  } else {
    echo "ファイルをアップロードできません。";
  }
} else {
  echo 'ファイルが選択されていません。<br><br>';
  echo '<a href="file_upload.php">ファイル選択へ戻る</a>';
}

?></p>
</body>
</html>