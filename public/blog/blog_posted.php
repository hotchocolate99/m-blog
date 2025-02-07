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
  $users_id = $user[0]['id'];
//--------------------------------

ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';



//お知らせの隣に表示させる未読のコメント数（これはログインユーザーの。セッションの。）
$UnreadCommentCount = getCommentCount($users_id, 0);


?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>投稿完了</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <!--<link rel="stylesheet" href="../css/home.css">-->
        <link rel="stylesheet" href="./../../css/header.css">
        <link rel="stylesheet" href="./../../css/form.css">
    </head>

    <body>

      <?php include './../../header.php';?>

         <label for="check">
            <div class="wrapper">
                <div class="container">
                　   <div class="typein">
                        <p class="form_title"></p>
                        <p>投稿を完了しました。</P>

                         <!--最新記事＝ここで投稿している記事なので、ここで最新記事のidを取得することで、詳細ページへ飛べるようにしている。-->
                        <?php $this_blog = getNewestBlog(1);?>
                          <a class="fixed_btn" href="./blog_detail.php?id=<?php echo h($this_blog[0]['id'])?>">記事詳細へ</a>
                        <br>
                     </div>
                </div>
              </div>
         </label>
    </body>
  </html>