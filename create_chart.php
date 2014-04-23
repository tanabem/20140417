<?php
/*************start code**************/
$proxy = array(
      "http" => array(
         "proxy" => "tcp://144.213.5.124:8888",
         'request_fulluri' => true,
      ),
);
$proxy_context = stream_context_create($proxy);
$get_file = file_get_contents("https://www.google.com/jsapi", false, $proxy_context);
$get_file2 = file_get_contents("https://www.google.com/uds/api/visualization/1.0/ce05bcf99b897caacb56a7105ca4b6ed/ui+ja.css", false, $proxy_context);
$get_file3 = file_get_contents("https://www.google.com/uds/api/visualization/1.0/ce05bcf99b897caacb56a7105ca4b6ed/format+ja,default+ja,ui+ja,corechart+ja.I.js", false, $proxy_context);
/**************end code**************/

//putenv("http_proxy=http://wwwout.nims.go.jp:8888");
//putenv("https_proxy=http://wwwout.nims.go.jp:8888");

//echo $_ENV['http_proxy'];

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

echo 'ようこそ！'.$user_name.'('.$name.')さん<br>';
echo '所属機関ID = '.$library.'<br>';
echo '<hr />';

$debug = false;
//$debug = true;

# 別ファイルのユーザー定義関数「makeChartParts()」を読み込みます。
require_once './make_chart_parts.php';

if($debug) print_r($HTTP_POST_VARS);

$DN1 = $_POST['dn1'];
$DN2 = $_POST['dn2'];

//エラーチェック
 //リクエストメソッドチェック
if($_SERVER['REQUEST_METHOD'] != "POST") {
 print "Error: invalid method";
 exit();
}

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

$sql1 = "SELECT count(*) as cnt1 FROM logs WHERE time_stamp > '2012-01-01' and time_stamp < '2013-01-01'";

$sql2 = "SELECT count(*) as cnt2 FROM logs WHERE time_stamp > '2013-01-01' and time_stamp < '2014-01-01'";

$sql3 = "SELECT count(*) as cnt3 FROM logs WHERE time_stamp > '2014-01-01' and time_stamp < '2015-01-01'";

mysqli_query($link, 'set character set utf8');

$result1 = mysqli_query($link, $sql1);
$num_rows1 = mysqli_num_rows($result1);
$row1 = mysqli_fetch_assoc($result1);
$CN1 = $row1['cnt1'];
$nC1 = intval($CN1);
//print $CN1;

$result2 = mysqli_query($link, $sql2);
$num_rows2 = mysqli_num_rows($result2);
$row2 = mysqli_fetch_assoc($result2);
$CN2 = $row2['cnt2'];
$nC2 = intval($CN2);
//print $CN2;

$result3 = mysqli_query($link, $sql3);
$num_rows3 = mysqli_num_rows($result3);
$row3 = mysqli_fetch_assoc($result3);
$CN3 = $row3['cnt3'];
$nC3 = intval($CN3);
//print $CN3;

// グラフの値
//$data = array();
//$data[] = array('', '利用数');  // 見出し
//$data[] = array('2012年', $nC1);
//$data[] = array('2013年', $nC2});
//$data[] = array('2014年', $nC3});
$data = array(
  array('', '利用数'),
  array('2012年', $nC1), array('2013年', $nC2), array('2014年', $nC3)
);

// グラフのオプション
$options = array(
  'title'  => $DN1.' ： パッケージ別グラフ',             // グラフタイトル
  'titleTextStyle' => array('fontSize' => 16),  // タイトルのスタイル
  'hAxis'  => array('title' => '年度',  // 横軸ラベル
                    'titleTextStyle' => array('color' => 'blue')),  // スタイル
  'vAxis'  => array('minValue' => 0, 'maxValue' => 800,  // 縦軸範囲
                    'title' => '単位：アクセス数'),              // 縦軸ラベル
  'width'  => 500,  // 幅
  'height' => 400,  // 高さ
  'bar'    => array('groupWidth' => '50%'),  // バーの太さ
  'legend' => array('position' => 'top', 'alignment' => 'end'));  // 凡例

// グラフ種類（縦棒グラフ）
$type = 'ColumnChart';

// グラフ描画のJavaScriptの関数、表示させる<div>タグの生成
list($chart, $div) = makeChartParts($data, $options, $type);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>グラフを生成したい</title>
<script type="text/javascript">
  <?php echo $get_file; ?>;
</script>
<script>
<?php
# グラフ描画のためのJavaScriptの関数を表示します。
echo $chart;
?>
</script>
<link rel="stylesheet" type="text/css" href="https://www.google.com/uds/api/visualization/1.0/ce05bcf99b897caacb56a7105ca4b6ed/ui+ja.css" >
</link>
<script type="text/javascript">
  <?php echo $get_file3; ?>;
</script>
</head>
<body>
<div>
<?php
# グラフを表示させる<div>タグを適切な場所に配置します。
echo $div;
?>
</div>
</body>
</html>
