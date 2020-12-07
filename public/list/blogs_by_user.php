<?php
//----ログイン状態-----------------
session_start();

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
//--------------------------------

require_once './../../private/database.php';
require_once './../../private/functions.php';

ini_set('display_errors',true);


function getBlogByUser($users_id){
    $dbh = dbConnect();
    
        $sql = "SELECT posts.id, title, category, post_at, content, likes, user_name, nickname, intro_text FROM posts JOIN users ON posts.users_id = users.id WHERE posts.users_id = :users_id ORDER BY posts.id DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':users_id',$users_id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;

}

//index.phpのプロフィールから送られてくるユーザーのidを使ってそのユーザーの書いた記事を全て取得
$users_id = $_GET['id'];
$blogsByUser = getBlogByUser($users_id);
//var_dump($blogsByUser);

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getUnreadCommentCount($users_id);

?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ユーザー別記事一覧</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/cate_list.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

        <?php include './../../header.php';?>

        <div class="wrapper">
            <div class="container">
            　  <div class="typein">

            　　　　<div class="profile">

                    　<p class="prof_title"><strong><i class="fas fa-user-circle">&nbsp;<?php echo $blogsByUser[0]['nickname'];?>&nbsp;&nbsp;さんのプロフィール</i></strong></p>
                    　　　
                    　　　<h3 class="nickname"><?php echo $blogsByUser[0]['nickname'];?></h3>
                    　　　
                    　　　<p class="text"><?php echo $blogsByUser[0]['intro_text'];?></p>
            　　　　</div>

　　　　　　　　　　　　<h2 class="cate_title"><i class="fas fa-file"></i><?php echo $blogsByUser[0]['nickname'];?>さんの記事一覧</h2>
                  <div class="frame">

                                <?php foreach($blogsByUser as $blogByUser):?>　
                                    <div class="result_box">
                                        <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($blogByUser['id'])?>">
                                        <dl>
                                                <dt><strong><?php echo $blogByUser['title'];?></strong></dt>
                                                <dd class="date"><?php echo setCateName($blogByUser['category']);?>&nbsp;&nbsp;<?php echo $blogByUser['post_at'];?>&nbsp;&nbsp;(<i class="fas fa-heart"></i><?php echo h($blogByUser['likes'])?>)</dd>
                                                <br>
                                                <dd class="content"><?php echo mb_substr($blogByUser['content'],0,60);?></dd>
                                        </dl>
                                        </a>

                                    </div>
                                <?php endforeach;?>
                    </div><!--frame-->

                  <a href="#" class="fixed_btn to_home">TOPへ戻る</a><br>

               </div><!--typein-->
            </div><!--container-->
        </div><!--wrapper-->

    </body>
</html>