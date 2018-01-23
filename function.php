<?php 
session_start();

// ログインチェクを行う関数
// 関数とは一定の処理をまとめて名前をつけておいているプログラムの塊
// なんども同じ処理をしたい場合は万里
// プログラミング言語があらかじめ用意している関数：組み込み関数
// 自分で定義して作成して関数：自作関数
// 


function login_check(){

  if(isset($_SESSION['id'])){
// ログインししている

  }else{


    // ログインしていない
    // ログイン画面へ飛ばす
    header("Location: login.php");
    exit();
  }
}


function delete_tweet(){

  require('dbconect.php');

  // 削除したいいツイートID
  $delete_tweet_id = $_GET['tweet_id'];


  // 論理削除用のSQL
  $sql = "UPDATE `tweets` SET `delete_flag` = '1' WHERE `tweets`.`tweet_id` =".$delete_tweet_id;

  // SQL実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  header("Location: index.php");
  exit();
}

?>