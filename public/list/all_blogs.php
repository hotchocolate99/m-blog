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

//ブログの全件の全てのデータを取得し、記事一覧を表示-----
$blogDatas = getData();
//var_dump($blogDatas);
foreach($blogDatas as $blogData){
  //var_dump($blogData);
}

//ブログの総件数を取得----------------------------
$blogs_total = getDataCount();
//↑ は配列だったので ↓
//var_dump($total["COUNT(*)"]);


//お知らせの隣に表示させる未読のコメント数（これはログインユーザーの。セッションの。）
$UnreadCommentCount = getCommentCount($users_id, 0);
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

       <label for="check">
        <div class="wrapper">
            <div class="container">
            　  <div class="typein">
                   <h2><i class="fas fa-pencil-alt"></i>記事一覧<span>（全<?php echo $blogs_total["COUNT(*)"];?>件）</span></h2>

            <div class="frame">

                      <table>

                            <tr>
                            <td>

                            <?php for($i=0; $i<$blogs_total['COUNT(*)']; $i++):?>
                                <?php $blogData = $blogDatas[$i];?>
                                
                                    
                            <div class="result_box"> 
                            <strong><?php echo $i+1;?>.</strong>
                                <p><a class="link_aa" href="/public/list/blogs_by_user.php?id=<?php echo h($blogData['users_id'])?>"><?php echo h($blogData['nickname'])?></a>&nbsp;さんの投稿</p>
                                <a class="link_aa" href="/public/blog/blog_detail.php?id=<?php echo h($blogData['id'])?>">
                                    <dl>
                                        <dt class="detail"><h3><?php echo h($blogData['title'])?></h3></dt>
                                        <!--<div class="flex">-->
                                          <dd class=date><?php echo h($blogData['post_at'])?>&nbsp;&nbsp;<?php echo h(setCateName($blogData['category']))?>&nbsp;&nbsp;(<i class="fas fa-heart"></i><?php echo h($blogData['likes'])?>)</dd>
                                        <!--</div>-->
                                    </dl>
                                </a>
                            </div>
                            
                            
                            <?php endfor;?>

                         </td>
                         </tr>
                         </table>
                  </div>


                  <a href="#" class="fixed_btn to_home">TOPへ戻る</a><br>

               </div><!--typein-->
            </div><!--container-->
        </div><!--wrapper-->
      </label>
    </body>
</html>