<?php
//----ログイン状態-----------------
session_start();

if (!$_SESSION['login']) {
    header('Location: ./../../login.php');
    exit();
  }

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
//--------------------------------

ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';

$blogs = $_POST;
//var_dump($blogs);
//var_dump($blogs["file_name"]);
//var_dump($blogs["file_path"]);
//$file = $_FILES;

var_dump($_FILES);
$file = $_FILES;

//ファイルのデータもポストで送られてきている。（$_FILESでは無く）




//---------------------------------------------------
blogValidate($blogs);
blogUpdate($blogs);
if(!empty($file)){
fileUpdate($blogs,$file);
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>更新完了</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/comp_msg.css">
        <link rel="stylesheet" href="./../../css/header.css">

    </head>

    <body>

           <?php include './headerB.php';?>

            <div class="wrapper">
                <div class="container">
                　 <div class="typein">
                    <p class="form_title">記事は更新されました。</p>
                    <a class="fixed_btn link_aa" href="./blog_detail.php?id=<?php echo h($comment['posts_id'])?>">記事へ戻る</a>
                    <a class="fixed_btn" href="./blog_detail.php?id=<?php echo h($blogs['id'])?>">記事へ戻る</a></div><br>
                </div>

                </div>
            </div>

    </body>
</html>