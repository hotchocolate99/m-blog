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
        
        //パスワードはここでハッシュ化すること！！　DBに入れる時！「password_hash(パスワード,PASSWORD_DEFAULT);」 とする。
        //第二引数は決まり文句。意味：デフォルトでハッシュ化する。

          $dbh = dbConnect();
          $stmt = $dbh->prepare($sql);
          $stmt->bindValue(':user_name', $userData['user_name'],PDO::PARAM_STR);
          $stmt->bindValue(':email', $userData['email'],PDO::PARAM_STR);
          $stmt->bindValue(':password', password_hash($userData['password'],PASSWORD_DEFAULT),PDO::PARAM_STR);
          $result = $stmt->execute();

          return $result;

      }
    }
           //↑プレースホルダーの値が文字列のものだけ、または、数字でも文字列として扱ってもOKな場合は、＄arrのように配列にして、
           //$execute()の引数に入れることが出来る。bindValue使わなくても大丈夫。
  
  
    /**
     * email,DBでユーザーを取得
     * ＠param string $dbh
     * ＠param string $email
     * return array $user (ログインフォームに入力されたアドレスでDBに入っているデータを検索し、照会できたらそのユーザーの登録情報全てを配列で取得)
     */
    if(!function_exists('findUserByEmail')) {
      function findUserByEmail($dbh, $email){

          $sql = "SELECT * FROM users WHERE email = :email";

          $dbh = dbConnect();
          $stmt = $dbh->prepare($sql);
          $stmt->bindValue(':email', $email, PDO::PARAM_STR);
          $stmt->execute();
          $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
           
          return $user;
        
      }
   }


//----------------------投稿--------------------------------------


    /**ブログ新規投稿(画像ありの場合)
     * ＠param array $blogs
     * ＠param string $filename
     * ＠param string $save_path
     * ＠param string $caption
     * ＠param int $users_id
     * return NA
     */
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

    /**ブログ新規投稿(画像なしの場合)
     * ＠param array $blogs
     * ＠param int $users_id
     * return NA
    */
 if(!function_exists('blogCreateWithoutFile')){
    function blogCreateWithoutFile($blogs, $users_id){

            $sql = "INSERT INTO posts(title, content, category, publish_status, likes, users_id)
                    VALUES(:title, :content, :category, :publish_status, 0, :users_id)";

            $dbh = dbConnect();
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':title', $blogs['title'],PDO::PARAM_STR);
            $stmt->bindValue(':content', $blogs['content'],PDO::PARAM_STR);
            $stmt->bindValue(':category', $blogs['category'],PDO::PARAM_INT);
            $stmt->bindValue(':publish_status', $blogs['publish_status'],PDO::PARAM_INT);
            $stmt->bindValue(':users_id', $users_id,PDO::PARAM_INT);
            $stmt->execute();

    }
 }

    /**プロフィールの入力（ニックネームと自己紹介文）
     * ＠param int $users_id
     * ＠param str $nickname
     * ＠param str $intro_text
     * return NA
     */
if(!function_exists('CreateProfile')) {
    function CreateProfile($user_id,$nickname,$intro_text){

                    $sql = "UPDATE users SET
                            nickname = :nickname, intro_text = :intro_text
                            WHERE id = :id;";

                    $dbh = dbConnect();
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(':nickname', $nickname,PDO::PARAM_STR);
                    $stmt->bindValue(':intro_text', $intro_text,PDO::PARAM_STR);
                    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();

    }
}


    /**コメントを投稿
     * ＠param str $comment
     * return NA
     */
if(!function_exists('commentCreate')) {
    function commentCreate($comment){

            $sql = "INSERT INTO comments(name, c_content, posts_id)
                    VALUES(:name, :c_content, :posts_id)";

            $dbh = dbConnect();
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':name', $comment['name'],PDO::PARAM_STR);
            $stmt->bindValue(':c_content', $comment['c_content'],PDO::PARAM_STR);
            $stmt->bindValue(':posts_id', $comment['posts_id'],PDO::PARAM_INT);
            $stmt->execute();
    }
}


//-----------------更新--------------------------------------------------------

    /**ブログの更新
     * ＠param array $blogs
     * ＠param int $posts_id
     * return NA
     */
