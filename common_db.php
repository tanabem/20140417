<?php
  function executeQuery($sql){
    $url = "127.0.0.1";
    $user = "root";
    $pass = "NIMS-mysql!";
    $db = "proxy_log_db";

    // MySQLへ接続する
    $link = mysql_connect($url,$user,$pass) or die("MySQLへの接続に失敗しました。");

    // データベースを選択する
    $sdb = mysql_select_db($db,$link) or die("データベースの選択に失敗しました。");

    // UTF-8対応処理
    mysql_query('set character set utf8');

    // クエリを送信する
    $result = mysql_query($sql, $link) or die("クエリの送信に失敗しました。<br />SQL:".$sql);

    // MySQLへの接続を閉じる
    mysql_close($link) or die("MySQL切断に失敗しました。");

    //戻り値
    return($result);
  }
?>
