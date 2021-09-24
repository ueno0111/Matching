<?php
session_start();
	$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';//データベース接続
	$user = 'takeshiueno_0111';
	$password = '5050Rock';


	//相手の情報を取得
	try {
		$dbh = new PDO($dsn, $user, $password);
		//いいね！テーブルにデータ登録、いいね！ボタンを押したときだけ発動する
		if($_GET["action"]=="nice"){ //action指定した箇所が正しければデータ登録される

			$sql = 'SELECT COUNT(*) AS cnt from nice_send where my_id=:my_id and your_id=:your_id'; //POSTされたメールアドレスの情報取得(ログイン時の情報のこと)
			$prepare = $dbh->prepare($sql);//SQLを実行するための準備
			$prepare->bindValue(':my_id', $_SESSION["user_id"], PDO::PARAM_INT);//:mailの箇所をPOSTされたmailの部分に置き換える
			$prepare->bindValue(':your_id', $_GET["user_id"], PDO::PARAM_INT);//:mailの箇所をPOSTされたmailの部分に置き換える
			$prepare->execute();
			$result = $prepare->fetchAll(PDO::FETCH_ASSOC);//$resultへ配列形式で値を代入する　連想配列として 実行
			
			if ($result[0]["cnt"] == 0) {	
				$sql = 'INSERT INTO nice_send (my_id,your_id,up_date) VALUE (:my_id, :your_id,now())';//now()はMYSQLの標準日時取得する
				$prepare = $dbh->prepare($sql);//SQLを実行するための準備
				$prepare->bindValue(':my_id', $_SESSION["user_id"], PDO::PARAM_INT);//STR文字列 ログイン側
				$prepare->bindValue(':your_id', $_GET["user_id"], PDO::PARAM_INT);//INT数字　相手のプロフ側
				$prepare->execute();
			}
		}

		//mysqlに接続するためのクラス、PDOとはデータベースにアクセスする手段
	
		$sql = 'SELECT * from nice_send where my_id=:my_id'; //POSTされたメールアドレスの情報取得(ログイン時の情報のこと)
		$prepare = $dbh->prepare($sql);//SQLを実行するための準備
		$prepare->bindValue(':my_id', $_SESSION["user_id"], PDO::PARAM_INT);//:mailの箇所をPOSTされたmailの部分に置き換える
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
	<link rel="stylesheet" href="like.css">
	<script src="jquery-3.5.1.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet">
	<meta name=”viewport” content=”width=device-width, initial-scale=1”>
	<script src="matching.js"></script>
	<title>マッチングアプリ</title>
</head>

<!----------------------------------ヘッダー部分　始まり---------------------------------------------------------------->
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
<!----------------------------------ヘッダー部分　終わり---------------------------------------------------------------->


<!---------------------------------いいね！した相手を表示する　始まり------------------------------------------------------>
<main>
	<div class="history-wrapper">
		<div class="container">

			<div class="heading">		
				<h2>いいね！をしたお相手はこちら</h2>
			</div>

			<div class="prof-content">
				<?php foreach($result as $val){
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'SELECT * from user where user_id=:user_id'; 
				$prepare = $dbh->prepare($sql);
				$prepare->bindValue(':user_id', $val["your_id"], PDO::PARAM_INT);
				$prepare->execute();
				$user_info = $prepare->fetchAll(PDO::FETCH_ASSOC);

				$dbh = new PDO($dsn, $user, $password);
				$sql = 'SELECT * from upload_image where user_id=:user_id and file_category=1'; 
				$prepare = $dbh->prepare($sql);
				$prepare->bindValue(':user_id', $val["your_id"], PDO::PARAM_INT);
				$prepare->execute();
				$image = $prepare->fetchAll(PDO::FETCH_ASSOC);?>

				<a href="partner.php?user_id=<?php echo $val["your_id"]; ?>&action=search">
				<div class="prof">
				<div class="user">
						<img class="prof-img" src="<?php echo $image[0]["file_name"]; ?>" alt="プロフ画像">
						<h3><?php echo $user_info[0]["name"]; ?><span>&nbsp;&nbsp;<?php echo $user_info[0]["age"];?> 才</span></h3>
						<p class="text"><?php echo $user_info[0]["text"]; ?></p>
				</div>
				</div>
				</a>
				<?php } ?>

			</div>
		</div>
	</div>
</main>
<!---------------------------------いいね！した相手を表示する　終わり------------------------------------------------------>

<!------------------------------フッター部分　始まり------------------------------------------------------->
<footer>
	<section class="FooterSection">
			<div class="Footer-Inner">
				<div class="Footer-Inner-List">
					<a href="matching.php" class="Footer-Inner-List-Item">ホームへ戻る</a>
					<a href="mypage.php" class="Footer-Inner-List-Item">マイページへ戻る</a>
					<a href="mypage.php#link6" class="Footer-Inner-List-Item">条件検索</a>
					<a href="matching.php#link5" class="Footer-Inner-List-Item">お問い合わせ</a>
				</div>
				<div class="Footer-Inner-CopyRight">
							©2020 婚活マッチング制作
				</div>
			</div>
	</section>
</footer>

<!------------------------------フッター部分　終わり------------------------------------------------------->


</body>
</html>