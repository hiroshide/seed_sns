<?php
// session_start();

require('function.php');
require('tag_function.php');

// ログインチェック
login_check();

require('dbconect.php');

// POST送信されていたらつぶやきをインサートで保存

  
  // $_POST["tweet"]
if(isset($_POST) && !empty($_POST["tweet"])){
// 変数に入力された値を代入して扱いやすいようにする
    $tweet = $_POST["tweet"];//ここ質問　["tweet"]をつけなかったときArrayとなった
    $member_id = $_SESSION["id"];//ここも質問　なぜlogin_member["member_id"]はつかないのか→プログラムは上から下に読み込むから
    // $reply_tweet_id = $_SESSION['tweets']['reply_tweet_id'];

  try {
    //DBに会員情報を登録するSQL文を作成
// now()
      // mysqlが用意してくれてる関数　現在日時を取得できる
      $sql = 'INSERT INTO `tweets`(`tweet`, `member_id`, `reply_tweet_id`,`created`) VALUES (?,?,-1,now())';

      // $sql = 'INSERT INTO `survey` (`nickname`,`email`,`content`)  VALUES ("'.$nickname.'","'.$email.'","'.$content.'");';

    // SQL文実行
      // sha1() 暗号化を行う
      $data = array($tweet,$member_id);//ここも質問
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);//ここも質問

// insertされたつぶやきのIDを取得
      $new_tweet_id = $dbh->lastInsertId('tweet_id');

// タグ登録機能
      $input_tags = $_POST["hashtag"];
      $input_tags = explode(" #", $input_tags);
      // $input_tags = array("#なつ","夏","海外","セブ");
      foreach ($input_tags as $tag_each) {
         $input_tag = str_replace("#", "",$tag_each);
        exists_tag($input_tag,$dbh);
      }
      // タグとつぶやきの関連付けをDBに保存
      create_tweet_tags($new_tweet_id,$input_tags,$dbh);

// unset();指定した変数を削除
      // unset($_SESSION["tweets"]);
    // 自分のページに移動　データの再送信防止
      header('Location: index.php');
      exit();

  } catch (Exception $e) {
      // tryで囲まれたところでエラーが発生したときにやりたい処理
      // echo 'SQL実行エラー：'.$e->getMessaga();
    }
}


// ページング処理ーーーーーーーーーー
// 
$page = "";

// パラメータが存在していたらページ番号代入
if(isset($_GET["page"])){
  $page = $_GET["page"];
}else{
  // 存在していない時はページ番号を１とする
  $page = 1;
  }

// １以下のイレギュラーな数字が入ってきたときページ番号を強制的に１にする
  // Max カンマ区切りで羅列された数字の中から最大の数字を取得
$page = max($page,1);


// １ページ分の表示件数
$page_row = 5;

// データの件数から最大ページ数を計算する
$sql = "SELECT COUNT(*) AS `cnt` FROM`tweets` WHERE`delete_flag`=0";
$page_stmt = $dbh->prepare($sql);
$page_stmt->execute();

$record_count = $page_stmt->fetch(PDO::FETCH_ASSOC);
// ceil 小数点の切り上げ
$all_page_number = ceil($record_count['cnt'] / $page_row);

// パラメータのページ番号が最大ページを超えていれば強雨静的に最後のページとする
// min カンマ区切りの数字の羅列の中から、最小の数字を取得する
$page = min($page,$all_page_number);

// 表皮するデータを取得開始場所
$start = ($page-1)*$page_row;


// ーーーーーーーーーーーーーーーーーー

