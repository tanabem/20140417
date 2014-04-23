<?php
/* POST 変数の受けとり */
$f_username = $_POST['f_username'];
$f_passwd = $_POST['f_passwd'];

/* user 名が空だったらトラップ */
if ( $f_username == "" ) {
echo  <<< EOT
<HTML>
<HEAD>
   <TITLE>Authentication Falure</Title>
   <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
</HEAD>
<body>
Authentication Failure : Cannot specify your username.<br>
<br>
ユーザ名が指定されていません。認証は失敗しました。<br>
<br>
<br>
<div align="center">
<a href="login.php">戻る</a></div>
EOT;
exit;
}

/* ここからセッション開始 */
session_start();

// MySQLへ接続する準備。DB名や認証に必要な情報を格納
$url = "127.0.0.1";
$user = "root";
$pass = "NIMS-mysql!";
$db = "proxy_log_db";

//DB接続
//mysql_connect($url,$user,$pass);
//mysql_select_db($db);

$link = mysqli_connect($url,$user,$pass,$db);

/* 接続状況をチェックします */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$table = "users_tbl";  /* ユーザ情報が入っているテーブル名 */
$unamefield = "uid"; /* ユーザ名が入っているフィールド名 */
$pwfield = "pass"; /* パスワードフィールド名 */
$permfield = "perm"; /* 権限フィールド名 */
$namefield = "name"; /* 氏名フィールド名 */
$libraryfield = "library_code"; /* 所属機関名フィールド名 */

$query = "SELECT * FROM $table WHERE $unamefield LIKE '" . addslashes($f_username) . "'";

mysqli_query($link, 'set character set utf8');
$result = mysqli_query($link, $query);
$num_rows = mysqli_num_rows($result);

if ( $num_rows > 1) {
/* ユーザ名重複？ */
    echo <<< EOT
<HTML>
<HEAD>
<TITLE>Authentication Falure</Title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
</HEAD>
<body>
Authentication Failure<br>
<br>
データベース内部エラーにより認証は失敗しました。管理者に連絡してください。<br>
<br>
<br>
<div align="center">
<a href="login.php">戻る</a></div>
</body></html>
EOT;
    session_destroy();
    exit;
}

if($num_rows == 0) $message = "該当するデータはありませんでした";

$row = mysqli_fetch_assoc($result);

$d_passwd = $row["pass"];

echo "ROW = ". $num_rows."<br>";
echo "PASS = ". $d_passwd."<br>";

/* 該当するユーザ名がない場合には NULL が返ってくる */

if ( ($d_passwd == NULL) || ($f_passwd != $d_passwd) ) {
    /* パスワードが一致しない */
    echo <<< EOT
<HTML>
<HEAD>
<TITLE>Authentication Falure</Title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
</HEAD>
<body>
Authentication Failure<br>
<br>
認証は失敗しました。<br>
<br>
<div align="center">
<a href="login.php">戻る</a></div>
EOT;
    session_destroy();
    exit;
}

/* ここまできたら認証成功の時の処理を行う */
/* まずセッション変数の登録 */
$d_perm = $row["perm"];
$d_name = $row["name"];
$d_library = $row["library_code"];

// ログインが成功した証をセッションに保存
$_SESSION["user_name"] = $f_username;
$_SESSION["perm"] = $d_perm;
$_SESSION["name"] = $d_name;
$_SESSION["library"] = $d_library;

/* 自動的に input.php へ画面遷移する */
header("Location: input.php");
?>

/* Location ヘッダを認識してくれないブラウザの場合にはリンクを表示 */

<html>
<head></head>
<body>
Authentication Completed!<br>
<br>
認証に成功しました。<br>
<br>
<a href="input.php">進む</a>
</body>
</html>
