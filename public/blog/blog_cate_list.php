<?php
//----ログイン状態-----------------
session_start();

  if ($_SESSION['login']= true) {
    $user = $_SESSION['user'];
  }
  $users_id = $user[0]['id'];
//--------------------------------

require_once './../../private/database.php';
require_once './../../private/functions.php';

//ini_set('display_errors',true);

//カテゴリ別に記事を取得（公開記事のみ）　引数はカテゴリーの番号
function getBlogByCate($cate){
    $dbh = dbConnect();
    
        $sql = "SELECT * FROM posts WHERE category = :category AND publish_status = 1 ORDER BY id DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':category',$cate, PDO::PARAM_INT);
        $stmt->execute();
        $blogByCate = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $blogByCate;

}

$blogsByCate1 = getBlogByCate(1);

$blogsByCate2 = getBlogByCate(2);

$blogsByCate3 = getBlogByCate(3);

//カテゴリー別の記事件数を取得
function getBlogCount(){
    $dbh = dbConnect();

    $sql = 'SELECT category,COUNT(*)FROM posts WHERE publish_status = 1 GROUP BY category';

    $stmt = $dbh->query($sql);

    $blogCount = $stmt->fetchall(PDO::FETCH_ASSOC);

    return $blogCount;
}
$blogCounts = getBlogCount();
//var_dump($blogCounts);

$cate1 = $blogCounts['0'];
//var_dump($cate1["COUNT(*)"]);
//var_dump($cate1["category"]);

$cate2 = $blogCounts['1'];

$cate3 = $blogCounts['2'];

//お知らせの隣に表示させる未読のコメント数
$UnreadCommentCount = getCommentCount($users_id, 0);

?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>テーマ別記事一覧</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <link rel="stylesheet" href="./../../css/cate_list.css">
        <link rel="stylesheet" href="./../../css/header.css">
    </head>

    <body>

        <?php include './../../header.php';?>

        <label for="check">
            <div class="wrapper">
                <div class="container">
                　  <div class="typein">
    　　　　　　　　　　　　<h2 class="cate_title"><i class="fas fa-file"></i>テーマ別記事一覧</h2>
                    <div class="frame">
                                <h2 class="form_title"><a name="cate1">テーマ１（<?php echo $cate1["COUNT(*)"];?>件）</a></h2>
                                <table>
                                　　<tr>
                                　　　<td>
                                　　　　　<?php for($i=0; $i<$cate1["COUNT(*)"]; $i++):?>
    　　　　　　　　　　　　　　　　　　　　　　　　<?php $blogByCate1 = $blogsByCate1[$i];?>
                                        　　　
                                        　　　<div class="result_box">
                                        　　　　　<strong><?php echo $i+1;?>.</strong>
                                            　　　<a class="link_aa" href="./blog_detail.php?id=<?php echo h($blogByCate1['id'])?>">
                                            　　　　<dl>
                                                    　<dt><strong><?php echo $blogByCate1['title'];?></strong></dt>
                                                    　<dd class="date"><?php echo setCateName($blogByCate1['category']);?>&nbsp;&nbsp;<?php echo $blogByCate1['post_at'];?></dd>
                                                    　<br>
                                                    　<dd class="content"><?php echo mb_substr($blogByCate1['content'],0,60);?></dd>
                                            　　　　</dl>
                                            　　　</a>
                                        　　　</div>

                                    　　　<?php endfor;?>
                                    </td>
                                　</tr>
                            　　</table>
                    　</div><!--frame-->


                    　<div class="frame">
                                <h2 class="form_title"><a name="cate2">テーマ2（<?php echo $cate2["COUNT(*)"];?>件）</a></h2>
                                　<table>
                                　　　<tr>
                                　　　　<td>
                                　　　　　　<?php for($i=0; $i<$cate2["COUNT(*)"]; $i++):?>
                                    　　　　　<?php $blogByCate2 = $blogsByCate2[$i];?>
                                        
                                        　　　<div class="result_box">
                                        　　　　　<strong><?php echo $i+1;?>.</strong>
                                            　　　<a class="link_aa" href="./blog_detail.php?id=<?php echo h($blogByCate2['id'])?>">
                                                　　<dl>
                                                    　　<dt><strong><?php echo $blogByCate2['title'];?></strong></dt>
                                                    　　<dd class="date"><?php echo setCateName($blogByCate2['category']);?>&nbsp;&nbsp;<?php echo $blogByCate2['post_at'];?></dd>
                                                    　　<br>
                                                    　　<dd><?php echo mb_substr($blogByCate2['content'],0,60);?></dd>
                                                　　</dl>
                                            　　　</a>
                                        　　　</div>

                                　　　　　　<?php endfor;?>
                                    　</td>
                                　　</tr>
                                </table>
                    　</div><!--frame-->

                    　<div class="frame">
                                <h2 class="form_title"><a name="cate3">その他（<?php echo $cate3["COUNT(*)"];?>件）</a></h2>
                                　<table>
                                　　　<tr>
                                　　　　<td>
                                　　　　　　<?php for($i=0; $i<$cate3["COUNT(*)"]; $i++):?>
    　　　　　　　　　　　　　　　　　　　　　　　　　<?php $blogByCate3 = $blogsByCate3[$i];?>

                                        　　　<div class="result_box">
                                        　　　　　<strong><?php echo $i+1;?>.</strong>
                                            　　　<a class="link_aa" href="blog_detail.php?id=<?php echo h($blogByCate3['id'])?>">
                                            　　　　　<dl>
                                                    　　<dt><strong><?php echo $blogByCate3['title'];?></strong></dt>
                                                    　　<dd class="date"><?php echo setCateName($blogByCate3['category']);?>&nbsp;&nbsp;<?php echo $blogByCate3['post_at'];?></dd>
                                                    　　<br>
                                                    　　<dd><?php echo mb_substr($blogByCate3['content'],0,60);?></dd>
                                            　　　　　</dl>
                                            　　　</a>
                                        　　　</div>

                                    　　　<?php endfor;?>
                                    　</td>
                                　　</tr>
                                </table>
                    　</div><!--frame-->

                    <a href="#" class="fixed_btn to_home">TOPへ戻る</a><br>

                </div><!--typein-->
                </div><!--container-->
            </div><!--wrapper-->
       </label>
    </body>
</html>