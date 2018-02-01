<?php
  // タグ登録機能

// TAGSテーブルに今つけられたタグが存在するかチェック
function exists_tag($tag,$dbh){
  // require('dbconect.php');

  // tagsテーブルへ存在するかチェックするSqlを作成
  $tag_sql = "SELECT COUNT(*) AS `cnt` FROM `tags` WHERE `tag` =?";

  // Sql実行
  $data = array($tag);
  $stmt = $dbh->prepare($tag_sql);
  $stmt->execute($data);
  // Fetck
  $tag_count = $stmt->fetch(PDO::FETCH_ASSOC);
  // 存在しなかったら追加
  if($tag_count['cnt'] == 0){
     // Tagsテーブルへデータ追加するSql文作成
    $tag_create_sql = "INSERT INTO `tags` (`tag`) VALUES (?);";
  // Sql実行
    $data = array($tag);
    $create_stmt = $dbh->prepare($tag_create_sql);
    $create_stmt->execute($data);
  }
 
}

function create_tweet_tags($relate_tweet_id,$input_tags,$dbh){
  $input_tags_string = "";
// 一番最後を見極めるためのカウンタ
  $i = 0;
  foreach ($input_tags as $tag_each) {
    $tag_each = str_replace("#", "",$tag_each);

    $input_tags_string .= "'".$tag_each."'";
    $i++;
    // 一番最後が成り立たない
    if($i < count($input_tags)){
    $input_tags_string .= ",";
    }
  }

// それぞれのハッシュタグのIDをtagsテーブルから探して保存

  $sql = "SELECT * FROM `tags` WHERE `tag` IN (".$input_tags_string.")";

  // sql実行

  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  while (1) {
    $one_tag = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($one_tag == false) {
      break;
    }

// tweet_tagsテーブルへ登録
    $create_tweet_tags_sql = "INSERT INTO `tweet_tags` (`tweet_id`,`tag_id`)VALUES(".$relate_tweet_id.",".$one_tag["id"].");";
    $ctt_stmt = $dbh->prepare($create_tweet_tags_sql);
    $ctt_stmt->execute();

  }


}
?>