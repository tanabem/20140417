<?php
session_start();

// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["user_name"])) {

// 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレ>クトさせる
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
<title>PHP SEARCH RESULT</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<hr />

<?php
echo 'ようこそ！'.$user_name.'('.$name.')さん<br>';
echo '所属機関ID = '.$library.'<br>';
echo '<hr />';

$debug = false;
//$debug = true;

//DB接続
//mysql_connect("mysql309.db.sakura.ne.jp",mmtwins","mmtwins-mysql");
//mysql_select_db("mmtwins_hr");

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

if($debug) print_r($HTTP_GET_VARS);

$PUB = $_GET['pub'];

//エラーチェック
 //リクエストメソッドチェック
if($_SERVER['REQUEST_METHOD'] != "GET") {
 print "Error: invalid method";
 exit();
}

//クエリ生成
$query = "SELECT * FROM domains ";

//クエリ条件生成
 //出版社
 if(!empty($PUB)) {
  $PUB = addslashes($PUB);
  $where = "Publisher = '$PUB' ";
 }

if(!empty($where)) {
 $where = "WHERE " . $where;
}
$query .= $where;
if($debug) {
 print "<BR><BR>";
 print $query;
}

mysqli_query($link, 'set character set utf8');

$result = mysqli_query($link, $query);
$num_rows = mysqli_num_rows($result);

if($num_rows == 0) $message = "該当するデータはありませんでした";
else $message = $num_rows . "件ヒットしました";

$tempHtml = "<input type=\"submit\" name=\"submit\" value=\" ログ表示 \">";

// MySQLへの接続を閉じる
//mysqli_close($link);

?>
検索結果<br>
<?=$message?>
<form action="log_disp.php" method="post">

<?php
if ($result = mysqli_query($link, $query)) {

    $out = "<table border='1'><tr><td>出版社</td><td>ベースURL</td><td>ドメイン1</td><td>ドメイン2</td><td>ドメイン3</td><td>ドメイン4</td><td>ドメイン5</td></tr>\n";
    while($row = mysqli_fetch_assoc($result)) {
      $out.= "<tr>\n";
      $out.= "<td><a href='./domain_edit.php?pub=".$row['Publisher']."'>".$row['Publisher']."</a></td>\n";
      $out.= "<td>" .$row['BaseURL']. "</td>\n";
      $out.= "<td>" .$row['Domain1']. "</td>\n";
      $out.= "<td>" .$row['Domain2']. "</td>\n";
      $out.= "<td>" .$row['Domain3']. "</td>\n";
      $out.= "<td>" .$row['Domain4']. "</td>\n";
      $out.= "<td>" .$row['Domain5']. "</td>\n";
      $out.= "</tr>\n";
      $out.= "<input type='hidden' name='dn1' value='".$row['Domain1']."'>\n";
      $out.= "<input type='hidden' name='dn2' value='".$row['Domain2']."'>\n";
      $out.= "<input type='hidden' name='dn3' value='".$row['Domain3']."'>\n";
      $out.= "<input type='hidden' name='dn4' value='".$row['Domain4']."'>\n";
      $out.= "<input type='hidden' name='dn5' value='".$row['Domain5']."'>\n";
    }
    $out.= "</table>\n";

}
?>
<?= $out; ?>
<br />
<?= $tempHtml ?>
</form>
<br />
<a href="input.php">検索へ戻る</a>
</body>
</html>
