<?php
	session_start();
	$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';//データベース接続
	$user = 'takeshiueno_0111';
	$password = '5050Rock';

	//対象の相手のidを削除する
	try{
		$dbh = new PDO($dsn, $user, $password);//PDOでデータベースへ接続
		$sql = 'DELETE from user where user_id =:user_id';//ユーザーidを削除
		$prepare = $dbh->prepare($sql);
		$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
		$prepare->execute();
	}
	catch (PDOException $e) {
	echo "接続失敗: " . $e->getMessage() . "\n";
	exit();
	}

	session_destroy();//セッションを終らせる
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="delete_id_done.css">
		<script src="jquery-3.5.1.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet">
		<meta name=”viewport” content=”width=device-width, initial-scale=1”>
		<script src="matching.js"></script>
		<title>マッチングアプリ</title>
	</head>

	<!--ヘッダー-->
	<header>
		<div class="gl-Header">
			<div class="gl-Header_Inner">
				<!--ヘッダーロゴ画像部分-->
				<img class="Header-Logo" src="logo.PNG" alt="ロゴ画像">
			</div>
		</div>
	</header>

    <body>
	
		<!--退会完了画面-->

		<main>
			<div class="delete-wrap">
				<div class="container">
					<h2 class="title" style="color:#111;">退会手続きが完了いたしました。</h2>
					<p class="text" style="color:#111;">ご利用頂きましてありがとうございました。</p>
					<a href="matching.php" class="top_back">トップへ戻る</a>
				</div>
			</div>

		</main>

		<!----フッター-->
		<footer>
			<section class="FooterSection">
					<div class="Footer-Inner">
						<div class="Footer-Inner-List">
							<a href="matching.php" class="Footer-Inner-List-Item">ホームへ戻る</a>
							<a href="matching.php#link1" class="Footer-Inner-List-Item">アプリのご登録方法</a>
							<a href="matching.php#link2" class="Footer-Inner-List-Item">ご利用方法</a>
							<a href="matching.php#link5" class="Footer-Inner-List-Item">お問い合わせ</a>
						</div>
						<div class="Footer-Inner-CopyRight">
							©2022 婚活マッチング制作
						</div>
					</div>
			</section>
		</footer>
    </body>
</html>