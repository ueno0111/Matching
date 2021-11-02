<?php
	session_start();
	$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';//データベース接続
	$user = 'takeshiueno_0111';
	$password = '5050Rock';


	try {
		//mysqlに接続するためのクラス、PDOとはデータベースにアクセスする手段
		$dbh = new PDO($dsn, $user, $password);
		$sql = 'SELECT * from user where user_id=:user_id'; //POSTされたメールアドレスの情報取得(ログイン時の情報のこと)
		$prepare = $dbh->prepare($sql);//SQLを実行するための準備
		$prepare->bindValue(':user_id', $_GET["user_id"], PDO::PARAM_INT);//:mailの箇所をPOSTされたmailの部分に置き換える
		$prepare->execute();
		$result = $prepare->fetchAll(PDO::FETCH_ASSOC);//$resultへ配列形式で値を代入する　連想配列として 実行

		//アップロードイメージ
		$dbh = new PDO($dsn, $user, $password);
		$sql = 'SELECT * from upload_image where user_id=:user_id'; 
		$prepare = $dbh->prepare($sql);
		$prepare->bindValue(':user_id', $_GET["user_id"], PDO::PARAM_INT);
		$prepare->execute();
		$image = $prepare->fetchAll(PDO::FETCH_ASSOC);

		if($_GET["action"]=="search"){//閲覧履歴テーブルにデータ登録
			$dbh = new PDO($dsn, $user, $password);
			$sql = 'INSERT INTO matching_history (user_id,your_id,up_date) VALUE (:user_id, :your_id,now())';//now()はMYSQLの標準日時取得する
			$prepare = $dbh->prepare($sql);//SQLを実行するための準備
			$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);//STR文字列 ログイン側
			$prepare->bindValue(':your_id', $_GET["user_id"], PDO::PARAM_INT);//INT数字　相手のプロフ側
			$prepare->execute();
		}
	} catch (PDOException $e) {
		echo "接続失敗: " . $e->getMessage() . "\n";
		exit();
}

?>
<!--------------------------HTML　始まり--------------------------------------------------->
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="partner.css">
		<script src="jquery-3.5.1.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet">
		<meta name=”viewport” content=”width=device-width, initial-scale=1”>
		<script src="matching.js"></script>
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<!--<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>-->
		<title>マッチングアプリ</title>
	</head>

	<body>
		<!--------------------------ヘッダーロゴ ------------------------------------------>
		<header class="gl-Header">
			<div class="gl-Header_Inner">
				<img class="Header-Logo" src="logo.PNG" alt="ロゴ画像">

				<div class="header-title">
					<div class="header-subtitle">【お相手のプロフィール】</div>
				</div>
			</div>
		</header>

		<!--プロフィールの内容を表示-->
		<form method="post" action="matching.php?action=user_login">
			<!--プロフィール画像-->
			<img src="<?php echo $image[0]["file_name"]; ?>" alt="サンプル画像" class="img1">
			<section class="check-box2">

				<div class="name2">お名前</div>
				<div class="name2-text"><?php echo $result[0]['name']; ?></div>

				<!--フリガナ-->
				<div class="kana2">フリガナ</div>
				<div class="kana2-text"><?php echo $result[0]['kana']; ?></div>

				<!--性別-->
				<div class="gender2">性別</div>
				<div class="gender-type2"><?php echo $result[0]['gender']; ?></div>

				<!--ご年齢-->
				<div class="age2">ご年齢</div>
				<div class="age2-text"><?php echo $result[0]['age']; ?>才</div><!--selectedという文字列が入ってきたら選択状態になります。新規登録には必要ないが、更新にはデータを保持する場合に使う-->

				<!--メールアドレス-->
				<div class="mail2">メールアドレス</div>
				<div class="mail2-text"><?php echo $result[0]['mail']; ?></div>


				<div class="tel2">電話番号</div>
				<div class="tel2-text"><?php echo $result[0]['tel']; ?></div>


				<div class="area2">お住まいのエリア</div>
				<div class="form-control2"><?php echo $result[0]['area']; ?></div>

				<div class="job2">お仕事</div>
				<div class="form-control2"><?php echo $result[0]['job']; ?></div>

				<!--ご利用目的-->
				<div class="form-group">
					<div class="title-purpose2">出会いの目的</div>
					<div class="form-control2"><?php echo $result[0]['purpose']; ?></div>
				</div>

				<!--お相手の条件-->
				<div class="form-group">
					<div class="checkbox4">好みの第一印象を教えてください</div>
					<div class="your-like2"><?php echo $result[0]['your_like']; ?></div>
				</div>


				<div class="checkbox5">あなたの好きなものを教えてください</div>
				<div class="your-hobby2"><?php echo $result[0]['your_hobby']; ?></div>

				<div class="checkbox6">あなたの性格を教えてください</div>
				<div class="your-personality2"><?php echo $result[0]['your_personality']; ?></div>

				<div class="intro2">自己紹介</div>
				<div class="textarea2"><?php echo $result[0]['text']; ?></div>

			
			</section>
		</form>

		<!--いいね！を送る-->
		<form action="like.php?user_id=<?php echo $_GET["user_id"]; ?>&action=nice" method="post">
			<img src="good-heart.png" alt="いいね！ハート" class="img2">
			<button type="submit"　class="button2" onclick="oopsSwalImageSample()">いいね！を送る</button>
		</form>

		<!--フッター部分-->
		<footer>
			<div class="footer-logo"></div>
			<div class="footer-list">
				<ul>
					<li><a href="matching.php">トップページ</a></li>
					<li><a href ="mypage.php">マイページ</a></li>
					<li><a href="mypage#link6">条件検索</a></li>
					<li><a href="mypage#link5">お問い合わせ</a></li>
				</ul>
			</div>
		</footer>
	
	</body>
</html>