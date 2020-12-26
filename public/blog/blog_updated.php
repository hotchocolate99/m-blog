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

//ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';

$blogs = $_POST;
var_dump($blogs);
$posts_id = $blogs['posts_id'];
//var_dump($posts_id);


if(isset($_POST['file_path_to_delete'])){
 $file_path_to_delete = $_POST['file_path_to_delete'];
}
//var_dump($file_path_to_delete);

//var_dump($_FILES['pic']);
if($_FILES['img']){
   $file = $_FILES['img'];
}else if($_FILES['pic']){
    $file = $_FILES['pic'];
}

var_dump($_FILES);

$filename = basename($file['name']);
$tmp_path = $file['tmp_name'];
$file_err = $file['error'];
$filesize = $file['size'];
$upload_dir = 'images/';
$save_filename = date('YmdHis'). $filename;
$caption = filter_input(INPUT_POST,'caption',FILTER_SANITIZE_SPECIAL_CHARS);


if(empty($blogs['title'])){
    header('Location: ./blog_update.php?error=invalid_title&posts_id='.$posts_id);
    exit();
}

if(mb_strlen($blogs['title'])>30){
    header('Location: ./blog_update.php?error=invalid_title_length&posts_id='.$posts_id);
    exit();
}

if(empty($blogs['content'])){
    header('Location: ./blog_update.php?error=invalid_content&posts_id='.$posts_id);
    exit();
}

if(empty($blogs['category'])){
    header('Location: ./blog_update.php?error=invalid_category&posts_id='.$posts_id);
    exit();
}

if(empty($blogs['publish_status'])){
    header('Location:./blog_update.php?error=invalid_status&posts_id='.$posts_id);
    exit();
}

if($caption){
    if(strlen($caption) > 140){
        header('Location:./blog_update.php?error=invalid_caption_length&posts_id='.$posts_id);
        exit();
    }
    if(empty($file)){
        header('Location:./blog_update.php?error=invalid_file&posts_id='.$posts_id);
      exit();
    }
}

//ファイルサイズバリデーション。エラーの数字が２の時はサイズオーバー。
if($filesize){
  if($filesize > 1048576 || $file_err == 2){
      header('Location:./blog_update.php?error=invalid_filesize&posts_id='.$posts_id);
      exit();
  }
}

//ファイルの拡張子のバリデーション
//許容するファイルの拡張子↓
$allow_ext = array('jpg','jpeg','png');
//実際のファイルの拡張子を確認 ↓　pathinfo関数で。＄file_extには実際のファイルの拡張子が入る。
$file_ext = pathinfo($filename,PATHINFO_EXTENSION);
$save_path = $upload_dir.$save_filename;

//これ（$save_path）がメソッドに渡す引数である$file_pathになる！！！
//var_dump($save_path);
//in_array関数で＄file_ext が $allow_ext　のどれに当てはまるかのチェック。strtolowerは実際のファイルの拡張子が大文字だったら小文字に直してくれる。
if($file_ext && $allow_ext){
    if(!in_array(strtolower($file_ext),$allow_ext)){
      header('Location:./blog_update.php?error=invalid_file&posts_id='.$posts_id);
      exit();
    }
}

//ファイルがアップロードされているかのバリデーション。　アップロード＝一時保存　is_uploaded_file($tmp_path)関数で、$tmp_pathにアップロードされているかをみる。trueならアップロード成功。
//次にmove_uploaded_file($tmp_path, $save_path）関数で、第一引数から第二引数に場所を移す。（一時保存場所から本当の保存先へ）

$msgs = [];
if($tmp_path && $save_path && $upload_dir){
   if(is_uploaded_file($tmp_path)){
       if(move_uploaded_file($tmp_path, $save_path)){
          $msgs[] = $filename .'を'.$upload_dir .'に保存しました。';
       }else{
          $msgs[] = 'ファイルが保存できませんでした。';
       }
    }
}

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);


//---------------------------------------------------
//$_FILES['img']は既存の画像。＄＿FILES['pic']は新規の画像。

blogUpdate($blogs, $posts_id);
if(!empty($_FILES['img']['name'])){
   fileUpdate($blogs, $file, $save_path, $posts_id);
}else if(isset($file_path_to_delete)){
    deleteFile($posts_id, $file_path_to_delete);
}

if(!empty($_FILES['pic'])){
    addNewFile($blogs, $file, $save_path, $posts_id);
}

if(!empty($caption)){
    addCaption($caption, $posts_id);
}



?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>更新完了</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/comp_msg.css">
        <link rel="stylesheet" href="./../../css/header.css">

    </head>

    <body>

           <?php include './../../header.php';?>

           <label for="check">
              <div class="wrapper">
                 <div class="container">
                　  <div class="typein">
                
                       <p class="form_title">記事は更新されました。</p>
                       <a class="fixed_btn link_aa" href="./blog_detail.php?id=<?php echo h($posts_id)?>">記事へ戻る</a>
                       
                    </div>
                 </div>
             　</div>
          　</label>
    </body>
</html>