<?php
//----ログイン状態-----------------
session_start();

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
//--------------------------------
ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';


$comment = $_POST;
var_dump($comment);
$posts_id = $comment['posts_id'];
var_dump($posts_id);

//$posts_id = $_POST['posts_id'];
//var_dump($posts_id);
//var_dump($POST_id);

//if(!empty($comment)){
  if(empty($comment['name'])){
    header('Location: ./comment_post.php?error=invalid_c_name&posts_id=$posts_id');
    exit();
  }
//}

  if(empty($comment['c_content'])){
    header('Location: ./comment_post.php?error=invalid_c_content&posts_id=$posts_id');
    exit();
  }

  if(!empty($comment['c_content']) && mb_strlen($comment['c_content'])>200){
    header('Location: ./comment_post.php?error=invalid_c_content&posts_id=$posts_id');
    exit();
  }



//if(!empty($comment['name'])){
  //if(empty($comment)){
    //header('Location: ./comment_post.php?error=invalid_c_content&posts_id=$posts_id');
    //exit();
  //}
//}
commentCreate($comment);

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);

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

        <?php include './../../header.php';?>

      <label for="check">
        <div class="wrapper">
            <div class="container">
               <div class="typein">
　　　　　　　　　　　　　<p>コメントを投稿しました。</p>
                      <br>
                      <a class="fixed_btn link_aa" href="./../blog/blog_detail.php?id=<?php echo h($comment['posts_id'])?>">記事へ戻る</a>
                      
                 </div><!--typein-->
            </div><!--container-->
        </div> <!--wrapper-->
      </label>
    </body>
  </html>