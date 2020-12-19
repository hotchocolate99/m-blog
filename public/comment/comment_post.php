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

//詳細ページから渡ってきた記事のid

if(isset($_GET['id'])){
    $posts_id = $_GET['id'];

}
//elseif(isset($_GET['posts_id'])){
  //  $posts_id = $_GET['posts_id'];
//}

var_dump($posts_id);

//下は、コメント投稿ページから完了ページに飛ぶときにあたいが入るので、ここでは空っぽ。
//var_dump($_POST);
//var_dump((int)$id);
$comment = $_POST;
//var_dump($comment);


//$p = getFile((int)$id);
//var_dump($p);
//foreach($p as $q){
//    echo $q['id'];
//}
//$result = getById($id,'posts');
//テーブル名はクォーテーション付けなくても大丈夫だった。
//$_SESSION = $result;
//header('Location:home.php#selected_topic');
//var_dump($SESSION);
//↑ちゃんと配列になって入っている。

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);

?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>コメント投稿</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/form.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

        <?php include './../../header.php';?>

        <label for="check">
        <div class="wrapper">
            <div class="container">
                <div class="typein">

                        <h2 class="form_title"><span><i class="fas fa-comment"></i>この記事にコメントする</span></h2>
                        
                        <div class="error_msg">
                        　<?php if (isset($_GET["error"])):?>

                            <?php if ($_GET["error"]=="invalid_c_name"):?>
                            <p><?php echo " 名前を入力して下さい。";?></p>
                            <?php endif;?>

                            <?php if ($_GET["error"]=="invalid_c_content"):?>
                            <p><?php echo "コメントは200字以下で入力して下さい。";?></p>
                            <?php endif;?>

                        　<?php endif;?>
                        </div><!--error_msg-->

                            <form action="./comment_posted.php" method="POST"　class="formspace">
                            <input type="hidden" name="posts_id" value="<?php echo h($posts_id) ?>">

                            <p class="form_item">名前</p>
                            <input type="text" class="form_text" name="name">

                            <div class="form_item"><p>コメント</p></div>
                            <textarea name="c_content" id="c_content" cols="30" rows="10"></textarea>
                            <br>

                            <input type="submit" value="投稿" class="btn">
                            </form>





                            <a class="fixed_btn" href="./../blog/blog_detail.php?id=<?php echo h($posts_id)?>">記事へ戻る</a><br>
                </div><!--typein-->
            </div><!--container-->
        </div> <!--wrapper-->
     </label>
    </body>
</html>