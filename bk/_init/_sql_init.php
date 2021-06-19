<?php

//------------------------------------------------------------------
// データベース初期化
//------------------------------------------------------------------

  // データベース情報を変数へセット
    $dburl  = "localhost";
    $dbuser = "desi_bk";
    $dbpass = "hahifu";
    $dbname = "desi_bk";
    //$user_no= $_SESSION['user_no'];

  // MySQLへ接続
    $link = mysql_connect($dburl,$dbuser,$dbpass) or die("MySQLへの接続に失敗しました。");

  // 文字化け対策　SET NAMES クエリの発行
    $sql = "SET NAMES utf8";
    $result = mysql_query($sql, $link) or die("文字化け対策の送信に失敗しました。<br />SQL:".$sql);;

  // データベースを選択
    $sdb = mysql_select_db($dbname,$link) or die("データベースの選択に失敗しました。");

?>
