<?php
require_once 'database.php';

//---------------ユーザー登録、ログイン-----------------
/** ユーザー登録
     * ＠param array $userData
     * return bool $result
    */
    if(!function_exists('createUser')) {
    function createUser($userData){

        $result = false;
          $sql = "INSERT INTO users (user_name, email, password) VALUE(:user_name, :email, :password)";

          $dbh = dbConnect();
        //sign_upファイルの方でこのcreateUserメソッドの呼び出し時に引数にPOSTを入れた。そのため、POSTがこの＄userDataに
        //入り、＄userData[name]とすることで、＄＿POST[name]の値が取得できる。
        //パスワードはここでハッシュ化すること！！　DBに入れる時！「password_hash(パスワード,PASSWORD_DEFAULT);」 とする。
        //第二引数は決まり文句。意味：デフォルトでハッシュ化する。
        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':user_name', $userData['user_name'],PDO::PARAM_STR);
            $stmt->bindValue(':email', $userData['email'],PDO::PARAM_STR);
            $stmt->bindValue(':password', password_hash($userData['password'],PASSWORD_DEFAULT),PDO::PARAM_STR);
            
            $result = $stmt->execute();

             return $result;

          }catch(PDOException $e){
            
            exit($e);
        }
      }
    }
           //↑プレースホルダーの値が文字列のものだけ、または、数字でも文字列として扱ってもOKな場合は、＄arrのように配列にして、
           //$execute()の引数に入れることが出来る。bindValue使わなくても大丈夫。
  
  
    /**
     * email,DBでユーザーを取得
     * ＠param string $dbh 
     * ＠param string $email 
     * return array   (ログインフォームに入力されたアドレスでDBに入っているデータを検索し、照会できたらそのユーザーの登録情報全てを配列で取得)
     */

    if(!function_exists('findUserByEmail')) {
      function findUserByEmail($dbh, $email){
  
        try{
          $sql = "SELECT * FROM users WHERE email = :email";

          $dbh = dbConnect();
          $stmt = $dbh->prepare($sql);
          $stmt->bindValue(':email', $email, PDO::PARAM_STR);
          $stmt->execute();
          $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
           return $user;
        }catch(PDOException $e){
           
            exit($e);
        }
        
      }
   }


//----------------------投稿--------------------------------------

//ブログ新規投稿(画像あり)
if(!function_exists('blogCreateWithFile')) {
    function blogCreateWithFile($blogs, $filename, $save_path, $caption, $users_id){

        $sql = "INSERT INTO posts(title, content, category, publish_status, likes, users_id)
                VALUES(:title, :content, :category, :publish_status, 0, :users_id)";

        $dbh = dbConnect();
        $dbh->beginTransaction();

        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':title', $blogs['title'],PDO::PARAM_STR);
            $stmt->bindValue(':content', $blogs['content'],PDO::PARAM_STR);
            $stmt->bindValue(':category', $blogs['category'],PDO::PARAM_INT);
            $stmt->bindValue(':publish_status', $blogs['publish_status'],PDO::PARAM_INT);
            $stmt->bindValue(':users_id', $users_id,PDO::PARAM_INT);

            $stmt->execute();

            $posts_id = $dbh->lastInsertId();


            $sql = "INSERT INTO files(file_name, file_path, caption, posts_id)VALUES(:file_name, :file_path, :caption, :post_id)";

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':file_name',$filename,PDO::PARAM_STR);
            $stmt->bindValue(':file_path',$save_path,PDO::PARAM_STR);
            $stmt->bindValue(':caption',$caption,PDO::PARAM_STR);
            $stmt->bindValue(':post_id',$posts_id,PDO::PARAM_INT);

            $stmt->execute();
            $dbh->commit();

        }catch(PDOException $e){
            $dbh->rollBack();
            exit($e);
        }
    }

}

 //ブログ新規投稿(画像なし)
 if(!function_exists('blogCreateWithoutFile')) {
    function blogCreateWithoutFile($blogs, $users_id){

        $sql = "INSERT INTO posts(title, content, category, publish_status, likes, users_id)
                VALUES(:title, :content, :category, :publish_status, 0, :users_id)";

        $dbh = dbConnect();
        $dbh->beginTransaction();

        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':title', $blogs['title'],PDO::PARAM_STR);
            $stmt->bindValue(':content', $blogs['content'],PDO::PARAM_STR);
            $stmt->bindValue(':category', $blogs['category'],PDO::PARAM_INT);
            $stmt->bindValue(':publish_status', $blogs['publish_status'],PDO::PARAM_INT);
            $stmt->bindValue(':users_id', $users_id,PDO::PARAM_INT);

            $stmt->execute();
            $dbh->commit();

        }catch(PDOException $e){
            $dbh->rollBack();
            exit($e);
        }
    }
}



