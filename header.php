<header>

    <div class="h_container">

            <div class="logo"><span><i class="fas fa-pencil-alt"></i>m-blog</span></div>

            <div class="navi">
            <ul>
                <li><a href="/" class="link_a"><span><i class="fas fa-home"></i>ホーム</span></a></li>

               <?php if(!empty($_SESSION['user'])):?>
                　<li><span><a href="/public/blog/blog_post.php" class="link_a"><i class="fas fa-pencil-alt"></i>新記事投稿</span></a></li>
                <?php endif;?>

                <?php if(empty($_SESSION['user'])):?>
                  <li><a href="/public/account/register.php" class="link_a"><span><i class="fas fa-user"></i>新規登録</span></a></li>
                <?php else:?>
                  <li><a href="/public/list/notice.php" class="link_a"><span><i class="fas fa-bell"></i>お知らせ(<?php echo $UnreadCommentCount['COUNT(*)'];?>)</span></a></li>
                <?php endif;?>

                <?php if(empty($_SESSION['user'])):?>
                　<li><a href="/public/account/login.php" class="link_a"><span><i class="fas fa-lock"></i>ログイン</span></a></li>
                <?php else:?>
                  <li class="listH"><strong><?php echo $_SESSION['user'][0]['user_name'];?></strong>&nbsp;さん
                  <ul>
                    <li><a href="/public/account/logout.php" class="link_a"><span><i class="fas fa-lock"></i>ログアウト</span></a></li>
                    <li><a href="/public/profile/profile_post.php" class="link_a"><span><i class="fas fa-user"></i>プロフィール</span></a></li>
                    <li><a class="link_a" href="/public/list/blogs_by_user.php?id=<?php echo $_SESSION['user'][0]['id'];?> "><span><i class="fas fa-file"></i>ユーザー記事一覧<span></a></li>
                </ul>
                  </li>
                <?php endif;?>
              </ul>
         </div><!--navi-->
    </div><!--container-->

</header>