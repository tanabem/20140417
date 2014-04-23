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
<title>NIMS LOG SEARCH</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<h2>
物質・材料研究機構科学情報室／ログ検索(2014/3/17)
</h2>
<hr />

<?php
echo 'ようこそ！'.$user_name.'('.$name.')さん<br>';
echo '所属機関ID = '.$library.'<br>';
echo '<hr />';
?>

<body bgcolor="#FFFFFF" text="#000000">
<form name="" method="post" action="journal_search.php">
  検索条件を指定してください<br>
  <table width="500" border="1" cellspacing="1" cellpadding="0">
    <tr>
      <td>E-ISSN</td>
      <td>
        <input type="text" name="eISSN" size="20" maxlength="255">
      </td>
    </tr>
    <tr> 
      <td>タイトル</td>
      <td> 
        <input type="text" name="title" size="50" maxlength="255">
      </td>
    </tr>
    <tr> 
      <td>出版社／パッケージ</td>
      <td> 
        <input type="text" name="publisher" size="50" maxlength="255">
      </td>
    </tr>
  </table>
  <input type="submit" name="submit" value="検索">
  <input type="reset" value="条件クリア">
</form>
</body>
</html>
