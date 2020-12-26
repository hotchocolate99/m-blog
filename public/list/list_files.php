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

function getAllFiles(){
  $dbh = dbConnect();

       $sql = "SELECT * FROM posts JOIN files ON posts.id = files.posts_id WHERE publish_status = 1 ORDER BY posts.id DESC";

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

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $results;
}

$allFiles = getAllFiles();
foreach($allFiles as $allFile){
//var_dump($allFile['id'].$allFile['file_path']);
}


$results = getFilesCount();
//var_dump($allFiles);
//var_dump($results);
//↑も配列だった
//var_dump($_SERVER['DOCUMENT_ROOT']);


//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);
//var_dump($allFiles);
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

        <?php include './../../header.php';?>
        
        <label for="check">
          <div class="wrapper">
              <div class="container">
              　  <div class="typein">

                      <?php foreach($results as $result):?>
                        <h2 class="form_title">画像一覧（全<?php echo $result["COUNT(*)"];?>件）</h2>
                      <?php endforeach;?>

                      <div class="frame">
                      　　<?php if($allFiles):?>
                          　　<?php foreach($allFiles as $allFile):?>

                                <div class="file_box">
                                  <a class="link_aa" href="./../blog/blog_detail.php?id=<?php echo h($allFile['posts_id'])?>">
                                  <p><strong><?php echo "{$allFile['title']}";?></strong></P>
                                  <p><?php echo "{$allFile['post_at']}";?></P>
                                  <img src="./../blog/<?php echo "{$allFile['file_path']}";?>"　width="120px" height="200px" alt="blog_image" >
                                  <p><?php echo $allFile['caption'];?></p>
                                  </a>
                                </div>
                          　　<?php endforeach;?>
                          <?php endif;?>

                          <a href="#" class="fixed_btn">TOPへ戻る</a></div><br>
                          
                      </div>

                </div><!--typein-->
              </div><!--container-->
          </div><!--wrapper-->
      　</label>
      
    </body>
</html>

