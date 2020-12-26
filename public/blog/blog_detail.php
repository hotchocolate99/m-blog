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

//topページから詳細ページに遷移する時に$_GET['id']が渡ってくる。また、コメントのページから詳細ページに戻ってくる時は$_GET['posts_id']が渡ってくる。これらは同じ値なので、ここで$idに統一して、この後の処理で使いやすくしている。（反省点）
if(isset($_GET['id'])){
  $id = $_GET['id'];

}elseif(isset($_GET['posts_id'])){
$id = $_GET['posts_id'];
}
//var_dump($id);

//選択された記事の詳細を表示するため、前ページからGETで受け取ったidを引数にしてgetById()を呼び出し、詳細情報を取得。またこの＄resul['id']を次ページ（編集やコメント、削除）へ送る。
$idResult = getById($id,);

//表示する画像がある場合(引数は記事のid)----------------------------------------------------------
$fileDatas = getFileById($id);
//var_dump($fileDatas);


//いいねボタンの処理(引数は記事のid)-------------------------------------------------------------

//var_dump($id);

//ハートの隣にいいねの数を表示するため、いいねの総数を取得
function likesSoFar($id){
    $dbh = dbConnect();

    $sql = "SELECT likes FROM posts WHERE id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindValue(':id',(int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();

    return $result;
 }

//いいねが押された時の処理（likesカラムに１を足す処理）
function likesCount($id){
    $dbh = dbConnect();

    $sql = "UPDATE posts SET likes = likes + 1 WHERE id = :id;";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindValue(':id',(int)$id, PDO::PARAM_INT);
    $stmt->execute();
 
    $sql = "SELECT likes FROM posts WHERE id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindValue(':id',(int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
 
    return $result;
 }


 //いいねが押されてたら、いいねを一つ増やし、URLのplusLikeのクエリを消す処理（plusLikeを消さないと、ページをリロードするたびにいいねが増えてしまうので。）
  if(!empty($_GET["plusLike"])){
      $plusLike = $_GET["plusLike"];
      $likesCount = likesCount($id);

      //$_SERVER['DOCUMENT_ROOT']は絶対パス。現在のページは$_SERVER['PHP_SELF']変数、または$_SERVER['SCRIPT_NAME']変数で取得出来る。そこにid足す。stayAtLikeBtnはいいねボタンが押された時にページのトップへ行かないように付けたもの。
      header("Location:" .$_SERVER['PHP_SELF']."?id={$id}"."#stayAtLikeBtn");
  }

//ハートの隣にいいねの数を表示する
  $likesSoFar = likesSoFar($id);
 //var_dump($likesCount);

//---------------------------------------------------------

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);

//全ユーザーのデータを取得------------------------------------------------
$allUsers = getAllusers();
//var_dump($allUsers);
foreach($allUsers as $allUser){
  //var_dump($allUser['id']);
}

