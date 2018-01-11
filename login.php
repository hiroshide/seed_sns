<?php
// SESSION変数を使用する時はファイルの上の方にsession_start();を記述する必要がある
session_start();

// クッキーに情報が存在していたら　自動ログイン
// ＄＿POSTに送信情報を保存
if (isset($_COOKIE["email"]) && !empty($_COOKIE["email"])) {
  # code...
  $_POST["email"] = $_COOKIE["email"];
  $_POST["password"] = $_COOKIE["password"];
  $_POST["save"] = "on";

}

// DBに接続
require('dbconect.php');

// POST送信されていたら
if(isset($_POST) && !empty($_POST)){
// 認証処理
  try {
    // メンバーテーブルでテーブルの中からメールアドレスとパスワードが入力されたものと合致するデータを取得
        $sql = "SELECT  * FROM`members` WHERE`email`=? AND `password`=?;";

        // spl文実行
        // パスワードは入力されたものを暗号化した上で使用する
        $data = array($_POST["email"],sha1($_POST["password"]));
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

        // 一行取得
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        // echo "<pre>";
        // var_dump($member);
        // echo "</pre>";
        if ($member == false){
          // 認証失敗
          $error["login"] = "failed";
        } else{
          // 認証成功
          // １　SESSION変数に会員のIDを保存
          $_SESSION["id"] = $member["member_id"];
          //２　ログインした時間をセッション変数の保存
          $_SESSION["time"] = time(); 
          // ３　自動ログインの処理
          if($_POST["save"] == "on"){
            // クッキーにログイン情報を記憶
            // 保存したい名前　保存したい値　保存したい期間：秒数
            setcookie('email',$_POST["email"],time()+60*60*24*14);
            setcookie('password',$_POST["password"],time()+60*60*24*14);

          }

          header("Location: index.php");
          exit();
        }
        } catch (Exception $e){

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
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>ログイン</legend>
        <form method="post" action="" class="form-horizontal" role="form">
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
          
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="">
            </div>
          </div>
          <!-- 自動ログイン -->
          <div class="form-group">
            <label class="col-sm-4 control-label">自動ログイン</label>
            <div class="col-sm-8">
            <input type="checkbox" name="save" placeholder="">オンにする
            </div>
          </div>

          <?php if((isset($error["login"])) && ($error["login"] == 'failed')) { ?>
            <p class="error">＊EMAILかパスワードが間違っています</p>
            <?php }?>
          <input type="submit" class="btn btn-default" value="ログイン">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
