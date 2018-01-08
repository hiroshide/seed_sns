<?php
  session_start();//SESSIONを使うときは絶対必要


// 書き直し処理(check.phpで書き直しというボタンが押された時)
  if (isset($_GET['action']) && $_GET['action'] == 'rewrite'){

    // 書き直すために初期表示する情報を変数に格納
    $nick_name = $_SESSION['join']['nick_name'];
    $email = $_SESSION['join']['email'];
    $password = $_SESSION['join']['password'];
    // $picture_name = $_SESSION['join']['picture_name'];

  }else{
    $nick_name = '';
    $email = '';
    $password = '';
  }
// Post送信された時
// ＄＿Postが存在しているかつ＄＿POSTの中身が空でないとき
// EMpty  中身が空か判定　０　”” NULL Falseを全てと認識する
  if(isset($_POST) && !empty($_POST)){



// 入力チェック
// ニックネーム空だったら
    // nicknameはblankだったというマークを保存
    if ($_POST["nick_name"] == ''){

      $error["nick_name"] = 'blank';
    }

    // Email
    if ($_POST["email"] == ''){

      $error["email"] = 'blank';
    }

    // Password
    if ($_POST["password"] == ''){

      $error["passeword"] = 'blank';
    }elseif (strlen($_POST["password"]) < 4 ) {
      $error["password"] = 'length';
    }

// 入力チェック後エラーがなければCheck.phpに移動
    // $errorが存在してなかったら入力は正常と認識
    if (!isset($error)){

      // 画像のアップロード処理
      // ide1.png を指定した時　＄Picture_nameの中身は20171222142530ide1.png というような文字列が代入される
      // Yyear m month d day h hour s second
      // ファイル名の決定
      $picture_name = date('Ymdhis') .$_FILES['picture_path']['name'];

// アップロード
      // フォルダに書き込み権限がないと保存されない
      // move_uploaded_file(uoloadしtくぃファイル, サーバのどこにどういう名前でアップロードするか指定)
      move_uploaded_file($_FILES['picture_path']['tmp_name'], '../picture_path/'.$picture_name);

// Session変数に保存された値を保存(どこの画面からでも使用できる)
      // 注意　必ずファイルの一番上にsession_start();と書く
      // POST送信された情報をJOINというきーを指定して保存
      $_SESSION['join'] = $_POST;
      $_SESSION['join']['picture_path'] = $picture_name;


// Check.phpに移動
      header('Location: check.php');
// これ以下のコードを無駄にしないようにこのページの処理を終了する
      exit();

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
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->

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
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
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
        <legend>会員登録</legend>
        <form method="post" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <!-- ニックネーム -->
          <div class="form-group">
            <label class="col-sm-4 control-label">ニックネーム</label>
            <div class="col-sm-8">
              <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun" value="<?php echo $nick_name; ?>">
            </div>
            <?php if((isset($error["nick_name"])) && ($error["nick_name"] == 'blank')) { ?>
            <p class="error">＊ニックネームを入力してください</p>
            <?php }?>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com" value="<?php echo $email; ?>">
            </div>
            <?php if((isset($error["email"])) && ($error["email"] == 'blank')) { ?>
            <p class="error">＊Emailを入力してください</p>
            <?php }?>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="" value="<?php echo $email; ?>">
            </div>
            <?php if((isset($error["password"])) && ($error["password"] == 'blank')) { ?>
            <p class="error">＊パスワードを入力してください</p>
            <?php }?>
            <?php if((isset($error["password"])) && ($error["password"] == 'length')) { ?>
            <p class="error">＊パスワードを４文字以上で入力してください</p>
            <?php }?>

          </div>
          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="picture_path" class="form-control">
            </div>
          </div>

          <input type="submit" class="btn btn-default" value="確認画面へ">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>
