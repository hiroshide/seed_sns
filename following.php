<?php 
  session_start();

  require('dbconect.php');

  // ログインしている人のプロフィール情報をmembersテーブルから取得

    $sql = "SELECT * FROM`members`WHERE `member_id`=".$_SESSION["id"];

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
      // 個別ページに表示するデータを取得
    $profile_member = $stmt->fetch(PDO::FETCH_ASSOC);

  // フォロー処理
  // Prolile.php?follow_id=7 GET送信というリンクが押された＝フォローボタンが押された
//   if (isset($_GET["follow_id"])){
//     // follow情報を記憶するsql文を作成
//     $sql = "INSERT INTO `follows` (`member_id`, `follower_id`) VALUES (?,?);";

//     $fl_data = array($_SESSION["id"],$_GET["follow_id"]);
//     $fl_stmt = $dbh->prepare($sql);
//     $fl_stmt->execute($fl_data);

// // フォロー押す前の状態に戻す　再読み込みで再度フォロー処理が動くのを防ぐ
//     header("Location: following.php");
//   }



    $nsql = "SELECT * FROM `members` INNER JOIN `follows` ON `members`.`member_id` = `follows`.`follower_id` WHERE `follows`.`member_id`=? ORDER BY`follows`.`created` DESC";

    $data = array($_SESSION["id"]);
    $nstmt = $dbh->prepare($nsql);
    $nstmt->execute($data);

// 一覧表示用の配列を用意
    $tweet_list = array();
// 複数行データを取得するためループ
  while(1){
    $tweeet = $nstmt->fetch(PDO::FETCH_ASSOC);
     
    if($tweeet == false){
      break;
    }else{
      // Following_flagを用意して自分もフォローしていたら１、してなかったら０を代入する
     
            // データ取得できている
    $tweet_list[] = $tweeet;
    }
  }

  
  if(isset($_GET["unfollow_id"])){
// 登録されているfフォロー情報をテーブルから削除

    $un_sql = "DELETE FROM `follows` WHERE `member_id`=".$_SESSION["id"]." AND `follower_id`=".$_GET["unfollow_id"];

  // // // sql実行
    $un_stmt = $dbh->prepare($un_sql);
    $un_stmt->execute();

  header("Location: following.php");
  }

  // $login_member_id = $_SESSION["id"];
  // $unfollow_id = $_GET["unfollow_id"];

  // function unfollow($login_member_id,$unfollow_id){
  //   require ('dbconect.php');

  //   $f_sql = "DELETE FROM `follows` WHERE `member_id`=".$login_member_id." AND `follower_id`=".$_GET["unfollow_id"];

  // // sql実行
  //   $f_stmt = $dbh->prepare($f_sql);
  //   $f_stmt->execute($data);

  //   header("Location: follow.php");
  // }
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-3 content-margin-top">
        <img src="picture_path/<?php echo $profile_member["picture_path"];?>" width="250" height="250">
        <h3><?php echo $profile_member["nick_name"]; ?></h3>

        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>        
      </div>
      <div class="col-md-9 content-margin-top">
        <div class="msg_header">
          <a href="#">Following<span class="badge badge-pill badge-default"><?php echo count($tweet_list); ?></span></a>
        </div>
        <!-- 繰り返し部分 -->
      <?php foreach ($tweet_list as $tweeet) { ?>
        <div class="msg">
          <img src="picture_path/<?php echo $tweeet["picture_path"];?>" width="48" height="48">
          <p><span class="name"> <?php echo $tweeet["nick_name"];?> </span></p>     
          <a href="following.php?unfollow_id=<?php echo $tweeet["follower_id"]; ?>";" ><button class="btn btn-default">フォロー解除</button></a>

        </div>
      <?php } ?>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>