//プロフィールの入力（ニックネームと自己紹介文）
if(!function_exists('CreateProfile')) {
    function CreateProfile($user_id,$nickname,$intro_text){

        
            $sql = "UPDATE users SET
                    nickname = :nickname, intro_text = :intro_text

                    WHERE id = :id;";

                    $dbh = dbConnect();
                    $dbh->beginTransaction();
         try{
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(':nickname', $nickname,PDO::PARAM_STR);
                    $stmt->bindValue(':intro_text', $intro_text,PDO::PARAM_STR);
                    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

                    $stmt->execute();
                    $dbh->commit();

         }catch(PDOException $e){
            $dbh->rollBack();
            exit($e);
         }
    }
}
    

//コメントを投稿
if(!function_exists('commentCreate')) {
    function commentCreate($comment){

        $sql = "INSERT INTO comments(name, c_content, posts_id)
                VALUES(:name, :c_content, :posts_id)";


        $dbh = dbConnect();
        $dbh->beginTransaction();


        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':name', $comment['name'],PDO::PARAM_STR);
            $stmt->bindValue(':c_content', $comment['c_content'],PDO::PARAM_STR);
            $stmt->bindValue(':posts_id', $comment['posts_id'],PDO::PARAM_INT);

            $stmt->execute();
            $dbh->commit();
            //echo 'コメントを投稿しました。';

        }catch(PDOException $e){
            $dbh->rollBack();
            //exit($e);
        }
    }
}


//-----------------更新--------------------------------------------------------

//ブログの更新　（トランザクションの意味ある？？）
if(!function_exists('blogUpdate')) {
    function blogUpdate($blogs){

        $sql = "UPDATE posts SET
                    title = :title, content = :content, category = :category, publish_status = :publish_status

                WHERE id = :id;";

        $dbh = dbConnect();
        $dbh->beginTransaction();

        try{
            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':title', $blogs['title'],PDO::PARAM_STR);
            $stmt->bindValue(':content', $blogs['content'],PDO::PARAM_STR);
            $stmt->bindValue(':category', $blogs['category'],PDO::PARAM_INT);
            $stmt->bindValue(':publish_status', $blogs['publish_status'],PDO::PARAM_INT);

            $stmt->bindValue(':id', $blogs['id'],PDO::PARAM_INT);


            $stmt->execute();
            $dbh->commit();
            //echo 'ブログを更新しました。';

        }catch(PDOException $e){
            $dbh->rollBack();
            exit($e);
        }
    }
}


//画像の更新
if(!function_exists('fileUpdate')) {
    function fileUpdate($blogs, $file, $save_path){

        $dbh = dbConnect();

        try{
            $sql = "UPDATE files SET file_name = :file_name, file_path = :file_path, caption = :caption

                WHERE posts_id = :posts_id;";

                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':file_name', $file['name'],PDO::PARAM_STR);
                $stmt->bindValue(':file_path', $save_path,PDO::PARAM_STR);
                $stmt->bindValue(':caption', $blogs['caption'],PDO::PARAM_INT);
                $stmt->bindValue(':posts_id', $blogs['id'],PDO::PARAM_INT);
            
                $stmt->execute();

        }catch(PDOException $e){
           
            exit($e);}

    }
}

