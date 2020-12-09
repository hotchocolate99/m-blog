<?php
//----ログイン状態-----------------
session_start();

/*if (!$_SESSION['login']) {
    header('Location: ./../../account/login.php');
    exit();
  }*/

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
//--------------------------------

ini_set('display_errors',true);


require_once './private/database.php';
require_once './private/functions.php';
//var_dump($_SESSION['user']);
//ブログ関連-----------------------------------------------------------------------
//ブログの全件の全てのデータを取得し、記事一覧を表示-----
$blogDatas = getData($_SESSION['user']);
//var_dump($blogDatas);
foreach($blogDatas as $blogData=>$val){
  //var_dump($val);
}

//ブログの総件数を取得----------------------------
$blogs_total = getDataCount();
//↑ は配列だったので ↓
//var_dump($total["COUNT(*)"]);

//最新記事からでも記事一覧からでも詳細ページに遷移するために、idをGETで、渡している。


//最新ブログ記事の取得(postsテーブルのデータのみで画像はなし)-------
$newestBlogs = getNewestBlog();
foreach($newestBlogs as $newestBlog){
  //var_dump($newestBlog);
}
$users_id = $newestBlog['users_id'];
//上のidを引数に入れて、最新記事の画像を取得
$fileDatas = getFile($newestBlog['id']);
//var_dump($fileDatas);

//プロフィールの表示-------------------------------------------------------------------
//引数の$users_idは最新記事を書いたユーザーのid
$profileDatas = getProfileDatas($users_id);
//var_dump($profileDatas);
$nickname = $profileDatas['0']['nickname'];
$intro_text = $profileDatas['0']['intro_text'];


//いいねランキング--------------------------------------------------------------------
//自分で試みたランキング（失敗）
/*$likes_results = likesRanking();

$i=0;
for($i=0; $i<10; $i++){
   foreach($likes_results as $likes_result){
     //var_dump($likes_results[0]['likes']);
     //var_dump($likes_results[0]['rank']);

     if($likes_results[$i+1]['likes'] !== $likes_results[$i]['likes']){
      $likes_results[$i]['rank'] = $i+1;
     }else{$likes_results[$i+1]['rank'] = $likes_results[$i]['rank'];
     }

   }
   var_dump($likes_results[7]);
}
*/

//いいねのランキング（先生の）
$data = likesRanking();

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

$rankingData = addRanking($data);

//var_dump($rankingData);
foreach($rankingData as $key=>$value){
  //var_dump($value);
  //var_dump($value['ranking'].'位'.'/いいね獲得数は'.$value['likes'].'/タイトルは'.$value['title']);
}


//お知らせの隣に表示させる未読のコメント数-------------------------------------
$UnreadCommentCount = getUnreadCommentCount($users_id);

