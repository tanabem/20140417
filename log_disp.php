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
<title>PHP SEARCH RESULT</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <STYLE TYPE="text/css">
  <!--
    TD {font-size: 10pt; color: green;}
    A {font-weight: bold;}

    table.example1 {
    empty-cells: show;
    }
    table.example2 {
    empty-cells: hide;
    }
  -->
 </STYLE>
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

//if($debug) print_r($HTTP_POST_VARS);
if($debug) {
  if (isset($HTTP_POST_VARS)) {
     print_r($HTTP_POST_VARS);
  }
}

$DN1 = $_POST['dn1'];
$DN2 = $_POST['dn2'];

//$DN1 = "pubs.acs.org";
//$DN2 = "acs.org";

//エラーチェック
 //リクエストメソッドチェック
if($_SERVER['REQUEST_METHOD'] != "POST") {
 print "Error: invalid method";
 exit();
}

//クエリ生成
$query = "SELECT * FROM logs ";

//クエリ条件生成
 //ドメイン名
 if(!empty($DN1)) {
  $DN1 = addslashes($DN1);
  $where = "request_uri LIKE '%$DN1%' || ";
 }

 if(!empty($DN2)) {
  $DN2 = addslashes($DN2);
  $where .= "request_uri LIKE '%$DN2%' || ";
 }

if(!empty($where)) {
 $where = substr($where, 0, -4);
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

$tempHtml = "<input type=\"submit\" name=\"submit\" value=\" グラフ表示 \">";

// MySQLへの接続を閉じる
//mysqli_close($link);

?>
検索結果<br>
<?=$message?>
<form action="create_chart.php" method="post">
<?php
if ($result = mysqli_query($link, $query)) {

    $out = "<table width = '800' border = '1' cellspacing='1' class='example1'><tr bgcolor='##ccffcc'><td>remote_host</td><td>ident_user</td><td>auth_user</td><td>time_stamp</td><td>request_method</td><td>request_uri</td><td>request_protocol</td><td>status</td><td>bytes</td><td>referer</td><td>user_agent</td></tr>\n";
    while($row = mysqli_fetch_assoc($result)) {
      $out.= "<tr>\n";
      $out.= "<td>" .$row['remote_host']. "</td>\n";
      $out.= "<td>" .$row['ident_user']. "</td>\n";
      $out.= "<td>" .$row['auth_user']. "</td>\n";
      $out.= "<td>" .$row['time_stamp']. "</td>\n";
      $out.= "<td>" .$row['request_method']. "</td>\n";
      $out.= "<td>" .$row['request_uri']. "</td>\n";
      $out.= "<td>" .$row['request_protocol']. "</td>\n";
      $out.= "<td>" .$row['status']. "</td>\n";
      $out.= "<td>" .$row['bytes']. "</td>\n";
      $out.= "<td>" .$row['referer']. "</td>\n";
      $out.= "<td>" .$row['user_agent']. "</td>\n";
      $out.= "</tr>\n";
    }
    $out.= "</table>\n";

}
?>
<?= $out; ?>
<br />
<input type="hidden" name="dn1" value="<?=$DN1?>">
<input type="hidden" name="dn2" value="<?=$DN2?>">
<?= $tempHtml ?>
</form>
<br />
<a href="input.php">検索へ戻る</a>
</body>
</html>
