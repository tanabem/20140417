<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["user_name"])) {

// 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
$no_login_url = "http://{$_SERVER["HTTP_HOST"]}/login.php";
header("Location: {$no_login_url}");
exit;
}else{
//print $_SESSION["user_name"];
//print $_SESSION["perm"];
$user_name = $_SESSION["user_name"];
$perm = $_SESSION["perm"];
$name = $_SESSION["name"];
$library = $_SESSION["library"];
}
?>

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ドメイン情報更新</title>
</head>
<body>
<hr />

<?php
echo 'ようこそ！'.$user_name.'('.$name.')さん<br>';
echo '所属機関ID = '.$library.'<br>';
echo '<hr />';

$debug = false;
//$debug = true;

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

//DB接続情報ファイルを読み込む
//require_once("common_db.php");

if($debug) print_r($HTTP_POST_VARS);

//データを取得する
$PUB = $_POST['pub'];
$BaseURL = $_POST['base_url'];
$Domain1 = $_POST['domain1'];
$Domain2 = $_POST['domain2'];
$Domain3 = $_POST['domain3'];
$Domain4 = $_POST['domain4'];
$Domain5 = $_POST['domain5'];

//空データのNULL処理追加
if ( empty($BaseURL) || $BaseURL == "") { $BaseURL = NULL; }
if ( empty($Domain1) || $Domain1 == "") { $Domain1 = NULL; }
if ( empty($Domain2) || $Domain2 == "") { $Domain2 = NULL; }
if ( empty($Domain3) || $Domain3 == "") { $Domain3 = NULL; }
if ( empty($Domain4) || $Domain4 == "") { $Domain4 = NULL; }
if ( empty($Domain5) || $Domain5 == "") { $Domain5 = NULL; }

//エラーチェック
 //リクエストメソッドチェック
if($_SERVER['REQUEST_METHOD'] != "POST") {
 print "Error: invalid method";
 exit();
}

$DateTime = date('Y-m-d H:i:s');

//クォートでくくる
function fnc_quot($str){
  if (is_null($str)){
    return "NULL";
  }else{
    return "'$str'";
  }
}

// クエリを送信する
if (isset($PUB) && $PUB != "") {
   //$query = "UPDATE domains SET BaseURL = ".fnc_quot($BaseURL).",Domain1 = ".fnc_quot($Domain1).", Domain2 = ".fnc_quot($Domain2).", Domain3 = ".fnc_quot($Domain3).", Domain4 = ".fnc_quot($Domain4).", Domain5 = ".fnc_quot($Domain5).", update_date = '".$DateTime."' WHERE Publisher = ".$PUB;
   $query = "UPDATE domains SET BaseURL = '$BaseURL',Domain1 = '$Domain1', Domain2 = '$Domain2', Domain3 = '$Domain3', Domain4 = '$Domain4', Domain5 = '$Domain5', update_date = '$DateTime' WHERE Publisher = '$PUB'";
}else{
   print "Error: POST paramters";
   exit();
}

mysqli_query($link, 'set character set utf8');
$result = mysqli_query($link, $query);
$num_rows = mysqli_num_rows($result);
//if($num_rows == 0) $message = "更新できませんでした";
//else $message = $PUB . "のドメイン情報を更新しました。";
$message = $PUB . "のドメイン情報を更新しました。";
?>

 <h3>ドメイン情報更新</h3>
  <?= $query ?><br />
  <p><?=$message?></p>
  <a href="input.php" target="_self">検索メニューへ</a>
</body>
</html>
