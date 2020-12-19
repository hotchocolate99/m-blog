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

//echo __FILE__;
//ini_set('display_errors',true);

require_once './../../private/database.php';
require_once './../../private/functions.php';

$blogs = $_POST;
//var_dump($blogs);

//$fileには配列でデータが入っている。
$file = $_FILES['img'];
//var_dump($file);
//↓basename()関数で、ディレクトリトラバーサル対策。ファイルのパスを排除し、最後のファイル名の部分だけを返してくれるようにする。これでパスから情報を盗まれることはない。
$filename = basename($file['name']);
$tmp_path = $file['tmp_name'];
$file_err = $file['error'];
$filesize = $file['size'];
$upload_dir = 'images/';
$save_filename = date('YmdHis'). $filename;
//↑fileに日付をつけることで、同じ画像も何度でも保存出来るようになる。
//キャプションはPOSTで送られてくるので、↓で、本当にPOSTで送られて来たかのチェックと、sanitizing. $blogで受け取っているけど、画像とセットでfileテーブルに送るのでここに書いておく。
$caption = filter_input(INPUT_POST,'caption',FILTER_SANITIZE_SPECIAL_CHARS);


//ブログ記事、ファイルとキャプションのバリデーション　返り値はなしでOK？？？

    if(empty($blogs['title'])){
        header('Location: ./blog_post.php?error=invalid_title');
        exit();
    }

    if(mb_strlen($blogs['title'])>30){
        header('Location: ./blog_post.php?error=invalid_title_length');
        exit();
    }

    if(empty($blogs['content'])){
        header('Location: ./blog_post.php?error=invalid_content');
        exit();
    }

    if(empty($blogs['category'])){
        header('Location: ./blog_post.php?error=invalid_category');
        exit();
    }

    if(empty($blogs['publish_status'])){
        header('Location:./blog_post.php?error=invalid_status');
        exit();
    }

    //if(empty($caption)){
      //  header('Location:./blog_post.php?error=invalid_caption');
      //  exit();
    //}

    
   if($caption){
        if(strlen($caption) > 140){
            header('Location:./blog_post.php?error=invalid_caption_length');
            exit();
        }
    }

    //ファイルサイズバリデーション。エラーの数字が２の時はサイズオーバーしているということなので。
    if($filesize){
      if($filesize > 1048576 || $file_err == 2){
          header('Location:./blog_post.php?error=invalid_filesize');
          exit();
      }
    }

    //ファイルの拡張子のバリデーション
    //許容するファイルの拡張子↓
    $allow_ext = array('jpg','jpeg','png');
    //実際のファイルの拡張子を確認 ↓　pathinfo関数で。＄file_extには実際のファイルの拡張子が入る。
    $file_ext = pathinfo($filename,PATHINFO_EXTENSION);
    $save_path = $upload_dir.$save_filename;

//in_array関数で＄file_ext が $allow_ext　のどれかに当てはまるかのチェック。strtolowerは実際のファイルの拡張子が大文字だったら小文字に直してくれる。
    if($file_ext && $allow_ext){
        if(!in_array(strtolower($file_ext),$allow_ext)){
          header('Location:./blog_post.php?error=invalid_file');
          exit();
        }
    }

//ファイルがアップロードされているかのバリデーション。　アップロード＝一時保存　is_uploaded_file($tmp_path)関数で、$tmp_pathにアップロードされているかをみる。trueならアップロード成功。
//次にmove_uploaded_file($tmp_path, $save_path）関数で、第一引数から第二引数に場所を移す。（一時保存場所から本当の保存先へ）

$msgs = [];
if($tmp_path && $save_path && $upload_dir){
   if(is_uploaded_file($tmp_path)){
        if(move_uploaded_file($tmp_path, $save_path)){
            //$msgs[] = $filename .'を'.$upload_dir .'に保存しました。';
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
var_dump($file);

if($blogs && $filename && $save_path || $caption){
   blogCreateWithFile($blogs, $filename, $save_path, $caption, $users_id);

  }else if(!empty($blogs) && empty($file['file_path'])){
     blogCreateWithoutFile($blogs, $users_id);

}

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
                        <?php if($msgs):?>
                          <?PHP foreach($msgs as $msg):?>
                            <p><?php echo $msg;?></p>
                          <?php endforeach ;?> 
                        <?php endif ;?>

                        <p>投稿を完了しました。</P>

                        <?php $this_blog = getNewestBlog(1);?>

                        <a class="fixed_btn" href="./blog_detail.php?id=<?php echo h($this_blog[0]['id'])?>">記事詳細へ</a></div><br>
                    </div>
                </div>
            </div>
         </label>

    </body>
</html>