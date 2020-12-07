<?php
session_start();

ini_set('display_errors', true);

require_once './../../private/database.php';
require_once './../../private/functions.php';

//var_dump($_SESSION['user']);
//var_dump($user);
//↑nullになる。。。なぜ？？
//↓はちゃんと配列になってユーザーのデータが入っている。なのに＄userに代入できない。
//var_dump($_SESSION['user']);
//var_dump($_POST['password']);
//空欄のままかと言うバリデーションではなく、入力されたアドレスとパスワードが、DBのと合っているかの照会。
$errors = [];

//var_dump($_POST);
if(!empty($_POST)){

    //require_once '../functions/database.php';
    $dbh = dbconnect();
    $user = findUserByEmail($dbh,$_POST['email']);
    //var_dump($user["0"]["password"]);
    
      if(!empty($user)){
        if(password_verify($_POST["password"], $user["0"]["password"])){
              session_regenerate_id(true);
              $_SESSION['login'] = true;
              $_SESSION['user'] = $user;
              header('Location: ./../../../index.php');
              exit();

            }else{
              $errors[] = 'パスワードが違います。';
            }
           
      }
      

        if(!$user){
            $errors[] = 'メールアドレスが違います。';
        }


  }

  
?>


<!DOCTYPE html>
<html lang="ja">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>ログイン</title>
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
      <link rel="stylesheet" href="./../../css/form.css">
      <link rel="stylesheet" href="./../../css/header.css">
  </head>

  <body>
      <?php include './../../header.php';?>
    
      <div class="wrapper">
         <div class="container">
            <div class="typein">
                <h1 class="form_title">ログイン</h1>
                    <?php if(isset($errors)): ?> 

                      <?php// var_dump($_SESSION); ?>
                    <ul class="error-box">
                　　  <?php foreach($errors as $error): ?> 
                  　     <li><?php echo $error; ?></li>
                　　   <?php endforeach;?>
                　　  </ul>
                  <?php endif;?> 

            
           
                <form action="./login.php" method="post">

                    <div class="form-item">
                        <label for="exampleInputEmail">メールアドレス</label><br>
                        <input type="email" name="email" id="exampleInputEmail" value="<?php if(isset($_POST['email'])){echo h($_POST['email']);}?>" required>
                        <!--上で、if文を使わず、echo〜とすると、エラーになる。POSTはグロバール変数だから初回アクセスなのか２回目アクセスなのか処理を切り分けられない。なので初回アクセス時にはもし値が入っていたらと条件をつける-->
                    </div>
                    <br>
                    <div class="form-item">
                        <label for="exampleInputPassword">パスワード</label><br>
                        <input type="password" name="password" id="exampleInputPassword" value="<?php if(isset($_POST['password'])){echo h($_POST['password']);}?>" required>
                    </div>
                    <br>
                    <input class="btn" type="submit" value="ログイン"><br>
                    <a class="fixed_btn link_aa" href="./register.php">新規登録はこちら</a>


                </form>
            </div><!--typein-->
         </div><!--container-->
      </div><!--wrapper-->
  </body>
</html>