<?php
session_start();
// DB接続

// likeが押された時
if(isset($_GET["like_tweet_id"])){
// like情報をLIkesテーブルを登録

  like($_GET["like_tweet_id"],$_SESSION["id"],$_GET["page"]);
  // $sql = "INSERT INTO `likes` (`tweet_id`, `member_id`) VALUES (".$_GET["like_tweet_id"].",".$_SESSION["id"].");";

  // // sql実行
  // $stmt = $dbh->prepare($sql);
  // $stmt->execute($data);

  // header("Location: index.php");
}
// unlikeが押された時osaretatoki
if(isset($_GET["unlike_tweet_id"])){
// 登録されているLike情報をテーブルから削除
  unlike($_GET["unlike_tweet_id"],$_SESSION["id"],$_GET["page"]);
  // $sql = "DELETE FROM `likes` WHERE `tweet_id`=".$_GET["unlike_tweet_id"]." AND `member_id`=".$_SESSION["id"];

  // // sql実行
  // $stmt = $dbh->prepare($sql);
  // $stmt->execute($data);

  // header("Location: index.php");
}


// Like関数
// 引数は3個
  function like($like_tweet_id,$login_member_id,$page){
    require ('dbconect.php');

    $sql = "INSERT INTO `likes` (`tweet_id`, `member_id`) VALUES (".$like_tweet_id.",".$login_member_id.");";

  // sql実行
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header("Location: index.php?page=".$page);
  }

  function unlike($like_tweet_id,$login_member_id,$page){
    require ('dbconect.php');

    $sql = "DELETE FROM `likes` WHERE `tweet_id`=".$like_tweet_id." AND `member_id`=".$login_member_id;

  // sql実行
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    header("Location: index.php?page=".$page);
  }
?>