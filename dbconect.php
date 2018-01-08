<?php
// 開発環境用
 
    $dsn = 'mysql:dbname=seed_sns;host=localhost';
  // データーベース接続用のユーザー
  // パスワード・
    $user = 'root';
    $password='';
  // 
   


    // データーベース接続オブジェクト
    $dbh = new PDO($dsn, $user, $password);

    // 例外処理を使用可能にする方法（エラー文を表示することができる
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    // 今から実行するSqLをUTF８で送るという設定
    $dbh->query('SET NAMES utf8');
?>