<?php
ini_set('display_errors', true);

require_once './../../private/database.php';
require_once './../../private/functions.php';

$errors =[];

ini_set('display_errors', true);
//if(!empty($_POST)){}がないと、最初からフォーム画面にエラーメッセージが表示される。
if(!empty($_POST)){

    $user_name = $_POST['user_name'];
    if(!$user_name || 20 < strlen($user_name)){
        $errors[] = '名前を入力して下さい。';
    }

    $email = $_POST['email'];
    if(!$email || !filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors[] = 'メールアドレスを入力して下さい。';
    }

    $dbh = dbconnect();
    $user = findUserByEmail($dbh, $email);
    if($user){
        $errors[] = 'このメールアドレスは使えません。';
    }

    $password = $_POST['password'];
    if(!preg_match("/\A[a-z\d]{8,100}+\z/i",$password)){
        $errors['password'] = 'パスワードは英数字８文字以上１００文字以下にしてください。';
    }

    $password_conf = $_POST['password_conf'];
    if($password !== $password_conf){
        $errors[] = '確認用パスワードが間違っています。';
    }


    var_dump($_POST);
    if(count($errors) === 0){

        //require '../functions/classes.php';

        $hasCreated = createUser($_POST);
        header('Location: ./registered.php');

        if(!$hasCreated){
            $errors[] = '登録に失敗しました';
        }
    }

}

?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ユーザー登録</title>
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
                    <h1 class="form_title">ユーザー登録</h1>
                    <br>

                    <?php if(isset($errors)): ?> 
                        <ul class="error-box">
                          <?php foreach($errors as $error): ?> 
                             <li><?php echo $error; ?></li>
                          <?php endforeach ?> 
                        </ul>
                    <?php endif ?>

                    <form action="./register.php" method="post">
                        <div class="form-item">
                            <label for="exampleInputName">名前</label><br>
                            <input type="text" name="user_name" id="exampleInputName"  placeholder="名前" value="<?php if(isset($_POST['name'])){echo h($name);}?>" placeholder="メールアドレス"required>
                        </div>
                        <br>

                        <div class="form-item">
                            <label for="exampleInputEmail">メールアドレス</label><br>
                            <input type="email" name="email" id="exampleInputEmail" value="<?php if(isset($_POST['email'])){echo h($email);}?>" placeholder="メールアドレス" required>
                        </div>
                        <br>

                        <div class="form-item">
                            <label for="exampleInputPassword">パスワード</label><br>
                            <input type="password" name="password" id="exampleInputPassword" value="<?php if(isset($_POST['password'])){ echo h($password);}?>" placeholder="パスワード" required>
                        </div>
                        <br>

                        <div class="form-item">
                            <label for="exampleInputPassword_conf">パスワード確認</label><br>
                            <input type="password" name="password_conf" id="exampleInputPassword_conf" value="<?php if(isset($_POST['password_conf'])){echo h($password_conf);}?>" placeholder="パスワード" required>
                        </div>
                        <br>
                        <input class="btn" type="submit" value="登録">

                    </form>
                </div><!--typein-->
            </div><!--container-->
        </body><!--wrappr-->
   </label>
</html>