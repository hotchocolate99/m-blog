<?php
//----ログイン状態-----------------
session_start();

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
  //!users_id はログインしているユーザーのid。　 user_idは最新記事投稿者のid。紛らわしい。。。
//--------------------------------

ini_set('display_errors',true);


require_once './private/database.php';
require_once './private/functions.php';
//var_dump($_SESSION['user']);


//ブログ関連-----------------------------------------------------------------------
//ブログの最新の５件を取得し、記事一覧を表示-----
$blogDatas = getNewestBlog(5);
//$_SESSION['user']
//var_dump($blogDatas);
foreach($blogDatas as $blogData){
  //var_dump($blogData['id']);
}


//最新ブログ記事の取得(postsテーブルのデータのみで画像はなし)-------
$newestBlogs = getNewestBlog(1);
//var_dump($newestBlogs);
foreach($newestBlogs as $newestBlog){
  //var_dump($newestBlog);
}
$newestPost_id = $newestBlog['id'];
//var_dump($user_id);
//上のidを引数に入れて、最新記事の画像を取得
$fileDatas = getFile($newestPost_id);
//var_dump($fileDatas);


//プロフィールの表示-------------------------------------------------------------------
//引数の$user_idは最新記事を書いたユーザーのid
$profileDatas = getProfileDatas($newestBlog['users_id']);
//var_dump($profileDatas);
$nickname = $profileDatas['0']['nickname'];
$intro_text = $profileDatas['0']['intro_text'];



//いいねのランキング（先生の）-------------------------------------------------------------
$data = likesRanking();
//var_dump($data);

$rankingData = addRanking($data);
//var_dump($rankingData);


//var_dump($rankingData);
foreach($rankingData as $key=>$value){
//var_dump($value);
//var_dump($value['ranking'].'位'.'/いいね獲得数は'.$value['likes'].'/タイトルは'.$value['title']);
}


//お知らせの隣に表示させる未読のコメント数-------------------------------------
$UnreadCommentCount = getCommentCount($users_id, 0);
//var_dump($users_id);

//全ユーザーのデータを取得------------------------------------------------
$allUsers = getAllusers();
//var_dump($allUsers);
foreach($allUsers as $allUser){
 // var_dump($allUser);
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
   
<label for="check">
  <div class="wrapper">
     <div class="container">
            <div class="left">

                   <div class="newest">
                           <h5><i class="fas fa-pencil-alt"></i>最新記事</h5>

                                <p><a class="link_aa" href="./public/list/blogs_by_user.php?id=<?php echo h($newestBlog['users_id'])?>"><?php echo $nickname;?></a>&nbsp;さんの投稿</p>
                                 <h2 class="title"><?php echo h($newestBlog['title']);?></h2>
                                 <div class="flex">
                                    <p><?php echo h($newestBlog['post_at']);?></p>
                                    <p><?php echo h(setCateName($newestBlog['category']))?></p>
                                 </div>
                                 <p class="blog_content"><?php echo nl2br(h($newestBlog['content']))?></p>

                              <?php if($fileDatas):?>
                                    
                                      <img src="./public/blog/<?php echo "{$fileDatas[0]['file_path']}";?>"　width="240px" height="400px" alt="blog_image" >
                                       <p><?php echo h("{$fileDatas[0]['caption']}");?></p>
                                    
                              <?php endif;?>

                                 <br>
                                 <div class="detail_a"><a class="link_aa" href="./public/blog/blog_detail.php?id=<?php echo h($newestBlog['id'])?>">詳細へ</a></div>

                   </div><!--newest-->

                  <div class="blogs">
                      <h2><i class="fas fa-pencil-alt"></i><span>最新記事５件</span></h2>
                      <table>

                            <tr>
                            <td>
                            <?php for($i=0; $i<5; $i++):?>
                                <?php $blogData = $blogDatas[$i];?>
                                
                            <div class="blog_box"> 
                            <strong><?php echo $i+1;?>.</strong>
                                <p><a class="link_aa" href="./public/list/blogs_by_user.php?id=<?php echo h($blogData['users_id'])?>"><?php echo h($blogData['nickname'])?></a>&nbsp;さんの投稿</p>
                                <a class="link_aa" href="./public/blog/blog_detail.php?id=<?php echo h($blogData['id'])?>">
                                    <dl>
                                        <dt class="detail"><h3><?php echo h($blogData['title'])?></h3></dt>
                                        <div class="flex">
                                          <dd class=date><?php echo h($blogData['post_at'])?></dd>
                                          <dd><?php echo h(setCateName($blogData['category']))?></dd>
                                          <dd>(<i class="fas fa-heart"></i><?php echo h($blogData['likes'])?>)</dd>
                                        </div>
                                    </dl>
                                </a>
                            </div>
                                
                         <?php endfor;?>
                         </td>
                         </tr>
                         </table>
                  </div>

                  <div><a href="#" class="fixed_btn to_top">TOPへ戻る</a></div><br>
            </div><!--left-->



            <div class="right">
                　　<div class="profile">
                    <p class="prof_title"><strong><i class="fas fa-user-circle">&nbsp;最新記事投稿者のプロフィール</i></strong></p>
                　　　 <?php if($profileDatas):?>
                    　　　<h3 class="nickname"><?php echo $nickname;?></h3>
                    　　　<p class="text"><?php echo nl2br($intro_text);?></p>
                    　　　<a class="link_aa from_profile" href="/public/list/blogs_by_user.php?id=<?php echo $profileDatas['0']['id'];?>"><?php echo $nickname;?>さんの記事一覧</a>

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

                        <li><a href="./public/list/all_blogs.php" class="link_a"><i class="fas fa-file"></i>投稿記事一覧</a></li>

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

                        <li><a href="./public/list/list_files.php" class="link_a"><i class="fas fa-camera"></i>画像一覧</a></li>
                        

　　　　　　　　　　　　　</ul>
　　　　　　　　　　　</div><!--menu-->

                  <div class="blogs">
                    <p class="ranking_title"><strong><i class="fas fa-crown"></i>人気記事ランキング（10位）</strong></p>


                            
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
                          </label>                         
</body>
</html>


                    