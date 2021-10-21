<?php
	session_start();
	$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';//データベース接続
	$user = 'takeshiueno_0111';
	$password = '5050Rock';



	///////////////////////////編集処理///////////////////////////////////////////////////////
	//会員登録情報をデータベースへ送る
	if($_GET["action"] == "user_update"){
		try {
			//mysqlに接続するためのクラス、PDOとはデータベースにアクセスする手段
			$dbh = new PDO($dsn, $user, $password);
			$sql = 'UPDATE user set name=:name,kana=:kana,gender=:gender,age=:age,mail=:mail,tel=:tel,area=:area,job=:job,purpose=:purpose,your_like=:your_like,your_hobby=:your_hobby,your_personality=:your_personality,text=:text,in_date,up_date)'; //POSTされたメールアドレスの情報取得(ログイン時の情報のこと)
			$prepare = $dbh->prepare($sql);//SQLを実行するための準備
			$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);//matching.phpでuser_idをSessionに入れている。mypage.phpでuser_idをsessionから取り出してる。
			$prepare->bindValue(':name', $_POST["name"], PDO::PARAM_STR);//STR文字列
			$prepare->bindValue(':kana', $_POST["kana"], PDO::PARAM_STR);//INT数字
			$prepare->bindValue(':gender', $_POST["gender"], PDO::PARAM_STR);
			$prepare->bindValue(':age', $_POST["age"], PDO::PARAM_INT);
			$prepare->bindValue(':mail', $_POST["mail"], PDO::PARAM_STR);		//:mailの箇所をPOSTされたmailの部分に置き換える
			$prepare->bindValue(':tel', $_POST["tel"], PDO::PARAM_STR);
			$prepare->bindValue(':area', $_POST["area"], PDO::PARAM_STR);
			$prepare->bindValue(':job', $_POST["job"], PDO::PARAM_STR);
			$prepare->bindValue(':purpose', $_POST["purpose"], PDO::PARAM_STR);
			$prepare->bindValue(':text', $_POST["text"], PDO::PARAM_STR);
				//your_likeの部分
			//初期化、変数を空で入れる
			$your_like = ""; 
			$spl = "";
			//チェックボックス入った情報をループする、チェックがついてあるものを取ってくる
			foreach($_POST["your_like"] as $your_likes){
				//チェックした項目の値が$your_likeに入る、一発目の$your_likeは空、$splも空、$your_likesにチェックされた一つめの値が入ってくる、
				$your_like = $your_like.$spl.$your_likes;
				//値と値の間に,を入れる
				$spl = ",";
			}
			$prepare->bindValue(':your_like', $your_like, PDO::PARAM_STR);


			//やり方その2　こっちのほうが簡単　チェックのついた値を取得する
			if (isset($_POST['your_hobby']) && is_array($_POST['your_hobby'])) {
				$your_hobby = implode(",", $_POST["your_hobby"]);
			}
			$prepare->bindValue(':your_hobby', $your_hobby, PDO::PARAM_STR);

			if (isset($_POST['your_personality']) && is_array($_POST['your_personality'])) {
				$your_personality = implode("、", $_POST["your_personality"]);
			}
			$prepare->bindValue(':your_personality', $your_personality, PDO::PARAM_STR);
      $prepare->execute();
      

			//対象の相手のプロフィール情報を一旦消す
			$dbh = new PDO($dsn, $user, $password);
			$sql = 'DELETE from upload_image where user_id =:user_id'; 
			$prepare = $dbh->prepare($sql);
      		$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
			$prepare->execute();

			

			//一時ディレクトリから指定したディレクトリにファイルを移動する
			move_uploaded_file($_FILES["main"]["tmp_name"],'img/'.$_FILES["main"]["name"]);
			//copy($_FILES["main"]["tmp_name"]."/".$_FILES["main"]["name"], 'img/'.$_FILES["main"]["name"]);　サーバーによっては使えない場合がある
			//新たに登録処理をする
			$dbh = new PDO($dsn, $user, $password);
			$sql = 'INSERT INTO upload_image(user_id,file_name,file_category,description,insert_time,update_time) values(:user_id,:file_name,:file_category,:description,now(),now())';
			$prepare = $dbh->prepare($sql);//SQLを実行するための準備
			$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);//matching.phpでuser_idをSessionに入れている。mypage.phpでuser_idをsessionから取り出してる。
			$prepare->bindValue(':file_name', 'img/'.$_FILES["main"]["name"], PDO::PARAM_STR);//STR文字列
			$prepare->bindValue(':file_category', 1, PDO::PARAM_INT);//INT数字
			$prepare->bindValue(':description', $_FILES["main"]['type'], PDO::PARAM_STR);
			$prepare->execute();
		} catch (PDOException $e) {
			echo "接続失敗: " . $e->getMessage() . "\n";
			exit();
		}
	}

		/////////////////////////////会員登録情報をデータベースへ送る//////////////////////////////////////////
			try {
				//mysqlに接続するためのクラス、PDOとはデータベースにアクセスする手段
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'SELECT * from user where user_id=:user_id'; //POSTされたメールアドレスの情報取得(ログイン時の情報のこと)
				$prepare = $dbh->prepare($sql);//SQLを実行するための準備
				$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);//:mailの箇所をPOSTされたmailの部分に置き換える
				$prepare->execute();
				$result = $prepare->fetchAll(PDO::FETCH_ASSOC);//$resultへ配列形式で値を代入する　連想配列として実行 取得の場合はfetchallが必要

				//uploadから情報を取得
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'SELECT * from upload_image where user_id=:user_id'; 
				$prepare = $dbh->prepare($sql);
				$prepare->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
				$prepare->execute();
				$image = $prepare->fetchAll(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				echo "接続失敗: " . $e->getMessage() . "\n";
				exit();
			}
		
?>

<!---------------------------HTML開始------------------------------------------------>
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

<body>
	<!-------------------------------ヘッダーロゴ ----------------------------------------->
<header class="gl-Header">
	<div class="gl-Header_Inner">
		<!--ヘッダーロゴ画像部分-->
		<img class="Header-Logo" src="logo.PNG" alt="ロゴ画像">

	</div>
</header>
	<!-------------------------------------お相手えお検索するを終了--------------------------------------------->

<main>
<div class="delete-wrap">
	<div class="container">
		<h2 class="title" style="color:#111;">退会手続きが完了いたしました。</h2>
		<p class="text" style="color:#111;">ご利用頂きましてありがとうございました。</p>
	</div>
</div>
</main>



	<!------------------------------------------------フッター部分------------------------------------------------------------------------------->
	<section class="FooterSection">
		<div class="Footer">
			<div class="Footer-Inner">
				<div class="Footer-Inner-List">
					<a href="mypage.php" class="Footer-Inner-List-Item">ホームへ戻る</a>
					<a href="matching.php#link1" class="Footer-Inner-List-Item">アプリのご登録方法</a>
					<a href="matching.php#link2" class="Footer-Inner-List-Item">ご利用方法</a>
					<a href="matching.php#link5" class="Footer-Inner-List-Item">お問い合わせ</a>
				</div>
				<div class="Footer-Inner-CopyRight">
					©2020 婚活マッチング制作
				</div>
			</div>
		</div>
	</section>
	<!------------------------------------------------フッター部分 終了------------------------------------------------------------------------------->
</body>
</html>