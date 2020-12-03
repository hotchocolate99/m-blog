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
//--------------------------------

require_once './../../private/database.php';
require_once './../../private/functions.php';

$comment = $_POST;
var_dump($comment['posts_id']);

//$posts_id = $_POST['posts_id'];
//var_dump($posts_id);
//var_dump($POST_id);

if(!empty($comment)){
  if(empty($comment['name'])){
    header('Location: ./comment_post.php?error=invalid_c_name');
    exit();
  }

  //if(empty($comment['content'])){
  //  header('Location: ./comment_post.php?error=invalid_c_content');
  //}

  if(mb_strlen($comment['c_content'])>200){
    header('Location: ./comment_post.php?error=invalid_c_content');
  }

}
commentCreate($comment);

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>コメント投稿完了</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/comp_msg.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

        <?php include './headerC.php';?>

        <div class="wrapper">
            <div class="container">
               <div class="typein">
　　　　　　　　　　　　　<p>コメントを投稿しました。</p>
                      <br>
                      <a class="fixed_btn link_aa" href="./../blog/blog_detail.php?id=<?php echo h($comment['posts_id'])?>">記事へ戻る</a>

                 </div><!--typein-->
            </div><!--container-->
        </div> <!--wrapper-->

    </body>
</html>