//記事の更新時に画像を追加する
if(!function_exists('addNewFile')) {
      function addNewFile($blogs, $file, $save_path){
              $dbh = dbConnect();

           try{

                $sql = "INSERT INTO files(file_name, file_path, caption, posts_id) VALUES (:file_name, :file_path, :caption, :posts_id)";

                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':file_name',$file['name'],PDO::PARAM_STR);
                $stmt->bindValue(':file_path',$save_path,PDO::PARAM_STR);
                $stmt->bindValue(':caption',$blogs['caption'],PDO::PARAM_STR);
                $stmt->bindValue(':posts_id',$blogs['id'],PDO::PARAM_INT);
                $stmt->execute();


           }catch(PDOException $e){
            exit($e);}
       }
}

//-------取得----------------------------------------------------------------

//idとテーブル名を引数にして、DBからデータを取得
if(!function_exists('getById')) {
    function getById($id,$table){
        if(empty($id)){
            exit('不正なIDです。');
        }

        $dbh = dbConnect();
        $stmt = $dbh->prepare("SELECT * FROM $table WHERE id = :id");
        //注意すること！！！　sql文内で変数を展開する時はダブルクォーテーションにする！！シングルだと、変数展開できない。
        $stmt->bindValue(':id',(int)$id, PDO::PARAM_INT);
        //GET で送られてきたidは文字列として入ってくるので、（int）をここにつけることで、int型になる。そして数字として認識させる。なぜその必要があるのか？？？
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}


//posts.idを引数にして、画像を取得
if(!function_exists('getFileById')) {
    function getFileById($id){
        if(empty($id)){
            exit('不正なIDです。');
        }

        $dbh = dbConnect();
        $sql = "SELECT posts_id, file_name, file_path, caption FROM files JOIN posts ON files.posts_id = posts.id WHERE files.posts_id = :id";
        $stmt = $dbh->prepare($sql);
        
        $stmt->bindValue(':id',(int)$id, PDO::PARAM_INT);
        
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}


//getById（）とほぼ同じ。。。引数なしで、順番DESCで取得するところが違うだけ。(公開記事のみ）
//投稿されたブログ記事と画像を全て取得??filesがある時はfilesのデータも取得したい...そんなことしなくてもコメントを取得するのと同じ方法でfileがある時だけ表示させる？？どっち？？
if(!function_exists('getData')) {
    function getData(){
        $dbh = dbConnect();

        $sql = 'SELECT posts.*, users.nickname FROM posts JOIN users ON posts.users_id = users.id WHERE publish_status = 1 ORDER BY posts.id DESC';
        //$sql = 'SELECT * FROM posts JOIN files ON posts.id = files.posts_id ORDER BY posts.id DESC';

        $stmt = $dbh->query($sql);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}

 //投稿されたブログの件数(公開のみ)を取得
 if(!function_exists('getDataCount')) {
    function getDataCount(){
        $dbh = dbConnect();

        $sql = 'SELECT COUNT(*) FROM posts WHERE publish_status = 1';

        $stmt = $dbh->query($sql);

        $result = $stmt->fetch();

        return $result;
    }
 }

//最新のブログ記事取得（公開のみ）引数は取得する記事件数
if(!function_exists('getNewestBlog')) {
    function getNewestBlog($amount){
        $dbh = dbConnect();

        //$sql = "SELECT posts.*, users.nickname FROM posts JOIN users ON posts.users_id = users.id WHERE publish_status = 1 ORDER BY posts.id DESC LIMIT 1";
        $sql = "SELECT posts.*, users.nickname FROM posts JOIN users ON posts.users_id = users.id WHERE posts.publish_status = 1 ORDER BY posts.id DESC LIMIT :LIMIT";
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':LIMIT',(int)$amount, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        return $result;
    }
}

 //コメントを取得
 if(!function_exists('getComment')) {
    function getComment($id){
        $dbh = dbConnect();

        $sql = "SELECT * FROM comments JOIN posts ON comments.posts_id = posts.id WHERE posts.id = :id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':id',(int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$result){
            //exit();
        }

        return $result;

    }
 }

 //全コメントを取得
 if(!function_exists('getAllComments')) {
    function getAllComments(){
        $dbh = dbConnect();

        $sql = "SELECT * FROM comments";

        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$results){
            //exit();
        }

        return $results;

    }
 }

//お知らせ機能で使うため、readstatus別にコメントを取得(!!なぜかcommentsテーブルのidを取得できていなかった。* from comments　としてもidがposts_idになっていた。。。)
if(!function_exists('getCommentsByReadstatus')) {
    function getCommentsByReadstatus($users_id, $readstatus){
        $dbh = dbConnect();

        $sql = "SELECT *, comments.id FROM comments JOIN posts ON posts.id = comments.posts_id WHERE posts.users_id = :users_id AND comments.read_status = :read_status ORDER BY comment_at DESC";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':users_id',(int)$users_id, PDO::PARAM_INT);
        $stmt->bindValue(':read_status',(int)$readstatus, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }
 }

//readstatus別コメント数の取得
 if(!function_exists('getCommentCount')) {
    function getCommentCount($users_id, $read_status){
        $dbh = dbConnect();

        $sql = "SELECT COUNT(*) FROM comments JOIN posts ON posts.id = comments.posts_id WHERE posts.users_id = :users_id AND comments.read_status = :read_status ";
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':users_id',(int)$users_id, PDO::PARAM_INT);
        $stmt->bindValue(':read_status',(int)$read_status, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result;

    }
}




//画像を取得　引数：記事のid(filesテーブルのposts_id)であってfilesテーブルのidではない。
if(!function_exists('getFile')) {
    function getFile($id){
        $dbh = dbConnect();

        $sql = "SELECT files.* FROM files JOIN posts ON files.posts_id = posts.id WHERE posts.id = :id";

        $stmt = $dbh->prepare($sql);
        $stmt ->bindValue(':id',(int)$id, PDO::PARAM_INT);
        $stmt->execute();
        $fileDatas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $fileDatas;

    }
}

//プロフィールのデータ（だけでなくuserテーブルの全てだけど）を取得
if(!function_exists('getProfileDatas')) {
    function getProfileDatas($users_id){
        $dbh = dbConnect();
         
        //  users.idと明確に指定しないと、idキーの値がposts_idになってしまう。しかもユーザーによってまちまちなので、本当に謎。。。
        $sql = "SELECT *, users.id FROM users JOIN posts ON users.id = posts.users_id WHERE users.id = :users_id";

        $stmt = $dbh->prepare($sql);
        $stmt ->bindValue(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        $profileDatas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $profileDatas;
    }
}

//全ユーザーのデータを取得
if(!function_exists('getAllusers')) {
    function getAllusers(){
        $dbh = dbConnect();

        //$sql = "SELECT * FROM users";
        $sql = "SELECT count(*) AS user_count, users.* FROM users GROUP BY id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}


//ーーーーーーーーー削除ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー

//ブログ削除　メインテーブルから　
if(!function_exists('deleteMain')) {
    function deleteMain($id,$table){
        if(empty($id)){
            exit('不正なIDです。');
        }

    $dbh = dbConnect();
    $stmt = $dbh->prepare("DELETE FROM $table WHERE id = :id");
    //注意すること！！！　sql文内で変数を展開する時はダブルクォーテーションにする！！シングルだと、変数展開できない。
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    //GET で送られてきたidは文字列として入ってくるので、（int）をここにつけることで、int型になる。そして数字として認識させる。なぜその必要があるのか？？？
    $stmt->execute();
    //echo '記事は削除されました。';
    }
}

//ブログ削除　付随のテーブルから
if(!function_exists('deleteSide')) {
    function deleteSide($id,$table){
    if(empty($id)){
        exit('不正なIDです。');
    }

    $dbh = dbConnect();
    $stmt = $dbh->prepare("DELETE FROM $table WHERE posts_id = :id");
    //注意すること！！！　sql文内で変数を展開する時はダブルクォーテーションにする！！シングルだと、変数展開できない。
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    //GET で送られてきたidは文字列として入ってくるので、（int）をここにつけることで、int型になる。そして数字として認識させる。なぜその必要があるのか？？？
    $stmt->execute();
    //echo '記事は削除されました。';

    }
}

//ブログのアップデート時に画像だけ削除
if(!function_exists('deleteFile')) {
    function deleteFile($posts_id, $file_path_to_delete){
        if(empty($posts_id)){
            exit('不正なIDです。');
        }
        /*$dbh = dbConnect();
        $stmt = $dbh->prepare("DELETE FROM files WHERE posts_id = :posts_id");
            $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
            $stmt->execute();*/

        $dbh = dbConnect();
        $stmt = $dbh->prepare("DELETE FROM files WHERE posts_id = :posts_id AND file_path = :file_path");
        $stmt->bindValue(':posts_id', (int)$posts_id, PDO::PARAM_INT);
        $stmt->bindValue(':file_path', $file_path_to_delete ,PDO::PARAM_STR);
        $stmt->execute();


   }

}

//-------------------その他-----------------------------

//いいねランキング
if(!function_exists('likesRanking')) {
    function likesRanking(){
     $dbh = dbConnect();
      //下の関数はMySQLでは使えない。。。
     //$sql = "SELECT title, RANK() OVER(ORDER BY likes DESC) AS ranking, post_at, likes FROM posts";
 
     $sql = "SELECT likes, posts.id, title, post_at, category, posts.users_id, users.nickname FROM posts JOIN users ON posts.users_id = users.id ORDER BY likes DESC LIMIT 10";
     $stmt = $dbh->query($sql);
     $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
     return $results;
 
     //$sql = "SELECT * FROM posts WHERE";
     //$stmt = $dbh->query($sql);
     //$results2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
     
     //return $results2
 
    }
 }


//XSS対策エスケープ
if(!function_exists('h')) {
    function h($s){
        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
    }

    //ブログのバリデーション　返り値はなしでOK？？？
    function blogValidate($blogs){

        if(empty($blogs['title'])){
            exit('タイトルを入力してください。');
        }

        if(mb_strlen($blogs['title'])>26){
            exit('タイトルは25字以下にして下さい。');
        }

        if(empty($blogs['content'])){
            exit('本文を入力して下さい。');
        }

        if(empty($blogs['category'])){
            exit('カテゴリーは必須です。');
        }

        if(empty($blogs['publish_status'])){
            exit('公開ステータスは必須です。');
        }
    }

    //カテゴリーを数字表記からちゃんとした文字表現に変更
    function setCateName($cate){
        if($cate ===0){
            return '指定なし';
        }

        if($cate=== 1){
            return 'テーマ１';
        }

        if($cate === 2){
            return 'テーマ２';
        }

        if($cate === 3){
            return 'その他';
        }
    }
}


//コメントステータスを既読にする
if(!function_exists('switchToRead')) {
    function switchToRead($comments_id){
  
      $sql = "UPDATE comments SET read_status = 1 WHERE id = :id;";
  
          $dbh = dbConnect();
          try{
              $stmt = $dbh->prepare($sql);
              //$stmt->bindValue(':publish_status', $blogs['publish_status'],PDO::PARAM_INT);
              $stmt->bindValue(':id', $comments_id,PDO::PARAM_INT);
              $stmt->execute();
  
          }catch(PDOException $e){
              exit($e);
          }
  
    }
  }

//コメントステータスを未読にする
  if(!function_exists('switchToUnread')) {
    function switchToUnread($comments_id){
  
      $sql = "UPDATE comments SET read_status = 0 WHERE id = :id;";
  
          $dbh = dbConnect();
          try{
              $stmt = $dbh->prepare($sql);
              //$stmt->bindValue(':publish_status', $blogs['publish_status'],PDO::PARAM_INT);
              $stmt->bindValue(':id', $comments_id,PDO::PARAM_INT);
              $stmt->execute();
  
          }catch(PDOException $e){
              exit($e);
          }
  
    }
  }

  //コメントの削除
  if(!function_exists('deleteComment')) {
    function deleteComment($comments_id){
        $dbh = dbConnect();
        $stmt = $dbh->prepare("DELETE FROM comments WHERE id = :id");
        $stmt->bindValue(':id', (int)$comments_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
  