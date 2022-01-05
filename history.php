<?php
session_start();
	$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';//データベース接続
	$user = 'takeshiueno_0111';
	$password = '5050Rock';


	////////////////////////相手の情報を取得///////////////////////////////////////////////////////
	try {
		//mysqlに接続するためのクラス、PDOとはデータベースにアクセスする手段
		$dbh = new PDO($dsn, $user, $password);
		$sql = 'SELECT * from matching_history where user_id=:user_id'; //POSTされたメールアドレスの情報取得(ログイン時の情報のこと)
		$prepare = $dbh->prepare($sql);//SQLを実行するための準備
		$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);//SESSIONを使うことで、前のページのユーザーデータを引き継いでいる。
		$prepare->execute();
		$result = $prepare->fetchAll(PDO::FETCH_ASSOC);//$resultへ配列形式で値を代入する　連想配列として 実行
	} catch (PDOException $e) {
			echo "接続失敗: " . $e->getMessage() . "\n";
			exit();
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="history.css">
		<script src="jquery-3.5.1.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet">
		<meta name=”viewport” content=”width=device-width, initial-scale=1”>
		<script src="matching.js"></script>
		<title>マッチングアプリ</title>
	</head>

	<body>
		<a id="index"></a>
		<!--ヘッダーロゴ -->
		<header>
			<div class="gl-Header">
				<div class="gl-Header_Inner">
					<!--ヘッダーロゴ画像部分-->
					<img class="Header-Logo" src="logo.PNG" alt="ロゴ">
				</div>
			</div>
		</header>


		<!---------------------------------------閲覧したお相手を表示する　始まり--------------------------------------------------------->
		<main>
			<div class="history-wrapper">
				<div class="container">

					<div class="heading">		
						<h2>閲覧履歴</h2>
					</div>
				
					<div class="prof-content">
						<!----------------$resultには閲覧履歴のデータ、SESSIONしているusr_idが代入されている。userテーブルにあるuser_idを取得する----------------------------------------->
						<?php foreach($result as $val){ //ユーザーが閲覧した相手が順順に表示させる
							$dbh = new PDO($dsn, $user, $password);
							$sql = 'SELECT * from user where user_id=:user_id'; 
							$prepare = $dbh->prepare($sql);
							$prepare->bindValue(':user_id', $val["your_id"], PDO::PARAM_INT);
							$prepare->execute();
							$user_info = $prepare->fetchAll(PDO::FETCH_ASSOC);

							///////////////////////////画像のデータを取得する///////////////////////////////////////////////////////////////////
							$dbh = new PDO($dsn, $user, $password);
							$sql = 'SELECT * from upload_image where user_id=:user_id and file_category=1'; 
							$prepare = $dbh->prepare($sql);
							$prepare->bindValue(':user_id', $val["your_id"], PDO::PARAM_INT);
							$prepare->execute();
							$image = $prepare->fetchAll(PDO::FETCH_ASSOC);?>
							<!--------------------$user_infoには閲覧した相手のデータが代入されている、ユーザーが閲覧した相手だけ表示--------------------------------------------------------->
							<a href="partner.php?user_id=<?php echo $val["your_id"]; ?>&action=search">
								<div class="prof">
									<div class="user">
										<img class="prof-img" src="<?php echo $image[0]["file_name"]; ?>" alt="プロフ画像">
										<h3><?php echo $user_info[0]["name"]; ?><span>&nbsp;&nbsp;<?php echo $user_info[0]["age"];?> 才</span></h3>
										<p class="partner-text"><?php echo $user_info[0]["text"]; ?></p>
									</div>
								</div>
							</a>
				
						<?php } ?>
					</div>
				</div>
			</div>
		</main>



		<!------------------------------------------------フッター部分　始まり------------------------------------------------------------------------------->
		<footer>
			<div class="footer-wrapper">
				<div class="Footer-Inner">
					<div class="Footer-Inner-List">
						<a href="matching.php" class="Footer-Inner-List-Item">ホームへ戻る</a>
						<a href="mypage.php" class="Footer-Inner-List-Item">マイページへ戻る</a>
						<a href="mypage.php#link6" class="Footer-Inner-List-Item">条件検索</a>
						<a href="matching.php#link5" class="Footer-Inner-List-Item">お問い合わせ</a>
					</div>
					<div class="Footer-Inner-CopyRight">
						©2022 婚活マッチング制作
					</div>
				</div>
			</div>
		</footer>



	</body>
</html>