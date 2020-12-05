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

ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';

$blogs = $_POST;
//var_dump($blogs);

$file = $_FILES['img'];
var_dump($file);

$filename = basename($file['name']);
$tmp_path = $file['tmp_name'];
$file_err = $file['error'];
$filesize = $file['size'];
$upload_dir = 'images/';
$save_filename = date('YmdHis'). $filename;
$caption = filter_input(INPUT_POST,'caption',FILTER_SANITIZE_SPECIAL_CHARS);

if($caption){
    if(strlen($caption) > 140){
        header('Location:./blog_update.php?error=invalid_caption_length');
        exit();
    }
}

//ファイルサイズバリデーション。エラーの数字が２の時はサイズオーバーしているということなので。
if($filesize){
  if($filesize > 1048576 || $file_err == 2){
      header('Location:./blog_update.php?error=invalid_filesize');
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
//in_array関数で＄file_ext が $allow_ext　のどれかに当てはまるかのチェック。strtolowerは実際のファイルの拡張子が大文字だったら小文字に直してくれる。
if($file_ext && $allow_ext){
    if(!in_array(strtolower($file_ext),$allow_ext)){
      header('Location:./blog_update.php?error=invalid_file');
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

}else{
    //$msgs[] = 'ファイルが選択されていません。';
}
}

//if($blogs && !empty($filename) && !empty($save_path) && !empty($caption)){
//   blogCreateWithoutFile($blogs);
//}
//var_dump($blogs);


//var_dump($save_filename);

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getUnreadCommentCount();
//var_dump($UnreadCommentCount['COUNT(*)']);




//---------------------------------------------------
blogValidate($blogs);
blogUpdate($blogs);
if(!empty($file['name'])){
fileUpdate($blogs,$file,$save_path);
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

           <?php include './headerB.php';?>

            <div class="wrapper">
                <div class="container">
                　 <div class="typein">
                    <p class="form_title">記事は更新されました。</p>
                    <a class="fixed_btn link_aa" href="./blog_detail.php?id=<?php echo h($comment['posts_id'])?>">記事へ戻る</a>
                    <a class="fixed_btn" href="./blog_detail.php?id=<?php echo h($blogs['id'])?>">記事へ戻る</a></div><br>
                </div>

                </div>
            </div>

    </body>
</html>