if(!function_exists('blogUpdate')) {
    function blogUpdate($blogs, $posts_id){

            $sql = "UPDATE posts SET
                    title = :title, content = :content, category = :category, publish_status = :publish_status
                    WHERE id = :id;";

            $dbh = dbConnect();
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':title', $blogs['title'],PDO::PARAM_STR);
            $stmt->bindValue(':content', $blogs['content'],PDO::PARAM_STR);
            $stmt->bindValue(':category', $blogs['category'],PDO::PARAM_INT);
            $stmt->bindValue(':publish_status', $blogs['publish_status'],PDO::PARAM_INT);
            $stmt->bindValue(':id', $posts_id,PDO::PARAM_INT);
            $stmt->execute();
    }
}

    /**ブログの更新時にキャプションを追加
     * ＠param str $caption
     * ＠param int $posts_id
     * return NA
     */
if(!function_exists('addCaption')) {
    function addCaption($caption, $posts_id){

            $sql = "UPDATE files SET caption = :caption WHERE posts_id = :id;";

            $dbh = dbConnect();
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':caption', $caption,PDO::PARAM_STR);
            $stmt->bindValue(':id', $posts_id,PDO::PARAM_INT);
            $stmt->execute();
        
    }
}


    /**画像の更新
     * ＠param array $blogs
     * ＠param array $file
     * ＠param str $save_path
     * ＠param int $posts_id
     * return NA
     */
if(!function_exists('fileUpdate')) {
    function fileUpdate($blogs, $file, $save_path, $posts_id){

                $sql = "UPDATE files SET file_name = :file_name, file_path = :file_path, caption = :caption
                       WHERE posts_id = :posts_id;";

                $dbh = dbConnect();
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':file_name', $file['name'],PDO::PARAM_STR);
                $stmt->bindValue(':file_path', $save_path,PDO::PARAM_STR);
                $stmt->bindValue(':caption', $blogs['caption'],PDO::PARAM_INT);
                $stmt->bindValue(':posts_id', $posts_id,PDO::PARAM_INT);
                $stmt->execute();

    }
}


    /**記事の更新時に画像を追加する
     * ＠param array $blogs
     * ＠param array $file
     * ＠param str $save_path
     * ＠param int $posts_id
     * return NA
     */
if(!function_exists('addNewFile')) {
      function addNewFile($blogs, $file, $save_path, $posts_id){

                $sql = "INSERT INTO files(file_name, file_path, caption, posts_id) VALUES (:file_name, :file_path, :caption, :posts_id)";

                $dbh = dbConnect();
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':file_name',$file['name'],PDO::PARAM_STR);
                $stmt->bindValue(':file_path',$save_path,PDO::PARAM_STR);
                $stmt->bindValue(':caption',$blogs['caption'],PDO::PARAM_STR);
                $stmt->bindValue(':posts_id',$posts_id,PDO::PARAM_INT);
                $stmt->execute();

       }
}

//-------取得----------------------------------------------------------------

    /**DBからデータを取得
     * ＠param int $id
     * return array $result (記事のタイトル、記事の内容、投稿日、公開か非公開か、いいねの数、投稿者のid)
     */
if(!function_exists('getById')) {
    function getById($id){

        $sql = "SELECT * FROM posts WHERE id = :id";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':id',(int)$id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;

    }
}


    /**画像を取得
     * ＠param int $id(記事のid)
     * return array $result (filesテーブルから、posts_id, file_name, file_path, caption)
    */
