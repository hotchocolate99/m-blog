<?php
//----ログイン状態-----------------
session_start();

if (!$_SESSION['login']) {
    header('Location: ./../../login.php');
    exit();
  }

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
//--------------------------------
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

       <?php include './headerB.php';?>


        <div class="wrapper">
            <div class="container">
                 <div class="typein">

                      <h2 class="form_title">記事投稿フォーム</h2>
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

        　　　　　　　　　　　<!--enctype="multipart/form-data" は画像ファイルのためにformタグに付ける決まり文句-->
                        <form action="./blog_posted.php" method="POST" enctype="multipart/form-data" >

                                <div class="form_item"><p>タイトル</p></div>
                                    <input type="text" class="form_text" name="title">

                                <div class="form_item"><p>ブログ本文</p></div>
                                    <textarea name="content" id="content" cols="100" rows="20"></textarea>
                                <br>

                                <div class="form_item"><p>カテゴリ</p></div>
                                    <select name="category">
                                        <option value=1>テーマ１</option>
                                        <option value=2>テーマ２</option>
                                        <option value=3>その他</option>
                                    </select>
                                <br>
                                <br>

                                <div class="form_item"></div>
                                    <input type="radio" name="publish_status" value="1" checked>公開
                                    <input type="radio" name="publish_status" value="2">非公開
                                <br>
                                <br>

                                <div class="form_item"><p>画像</p></div>
                                <input name="img" type="file" accept="image/*"/><br>
                                <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
                                <br>
                                <textarea
                                    name="caption"
                                    placeholder="キャプション（140文字以下）"
                                    id="caption" cols="50" rows="10"
                                ></textarea>
                                <br>
                                <br>

                                <input type="submit" value="投稿" class="btn">
                        </form>


                </div><!--typein-->
            </div><!--container-->
        </div> <!--wrapper-->

    </body>
</html>

