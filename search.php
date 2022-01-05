<?php
	$gender = $_POST["gender"];
	$age = $_POST["age"];
	$area = $_POST["area"];
	$job = $_POST["job"];
	$purpose = $_POST["purpose"];
	$your_like = $_POST["your_like"];
	$your_hobby = $_POST["your_hobby"];
	$your_personality = $_POST["your_personality"];

	//データベース接続
	$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';
	$user = 'takeshiueno_0111';
	$password = '5050Rock';


	try {
		$dbh = new PDO($dsn, $user, $password);
		$sql = 'SELECT * FROM user where ';
		if ($gender != "") {
			$sql .= "gender = '{$gender}' ";
		}
		if ($age != "") {
			$sql .= "and ( age = '{$age}' ";
		}
		if ($area != "") {
			$sql .= "or area = '{$area}' ";
		}
		if ($purpose != "") {
			$sql .= "or purpose = '{$purpose}' ";
		}

		if ($your_like != "") {
			$sql .= "or (";
			$or = "";
			foreach($your_like as $val){
				$sql .= $or . "your_like like '%{$val}%' ";//
				$or = "or ";
			}
			$sql .= ")";
		}
		if ($your_hobby != "") {
			$sql .= "or (";
			$or = "";
			foreach($your_like as $val){
				$sql .= $or . "your_like like '%{$val}%' ";//
				$or = "or ";
			}
			$sql .= ")";
		}
		if ($your_personality != "") {
			$sql .= "or (";
			$or = "";
			foreach($your_like as $val){
				$sql .= $or . "your_like like '%{$val}%' ";//
				$or = "or ";
			}
			$sql .= ")";
		}

		$sql .= ")";
		$prepare = $dbh->prepare($sql);//SQLを実行するための準備
		$prepare->execute();//SQL分を実行
		$result = $prepare->fetchAll();//取ってきたデータを配列に置き換えている。入力されたメールアドレスが正しいかどうか？ データを複数件取る場合はAllを使う 

	}
	catch (PDOException $e) 
	{
	echo "接続失敗: " . $e->getMessage() . "\n";
	exit();
	}
?>



<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="search.css">
		<script src="jquery-3.5.1.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet">
		<meta name=”viewport” content=”width=device-width, initial-scale=1”>
		<script src="matching.js"></script>
		<title>マッチングアプリ</title>
	</head>

	<body>
		<a id="index"></a>
		<!--ヘッダー-->
		<header>
			<div class="gl-Header">
				<div class="gl-Header_Inner">
					<!--ヘッダーロゴ画像部分-->
					<img class="Header-Logo" src="logo.PNG" alt="ロゴ">
				</div>
			</div>
		</header>

		<!--閲覧履歴-->
		<main>
			<div class="history-wrapper">
				<div class="container">

					<div class="heading">		
						<h2>条件に合うお相手はこちら</h2>
					</div>

					<!--相手のプロフィールを表示-->
					<div class="prof-content">
						<?php 
							foreach($result as $val){ 			
							$dbh = new PDO($dsn, $user, $password);
							$sql = 'SELECT * from upload_image where user_id=:user_id and file_category=1'; 
							$prepare = $dbh->prepare($sql);
							$prepare->bindValue(':user_id', $val["user_id"], PDO::PARAM_INT);
							$prepare->execute();
							$image = $prepare->fetchAll(PDO::FETCH_ASSOC);
						?>

							<a href="partner.php?user_id=<?php echo $val["user_id"]; ?>&action=search">
								<div class="prof">
									<div class="user">
										<img class="prof-img" src="<?php echo $image[0]["file_name"]; ?>" alt="プロフ画像">
										<h3><?php echo $val["name"]; ?><span>&nbsp;&nbsp;<?php echo $val["age"];?> 才</span></h3>
									</div>
										<p class="text"><?php echo $val["text"]; ?></p>
								</div>
							</a>
						<?php } ?>
					
					</div>
				</div>
			</div>
		</main>

		<!--Footer-->
		<footer>
			<div class="footer-wrapper">
				<div class="footer-list">
					<ul>
						<li><a href ="serach.php">上へ戻る</a></li>
						<li><a href="matching.php">ホームヘ戻る</a></li>
						<li><a href ="mypage.php#link6">条件検索</a></li>
						<li><a href="matching.php#link5">お問い合わせ</a></li>
					</ul>
				</div>
				<div class="Footer-Inner-CopyRight">©2022 婚活マッチング制作</div>
			</div>
		</footer>
	</body>
</html>