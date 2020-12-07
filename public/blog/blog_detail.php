<?php
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
//--------------------------------
require_once './../../private/database.php';
require_once './../../private/functions.php';

//↓getById（）とほぼ同じ。。。引数なしで、順番DESCで取得するところが違うだけ。サイドの記事一覧に使っている。
$blogData = getData();
//var_dump($blogData);
///$newest_blogData = getNewestBlog();
//var_dump($newest_blogData);

//↑の＄blogDataから、選択された$column['id']を取得するのが出来なかったので、一旦、それをdetail.phpにGETで送った。。。
//それをSESSIONに入れて、またこのhome.phpに戻し、詳細を表示するようにした。

$total = getDataCount();
//↑ は配列だったので ↓
//var_dump($total["COUNT(*)"]);

//他のページから詳細ページに遷移するためにGETを使っている。<a href="blog_detail.php?id=<?php echo h($変数['id'])>">詳細へ<
$id = $_GET['id'];


//選択された記事の詳細を表示するため、前ページからGETで受け取ったidを引数にしてgetById()を呼び出し、詳細情報を取得。またこの＄resul['id']を次ページ（編集やコメント、削除）へ送る。
$idResult = getById($id,'posts');
var_dump($idResult);
//テーブル名はクォーテーション付けなくても大丈夫だった。
//$_SESSION = $result;
//header('Location:home.php#selected_topic');
//var_dump($SESSION);
//↑ちゃんと配列になって入っている。
//var_dump($id);
//if(!empty(getComment($id))){
  //$comData = getComment($id);
//}

//if(comData['pots_id']){}
//var_dump($comData);
//nullになる。。。。


//表示する画像がある場合(引数は記事のid)----------------------------------------------------------
//var_dump($id);
$fileDatas = getFileById($id);
//var_dump($fileDatas);



//いいねボタンの処理-------------------------------------------------------------

//var_dump($id);
function likesSoFar($id){
    $dbh = dbConnect();

    $sql = "SELECT likes FROM posts WHERE id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindValue(':id',(int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();

    return $result;
 }



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

 //var_dump($_GET['id']);
 if(!empty($_GET["id"])){
    $id = $_GET["id"];
    //var_dump($like_id);

    if(!empty($_GET["plusLike"])){
        $plusLike = $_GET["plusLike"];
        $likesCount = likesCount($id);

        //$_SERVER['DOCUMENT_ROOT']は絶対パス。現在のページは$_SERVER['PHP_SELF']変数、または$_SERVER['SCRIPT_NAME']変数で取得出来る。そこにid足す　；）
        header("Location:" .$_SERVER['PHP_SELF']."?id={$id}"."#stayAtLikeBtn");

        }else{
          $likesSoFar = likesSoFar($id);
        }
    }

 //var_dump($likesCount);

//---------------------------------------------------------

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getUnreadCommentCount($users_id);

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

        <div class="wrapper">
            <div class="container">
                <div class="left">

                    <div class="frame">
                        <h2 class="title"><?php echo h($idResult['title']);?></h2>
                        <p class="date_posted"><?php echo h($idResult['post_at']);?></p>
                        <p><?php echo h(setCateName($idResult['category']));?></p>
                        <p class="blog_content"><?php echo h($idResult['content'])?></p>

                        <?php if($fileDatas):?>
                                <img src="<?php echo "{$fileDatas['file_path']}";?>"　width="240px" height="400px" alt="blog_image" >
                                <p><?php if(isset($fileDatas['caption'])){echo "{$fileDatas['caption']}";}?></p>
                        <!--↑{}で囲むのは、変数を展開させるから。-->
                        <?php endif;?>

                      <a class="stayAtLikeBtn" name="stayAtLikeBtn"></a>

                    </div><!--frame-->

                    <div>
　　　　　　　　　　　　　　<div class="likes"><a class="link_aa" href="./blog_detail.php?id=<?php echo h($idResult['id'])?>&plusLike=1"><i class="fas fa-heart"></i><p class="likes">いいね(<?php if(isset($likesCount)){echo $likesCount["likes"];}else{echo $likesSoFar["likes"];}?>)</p></a></div>

                        <a class="link_aa" href="./../comment/comment_post.php?id=<?php echo h($idResult['id'])?>"><span><i class="fas fa-comment"></i>この記事にコメントする</span></a>
                       <!--getで記事のidをコメントページに渡している-->
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
                                     　      <p ><?php echo h($comData['c_content'])?></p>
                                     <!--↑のp class="blog_content"-->
                                          </div>
                                    </div><!--comment_box-->

                                 <?php endforeach;?>
                             <?php endif;?>

                             <a href="#" class="fixed_btn">TOPへ戻る</a><br>


                    </div><!--frame-->
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

                        <li><a href="./../list/list_files.php" class="link_a"><i class="fas fa-camera"></i>画像一覧</a></li>
                        <li class="list"><a href="#" class="link_a"><i class="fas fa-file"></i>テーマ別記事一覧</a>
                          <ul>
                          <li><a href="./blog_cate_list.php#cate1" class="link_a">テーマ１</a></li>
                              <li><a href=".blog_cate_list.php#cate2" class="link_a">テーマ２</a></li>
                              <li><a href="./blog_cate_list.php#cate3" class="link_a">その他</a></li>
                          </ul>
                        </li>

　　　　　　　　　　　　　</ul>
　　　　　　　　　　　</div><!--menu-->

                  <div class="blogs">
                      <p><span><i class="fas fa-pencil-alt"></i>記事一覧<span>（全<?php echo $total["COUNT(*)"];?>件）</span></p>
                         <?php foreach($blogData as $column):?>

                            <div class="blog_box"> 
                                <p class="small"><a class="link_aa" href="./../list/blogs_by_user.php?id=<?php echo h($column['users_id'])?>"><?php echo h($column['nickname'])?></a>さんの投稿</p>
                                <a class="link_aa" href="./blog_detail.php?id=<?php echo h($column['id'])?>">

                                        <div class="detail small"><span><?php echo h($column['title'])?></span></div>
                                          <div class="date small"><?php echo h($column['post_at'])?></div>
                                          <div class="small"><?php echo h(setCateName($column['category']))?></div>
                                          <div class="small">(<i class="fas fa-heart"></i><?php echo h($column['likes'])?>)</div>
                                </a>
                            </div>
                         <?php endforeach;?>

                  </div>

                  <div class="side_footer">footer</div>
                　　　
            </div><!--right-->
            </div><!--container-->
        </div> <!--wrapper-->


    </body>
</html>