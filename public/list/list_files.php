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

function getAllFiles(){
    $dbh = dbConnect();

        $sql = "SELECT * FROM posts JOIN files ON posts.id = files.posts_id ORDER BY posts.id DESC";

        //JOIN posts ON files.posts_id = posts.id ORDER BY id DESC
        $stmt = $dbh->query($sql);

        $allFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);;

        return $allFiles;
}

function getFilesCount(){
    $dbh = dbConnect();

        $result;
        $sql = "SELECT COUNT(*) FROM files";

        $stmt = $dbh->query($sql);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}

$allFiles = getAllFiles();
$result = getFilesCount();
//var_dump($allFiles);
//var_dump($result);
//↑も配列だった
var_dump($_SERVER['DOCUMENT_ROOT']);
?>




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>画像一覧</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/files_list.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

        <?php include './headerL.php';?>
        

        <div class="wrapper">
            <div class="container">
            　  <div class="typein">

                    <?php foreach($result as $value):?>
                      <h2 class="form_title">画像一覧（全<?php echo $value["COUNT(*)"];?>件）</h2>
                    <?php endforeach;?>

                    <div class="frame">
                        <?php foreach($allFiles as $allFile):?>
                              <div class="file_box">
                                <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($allFile['posts_id'])?>">
                                <p><strong><?php echo "{$allFile['title']}";?></strong></P>
                                <p><?php echo "{$allFile['post_at']}";?></P>
                                <img src="<?php echo "{$allFile['file_path']}";?>"　width="180px" height="300px" alt="blog_image" >
                                <p><?php echo $allFile['caption'];?></p>
                                </a>
                              </div>
                        <?php endforeach;?>

                        <a href="#" class="fixed_btn">TOPへ戻る</a></div><br>
                        
                    </div>

               </div><!--typein-->
            </div><!--container-->
        </div><!--wrapper-->

    </body>
</html>

