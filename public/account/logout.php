<?php
session_start();

//ログアウトする時は、セッションの内容をdestroyで消去する。その前にセッションに空の配列を入れている。
//var_dump($_SESSION);　
$_SESSION = array();

// セッションクッキーを削除する。これはセッション削除とセットで覚える。このまま決まり文句として。
if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", '', time() - 1800, '/');
  }
session_destroy();
//var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ログアウト</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/comp_msg.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>
    <body>
        <?php include './../../header.php';?>

        <div class="wrapper">
            <div class="container">
               <div class="typein">
                   <p>ログアウトしました。</p>
                   <br>
                    <a class="fixed_btn link_aa" href="./login.php">ログイン画面へ</a>

               </div><!--typein-->
           </div><!--container-->
        </div> <!--wrapper-->
    </body>
</html>