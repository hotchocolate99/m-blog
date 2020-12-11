<?php
//----ログイン状態-----------------
session_start();

if (!$_SESSION['login']) {
    header('Location: ./../../account/login.php');
    exit();
  }

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
    $users_id = $_SESSION['user'][0]['id'];
  }

//--------------------------------
ini_set('display_errors',true);
var_dump($_SESSION['user'][0]['id']);
require_once './../../private/database.php';
require_once './../../private/functions.php';

//プロフィールの更新の際、既存のデータを表示できない。。。
//var_dump($user['0']['id']);
if(function_exists('getProfileDatas')) {
    $profileDatas = getProfileDatas($user['0']['id']);

    $nickname = $profileDatas['0']['nickname'];
    $intro_text = $profileDatas['0']['intro_text'];
}

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>プロフィール入力</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/form.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

       <?php include './../../header.php' ?>
       


        <div class="wrapper">
            <div class="container">
                 <div class="typein">

                      <h2 class="form_title">プロフィール入力フォーム</h2>
                         <div class="error_msg">
                            <?php if (isset($_GET["error"])):?>

                                <?php if ($_GET["error"]=="invalid_nickname"):?>
                                <p><?php echo "ニックネームを入力してください。";?></p>
                                <?php endif;?>

                                <?php if ($_GET["error"]=="invalid_intoro_text"):?>
                                <p><?php echo "自己紹介文は200字以下にして下さい。";?></p>
                                <?php endif;?>

                            <?php endif;?>
                        </div><!--error_msg-->

        　　　　　　　　　　　
                        <form action="./profile_posted.php" method="POST">

                        <?php if (isset($_GET["error"])):?>

                           　<?php if ($_GET["error"]=="invalid_nickname"):?>
                            　　<p><?php echo "ニックネームを入力してください。";?></p>
                             <?php endif;?>

                             <?php if ($_GET["error"]=="invalid_intro_text"):?>
                               <p><?php echo "自己紹介文は300字以下にして下さい。";?></p>
                        　　  <?php endif;?>
                        
                        <?php endif;?>






                                <div class="form_item"><p>ニックネーム</p></div>
                                    <input type="text" class="form_text" name="nickname" value="<?php if(isset($nickname)){echo h($nickname);}?>">

                                <div class="form_item"><p>自己紹介文</p></div>
                                    <textarea name="intro_text" id="intro_text" cols="50" rows="10"><?php if(isset($nickname)){echo h($nickname);}?></textarea>
                                <br>

                                <input type="submit" value="決定" class="btn">
                        </form>


                </div><!--typein-->
            </div><!--container-->
        </div> <!--wrapper-->

    </body>
</html>

