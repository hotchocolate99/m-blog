<?php
//----ログイン状態-----------------
session_start();

ini_set('display_errors', true);

if (!$_SESSION['login']) {
    header('Location: ./../public/account/login.php');
    exit();
  }

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }

  
//--------------------------------

ini_set('display_errors',true);


require_once './private/database.php';
require_once './private/functions.php';


//ブログの全件の全てのデータを取得し、記事一覧を表示
$blogData = getData();
//var_dump($blogData);


//最新ブログ記事取得
$newest_blogData = getNewestBlog();
$id = $newest_blogData['id'];
//↑の＄blogDataから、選択された$column['id']を取得するのが出来なかったので、一旦、それをdetail.phpにGETで送った。。。
//それをSESSIONに入れて、またこのhome.phpに戻し、詳細を表示するようにした。
//最新記事のがそうを取得
$fileDatas = getFile($id);
//var_dump($fileDatas);

//ブログの総件数を取得
$blogs_total = getDataCount();
//↑ は配列だったので ↓
//var_dump($total["COUNT(*)"]);

//最新記事からでも記事一覧からでも詳細ページに遷移するために、idをGETで、渡している。


//プロフィールの表示
//var_dump($user['0']['id']);
//if(getProfileDatas($user['0']['id']);){
$profileDatas = getProfileDatas($user['0']['id']);

//}
$nickname = $profileDatas['0']['nickname'];
$intro_text = $profileDatas['0']['intro_text'];


//いいねランキング（自分の）
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

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getUnreadCommentCount();

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

   <header>

    <div class="h_container">

            <div class="logo"><span><i class="fas fa-pencil-alt"></i>m-blog</span></div>

            <div class="navi">
               <ul>
               <?php// if(!empty($_SESSION['user']) || empty($_SESSION['user'])):?>
                 <li><a href="./index.php" class="link_a"><span><i class="fas fa-home"></i>ホーム</span></a></li>
               <?php// endif;?>

               <?php if(!empty($_SESSION['user'])):?>
                　<li><span><a href="./public/blog/blog_post.php" class="link_a"><i class="fas fa-pencil-alt"></i>新記事投稿</span></a></li>
                <?php endif;?>

                <?php if(!empty($_SESSION['user'])):?>
                　<li><a href="./public/list/notice.php" class="link_a"><span><i class="fas fa-bell"></i>お知らせ(<?php echo $UnreadCommentCount['COUNT(*)'];?>)</span></a></li>
　　　　　　　　　　<?php endif;?>

                <?php if(empty($_SESSION['user'])):?>
                  <li><a href="./public/account/register.php" class="link_a"><span><i class="fas fa-user"></i>新規登録</span></a></li>
                <?php else:?>
                　<li><a href="./public/profile/profile_post.php" class="link_a"><span><i class="fas fa-user"></i>プロフィール</span></a></li>
                <?php endif;?>

                <?php if(empty($_SESSION['user'])):?>
                　<li><a href="./public/account/login.php" class="link_a"><span><i class="fas fa-lock"></i>ログイン</span></a>
                <?php else:?>
                  <li><a href="./public/account/logout.php" class="link_a"><span><i class="fas fa-lock"></i>ログアウト</span></a>
                <?php endif;?>
              </ul>
         </div><!--navi-->
    </div><!--container-->

</header>

  <div class="wrapper">
     <div class="container">
            <div class="left">

                   <div class="newest">
                           <h5><i class="fas fa-pencil-alt"></i>最新記事</h5>

                             <?php if($newest_blogData):?>
                                 <h2 class="title"><?php echo h($newest_blogData['title']);?></h2>
                                 <div class="flex">
                                    <p><?php echo h($newest_blogData['post_at']);?></p>
                                    <p><?php echo h(setCateName($newest_blogData['category']))?></p>
                                 </div>
                                 <p class="blog_content"><?php echo h($newest_blogData['content'])?></p>
                              <?php endif;?>

                              <?php if($fileDatas):?>
                                    <?php foreach($fileDatas as $fileData):?>
                                       <img src="<?php echo h( "{$fileData['file_path']}");?>"　width="240px" height="400px" alt="blog_image" >
                                       <p><?php echo h("{$fileData['caption']}")?></p>
                                    <?php endforeach;?>
                              <?php endif;?>

                                 <br>
                                 <div class="detail_a"><a class="link_aa" href="./public/blog/blog_detail.php?id=<?php echo h($newest_blogData['id'])?>">詳細へ</a></div>

                   </div><!--newest-->

                  <div class="blogs">
                      <h2><i class="fas fa-pencil-alt"></i>記事一覧<span>（全<?php echo $blogs_total["COUNT(*)"];?>件）</span></h2>
                         <?php foreach($blogData as $column):?>

                            <div class="blog_box"> 
                                <a class="link_aa" href="./public/blog/blog_detail.php?id=<?php echo h($column['id'])?>">
                                    <dl>
                                        <dt class="detail"><h3><?php echo h($column['title'])?></h3></dt>
                                        <div class="flex">
                                          <dd class=date><?php echo h($column['post_at'])?></dd>
                                          <dd><?php echo h(setCateName($column['category']))?></dd>
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
                    <p class="prof_title"><i class="fas fa-user-circle"></i>プロフィール</p>
                    <?php if(!empty($_SESSION['user'])):?>
                　　　 <?php if($profileDatas):?>
                    　　　<h3 class="nickname"><?php echo $nickname;?></h3>
                    　　　<p class="text"><?php echo $intro_text;?></p>
                    　<?php endif;?>
                    <?php endif;?>
                　　</div>

                　　
                　　<div class="menu">
                    　<ul>
                        <li>
                            <div class="search">
                                <form action="./public/list/list_search_result.php" method="post">
                                    <input type="text" name="search_word" class="sample2Text" placeholder="記事検索">
                                </form>
                            </div>
                        </li>

                        <li><a href="./public/list/list_files.php" class="link_a"><i class="fas fa-camera"></i>画像一覧</a></li>
                        <li class="list"><a href="#" class="link_a"><i class="fas fa-file"></i>テーマ別記事一覧</a>
                          <ul>
                              <li><a href="./public/blog/blog_cate_list.php#cate1" class="link_a">テーマ１</a></li>
                              <li><a href="./public/blog/blog_cate_list.php#cate2" class="link_a">テーマ２</a></li>
                              <li><a href="./public/blog/blog_cate_list.php#cate3" class="link_a">その他</a></li>
                          </ul>
                        </li>
                        </li>

　　　　　　　　　　　　　</ul>
　　　　　　　　　　　</div><!--menu-->

                  <div class="blogs">
                    <p class="ranking_title"><strong><i class="fas fa-crown"></i>人気記事ランキング（10位）</strong></p>
                    
                    
                       
                         <?php// $i = 1; for($i=1; $i<=10; $i++){echo $i.'位';};?>
                    

                       
                            
                         <?php foreach($rankingData as $key=>$value):?>
                          <?php if($value['ranking'] !== null):?>
                              
                              <div class="blog_box"> 
                                  <a class="link_aa" href="./public/blog/blog_detail.php?id=<?php echo h($value['id'])?>">

                                            <div class="detail "><strong><?php echo $value['ranking'].'位'?>&nbsp;<?php echo h($value['title'])?></strong>&nbsp;&nbsp;(<i class="fas fa-heart"></i><?php echo h($value['likes'])?>)</div>
                                            <div class="date small"><?php echo h($value['post_at'])?></div>
                                            <div class="small"><?php echo h(setCateName($value['category']))?></div>
                                  </a>
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


                    