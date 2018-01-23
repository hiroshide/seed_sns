<?php 

require('function.php');


// ログインチェック
login_check();

// つぶやきを削除
delete_tweet();

// require('dbconect.php');

// // 削除したいいツイートID
// $delete_tweet_id = $_GET['tweet_id'];


// // 論理削除用のSQL
// $sql = "UPDATE `tweets` SET `delete_flag` = '1' WHERE `tweets`.`tweet_id` =".$delete_tweet_id;

// // SQL実行
// $stmt = $dbh->prepare($sql);
// $stmt->execute();

// header("Location: index.php");
// exit();

?>