if(!function_exists('getFileById')) {
    function getFileById($id){

        $sql = "SELECT posts_id, file_name, file_path, caption FROM files JOIN posts ON files.posts_id = posts.id WHERE files.posts_id = :id";

        $dbh = dbConnect();
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

        $sql = 'SELECT posts.*, users.nickname FROM posts JOIN users ON posts.users_id = users.id WHERE publish_status = 1 ORDER BY posts.id DESC';
        
        $dbh = dbConnect();
        $stmt = $dbh->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}


    /**投稿されたブログの件数(公開のみ)を取得
     * ＠param NA
     * return int $result (公開記事の総数)
     */
 if(!function_exists('getDataCount')) {
    function getDataCount(){

        $sql = 'SELECT COUNT(*) FROM posts WHERE publish_status = 1';

        $dbh = dbConnect();
        $stmt = $dbh->query($sql);
        $result = $stmt->fetch();

        return $result;
    }
 }
 

    /**最新のブログ記事取得（公開のみ）
     * ＠param $amount (取得したい記事件数)
     * return array $result (記事のタイトル、記事の内容、投稿日、公開か非公開か、いいねの数、投稿者のニックネーム)
     */
if(!function_exists('getNewestBlog')) {
    function getNewestBlog($amount){

        $sql = "SELECT posts.*, users.nickname FROM posts JOIN users ON posts.users_id = users.id WHERE posts.publish_status = 1 ORDER BY posts.id DESC LIMIT :LIMIT";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':LIMIT',(int)$amount, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}

/**コメントを取得
     * ＠param int $id(記事のid)
     * return array $result (コメントのid、コメント投稿者の名前、コメント、投稿日時、コメントの未読既読、コメントした記事のid)
     */
 if(!function_exists('getComment')) {
    function getComment($id){

        $sql = "SELECT * FROM comments JOIN posts ON comments.posts_id = posts.id WHERE posts.id = :id";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':id',(int)$id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }
 }

 
    /**全コメントを取得
     * ＠param NA
     * return array $result (コメントのid、コメント投稿者の名前、コメント、投稿日時、コメントの未読既読、コメントした記事のid)
     */
 if(!function_exists('getAllComments')) {
    function getAllComments(){

        $sql = "SELECT * FROM comments";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;

    }
 }


    /**お知らせ機能で使うため、ユーザー別に、またreadstatus別にコメントを取得(!!なぜかcommentsテーブルのidを取得できていなかった。* from comments　としてもidがposts_idになっていた。。。)
     * ＠param int $users_id
     * ＠param int $readstatus
     * return array $result (コメントのid、コメント投稿者の名前、コメント内容、投稿日時、コメントの未読既読、コメントした記事のid)
     */
if(!function_exists('getCommentsByReadstatus')) {
    function getCommentsByReadstatus($users_id, $readstatus){

        $sql = "SELECT *, comments.id FROM comments JOIN posts ON posts.id = comments.posts_id WHERE posts.users_id = :users_id AND comments.read_status = :read_status ORDER BY comment_at DESC";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':users_id',(int)$users_id, PDO::PARAM_INT);
        $stmt->bindValue(':read_status',(int)$readstatus, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }
 }

   /**ユーザー別に、readstatus別にコメント総数を取得
     * ＠param int $users_id
     * ＠param int $readstatus
     * return int $result (指定ユーザーの、指定readstatusのコメント総数)
     */
 if(!function_exists('getCommentCount')) {
    function getCommentCount($users_id, $read_status){

        $sql = "SELECT COUNT(*) FROM comments JOIN posts ON posts.id = comments.posts_id WHERE posts.users_id = :users_id AND comments.read_status = :read_status ";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':users_id',(int)$users_id, PDO::PARAM_INT);
        $stmt->bindValue(':read_status',(int)$read_status, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result;

    }
}

   /**画像を取得
     * ＠param int $id（記事のid = filesテーブルのposts_id）
     * return array $fileDatas (filesテーブルのid, file_name, file_path, caption, posts_id)
     */
if(!function_exists('getFile')) {
    function getFile($id){

        $sql = "SELECT files.* FROM files JOIN posts ON files.posts_id = posts.id WHERE posts.id = :id";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt ->bindValue(':id',(int)$id, PDO::PARAM_INT);
        $stmt->execute();
        $fileDatas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $fileDatas;

    }
}


   /**プロフィールのデータ（厳密にはuserテーブルの全て）を取得
     * ＠param int $users_id
     * return array $profileDatas(usersテーブルのid, user_name, email, password, ユーザーとして登録された日時, 登録内容が更新された日時, nickname, 自己紹介文)
     */
if(!function_exists('getProfileDatas')) {
    function getProfileDatas($users_id){

        // !! users.idと明確に指定しないと、idキーの値がposts_idになってしまう。
        $sql = "SELECT *, users.id FROM users JOIN posts ON users.id = posts.users_id WHERE users.id = :users_id";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt ->bindValue(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        $profileDatas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $profileDatas;
    }
}

   /**全ユーザーのデータを取得　
     * ＠param NA
     * return array $results(全ユーザーの数, 全ユーザーの usersテーブルのid, user_name, email, password, ユーザーとして登録された日時, 登録内容が更新された日時, nickname, 自己紹介文)
     */
if(!function_exists('getAllusers')) {
    function getAllusers(){

        $sql = "SELECT count(*) AS user_count, users.* FROM users GROUP BY id";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}


    /**検索窓に入力されたワードを記事タイトル、記事内容、投稿日から検索
     * ＠param str/int $search_word
     * return array $results
     */
if(!function_exists('getSearchWord')) {
   function getSearchWord($search_word){

        $results;

        if($search_word !== ""){
            $sql = "SELECT * FROM posts WHERE publish_status = 1 AND title LIKE '%".$search_word."%' OR content LIKE '%".$search_word."%' OR post_at LIKE '%".$search_word."%'";

            $dbh = dbConnect();
            $stmt = $dbh->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;

        }
   }
}

//ーーーーーーーーー削除ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー


    /**ブログ削除　　
     * ＠param int $id
     * ＠param str $table
     * return  NA
     */
if(!function_exists('delete')) {
    function delete($id, $table){

    $sql = "DELETE FROM $table WHERE $table.id = :id";

    $dbh = dbConnect();
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    
    }
}

   /**ブログのアップデート時に画像だけ削除　
     * ＠param int $posts_id
     * ＠param str $file_path_to_delete
     * return  NA
     */
if(!function_exists('deleteFile')) {
    function deleteFile($posts_id, $file_path_to_delete){

        $sql = "DELETE FROM files WHERE posts_id = :posts_id AND file_path = :file_path";

        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':posts_id', (int)$posts_id, PDO::PARAM_INT);
        $stmt->bindValue(':file_path', $file_path_to_delete ,PDO::PARAM_STR);
        $stmt->execute();

   }

}


    /**コメントの削除
     * ＠param int $comment_id
     * return  NA
     */
if(!function_exists('deleteComment')) {
    function deleteComment($comments_id){
        
        $sql = "DELETE FROM comments WHERE id = :id";
        
        $dbh = dbConnect();
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':id', (int)$comments_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

//-------------------その他-----------------------------


    /**いいねランキングを作るため、いいねの数が多い順に１０件取得
     * ＠param NA
     * return array $results
     */
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


    /**いいねランキングを作る
     * ＠param array $data(likesRanking関数の戻り値)
     * return array $data
     */
if(!function_exists('addRanking')) {
    function addRanking($data) {
        $ranking = 0;
        // １個目のlikesを入れておく
        $likes = $data[0]['likes'];
        // 最初の配列から最後から１個前の配列まで繰り返す
        for ($i = 0; $i < count($data) -1; $i++) {
            // 同一順位の場合に次のランキングを飛ばすための変数
            $rankingOffset = 0;
            // ランキングを加算する
            $ranking+=1;
            // ランキングをセットする
            $data[$i]['ranking'] = $ranking;
            // $iの次の配列から配列の最後まで繰り返す
            for ($j = $i + 1; $j < count($data); $j++) {
                // $iと$jのlikesが同じだったら同一のランキングをつける
                if ($data[$i]['likes'] == $data[$j]['likes']) {
                    $data[$j]['ranking'] = $ranking;
                    // 同一順位の場合は次のランキングは飛ばしたいので$rankingOffsetを加算する
                    $rankingOffset+=1;
                } else {
                    // 次のランキングを検査するためにrankingOffsetを$iに加算しておく
                    $i = $i + $rankingOffset;
                    // ランキングも同じようにrankingOffsetを加算する
                    $ranking = $ranking + $rankingOffset;
                    // チェックを打ち切る
                    break;
                }
            }
        }
        return $data;
      }
}

    /**XSS対策エスケープ
     * ＠param str $s
     * return method htmlspecialchars($s, ENT_QUOTES, "UTF-8");
     */
if(!function_exists('h')) {
    function h($s){
        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
    }


    
    /**カテゴリーを数字表記からちゃんとした文字表現に変更
     * ＠param int $cate
     * return str each category name
     */
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



    /**コメントステータスを既読にする
     * ＠param int $comments_id
     * return NA
     */
if(!function_exists('switchToRead')) {
    function switchToRead($comments_id){

          $sql = "UPDATE comments SET read_status = 1 WHERE id = :id;";

          $dbh = dbConnect();
          $stmt = $dbh->prepare($sql);
          $stmt->bindValue(':id', $comments_id,PDO::PARAM_INT);
          $stmt->execute();

    }
  }


    /**コメントステータスを未読にする
     * ＠param int $comments_id
     * return NA
     */
  if(!function_exists('switchToUnread')) {
    function switchToUnread($comments_id){

          $sql = "UPDATE comments SET read_status = 0 WHERE id = :id;";

          $dbh = dbConnect();
          $stmt = $dbh->prepare($sql);
          $stmt->bindValue(':id', $comments_id,PDO::PARAM_INT);
          $stmt->execute();

    }
  }

  
