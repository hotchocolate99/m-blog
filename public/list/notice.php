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

//ini_set('display_errors',true);

//未読コメント数
$unreadCommentCount = getUnreadCommentCount();

//未読コメントの内容
$unreadComments = getUnreadComments();
//var_dump($unreadComments);
//foreach($unreadComments as $unreadComment){
    //var_dump($unreadComment['name']);
//}

//コメントを既読にする
//var_dump($_POST);
if($_POST){
  $read = $_POST['read_status'];
  $comments_id = $_POST['comments_id'];

   if(!empty($read) && $comments_id){
     switchToRead($comments_id);

     //お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getUnreadCommentCount();
//var_dump($UnreadCommentCount['COUNT(*)']);
   }
   
}


//postのcomments_id(コメントテーブルのid)で照合して、配列$commentからコメントデータを排除したい。そうしないと、どんどんこのページが一杯になってしまう。
//var_dump($comment);

//下のだと、ちゃんとaの部分を排除できている。
//$array = ['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>5];
//unset($array['a']);
//var_dump($array);



?>




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>お知らせ</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/list.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

        <?php include './headerL.php';?>

        <div class="wrapper">
            <div class="container">
            　  <div class="typein">
                  <div class="frame">
                      <h2 class="form_title">未読のコメントが<?php echo $unreadCommentCount['COUNT(*)'].'件';?>あります。</h2>
                      <br>

                            <?php foreach($unreadComments as $unreadComment):?>
                                <div class="result_box">
                                    <dl>
                                            <dt><strong><?php echo $unreadComment['name'];?>&nbsp;さんがあなたの記事にコメントしました。</strong></dt>
                                            <br>
                                            <dd>コメント投稿日時：<?php echo $unreadComment['comment_at'];?></dd>
                                            <dd>コメント内容：<?php echo $unreadComment['c_content'];?></dd>
                                    </dl>
                                    <br>
                                    <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($unreadComment['posts_id'])?>">記事詳細ページへ</a>

                                    <form action="./notice.php" method="post">
                                        <input type="hidden" name="read_status" value="1">
                                        <input type="hidden" name="comments_id" value="<?php echo $unreadComment['id'];?>">
                                        <input class="btn" type="submit" value="既読にする">
                                    </form>
                                </div>
                                
                            <?php endforeach;?>


                  </div><!--frame-->
                  <a href="#" class="fixed_btn">TOPへ戻る</a><br>

               </div><!--typein-->
            </div><!--container-->
        </div><!--wrapper-->

    </body>
</html>