//全ユーザーのデータを取得
$allUsers = getAllusers();
//var_dump($allUsers);
foreach($allUsers as $allUser){
  var_dump($allUser['id']);
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/header.css">
</head>

<body>
<?php include './header.php';?>
   

  <div class="wrapper">
     <div class="container">
            <div class="left">

                   <div class="newest">
                           <h5><i class="fas fa-pencil-alt"></i>最新記事</h5>

                                <p><a class="link_aa" href="./public/list/blogs_by_user.php?id=<?php echo h($users_id)?>"><?php echo $nickname;?></a>&nbsp;さんの投稿</p>
                                 <h2 class="title"><?php echo h($newestBlog['title']);?></h2>
                                 <div class="flex">
                                    <p><?php echo h($newestBlog['post_at']);?></p>
                                    <p><?php echo h(setCateName($newestBlog['category']))?></p>
                                 </div>
                                 <p class="blog_content"><?php echo h($newestBlog['content'])?></p>

                              <?php if($fileDatas):?>
                                    <?php// foreach($fileDatas as $fileData):?>
                                      <img src="./public/blog/<?php echo "{$fileDatas[0]['file_path']}";?>"　width="240px" height="400px" alt="blog_image" >
                                       <p><?php echo h("{$fileDatas[0]['caption']}");?></p>
                                    <?php //endforeach;?>
                              <?php endif;?>

                                 <br>
                                 <div class="detail_a"><a class="link_aa" href="./public/blog/blog_detail.php?id=<?php echo h($newestBlog['id'])?>">詳細へ</a></div>

                   </div><!--newest-->

                  <div class="blogs">
                      <h2><i class="fas fa-pencil-alt"></i>記事一覧<span>（全<?php echo $blogs_total["COUNT(*)"];?>件）</span></h2>
                         <?php foreach($blogDatas as $blogData => $val):?>

                            <div class="blog_box"> 
                                <p><a class="link_aa" href="./public/list/blogs_by_user.php?id=<?php echo h($val['users_id'])?>"><?php echo h($val['nickname'])?></a>&nbsp;さんの投稿</p>
                                <a class="link_aa" href="./public/blog/blog_detail.php?id=<?php echo h($val['id'])?>">
                                    <dl>
                                        <dt class="detail"><h3><?php echo h($val['title'])?></h3></dt>
                                        <div class="flex">
                                          <dd class=date><?php echo h($val['post_at'])?></dd>
                                          <dd><?php echo h(setCateName($val['category']))?></dd>
                                          <dd>(<i class="fas fa-heart"></i><?php echo h($val['likes'])?>)</dd>
                                        </div>
                                    </dl>
                                </a>
                            </div>
                         <?php endforeach;?>

                  </div>

                  <div><a href="#" class="fixed_btn to_top">TOPへ戻る</a></div><br>
            </div><!--left-->



            <div class="right">
                　　<div class="profile">
                    <p class="prof_title"><strong><i class="fas fa-user-circle">&nbsp;最新記事投稿者のプロフィール</i></strong></p>
                　　　 <?php if($profileDatas):?>
                    　　　<h3 class="nickname"><?php echo $nickname;?></h3>
                    　　　<p class="text"><?php echo $intro_text;?></p>
                    　　　<a class="link_aa from_profile" href="/public/list/blogs_by_user.php?id=<?php echo $allUser['id'];?>"><?php echo $allUser['nickname'];?>&nbsp;さんの記事一覧へ</a>

                  　　<?php endif;?>

                　　</div>

                　　
                　　<div class="menu">
                    　<ul>
                        <li>
                            <div class="search">
                                <form action="/public/list/list_search_result.php" method="post">
                                    <input type="text" name="search_word" class="sample2Text" placeholder="記事検索">
                                </form>
                            </div>
                        </li>

                        <li><a href="./public/list/list_files.php" class="link_a"><i class="fas fa-camera"></i>画像一覧</a></li>

                        <li class="list"><a href="#" class="link_a"><i class="fas fa-file"></i>テーマ別記事一覧</a>
                          <ul>
                              <li><a href="/public/blog/blog_cate_list.php#cate1" class="link_a">テーマ１</a></li>
                              <li><a href="/public/blog/blog_cate_list.php#cate2" class="link_a">テーマ２</a></li>
                              <li><a href="/public/blog/blog_cate_list.php#cate3" class="link_a">その他</a></li>
                          </ul>
                        </li>

                        <li class="list"><a href="#" class="link_a"><i class="fas fa-file"></i>ユーザー別記事一覧</a>
                          <ul>
                            <?php foreach($allUsers as $allUser):?>
                              <li><a class="link_a" href="/public/list/blogs_by_user.php?id=<?php echo h($allUser['id'])?>"><?php echo $allUser['nickname'];?>&nbsp;さんの記事一覧</a></li>
                            <?php endforeach;?>
                          </ul>
                        </li>
                        

　　　　　　　　　　　　　</ul>
　　　　　　　　　　　</div><!--menu-->

                  <div class="blogs">
                    <p class="ranking_title"><strong><i class="fas fa-crown"></i>人気記事ランキング（10位）</strong></p>
                    
                    
                       
                         <?php// $i = 1; for($i=1; $i<=10; $i++){echo $i.'位';};?>
                    

                       
                            
                         <?php foreach($rankingData as $key=>$value):?>
                          <?php if($value['ranking'] !== null):?>
                              
                              <div class="blog_box"> 
                              
                                  <a class="link_aa" href="/public/blog/blog_detail.php?id=<?php echo h($value['id'])?>">

                                            <div class="detail "><strong><?php echo $value['ranking'].'位'?>&nbsp;<?php echo h($value['title'])?></strong>&nbsp;&nbsp;(<i class="fas fa-heart"></i><?php echo h($value['likes'])?>)</div>
                                            <div class="date small"><?php echo h($value['post_at'])?></div>
                                            <div class="small"><?php echo h(setCateName($value['category']))?></div>
                                  </a>
                                  <p class="who_posts"><a class="link_aa" href="/public/list/blogs_by_user.php?id=<?php echo h($value['users_id'])?>"><?php echo $value['nickname'];?></a>&nbsp;さんの投稿</p>
                              </div>
                          <?php endif;?>   
                         <?php endforeach;?>
                         
                    
                  </div>


                  <div class="side_footer">footer</div>
                　　　
            </div><!--right-->
        </div><!--container-->
  </div> <!--wrapper-->

</body>
</html>


                    