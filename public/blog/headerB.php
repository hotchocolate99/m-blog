<header>

    <div class="h_container">

            <div class="logo"><span><i class="fas fa-pencil-alt"></i>m-blog</span></div>

            <div class="navi">
            <ul>
               <?php// if(empty($_SESSION['user'])):?>
                <li><a href="./../../index.php" class="link_a"><span><i class="fas fa-home"></i>ホーム</span></a></li>
               <?php// endif;?>

               <?php if(!empty($_SESSION['user'])):?>
                　<li><span><a href="./blog_post.php" class="link_a"><i class="fas fa-pencil-alt"></i>新記事投稿</span></a></li>
                <?php endif;?>

                <?php if(!empty($_SESSION['user'])):?>
                　<li><a href="./../list/notice.php" class="link_a"><span><i class="fas fa-bell"></i>お知らせ</span></a></li>
　　　　　　　　　　<?php endif;?>

                <?php if(empty($_SESSION['user'])):?>
                  <li><a href="./../account/register.php" class="link_a"><span><i class="fas fa-user"></i>新規登録</span></a></li>
                <?php //else:?>
                  
                <?php endif;?>

                <?php if(!empty($_SESSION['user'])):?>
                　<li><a href="./../profile/profile_post.php" class="link_a"><span><i class="fas fa-user"></i>プロフィール</span></a></li>
                <?php endif;?>

                <?php if(empty($_SESSION['user'])):?>
                　<li><a href="./../account/login.php" class="link_a"><span><i class="fas fa-lock"></i>ログイン</span></a>
                <?php else:?>
                  <li><a href="./../account/logout.php" class="link_a"><span><i class="fas fa-lock"></i>ログアウト</span></a>
                <?php endif;?>
              </ul>
         </div><!--navi-->
    </div><!--container-->

</header>