<?php
//----ログイン状態-----------------
session_start();

if (!$_SESSION['login']) {
    header('Location: ./../../account/login.php');
    exit();
  }

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
    $users_id = $user[0]['id'];
  }
//--------------------------------

require_once './../../private/database.php';
require_once './../../private/functions.php';

$profiles = $_POST;
//var_dump($profiles);

    if(empty($profiles['nickname'])){
        header('Location: ./profile_post.php?error=invalid_nickname');
        exit();
    }

    if(mb_strlen($profiles['intro_text'])>300){
        header('Location: ./profile_post.php?error=invalid_intro_text');
        exit();
    }

//var_dump($profiles['nickname']);

    if($profiles){
        CreateProfile($user['0']['id'],$profiles['nickname'],$profiles['intro_text']);
        $_SESSION['0']['nickname'] = $profiles['nickname'];
        $_SESSION['0']['intro_text'] = $profiles['intro_text'];

        }
        //var_dump($_SESSION['0']['nickname']);
       // var_dump($user);

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>プロフィール完了</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <!--<link rel="stylesheet" href="../css/home.css">-->
        <link rel="stylesheet" href="../../css/header.css">
        <link rel="stylesheet" href="../../css/form.css">
    </head>

    <body>

       <?php include './../../header.php';?>

       <label for="check">
            <div class="wrapper">
                <div class="container">
                　   <div class="typein">
                        <p class="form_title"></p>

                        <p>プロフィールを完了しました。</P>

                        <a class="fixed_btn" href="./../../index.php">HOMEへ</a></div><br>
                    </div>
                </div>
            </div>
    </label>

    </body>
</html>