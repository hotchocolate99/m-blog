<?php

/**
 1.notice.phpに既読ボタンをつけて、read_statusを変更するために１をpostで送る。
 2.それを使って、コメントテーブルに変更を加える。
 3.コメントテーブルからread_statusが０のものだけ全て取得する。*
 4.取得したデータを表示させる。*/

//----ログイン状態-----------------
session_start();

/*if (!$_SESSION['login']) {
    header('Location: ./../../account/login.php');
    exit();
  }*/

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
  //var_dump($users_id);
//--------------------------------
set_time_limit(120);

require_once './../../private/database.php';
require_once './../../private/functions.php';

//ini_set('display_errors',true);



//コメントの未読、既読の切り替え　＆　コメント削除--------------------------
//var_dump($_POST);
if($_POST){
    $read = $_POST['read_status'];
    $comments_id = $_POST['comments_id'];
    
    if(isset($_POST['delete'])){
        $toDelete = $_POST['delete'];
    }
    

    if(!empty($toDelete) && $comments_id){
        $toDelete = deleteComment($comments_id);
    }

     if($read == 1 && $comments_id){
       $toRead = switchToRead($comments_id);
       

     }else if($read == 0 && $comments_id){
         $toUnread = switchToUnread($comments_id);
         
     }

}
//--------------------------------------------------------------

//未読コメント数の取得
$unreadCommentCount = getCommentCount($users_id, 0);

//既読コメント数の取得
$readCommentCount = getCommentCount($users_id, 1);

//未読コメントの内容取得
$unreadComments = getCommentsByReadstatus($users_id, 0);
//var_dump($unreadComments);

//既読コメントの内容取得（！なぜかcommentsテーブルのidを取得できていなかった。* from comments　としてもidがposts_idになっていた。。。）
$readComments = getCommentsByReadstatus($users_id, 1);
foreach($readComments as $readComment){
//var_dump($readComment);
}
//ヘッダーに未読数を表示させるために、ここでも関数を呼び出している
$UnreadCommentCount = getCommentCount($users_id, 0);

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

        <?php include './../../header.php';?>

        <label for="check">
        <div class="wrapper">
            <div class="container">
            　  <div class="typein">
            <!--<h2 class="form_title">未読、既読コメントが合わせて<?php echo $unreadCommentCount['COUNT(*)'] + $readCommentCount['COUNT(*)'].'件';?>あります。</h2>-->
            <br>
                  <div class="frame">
                      <h2 class="form_title"><?php if($unreadCommentCount['COUNT(*)']==0){echo '未読コメントはありません。';}else{echo '未読のコメントが'.$unreadCommentCount['COUNT(*)'].'件あります。';}?></h2>
                      <br>
                      <table>
                          
                             <?php $a = 1;?>
                            <tr>
                            <td>
                            <?php//foreach($unreadComments as $unreadComment):?>
                                <?php for($i=0; $i<$unreadCommentCount['COUNT(*)']; $i++):?>
                                <?php $unreadComment = $unreadComments[$i];?>

                               <div class="result_box">
                               <strong><?php echo $i+1;?>.</strong>
                                    <div>
                                            <?php if ($unreadComment['publish_status'] == 2):?>
                                                <P class="private_post"><?php echo '非公開';?></p>
                                            <?php endif;?>
                                            <p>コメント投稿者：<?php echo $unreadComment['name'];?>&nbsp;さん</p>
                                            <p>コメント投稿日時：<?php echo $unreadComment['comment_at'];?></p>
                                            <p><?php echo $unreadComment['c_content'];?>
                                    </div>
                                    <br>
                                    <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($unreadComment['posts_id'])?>">記事詳細ページで確認する</a>
                                    <br>
                                    <form class="horizontal" action="./notice.php" method="post">
                                      
                                             <input type="hidden" name="read_status" value="1">
                                             <input type="hidden" name="comments_id" value="<?php echo $unreadComment['id'];?>">
                                             <input class="btn" type="submit" value="既読にする">
                                          
                                    </form>
                                    <form class="horizontal" action="./notice.php" method="post">
                                           <input type="hidden" name="delete" value="2">
                                           <input type="hidden" name="comments_id" value="<?php echo $unreadComment['id'];?>">
                                           <input class="btn red" type="submit" value="コメント削除">
                                    </form>
                                </div>
                               <?php endfor;?>
                             <?php// endforeach;?>
                             </td>
                            </tr>
                            </table>
                            
                            <br>
                            <br>
                            <h2 class="form_title"><?php if($readCommentCount['COUNT(*)']==0){echo '既読のコメントはありません';}else{echo '既読のコメントが'.$readCommentCount['COUNT(*)'].'件あります。';}?></h2>
                            <br>
                            <table>

                            
                            <tr>
                            <td>
                                <?php $b=1;?>
                             <?php foreach($readComments as $readComment):?>
                                <div class="result_box">
                                <strong><?php echo $b++;?>.</strong>
                                
                            
                                    <div>
                                            <?php if ($readComment['publish_status'] == 2):?>
                                                <P class="private_post"><?php echo '非公開';?></p>
                                            <?php endif;?>
                                            <p>コメント投稿者：<?php echo $readComment['name'];?>&nbsp;さん</p>
                                            <p>コメント投稿日時：<?php echo $readComment['comment_at'];?></p>
                                            <p><?php echo $readComment['c_content'];?></p>
                                    </div>
                                    <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($readComment['posts_id'])?>">記事詳細ページで確認する</a>
                                    <br>
                                
                                    <form class="horizontal" action="./notice.php" method="post">
                                      
                                           <input type="hidden" name="read_status" value="0">
                                           <input type="hidden" name="comments_id" value="<?php echo $readComment['id'];?>">
                                           <input class="btn" type="submit" value="未読にする">
                                        
                                    </form>
                                 
                                      <form class="horizontal" action="./notice.php" method="post">
                                           <input type="hidden" name="delete" value="2">
                                           <input type="hidden" name="comments_id" value="<?php echo $readComment['id'];?>">
                                           <input class="btn red" type="submit" value="コメント削除">
                                    </form>
                                    
                                 
                                </div>
                            <?php endforeach;?>
                            </td>
                            </tr>
                            </table>

                            


                  </div><!--frame-->
                  <a href="#" class="fixed_btn">TOPへ戻る</a><br>

               </div><!--typein-->
            </div><!--container-->
        </div><!--wrapper-->
     </label>
    </body>
</html>

