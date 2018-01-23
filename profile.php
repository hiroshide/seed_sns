<?php 
  session_start();

  require('dbconect.php');

  // GET送信されたmember_idを使ってプロフィール情報をmembersテーブルから取得

  $sql = "SELECT * FROM`members`WHERE `member_id`=".$_GET["member_id"];

  $stmt = $dbh->prepare($sql);
  $stmt->execute();
      // 個別ページに表示するデータを取得
  $profile_member = $stmt->fetch(PDO::FETCH_ASSOC);

  // フォロー処理
  // Prolile.php?follow_id=7 GET送信というリンクが押された＝フォローボタンが押された
  if (isset($_GET["follow_id"])){
    // follow情報を記憶するsql文を作成
    $sql = "INSERT INTO `follows` (`member_id`, `follower_id`) VALUES (?,?);";

    $fl_data = array($_SESSION["id"],$_GET["follow_id"]);
    $fl_stmt = $dbh->prepare($sql);
    $fl_stmt->execute($fl_data);
  }



  $nsql = "SELECT * FROM `tweets` WHERE `member_id`=? AND `delete_flag`=0 ORDER BY `tweets`.`modified` DESC";

  $data = array($_GET["member_id"]);
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
      // データ取得できている
      $tweet_list[] = $tweeet;
    }
  }

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
        <?php if($_SESSION["id"] != $_GET["member_id"]){ ?>
        <a href="profile.php?member_id=<?php echo $profile_member["member_id"]; ?>&follow_id=<?php echo $profile_member["member_id"]; ?>">
          <button class="btn btn-block btn-default">フォロー</button>
        </a>
        <?php }?>

        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>        
      </div>
      <div class="col-md-9 content-margin-top">

      <?php foreach ($tweet_list as $tweet) { ?>
        <div class="msg">
          <img src="picture_path/<?php echo $profile_member["picture_path"];?>" width="100" height="100">
          <p>投稿者 : <span class="name"> <?php echo $profile_member["nick_name"];?> </span></p>
          <p>
            つぶやき : <br>
            <?php echo $tweet["tweet"];?>
          </p>
          <p class="day">
            <?php 
                $modify_date = $tweet["modified"];
                // strtotime 文字型のデータを日時型に変換できる
                $modify_date = date("Y-m-d H:i",strtotime($modify_date));
                echo $modify_date;
              ?>
            <?php if($_SESSION["id"] == $profile_member["member_id"]){ ?>
            [<a href="#" style="color: #F33;">削除</a>]
            <?php }?>
          </p>
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
1 件のコメント