?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ブログ詳細</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/detail.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

        <?php include './../../header.php';?>
        <label for="check">
            <div class="wrapper">
                <div class="container">
                    <div class="left">

                        <div class="frame">
                            <h2 class="title"><?php echo h($idResult['title']);?></h2>

                            <?php if ($idResult['publish_status'] == 2):?>
                               <P class="private_post"><?php echo '非公開';?></p>
                            <?php endif;?>

                               <p class="date_posted"><?php echo h($idResult['post_at']);?></p>
                               <p><?php echo h(setCateName($idResult['category']));?></p>
                               <p class="blog_content"><?php echo nl2br(h($idResult['content']));?></p>

                            <?php if($fileDatas):?><!--↓{}で囲むのは、変数を展開させるから。-->
                                <img src="<?php echo "{$fileDatas['file_path']}";?>"　width="240px" height="400px" alt="blog_image" >
                                <p><?php if(isset($fileDatas['caption'])){echo nl2br("{$fileDatas['caption']}");}?></p>
                            <?php endif;?>

                            <!--いいねボタンが非同期処理ではないため、ボタンが押されるとページのトップへ行ってしまう。それをなんとかしたくて、ここに非表示のaタグを付けた。-->
                            <a class="stayAtLikeBtn" name="stayAtLikeBtn"></a>

                        </div><!--frame-->

                        <div class="icon_box">
    　　　　　　　　　　　　　　<div class="likes"><a class="link_aa" href="./blog_detail.php?posts_id=<?php echo h($idResult['id'])?>&plusLike=1"><i class="fas fa-heart"></i><p class="likes">いいね(<?php if(isset($likesCount)){echo $likesCount["likes"];}else{echo $likesSoFar["likes"];}?>)</p></a></div>

                            <a class="link_aa" href="./../comment/comment_post.php?id=<?php echo h($idResult['id'])?>"><span><i class="fas fa-comment"></i>コメントする</span></a>

                        </div>

                        <?php if(!empty($_SESSION['user'])):?>
                            <?php if($user[0]['id'] == $idResult['users_id']):?>

                        　　    <div class="opt">
                          　　     <a class="link_aa" href="./blog_update.php?id=<?php echo h($idResult['id'])?>"><i class="fas fa-edit"></i>記事の編集</a>
                          　　     <a class="link_aa" href="./blog_delete.php?id=<?php echo h($idResult['id'])?>"><i class="fas fa-trash-alt"></i>記事の削除</a>
                        　　    </div><!--opt-->
                            <?php endif ;?>
                        <?php endif;?>

                        <div class="frame second">
                            <h2 class="title">コメント一覧</h2>

                                <?php if($comDatas = getComment($id)):?>
                                    <?php foreach($comDatas as $comData):?>

                                        <div class="comment_box">
                                             <dl>
                                                <div class="flex">
                                        　        <dt class="title"><?php echo h($comData['name']);?>さんのコメント</dt>
                                        　        <dd class="date_posted"><?php echo h($comData['comment_at']);?></dd>
                                                </div>
                                              </dl>

                                              <div>
                                        　        <p ><?php echo nl2br(h($comData['c_content']));?></p>
                                              </div>
                                        </div><!--comment_box-->

                                    <?php endforeach;?>
                                <?php endif;?>

                                <a href="#" class="fixed_btn">TOPへ戻る</a><br>


                        </div><!--frame second-->
                    </div><!--left-->

                    <div class="right">
                    　　<div class="menu">
                        　<ul>
                            <li>
                                <div class="search">
                                    <form action="./../list/list_search_result.php" method="post">
                                        <input type="text" name="search_word" class="sample2Text" placeholder="記事検索">
                                    </form>
                                </div>
                             </li>

                             <li><a href="./../list/all_blogs.php" class="link_a"><i class="fas fa-file"></i>投稿記事一覧</a></li>
                            
                             <li class="list"><a href="#" class="link_a"><i class="fas fa-file"></i>テーマ別記事一覧</a>
                              
                              <ul>
                                  <li><a href="/public/blog/blog_cate_list.php#cate1" class="link_a">テーマ１</a></li>
                                  <li><a href="/public/blog/blog_cate_list.php#cate2" class="link_a">テーマ２</a></li>
                                  <li><a href="/public/blog/blog_cate_list.php#cate3" class="link_a">その他</a></li>
                              </ul>
                            </li>

                            <li class="list"><a href="#" class="link_a"><i class="fas fa-file"></i>ユーザー別記事一覧</a>
                              <ul>
                                <?php foreach($allUsers as $allUser):?>
                                   <li><a class="link_a" href="/public/list/blogs_by_user.php?id=<?php echo h($allUser['id'])?>"><?php echo $allUser['nickname'];?>&nbsp;さんの記事一覧</a></li>
                                <?php endforeach;?>
                              </ul>
                            </li>

                            <li><a href="./../list/list_files.php" class="link_a"><i class="fas fa-camera"></i>画像一覧</a></li>

    　　　　　　　　　　　　　</ul>
    　　　　　　　　　　　</div><!--menu-->

                      <div class="side_footer">footer</div>
                    　　　
                 </div><!--right-->
               </div><!--container-->
            </div> <!--wrapper-->

      </label>
    </body>
</html>

