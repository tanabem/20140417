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

if($debug) print_r($HTTP_POST_VARS);

$eISSN = $_POST['eISSN'];
$title = $_POST['title'];
$publisher = $_POST['publisher'];

//エラーチェック
 //リクエストメソッドチェック
if($_SERVER['REQUEST_METHOD'] != "POST") {
 print "Error: invalid method";
 exit();
}

//クエリ生成
$query = "SELECT * FROM journals ";

//検索条件生成
 //eISSN
 if(!empty($eISSN)) {
  $eISSN = addslashes($eISSN);
  $where = "online_issn = '$eISSN' && ";
 }
 //名前
 if(!empty($title)) {
  $title = addslashes($title);
  $where .= "title = '$title' && ";
 }
 //出版社
 if(!empty($publisher)) {
  $publisher = addslashes($publisher);
  //$where .= "publisher REGEXP '$publisher' && ";
  $where .= "publisher LIKE '%$publisher%' && ";
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

// MySQLへの接続を閉じる
//mysqli_close($link);

//if ($result = mysqli_query($link, $query)) {
//
//    /* 連想配列を取得します */
//    while ($row = mysqli_fetch_assoc($result)) {
//        printf ("%s (%s)\n", $row["publisher"], $row["title"]);
//    }
//
//    /* 結果セットを開放します */
//    mysqli_free_result($result);
//}

?>

検索結果<br>
<?=$message?><br>
<?php
if ($result = mysqli_query($link, $query)) {

    $out = "<table border='1'><tr><td>タイトル</td><td>出版社</td><td>P-ISSN</td><td>E-ISSN</td></tr>\n";
    while($row = mysqli_fetch_assoc($result)) {
      $out.= "<tr>\n";
      $out.= "<td><a href='./domain.php?pub=" .$row['publisher']."'>".$row['title']."</a></td>\n";
      $out.= "<td>" .$row['publisher']. "</td>\n";
      $out.= "<td>" .$row['print_issn']. "</td>\n";
      $out.= "<td>" .$row['online_issn']. "</td>\n";
      $out.= "</tr>\n";
    }
    $out.= "</table>\n";

}
?>
<?= $out; ?>
<a href="input.php">再検索</a>
</body>
</html>
