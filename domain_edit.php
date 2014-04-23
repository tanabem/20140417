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
<title>更新確認</title>
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
mysql_connect($url,$user,$pass);
mysql_select_db($db);

if($debug) print_r($HTTP_GET_VARS);

//データを取得する
$PUB = $_GET['pub'];

//エラーチェック
 //リクエストメソッドチェック
if($_SERVER['REQUEST_METHOD'] != "GET") {
 print "Error: invalid method";
 exit();
}

// クエリを送信する
if (isset($PUB) && $PUB != "") {
   $query = "SELECT * FROM domains WHERE Publisher = '$PUB' ";
}else{
   print "Error: GET paramters";
   exit();
}

if($debug) {
 print "<BR><BR>";
 print $query;
}

mysql_query('set character set utf8');

$result = mysql_query($query);
$num_rows = mysql_num_rows($result);

//表示するデータを作成
if($num_rows){
    $row = mysql_fetch_array($result);
    $Publisher = $row["Publisher"];
    $BaseURL = $row["BaseURL"];
    $Domain1 = $row["Domain1"];
    $Domain2 = $row["Domain2"];
    $Domain3 = $row["Domain3"];
    $Domain4 = $row["Domain4"];
    $Domain5 = $row["Domain5"];
    $tempHtml = "<input type=\"submit\" name=\"submit\" value=\" 更新 \">";
    $msg = "データを変更後、更新ボタンをクリックしてください。\n";
}else{
    $tempHtml = "<a href=\"#\" onClick=\"history.back(); return false;\">前の画面へ戻る</a>\n";
    $msg = "データがありません。\n";
}

//結果保持用メモリを開放する
mysql_free_result($result);
?>
    <h3>更新確認</h3>
    <?= $msg ?>
    <form action="domain_edit2.php" method="post">
      <table width = "800" border = "1" cellspacing="1" class="example1">
        <tr bgcolor="##ccffcc"><td>Publisher</td><td>BaseURL</td><td>Domain1</td><td>Domain2</td><td>Domain3</td><td>Domain4</td><td>Domain5</td></tr>
        <tr>
          <td nowrap><?= $Publisher ?></td>
          <td><input size="100" type="text" name="base_url" value="<?= $BaseURL ?>"></td>
          <td width = "50"><input type="text" name="domain1" value="<?= $Domain1 ?>"></td>
          <td width = "50"><input type="text" name="domain2" value="<?= $Domain2 ?>"></td>
          <td width = "50"><input type="text" name="domain3" value="<?= $Domain3 ?>"></td>
          <td width = "50"><input type="text" name="domain4" value="<?= $Domain4 ?>"></td>
          <td width = "50"><input type="text" name="domain5" value="<?= $Domain5
 ?>"></td>
        </tr>
      </table>
      <br />
      <input type="hidden" name="pub" value="<?= $PUB ?>">
      <?= $tempHtml ?>
    </form>
  </body>
</html>