// 表示用のデータ取得
try{
// ログインしている人の情報を取得
  $sql = "SELECT * FROM`members` WHERE`member_id` = ".$_SESSION["id"];

  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  $login_member = $stmt->fetch(PDO::FETCH_ASSOC);

  // 一覧用の情報を取得
  // テーブル結合
  // ORDER BY `tweets`.`modified` DESC 降順に並び替え
  // 論理削除に対応　delete_flag = 0 のものだけ取得
  $sql = "SELECT `tweets`.*,`members`.`nick_name`,`picture_path` FROM`tweets` INNER JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `delete_flag`=0 ORDER BY `tweets`.`modified` DESC LIMIT ".$start.",5";

  $stmt = $dbh->prepare($sql);
  $stmt->execute();

// 一覧表示用の配列を用意
  $tweet_list = array();
// 複数行データを取得するためループ
  while(1){
    $one_tweet = $stmt->fetch(PDO::FETCH_ASSOC);
     
    if($one_tweet == false){
      break;
    }else{
// LIKE数を求めるSQL文作成
      $like_sql = "SELECT COUNT(*)as`like_count` FROM `likes` WHERE `tweet_id`=".$one_tweet["tweet_id"];

      // Sql実行
      $like_stmt = $dbh->prepare($like_sql);
      $like_stmt->execute();

      $like_number = $like_stmt->fetch(PDO::FETCH_ASSOC);
// one_tweetの中身
// one_tweet["tweet"]つぶやき
// one_tweet["member_id"]つぶやいた人のID
// one_tweet["nick_name"]つぶやいた人のニックネーム
// one_tweet["picture_path"]つぶやいた人のプロフィール画像
// one_tweet["modified"]つぶやいた日時

//一行ぶんのデータに新しいキーを用意してLIKE数を代入 
      $one_tweet["like_count"] = $like_number["like_count"];

//ログインしている人がLIKEしているかどうかの情報を取得
      $login_like_sql = "SELECT COUNT(*)as`like_flag` FROM `likes` WHERE `tweet_id`=".$one_tweet["tweet_id"]." AND `member_id`=".$_SESSION["id"]; 

// SQL実行
      $login_like_stmt = $dbh->prepare($login_like_sql);
      $login_like_stmt->execute();

// フェッチして取得
      $login_like_number = $login_like_stmt->fetch(PDO::FETCH_ASSOC);


      $one_tweet["login_like_flag"] = $login_like_number["like_flag"];
      // データ取得できている
      $tweet_list[] = $one_tweet;
    }
  }

      $following_sql = "SELECT COUNT(*) as `cnt`FROM `follows` WHERE `member_id`=".$_SESSION["id"];

      $following_stmt = $dbh->prepare($following_sql);
      $following_stmt->execute();
      $following = $following_stmt->fetch(PDO::FETCH_ASSOC);

      $follower_sql = "SELECT COUNT(*) as `cnt`FROM `follows` WHERE `follower_id`=".$_SESSION["id"];
      
      $follower_stmt = $dbh->prepare($follower_sql);
      $follower_stmt->execute();
      $follower = $follower_stmt->fetch(PDO::FETCH_ASSOC);

      // タグの一覧を表示
      $tag_sql = "SELECT * FROM`tags`";
      $tag_stmt= $dbh->prepare($tag_sql);
      $tag_stmt->execute();

      $tag_list = array();
      while(1){
        $one_tag = $tag_stmt->fetch(PDO::FETCH_ASSOC);

        if($one_tag == false){
          break;
        }

        $tag_list[] = $one_tag;
      }

}catch(Exection $e){

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
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo $login_member["nick_name"];?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
              </div>
            </div>
<!-- タグ -->
            <div class="form-group">
              <label class="col-sm-4 control-label">タグ</label>
              <div class="col-sm-8">
                <input type="text" name="hashtag" class="form-control" placeholder="例：　#Japan #Cebu">
              </div>_
            </div>


          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if($page == 1){ ?>
                <li>前</li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php }else{ ?>
                <li><a href="index.php?page=<?php echo $page-1; ?>" class="btn btn-default">前</a></li>
                <?php } ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if ($page == $all_page_number){ ?>
                <li>次</li>
                <?php }else{ ?>
                <li><a href="index.php?page=<?php echo $page+1; ?>" class="btn btn-default">次</a></li>
                <?php } ?>
                <li><?php echo $page; ?>/全<?php echo $all_page_number;?>ページ </li>
          </ul>
        </form>
        <ul>
        <?php foreach($tag_list as $tag_each){?>
           <li><h5><a href="tag_search.php?tag_id=<?php echo $tag_each["id"]?>">#<?php echo $tag_each["tag"] ?></a></h5></li>
        <?php }?>
        </ul> 
      </div>

      <div class="col-md-8 content-margin-top">
        <div class="msg_header">
          <a href="follow.php?member_id=<?php echo $_SESSION["id"]; ?>">Followers<span class="badge badge-pill badge-default"><?php echo $follower["cnt"];?></span></a> <a href="following.php?member_id=<?php echo $_SESSION["id"]; ?>">Following<span class="badge badge-pill badge-default"><?php echo $following["cnt"];?></span></a>
        </div>
      <?php  
        foreach ($tweet_list as $one_tweet) { 
      ?>
          <!-- 繰り返すタグが書かれる場所 -->
          <div class="msg">
          <a href="profile.php?member_id=<?php echo $one_tweet["member_id"]; ?>"><img src="picture_path/<?php echo $one_tweet["picture_path"]; ?>" width="48" height="48"></a>
          <p>
          <?php echo $one_tweet["tweet"];?><br><span class="name"> (<?php echo $one_tweet["nick_name"];?>) </span>
            [<a href="reply.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>">Re</a>] 


            <?php if($one_tweet["login_like_flag"] == 0){ ?> 
              <a href="like.php?like_tweet_id=<?php echo $one_tweet['tweet_id']; ?>&page=<?php echo $page;?>"><i class="fa fa-thumbs-o-up" area-hidden="true"></i>Like</a>
            <?php }else{ ?>
              <a href="like.php?unlike_tweet_id=<?php echo $one_tweet['tweet_id']; ?>&page=<?php echo $page;?>"><i class="fa fa-thumbs-o-up" area-hidden="true"></i>unLike</a>
             <?php } ?> 

            <?php if($one_tweet["like_count"] > 0){ echo $one_tweet["like_count"];} ?> 
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>">
              <?php 
                $modify_date = $one_tweet["modified"];
                // strtotime 文字型のデータを日時型に変換できる
                $modify_date = date("Y-m-d H:i",strtotime($modify_date));
                echo $modify_date;   
              ?>
            </a>
            <?php if($_SESSION["id"]==$one_tweet["member_id"]){ ?>
            [<a href="edit.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>" style="color: #00994C;">編集</a>]
            [<a onclick="return confirm('削除します、よろしいですか？');" href="delete.php?tweet_id=<?php echo $one_tweet["tweet_id"] ?>" style="color: #F33;">削除</a>]
            <?php } ?>

            <?php if($one_tweet["reply_tweet_id"]>0){ ?>
            [<a href="view.php?tweet_id=<?php echo $one_tweet["reply_tweet_id"];?>" style="color: #a9a9a9;">元のメッセージを表示</a>]
            <?php } ?>
          </p>
        </div>
        <?php
          }
        ?>
      </div>

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="asets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
