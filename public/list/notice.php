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

ini_set('display_errors',true);
$comments = getAllComment();
//var_dump($comments);

foreach($comments as $comment => $val){
    var_dump($comment);
}



$commentCount = getCommentCount();
//var_dump($commentCount);
var_dump($_POST);
$read = $_POST['read'];
$comments_id = $_POST['comments_id'];
//$comment['id]はコメントテーブルのid
foreach($comments as $comment=>$val){
//var_dump($val['id']);
  if($read = 1 && $comments_id == $val['id']){
      var_dump($val['id']);
      unset($comments[$comment]);
       
   }
   

}
var_dump($comments);



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
                      <h2 class="form_title">お知らせは以下の通りです。(<?php echo $commentCount['COUNT(*)'].'件';?>)</h2>


                            <?php foreach($comments as $comment => $val):?>
                                <div class="result_box">
                                 <?php// if($comment['posts_id'] == $posts_id && $read == 1):?>
                                    <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($comment['posts_id'])?>">
                                      
                                    
                                    <dl>
                                            <dt><?php echo $val['name'];?>さんがあなたの記事にコメントしました。</dt>
                                            <dd><?php echo $val['comment_at'];?></dd>
                                            <dd><?php echo $val['c_content'];?></dd>
                                    </dl>
                                    
                                    </a>
                                            <form action="./notice.php" method="post">
                                               <input type="hidden" name="read" value="1">
                                               <input type="hidden" name="comments_id" value="<?php echo $val['id'];?>">
                                               <input type="submit" value="確認しました">
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

