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
require_once './../../private/database.php';
require_once './../../private/functions.php';

var_dump($_POST);
var_dump($_FILES);

if(!empty($_POST)){
    $blogs = $_POST;
        //var_dump($blogs);

         //ブログ記事、ファイルとキャプションのバリデーション
         $errors = [];
        
         if(empty($blogs['title'])){
             $errors[] = 'タイトルを入力してください。';
         }

         if(mb_strlen($blogs['title'])>31){
             $errors[] = 'タイトルは30字以下にして下さい。';
             
         }

         if(empty($blogs['content'])){
             $errors[] = '本文を入力して下さい。';
         }

         if(empty($blogs['category'])){
             $errors[] = 'カテゴリーは必須です。';
         }

         if(empty($blogs['publish_status'])){
             $errors[] = '公開ステータスは必須です。';
         }


    if(isset($_FILES['img'])){
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

            //画像がない時はキャプションも無しにする。（ちなみに画像ありのキャプション無しはあり。）
            if(empty($file['name']) && $caption){
                $errors[] = '画像を選択してください。';
            }
            
            if($caption && $file['name']){
                if(strlen($caption) > 141){
                    $errors[] = 'キャプションは140文字以内で入力して下さい。';
                }
            }
        

            //ファイルサイズバリデーション。エラーの数字が２の時はサイズオーバーしているということなので。
            if($filesize){
               if($filesize > 1048576 || $file_err == 2){
                  $errors[] = 'ファイルサイズは１MB未満にして下さい。';
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
                    $errors[] = '画像ファイルを添付して下さい。';
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

                //}else{
                    //$msgs[] = 'ファイルが選択されていません。';
                //}
                }
            }
    }    
    
    if(empty($errors && $msgs)){
        //画像ありの場合となしの場合の投稿
        if($blogs && $filename && $save_path || $caption){
            blogCreateWithFile($blogs, $filename, $save_path, $caption, $users_id);
            header("Location:./blog_posted.php");

        }else if(!empty($blogs) && empty($filename)){
            blogCreateWithoutFile($blogs, $users_id);
            header("Location:./blog_posted.php");
        }
          
        
    }
}

//お知らせの隣に表示させる未読のコメント数（これはログインユーザーの。セッションの。）
$UnreadCommentCount = getCommentCount($users_id, 0);


//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ブログ投稿</title>
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

                        <h2 class="form_title">記事投稿フォーム</h2>
                            <div class="error_msg">

                                <?php if(isset($errors)): ?> 
                                    <ul class="error-box">
                                        <?php foreach($errors as $error): ?> 
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach ?> 
                                    </ul>
                                <?php endif ?>

                                <?php if(isset($msgs)): ?> 
                                    <ul class="error-box">
                                        <?php foreach($msgs as $msg): ?> 
                                            <li><?php echo $msg; ?></li>
                                        <?php endforeach ?> 
                                    </ul>
                                <?php endif ?>
                                
                            </div><!--error_msg-->

            　　　　　　　　　　　<!--enctype="multipart/form-data" は画像ファイルのためにformタグに付ける決まり文句-->
                            <form action="./blog_post.php" method="POST" enctype="multipart/form-data" >

                                    <div class="form_item"><p>タイトル</p></div>
                                        <input type="text" class="form_text" name="title" value="<?php if(isset($blogs['title'])){echo h($blogs['title']);};?>">

                                    <div class="form_item"><p>ブログ本文</p></div>
                                        <textarea name="content" id="content" cols="100" rows="20"><?php if(isset($blogs['content'])){echo h($blogs['content']);}?></textarea>
                                    <br>
                                    
                                    <div class="form_item"><p>カテゴリ</p></div>
                                        <select name="category">

                                           <?php if(empty($blogs['category'])):?>
                                                <option value=1>テーマ１</option>
                                                <option value=2>テーマ２</option>
                                                <option value=3>その他</option>
                                            <?php elseif(!empty($blogs['category'])):?>
                                                <option value=1 <?php echo $blogs['category'] == '1 '? 'selected' : '' ?>>テーマ１</option>
                                                <option value=2 <?php echo $blogs['category'] == '2' ? 'selected' : '' ?>>テーマ２</option>
                                                <option value=3 <?php echo $blogs['category'] == '3' ? 'selected' : '' ?>>その他</option>
                                            <?php endif;?>
                                        </select>
                                    <br>
                                    <br>

                                    <?php if(empty($blogs['publish_status'])):?>
                                        <div class="form_item"></div>
                                            <input class="radio" type="radio" name="publish_status" value="1">公開
                                            <input class="radio" type="radio" name="publish_status" value="2" checked>非公開
                                        <br>
                                        <br>

                                    <?php elseif(!empty($blogs['publish_status'])):?>
                                        <div class="form_item"></div>
                                            <input class="radio" type="radio" name="publish_status" value="1" <?php echo $blogs['publish_status'] == '1' ? 'checked' : '' ?>>公開
                                            <input class="radio" type="radio" name="publish_status" value="2" <?php echo $blogs['publish_status'] == '2' ? 'checked' : '' ?>>非公開
                                        <br>
                                        <br>
                                    <?php endif;?>

                                    <div class="form_item"><p>画像</p></div>
                                        　<input name="img" type="file" accept="image/*"/><br>
                                        　<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                                    <br>
                                    
                                    <textarea
                                        class="caption"
                                        name="caption"
                                        placeholder="キャプション（140文字以下）"
                                        id="caption" cols="50" rows="10"
                                    ><?php if(isset($caption)){echo h($caption);}?></textarea>
                                    <br>
                                    <br>

                                    <input type="submit" value="投稿" class="btn">
                            </form>


                    </div><!--typein-->
                </div><!--container-->
            </div> <!--wrapper-->
      </label>
    </body>
</html>

