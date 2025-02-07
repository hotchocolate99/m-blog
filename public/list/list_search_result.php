<?php
//----ログイン状態-----------------
session_start();

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
//--------------------------------

//ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';



$search_word = $_POST['search_word'];
//var_dump($search_word);

$results = getSearchWord($search_word);
//var_dump($results);

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);

?>




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>記事検索の結果</title>
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
                      <div class="frame">
                        <h2 class="form_title"><i class="fas fa-search"></i>検索ワード「<?php echo h($search_word);?>」の記事検索結果は次の通りです。</h2>
                                <?php if(empty($results)):?>
                                    <p class="notfound"><?php echo '検索ワードは見つかりませんでした。';?></p>
                                <?php endif;?>

                                <?php foreach($results as $result):?>

                                    <div class="result_box">
                                        <?php if($result['publish_status']==1):?>
                                           <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($result['id'])?>">
                                             <dl>
                                                <dt><?php echo $result['title'];?></dt>
                                                <dd><?php echo strstr($result['content'],$search_word);?></dd>
                                             </dl>
                                           </a>
                                        <?php endif;?>
                                    </div><!--result_box-->

                                <?php endforeach;?>
                                

                      </div><!--frame-->
                      <a href="./../../index.php" class="fixed_btn">HOMEへ戻る</a><br>

                   </div><!--typein-->
                </div><!--container-->
            </div><!--wrapper-->
      </label>
    </body>
</html>

