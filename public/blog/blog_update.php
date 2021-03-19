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


//blog_detail.phpから＄GETでidを受け取った。
//if($_GET['id']){
 //$id = $_GET['id'];
//}


if(isset($_GET['id'])){
    $posts_id = $_GET['id'];

}elseif(isset($_GET['posts_id'])){
  $posts_id = $_GET['posts_id'];
}

var_dump($_GET);
$result_posts = getById($posts_id,'posts');
if($fileDatas = getFileById($posts_id)){
    $file_name = $fileDatas['file_name'];
    $file_path = $fileDatas['file_path'];
    $caption = $fileDatas['caption'];
    $posts_id = $fileDatas['posts_id'];
  }


$posts_id = $result_posts['id'];
//＄id　を　「$_GET['id']」　から　「$result['id']」　に更新することで、インプットタイプのhiddenに
//乗せて、記事の更新や削除の過程へ移行する。
$title = $result_posts['title'];
$content = $result_posts['content'];
$category = (int)$result_posts['category'];
//result['category']は文字列なので、$categoryに数字として代入するために（int）を付ける。
$publish_status = $result_posts['publish_status'];

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>記事編集</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/header.css">
        <link rel="stylesheet" href="./../../css/form.css">
    </head>

    <body>

       <?php include './../../header.php';?>

       <label for="check">
            <div class="wrapper">
                <div class="container">
                    <div class="typein">

                        <h2 class="form_title">記事編集フォーム</h2>

                            <div class="error_msg">
                                    <?php if (isset($_GET["error"])):?>

                                        <?php if ($_GET["error"]=="invalid_title"):?>
                                        <p><?php echo "タイトルを入力してください。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_title_lengh"):?>
                                        <p><?php echo "タイトルは25字以下にして下さい。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_content"):?>
                                        <p><?php echo "本文を入力して下さい。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_category"):?>
                                            <p><?php echo "カテゴリーは必須です。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_status"):?>
                                            <p><?php echo "公開ステータスは必須です。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_caption"):?>
                                            <p><?php echo "キャプションを入力して下さい";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_file"):?>
                                            <p><?php echo "キャプションをつける画像がありません。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_caption_length"):?>
                                            <p><?php echo "キャプションは140文字以内で入力して下さい。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_filesize"):?>
                                            <p><?php echo "ファイルサイズは１MB未満にして下さい。";?></p>
                                        <?php endif;?>

                                        <?php if ($_GET["error"]=="invalid_file"):?>
                                            <p><?php echo "画像ファイルを添付して下さい。";?></p>
                                        <?php endif;?>


                                    <?php endif;?>
                            </div><!--error_msg-->

                        <form action="./blog_updated.php" method="POST" enctype="multipart/form-data" class="formspace">
                            <input type="hidden" name="posts_id" value="<?php echo h($posts_id) ?>">

                                <div class="form_item"><p>タイトル</p></div>
                                <input type="text" class="form_text" name="title" value="<?php echo h($title)?>">

                                <div class="form_item"><p>ブログ本文</p></div>
                                <textarea name="content" id="content" cols="100" rows="20"><?php echo h($content)?></textarea>
                                <br>

                                <div class="form_item"><p>カテゴリ</p></div>
                                <select name="category">
                                    <option value="0" <?php if($category === 0){ echo 'selected'; }?> >指定なし</option>
                                    <option value="1" <?php if($category === 1){ echo 'selected'; }?> >テーマ１</option>
                                    <option value="2" <?php if($category === 2){ echo 'selected'; }?> >テーマ２</option>
                                    <option value="3" <?php if($category === 3){ echo 'selected'; }?> >その他</option>

                                </select>
                                <br>
                                <br>

                                <div class="form_item"></div>
                                <input class="radio" type="radio" name="publish_status" value="1" <?php if($publish_status === 1){ echo 'checked'; }?> >公開
                                <input class="radio" type="radio" name="publish_status" value="2" <?php if($publish_status === 2){ echo 'checked'; }?> >非公開
                                <br>
                                <br>

                                <div class="form_item">画像</div>
                                　<?php if(!empty($fileDatas['file_path'])):?>
                                　　　<img src="./../blog/<?php echo "{$fileDatas['file_path']}";?>"　width="120px" height="200px" alt="blog_image" >
                                    <br>
                                    <br>
                                    　<p>画像を変更する場合は、新しい画像を選択してください。</p>
                                        <input name="img" type="file"/>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />

                                    <br>

                                    <p>画像を削除する場合は、画像削除にチェックを入れて下さい。</p>
                                        <div class="form_item"></div>
                                        <input type="radio" name="file_path_to_delete" value="<?php echo "{$fileDatas['file_path']}";?>">画像削除

                                　<?php else:?>
                                    <p>画像を追加する場合は、画像を選択して下さい。</p>
                                    <input name="pic" type="file"/>
                                    <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                                  <?php endif;?>
                                     <?php// var_dump($_FILES['pic']);?>
                                <br>
                                <br>
                                <textarea
                                calss="caption"
                                name="caption"
                                placeholder="キャプション（140文字以下)"
                                id="caption" cols="50" rows="10" 
                                ><?php if(isset($fileDatas['caption'])){echo h($fileDatas['caption']);}?></textarea>

                                <br>
                                <br>

                                <input type="submit" value="更新" class="btn">

                        </form>


                    </div><!--typein-->
                </div><!--container-->
            </div><!--wrapper-->
      </label>
      
    </body>
</html>