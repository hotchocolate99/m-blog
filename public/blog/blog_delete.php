<?php
//----ログイン状態-----------------
session_start();

if (!$_SESSION['login']) {
    header('Location: ./../../account/login.php');
    exit();
  }

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
//--------------------------------

ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';

$id = $_GET['id'];

deleteMain($id,'posts');
deleteSide($id,'comments');
deleteSide($id,'files');


//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);


?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>削除メッセージ</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/header.css">
        <link rel="stylesheet" href="./../../css/comp_msg.css">
    </head>

    <body>

        <?php include './../../header.php';?>

            <div class="wrapper">
                <div class="container">
                　  <div class="typein">
                    <p class="form_title">記事は削除されました。</p>

                    <a href="./../../index.php" class="fixed_btn">HOMEへ戻る</a></div><br>

                    </div>
                </div>
            </div>

    </body>
</html>