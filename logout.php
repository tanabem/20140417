<?php
// セッションの初期化
// session_name("something")を使用している場合は特にこれを忘れないように!
session_start();

// セッション変数を全て解除する
$_SESSION = array();

// セッションを切断するにはセッションクッキーも削除する。
// Note: セッション情報だけでなくセッションを破壊する。
if (isset($_COOKIE[session_name()])) {
   setcookie(session_name(), '', time()-42000, '/');
}

if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", '', time() - 1800, '/');
}

// 最終的に、セッションを破壊する
session_destroy();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<meta http-equiv="content-style-type" content="text/css">
<link href="./css/bootstrap.min.css" rel="stylesheet" media="screen">
<title>メッセージ</title>
</head>
<body>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
<div id="container">

<div id="navigation">
<h1></h1>
</div>

<div id="header">
<h2></h2>
<br><h4 align="left">ログオフしました。セッションは無効となりました。<br>
<A HREF="./login.php">ログイン画面</A>へ</h4>
</div>

<div id="footer">
<h1></h1>
</div>

</div>

</body>
</html>
