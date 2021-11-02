<?php
		session_start();
		//入力チェック //
		//初期化、空の値を代入することで、echoをした時に何も表示されない
		$error_name="";
		$error_body="";
		$error_succes="";

		//データベースに接続
		$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';//データベース接続
		$user = 'takeshiueno_0111';
		$password = '5050Rock';

		//お問い合わせフォームからデータベースへ//
		if($_GET["action"] == "send"){
			$error_flag=true;
		///nameに値が空の場合//
		if($_POST["name"] == ""){
			$error_name= "お名前を入力して下さい";
			$error_flag=false;
		}
		//textの値が空だった場合//
		if($_POST["text"] == ""){
			$error_body="内容を入力して下さい";
			$error_flag=false;
		}else{
		echo "<script>alert('お問い合わせ内容を承りました');</script>";//問題なく内容が送信が出来た時にメッセージを表示する
		}
		//$error_flagの中身はtrue,false。問い合わせフォームの必須の部分が入力されていれば、データベースと接続する//
		if($error_flag){
		//お問い合わせデータ登録始まり//
			try {
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'INSERT INTO matching_contact (name,age,type,text,in_date,up_date) VALUE (:name, :age,:type,:text,now(),now())';//now()はMYSQLの標準日時取得する
				$prepare = $dbh->prepare($sql);//SQLを実行するための準備
				$prepare->bindValue(':name', $_POST["name"], PDO::PARAM_STR);//STR文字列
				$prepare->bindValue(':age', $_POST["age"], PDO::PARAM_INT);//INT数字
				$prepare->bindValue(':type', $_POST["type"], PDO::PARAM_STR);
				$prepare->bindValue(':text', $_POST["text"], PDO::PARAM_STR);
				$prepare->execute();

				} catch (PDOException $e) {
						echo "接続失敗: " . $e->getMessage() . "\n";
						exit();
				$error_succes="お問い合わせ内容を送ることができませんでした";
				}
			}
		}







		//ログインする際に必要でデータベースに接続する時の文//
		//ログイン画面で入力した値が正しくGET送信された場合、データベースに接続することができる//
		if($_GET["action"] == "user_login"){
			try {
				$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';
				$user = 'takeshiueno_0111';
				$password = '5050Rock';
				$dbh = new PDO($dsn, $user, $password);
		
			} catch (PDOException $e) {
				echo "接続失敗: " . $e->getMessage() . "\n";
				exit();
			}

			//ログインのアドレスと登録してあるアドレスが一致するか？//
			//エラーメッセージの初期状態を空にする//
			$error = "";
			//サブミットボタンが押されたときの処理、ボタンを押した時に値が入っているかどうか//
			if (isset($_POST['login'])) {
				$mail = $_POST['mail'];

				//データが渡ってきた場合//
				try {
						$dbh = new PDO($dsn, $user, $password);
						$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';
						$user = 'takeshiueno_0111';
						$password = '5050Rock';

						/////////////SQL文、DBからmailのデータを取得////////////////////////////
						$sql = 'SELECT * FROM user where mail = :mail';
						$prepare = $dbh->prepare($sql);//SQLを実行するための準備
						$prepare->bindValue(':mail', $_POST["mail"], PDO::PARAM_STR);
						$prepare->execute();//SQL分を実行
						$result = $prepare->fetch();//取ってきたデータを配列に置き換えている。入力されたメールアドレスが正しいかどうか？ 
						$dbh = null;

						//ログイン認証ができたときの処理//
						//SQL文で取得したデータと、入力したデータが合っているか//
						if(isset($result)&&!empty($result)) { 
							$_SESSION['user_id'] = $result['user_id'];
							$_SESSION['mail'] = $result['mail'];
							//入力した値とDBに登録したデータが一致すれば指定したページへ遷移//
							header('Location:http://takeshiueno.php.xdomain.jp/matching/mypage.php?action=user_login',true,307);//hader関数は３つ引数を入れられる。入力した値をリダイレクト時にPOSTする（307をつけないとmailの情報がPOSTされない)
						}else{
							if($result==false){
								$alert="";
								$alert="<script type='text/javascript'>alert('メールアドレスが間違っています。');</script>";
								echo $alert;
							}
						}
					}		//送信できていなかったら元に戻す//
							catch (PDOException $e) {
							echo "接続失敗: " . $e->getMessage() . "\n";
							exit();
				}
							$error_succes="ログインできません";
			}
		}








		//新規会員登録情報をデータベースへ送る//
		if($_GET["action"] == "user_insert"){
			try {
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'INSERT INTO user (name,kana,gender,age,mail,tel,area,job,purpose,your_like,your_hobby,your_personality,text,in_date,up_date) VALUE (:name,:kana,:gender,:age,:mail,:tel,:area,:job,:purpose,:your_like,:your_hobby,:your_personality,:text,now(),now())';//now()はMYSQLの標準日時取得する
				$prepare = $dbh->prepare($sql);//SQLを実行するための準備
				$prepare->bindValue(':name', $_POST["name"], PDO::PARAM_STR);//STR文字列
				$prepare->bindValue(':kana', $_POST["kana"], PDO::PARAM_STR);//INT数字
				$prepare->bindValue(':gender', $_POST["gender"], PDO::PARAM_STR);
				$prepare->bindValue(':age', $_POST["age"], PDO::PARAM_INT);
				$prepare->bindValue(':mail', $_POST["mail"], PDO::PARAM_STR);
				$prepare->bindValue(':tel', $_POST["tel"], PDO::PARAM_STR);
				$prepare->bindValue(':area', $_POST["area"], PDO::PARAM_STR);
				$prepare->bindValue(':job', $_POST["job"], PDO::PARAM_STR);
				$prepare->bindValue(':purpose', $_POST["purpose"], PDO::PARAM_STR);
				$prepare->bindValue(':text', $_POST["text"], PDO::PARAM_STR);

				//初期化する
				$your_like = ""; 
				$spl = "";
				//チェックボックス入った情報をループする、チェックがついてあるものを取ってくる
				foreach($_POST["your_like"] as $your_likes){
					//チェックした項目の値が$your_likeに入る、一発目の$your_likeは空、$splも空、$your_likesにチェックされた一つめの値が入ってくる、
					$your_like = $your_like.$spl.$your_likes;
					//登録する時に値と値の間に,を入れる
					$spl = ",";
				}
					$prepare->bindValue(':your_like', $your_like, PDO::PARAM_STR);//実行する


				//やり方その2　 簡易版　チェックのついた値を取得する
				if (isset($_POST['your_hobby']) && is_array($_POST['your_hobby'])) {
					$your_hobby = implode(",", $_POST["your_hobby"]);
				}
					$prepare->bindValue(':your_hobby', $your_hobby, PDO::PARAM_STR);

				if (isset($_POST['your_personality']) && is_array($_POST['your_personality'])) {
					$your_personality = implode("、", $_POST["your_personality"]);
				}
					$prepare->bindValue(':your_personality', $your_personality, PDO::PARAM_STR);
					$prepare->execute();








				//画像を登録する//
				//user_idの降順（一番最後の最新のデータ)を取得//
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'SELECT * FROM user order by user_id desc limit 1';
				$prepare = $dbh->prepare($sql);//SQLを実行するための準備
				$prepare->execute();
				$result = $prepare->fetch(); //fetchは１件、fetchallは複数件の場合
				

				//対象の相手のプロフィール情報を一旦削除//
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'DELETE from upload_image where user_id =:user_id'; 
				$prepare = $dbh->prepare($sql);//SQLを実行するための準備
				$prepare->bindValue(':user_id', $result["user_id"], PDO::PARAM_INT);
				$prepare->execute();

				//一時ディレクトリから指定したディレクトリにファイルを移動する//
				move_uploaded_file($_FILES["main"]["tmp_name"],'img/'.$_FILES["main"]["name"]);

				//画像を新たに登録処理をする
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'INSERT INTO upload_image(user_id,file_name,file_category,description,insert_time,update_time) values(:user_id,:file_name,:file_category,:description,now(),now())';
				$prepare = $dbh->prepare($sql);//SQLを実行するための準備
				$prepare->bindValue(':user_id', $result["user_id"], PDO::PARAM_INT);
				$prepare->bindValue(':file_name', 'img/'.$_FILES["main"]["name"], PDO::PARAM_STR);//STR文字列
				$prepare->bindValue(':file_category', 1, PDO::PARAM_INT);//INT数字
				$prepare->bindValue(':description', $_FILES["main"]['type'], PDO::PARAM_STR);
				$prepare->execute();
			}	catch (PDOException $e) {
						echo "接続失敗: " . $e->getMessage() . "\n";
						exit();
			}
		}

?>




<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="matching.css">
		<script src="jquery-3.5.1.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet">
		<meta name=”viewport” content=”width=device-width, initial-scale=1”>
		<script src="matching.js"></script>
		<title>マッチングアプリ</title>
	</head>

	<body>
		<!--ロゴ -->
		<header class="gl-Header">
			<!--ロゴ画像--->
			<img class="Header-Logo" src="logo.PNG" alt=ロゴ画像>
			<div class="gl-Header_Inner">
				<ul class="gl-header-container">
					<div class="hamburger">
						<span></span>
						<span></span>
						<span></span>
					</div>

					<!--ハンバーガーメニューの中身-->
					<nav class="globalMenuSp">
						<ul>
							<li><a href="matching.php#link1">ご登録方法</a></li>
							<li><a href="matching.php#link2">ご利用の流れ</a></li>
							<li><a href="matching.php#link3">プライバシーポリシー</a></li>
							<li><a href="matching.php#link4">利用者様からのレビュー</a></li>
							<li><a href="matching.php#link5">お問い合わせ</a></li>
							<li><a href="matching.php#link6">退会はこちら<br>※ログインをおこなって下さい。</a></li>
						</ul>
					</nav>
				</ul>
			</div>
		</header>



		<!--ヘッダー画像-->
		<figure>
			<img src="opencar.jpg" alt="文字の代替">
			<figcaption class="decorator">
				<div class="title-text">
					<h3 class="titile-text1">マッチングアプリ注目度</h3>
					<h1 class="title-text2">NO.1</h1>
					<h2 class="title-text3">好きなことでつながろう</h2>
					<div class="title-text4"><p>毎月<br>1万人組の<br>カップル誕生</p></div>
				</div>
			</figcaption>	
		</figure>




		<!--今すぐ登録する！-->
		<!--モーダル-->
		<button id="openModal">今すぐ登録する！</button>
		<section id="modalArea" class="modalArea">
			<div id="modalBg" class="modalBg"></div>
			<div class="modalWrapper">
				<div class="modalContents">
					<div class="modal-title">【会員登録フォーム】</div>

					<form method="post" action="matching.php?action=user_insert" enctype="multipart/form-data">
						<section class="check-box">
							<div class="prof-image">プロフィール画像</div>
							<input type="file" name="main" class="image-file">
							
							<!--お名前-->
							<div class="name"><span>お名前</span>    (必須)</div>
							<input type="text" id="name" name="name" class="form-control" placeholder="例：山田　太郎" autofocus required>
					
							<!--フリガナ-->
							<div class="kana"><span>フリガナ</span>    (必須)</div>
							<input type="text" id="kana" name="kana" class="form-control" placeholder="例：ヤマダ　タロウ" autofocus required><!--requiredはエラーメッセージを表示してくれる-->
						
							<!--性別-->
							<div class="gender"><span>性別</span>    (必須)</div>
							<div class="gender-type">
								<input type="radio" name="gender" id="gender1" value="男性" autofocus required><label for="gender1">男性</label>
								<input type="radio" name="gender" id="gender2" value="女性" autofocus required><label for="gender2">女性</label>
								<input type="radio" name="gender" id="gender3" value="その他" autofocus required><label for="gender3">その他</label> <!--cheakedを付けると最後のチェックが反映される-->
							</div>

							<!--生年月日-->
							<div class="age"><span>ご年齢</span>    (必須)</div>
							<select name="age">
								<option value="未選択" >選択してください</option>
								<?php
								for ($i=18; $i<=100;$i++) {
									echo "<option value='{$i}'>{$i}才</option>";
								}
								?>
							</select>
								
							<!--メールアドレス-->
							<div class="mail">メールアドレス<span>    （必須）</span></div>
							<input type="text" id="email" name="mail" class="form-control" placeholder="例：example@com" required>
							
							<!--電話番号-->
							<div class="tel-number">電話番号</div>
							<input type="text" id="tel" name="tel" autocomplete="tel" placeholder="例：080-1234-5678" required>
							
							<!--お住まいのエリア-->
							<div class="area-modal">お住まいのエリア<span>    (必須)</span></div>
							<select name="area">
								<option value="未選択" autofocus required>選択してください</option>
								<option value="北海道" autofocus required>北海道</option>
								<option value="青森"　autofocus required>青森</option>
								<option value="岩手"　autofocus required>岩手</option>
								<option value="宮城"　autofocus required>宮城</option>
								<option value="秋田"　autofocus required>秋田</option>
								<option value="山形" autofocus required>山形</option>
								<option value="福島" autofocus required>福島</option>
								<option value="茨城" autofocus required>茨城</option>
								<option value="栃木" autofocus required>栃木</option>
								<option value="群馬" autofocus required>群馬</option>
								<option value="埼玉" autofocus required>埼玉</option>
								<option value="千葉" autofocus required>千葉</option>
								<option value="東京" autofocus required>東京</option>
								<option value="神奈川" autofocus required>神奈川</option>
								<option value="新潟" autofocus required>新潟</option>
								<option value="富山" autofocus required>富山</option>
								<option value="石川" autofocus required>石川</option>
								<option value="福井" autofocus required>福井</option>
								<option value="山口" autofocus required>山口</option>
								<option value="長野" autofocus required>長野</option>
								<option value="岐阜" autofocus required>岐阜</option>
								<option value="静岡" autofocus required>静岡</option>
								<option value="愛知" autofocus required>愛知</option>
								<option value="三重" autofocus required>三重</option>
								<option value="滋賀" autofocus required>滋賀</option>
								<option value="京都" autofocus required>京都</option>
								<option value="大阪" autofocus required>大阪</option>
								<option value="兵庫" autofocus required>兵庫</option>
								<option value="奈良" autofocus required>奈良</option>
								<option value="和歌山" autofocus required>和歌山</option>
								<option value="鳥取" autofocus required>鳥取</option>
								<option value="島根" autofocus required>島根</option>
								<option value="岡山" autofocus required>岡山</option>
								<option value="広島" autofocus required>広島</option>
								<option value="山口" autofocus required>山口</option>
								<option value="徳島" autofocus required>徳島</option>
								<option value="香川" autofocus required>香川</option>
								<option value="愛媛" autofocus required>愛媛</option>
								<option value="高知" autofocus required>高知</option>
								<option value="福岡" autofocus required>福岡</option>
								<option value="佐賀" autofocus required>佐賀</option>
								<option value="長崎" autofocus required>長崎</option>
								<option value="熊本" autofocus required>熊本</option>
								<option value="大分" autofocus required>大分</option>
								<option value="宮崎" autofocus required>宮崎</option>
								<option value="鹿児島" autofocus required>鹿児島</option>
								<option value="沖縄" autofocus required>沖縄</option>
							</select>
							
							<!--お仕事-->
							<div class="job">お仕事</div>
							<select name="job">
								<option value="未選択" autofocus required>選択してください</option>
								<option value="事務" autofocus required>事務</option>
								<option value="教育" autofocus required>教育</option>
								<option value="不動産" autofocus required>不動産</option>
								<option value="サービス業" autofocus required>サービス業</option>
								<option value="飲食業界" autofocus required>飲食業界</option>
								<option value="運輸" autofocus required>運輸・物流</option>
								<option value="建設" autofocus required>建設</option>
								<option value="製造業" autofocus required>製造業</option>
								<option value="IT・エンジニア" autofocus required>IT・エンジニア</option>
								<option value="卸売業・小売業" autofocus required>卸売業・小売業</option>
								<option value="金融業" autofocus required>金融業</option>
								<option value="医療" autofocus required>医療</option>
								<option value="観光" autofocus required>観光</option>
								<option value="貿易" autofocus required>貿易</option>
								<option value="士業" autofocus required>士業</option>
								<option value="公務員" autofocus required>公務員</option>
								<option value="スポーツ" autofocus required>スポーツ</option>
								<option value="映像制作" autofocus required>映像制作</option>
								<option value="芸能" autofocus required>芸能</option>
								<option value="ETC" autofocus required>その他</option>
							</select>

							<!--ご利用目的-->
							<div class="form-group">
								<div class="title-purpose">出会いの目的</span>    (必須)</div>
								<select id="purpose" name="purpose" class="form-control">
									<option value="">選択してください</option>
									<option value="真剣な婚活がしたい" autofocus required>真剣な婚活がしたい</option>
									<option value="共通の趣味の友達が欲しい" autofocus required>共通の趣味の友達がほしい</option>
									<option value="気軽に会える友達が欲しい" autofocus required>気軽に会える友達が欲しい</option>
									<option value="未選択" autofocus required>未選択</option>
								</select>
							</div>

							<!--お相手の条件-->
							<div class="form-group">
								<div class="checkbox1">好みの第一印象を教えてください</div>
								<label>
									<input type="checkbox" id="q1_html1" name="your_like[]" value="清潔感"><label for="q1_html1">清潔感がある人が好き</lebel><br>
									<input type="checkbox" id="q1_html2" name="your_like[]" value="優しい"><label for="q1_html2">優しい人が好き</label><br>
									<input type="checkbox" id="q1_html3" name="your_like[]" value="大人しい"><label for="q1_html3">おとなしい人が好き</label><br>
									<input type="checkbox" id="q1_html4" name="your_like[]" value="明るい"><label for="q1_html4">明るい人が好き</label><br>
									<input type="checkbox" id="q1_html5" name="your_like[]" value="クール"><label for="q1_html5">クール系好き</label><br>
									<input type="checkbox" id="q1_html6" name="your_like[]" value="かわいい"><label for="q1_html6">かわいい系が好き</label><br>
									<input type="checkbox" id="q1_html7" name="your_like[]" value="キレイ"><label for="q1_html7">キレイ系が好き</label><br>
									<input type="checkbox" id="q1_html8" name="your_like[]" value="低身長"><label for="q1_html8">低身長が好き</label><br>
									<input type="checkbox" id="q1_html9" name="your_like[]" value="高身長"><label for="q1_html9">高身長が好き</label><br>
									<input type="checkbox" id="q1_html10" name="your_like[]" value="肉食系"><label for="q1_html10">肉食系が好き</label><br>
									<input type="checkbox" id="q1_html11" name="your_like[]" value="草食系"><label for="q1_html11">草食系が好き</label><br>
									<input type="checkbox" id="q1_html12" name="your_like[]" value="一重が好き"><label for="q1_html12">一重が好き</label><br>
									<input type="checkbox" id="q1_html13" name="your_like[]" value="二重が好き"><label for="q1_html13">二重が好き</label><br>
									<input type="checkbox" id="q1_html14" name="your_like[]" value="彫りが深い人が好き"><label for="q1_html14">彫りが深い人が好き</label><br>
									<input type="checkbox" id="q1_html15" name="your_like[]" value="彫りが浅い人が好き"><label for="q1_html15">彫りが浅い人が好き</label><br>
									<input type="checkbox" id="q1_html16" name="your_like[]" value="スリム体型が好き"><label for="q1_html16">スリム体型が好き</label><br>
									<input type="checkbox" id="q1_html17" name="your_like[]" value="筋肉質な人が好き"><label for="q1_html17">筋肉質な人が好き</label><br>
									<input type="checkbox" id="q1_html18" name="your_like[]" value="ぽっちゃり体型が好き"><label for="q1_html18">ぽっちゃり体型が好き</label><br>
									<input type="checkbox" id="q1_html19" name="your_like[]" value="おしゃれな人が好き"><label for="q1_html19">おしゃれな人が好き</label><br>
								</label>


								<div class="checkbox2">あなたの好きなものを教えてください</div>
								<label>
									<input type="checkbox" id="q1_html20" name="your_hobby[]" value="サッカー"><label for="q1_html20">サッカー</label><br>
									<input type="checkbox" id="q1_html21" name="your_hobby[]" value="野球"><label for="q1_html21">野球</label><br>
									<input type="checkbox" id="q1_html22" name="your_hobby[]" value="フットサル"><label for="q1_html22">フットサル</label><br>
									<input type="checkbox" id="q1_html23" name="your_hobby[]" value="テニス"><label for="q1_html23">テニス</label><br>
									<input type="checkbox" id="q1_html24" name="your_hobby[]" value="バドミントン"><label for="q1_html24">バドミントン</label><br>
									<input type="checkbox" id="q1_html25" name="your_hobby[]" value="ソフトボール"><label for="q1_html25">ソフトボール</label><br>
									<input type="checkbox" id="q1_html26" name="your_hobby[]" value="水泳"><label for="q1_html26">水泳</label><br>
									<input type="checkbox" id="q1_html27" name="your_hobby[]" value="フリスビー"><label for="q1_html27">フリスビー</label><br>
									<input type="checkbox" id="q1_html28" name="your_hobby[]" value="ラグビー"><label for="q1_html28">ラグビー</label><br>
									<input type="checkbox" id="q1_html29" name="your_hobby[]" value="卓球"><label for="q1_html29">卓球</label><br>
									<input type="checkbox" id="q1_html30" name="your_hobby[]" value="ダーツ"><label for="q1_html30">ダーツ</label><br>
									<input type="checkbox" id="q1_html31" name="your_hobby[]" value="ビリヤード"><label for="q1_html31">ビリヤード</label><br>
									<input type="checkbox" id="q1_html32" name="your_hobby[]" value="登山"><label for="q1_html32">登山</label><br>
									<input type="checkbox" id="q1_html33" name="your_hobby[]" value="スキューバダイビング"><label for="q1_html33">スキューバダイビング</label><br>
									<input type="checkbox" id="q1_html34" name="your_hobby[]" value="旅行"><label for="q1_html34">旅行</label><br>
									<input type="checkbox" id="q1_html35" name="your_hobby[]" value="ランニング"><label for="q1_html35">ランニング</label><br>
									<input type="checkbox" id="q1_html36" name="your_hobby[]" value="筋トレ"><label for="q1_html36">筋トレ</label><br>
									<input type="checkbox" id="q1_html37" name="your_hobby[]" value="絵を書くのが好き"><label for="q1_html37">絵</label><br>
									<input type="checkbox" id="q1_html38" name="your_hobby[]" value="映画観賞"><label for="q1_html38">映画観賞</label><br>
									<input type="checkbox" id="q1_html39" name="your_hobby[]" value="ドッジボール"><label for="q1_html39">ドッジボール</label><br>
									<input type="checkbox" id="q1_html40" name="your_hobby[]" value="格闘技"><label for="q1_html40">格闘技</label><br>
									<input type="checkbox" id="q1_html41" name="your_hobby[]" value="ライブ"><label for="q1_html41">ライブ</label><br>
									<input type="checkbox" id="q1_html42" name="your_hobby[]" value="アイドル"><label for="q1_html42">アイドル</label><br>
									<input type="checkbox" id="q1_html43" name="your_hobby[]" value="バンド"><label for="q1_html43">バンド</label><br>
									<input type="checkbox" id="q1_html44" name="your_hobby[]" value="パンクロック"><label for="q1_html44">パンク</label><br>
									<input type="checkbox" id="q1_html45" name="your_hobby[]" value="ロック"><label for="q1_html45">ロック</label><br>
									<input type="checkbox" id="q1_html46" name="your_hobby[]" value="V系" ><label for="q1_html46">V系</label><br>
									<input type="checkbox" id="q1_html47" name="your_hobby[]" value="洋楽"><label for="q1_html47">洋楽</label><br>
									<input type="checkbox" id="q1_html48" name="your_hobby[]" value="EDM"><label for="q1_html48">EDM</label><br>
									<input type="checkbox" id="q1_html49" name="your_hobby[]" value="J-POP"><label for="q1_html49">J-POP</label><br>
									<input type="checkbox" id="q1_html50" name="your_hobby[]" value="K-POP"><label for="q1_html50">K-POP</label><br>
									<input type="checkbox" id="q1_html51" name="your_hobby[]" value="アニメ好き"><label for="q1_html51">アニメ</label><br>
									<input type="checkbox" id="q1_html52" name="your_hobby[]" value=”ゲーム”><label for="q1_html52">ゲーム</label><br>
								</label>


								<div class="checkbox3">あなたの性格を教えてください</div>
								<label>
									<input type="checkbox" id="q1_html53" name="your_personality[]" value="ポジティブ"><label for="q1_html53">ポジティブ</label><br>
									<input type="checkbox" id="q1_html54" name="your_personality[]" value="ネガティブ"><label for="q1_html54">ネガティブ</label><br>
									<input type="checkbox" id="q1_html55" name="your_personality[]" value="向上心が高い"><label for="q1_html55">向上心が高い</label><br>
									<input type="checkbox" id="q1_html56" name="your_personality[]" value="おっとりした性格"><label for="q1_html56">おっとりした性格</label><br>
									<input type="checkbox" id="q1_html57" name="your_personality[]" value="優しい"><label for="q1_html57">優しい</label><br>
									<input type="checkbox" id="q1_html58" name="your_personality[]" value="キレイ好き"><label for="q1_html58">キレイ好き</label><br>
									<input type="checkbox" id="q1_html59" name="your_personality[]" value="連絡マメ"><label for="q1_html59">連絡マメ</label><br>
									<input type="checkbox" id="q1_html60" name="your_personality[]" value="明るい性格だと思う"><label for="q1_html60">明るい性格だと思う</label><br>
									<input type="checkbox" id="q1_html61" name="your_personality[]" value="喋るのは苦手"><label for="q1_html61">喋るのは苦手</label><br>
									<input type="checkbox" id="q1_html62" name="your_personality[]" value="メンヘラです..."><label for="q1_html62">メンヘラです...</label><br>
									<input type="checkbox" id="q1_html63" name="your_personality[]" value="お豆腐メンタル"><label for="q1_html63">お豆腐メンタル</label><br>
									<input type="checkbox" id="q1_html64" name="your_personality[]" value="リードしてほしいタイプ"><label for="q1_html64">リードしてほしいタイプ</label><br>
									<input type="checkbox" id="q1_html65" name="your_personality[]" value="リードしたいタイプ"><label for="q1_html65">リードしたいタイプ</label><br>
									<input type="checkbox" id="q1_html66" name="your_personality[]" value="Sです"><label for="q1_html66">Sです</label><br>
									<input type="checkbox" id="q1_html67" name="your_personality[]" value="Mです"><label for="q1_html67">Mです</label><br>
									<input type="checkbox" id="q1_html68" name="your_personality[]" value="恐がり"><label for="q1_html68">恐がり</label><br>
									<input type="checkbox" id="q1_html69" name="your_personality[]" value="好奇心旺盛"><label for="q1_html69">好奇心旺盛</label><br>
									<input type="checkbox" id="q1_html70" name="your_personality[]" value="忘れっぽい"><label for="q1_html70">忘れっぽい</label><br>
									<input type="checkbox" id="q1_html71" name="your_personality[]" value="根に持たない"><label for="q1_html71">根に持たない</label><br>
									<input type="checkbox" id="q1_html72" name="your_personality[]" value="神経質"><label for="q1_html72">神経質</label><br>
									<input type="checkbox" id="q1_html73" name="your_personality[]" value="めんどくさがり"><label for="q1_html73">めんどくさがり</label><br>
									<input type="checkbox" id="q1_html74" name="your_personality[]" value="真面目"><label for="q1_html74">真面目</label><br>
									<input type="checkbox" id="q1_html75" name="your_personality[]" value="社交的"><label for="q1_html75">社交的</label><br>
									<input type="checkbox" id="q1_html76" name="your_personality[]" value="一人が好き"><label for="q1_html76">一人が好き</label><br>
								</label>
							</div>
							
							<!--自己紹介-->
							<div class="introduction">【自己紹介】</div>
							<textarea name ="text" class="textarea"></textarea>
								<input type="submit" value="送信" id="send" class="btn btn--orange" autofocus required>
									<script>
											function disp(){
												window.alert('訪問時のメッセージ');
											}
											function disp2(){
												window.alert('退去時のメッセージ');
											}
									</script>
							</div>
							<div id="closeModal" class="closeModal">×</div>
						</section>
					</form>
				</div>
			</div>
		</section>
		





		<main>
			<!--空いた時間に気軽に会える！-->
			<!--タイトル-->
			<h1 class="main-visual1-left__register-prompt">
				空いた時間で気軽に会える！<br>
				出会いの総合マッチングアプリ
			</h1>

			<div class="main-visual-content">
				<div class="main-visual-step">
					<ul>
						<li class="main-visual-step1">簡単3ステップ会員登録</li>
						<li class="main-visual-step2">条件検索で効率的に出会う</li>
						<li class="main-visual-step3">空き時間に合コン参加！</li>
						<li class="main-visual-step4">共通の趣味の仲間に出会う</li>
					</ul>
				</div>
			</div>



			<!--かんたん無料登録！モーダル部分-->
			<a name="link7"></a>
			<button id="openModal2">かんたん無料登録！</button>
			<section id="modalArea2" class="modalArea2">
				<div id="modalBg2" class="modalBg2"></div>
				<div class="modalWrapper2">
					<div class="modalContents2">
						<div class="modal-title2">【会員登録フォーム】</div>

						<form method="post" action="matching.php?action=user_insert" enctype="multipart/form-data">
							<section class="check-box">

								<!--プロフィール画像-->
								<div class="prof-image">プロフィール画像</div>
								<input type="file" name="main" class="image-file">
								
								<!--お名前-->
								<div class="name"><span>お名前</span>    (必須)</div>
								<input type="text" id="name" name="name" class="form-control" placeholder="例：山田　太郎" autofocus required>

								<!--フリガナ-->
								<div class="kana"><span>フリガナ</span>    (必須)</div>
								<input type="text" id="kana" name="kana" class="form-control" placeholder="例：ヤマダ　タロウ" autofocus required>　<!--requiredはエラーメッセージを表示してくれる-->

								<!--性別-->
								<div class="gender"><span>性別</span>    (必須)</div>
								<div class="gender-type">
									<input type="radio" name="gender" id="gender1" value="男性" autofocus required><label for="gender1">男性</label>
									<input type="radio" name="gender" id="gender2" value="女性" autofocus required><label for="gender2">女性</label>
									<input type="radio" name="gender" id="gender3" value="その他" autofocus required><label for="gender3">その他</label> <!--cheakedを付けると最後のチェックが反映される-->
									<br>
								
								</div>

								<!--生年月日-->
								<div class="age"><span>ご年齢</span>    (必須)</div>
								<select name="age">
									<option value="未選択" >選択してください</option>
									<?php
										for ($i=18; $i<=100;$i++) {
											echo "<option value='{$i}'>{$i}才</option>";
										}
									?>
								</select>

								<!--メールアドレス-->
								<div class="mail">メールアドレス<span>    （必須）</span></div>
								<input type="text" id="email" name="mail" class="form-control" placeholder="例：example@com" required>

								<!--電話番号-->
								<div class="tel-number">電話番号</div>
								<input type="text" id="tel" name="tel" autocomplete="tel" placeholder="例：080-1234-5678" required>

								<!--お住まいのエリア-->
								<div class="area-modal">お住まいのエリア<span>    (必須)</span></div>
								<select name="area">
									<option value="未選択" autofocus required>選択してください</option>
									<option value="北海道" autofocus required>北海道</option>
									<option value="青森"　autofocus required>青森</option>
									<option value="岩手"　autofocus required>岩手</option>
									<option value="宮城"　autofocus required>宮城</option>
									<option value="秋田"　autofocus required>秋田</option>
									<option value="山形" autofocus required>山形</option>
									<option value="福島" autofocus required>福島</option>
									<option value="茨城" autofocus required>茨城</option>
									<option value="栃木" autofocus required>栃木</option>
									<option value="群馬" autofocus required>群馬</option>
									<option value="埼玉" autofocus required>埼玉</option>
									<option value="千葉" autofocus required>千葉</option>
									<option value="東京" autofocus required>東京</option>
									<option value="神奈川" autofocus required>神奈川</option>
									<option value="新潟" autofocus required>新潟</option>
									<option value="富山" autofocus required>富山</option>
									<option value="石川" autofocus required>石川</option>
									<option value="福井" autofocus required>福井</option>
									<option value="山口" autofocus required>山口</option>
									<option value="長野" autofocus required>長野</option>
									<option value="岐阜" autofocus required>岐阜</option>
									<option value="静岡" autofocus required>静岡</option>
									<option value="愛知" autofocus required>愛知</option>
									<option value="三重" autofocus required>三重</option>
									<option value="滋賀" autofocus required>滋賀</option>
									<option value="京都" autofocus required>京都</option>
									<option value="大阪" autofocus required>大阪</option>
									<option value="兵庫" autofocus required>兵庫</option>
									<option value="奈良" autofocus required>奈良</option>
									<option value="和歌山" autofocus required>和歌山</option>
									<option value="鳥取" autofocus required>鳥取</option>
									<option value="島根" autofocus required>島根</option>
									<option value="岡山" autofocus required>岡山</option>
									<option value="広島" autofocus required>広島</option>
									<option value="山口" autofocus required>山口</option>
									<option value="徳島" autofocus required>徳島</option>
									<option value="香川" autofocus required>香川</option>
									<option value="愛媛" autofocus required>愛媛</option>
									<option value="高知" autofocus required>高知</option>
									<option value="福岡" autofocus required>福岡</option>
									<option value="佐賀" autofocus required>佐賀</option>
									<option value="長崎" autofocus required>長崎</option>
									<option value="熊本" autofocus required>熊本</option>
									<option value="大分" autofocus required>大分</option>
									<option value="宮崎" autofocus required>宮崎</option>
									<option value="鹿児島" autofocus required>鹿児島</option>
									<option value="沖縄" autofocus required>沖縄</option>
								</select>

								<!--お仕事-->
								<div class="job">お仕事</div>
								<select name="job">
									<option value="未選択" autofocus required>選択してください</option>
									<option value="事務" autofocus required>事務</option>
									<option value="教育" autofocus required>教育</option>
									<option value="不動産" autofocus required>不動産</option>
									<option value="サービス業" autofocus required>サービス業</option>
									<option value="飲食業界" autofocus required>飲食業界</option>
									<option value="運輸" autofocus required>運輸・物流</option>
									<option value="建設" autofocus required>建設</option>
									<option value="製造業" autofocus required>製造業</option>
									<option value="IT・エンジニア" autofocus required>IT・エンジニア</option>
									<option value="卸売業・小売業" autofocus required>卸売業・小売業</option>
									<option value="金融業" autofocus required>金融業</option>
									<option value="医療" autofocus required>医療</option>
									<option value="観光" autofocus required>観光</option>
									<option value="貿易" autofocus required>貿易</option>
									<option value="士業" autofocus required>士業</option>
									<option value="公務員" autofocus required>公務員</option>
									<option value="スポーツ" autofocus required>スポーツ</option>
									<option value="映像制作" autofocus required>映像制作</option>
									<option value="芸能" autofocus required>芸能</option>
									<option value="ETC" autofocus required>その他</option>
								</select>

								<!--出会いの目的-->
								<div class="form-group">
									<div class="title-purpose">出会いの目的</span>    (必須)</div>
									<select id="purpose" name="purpose" class="form-control">
										<option value="">選択してください</option>
										<option value="真剣な婚活がしたい" autofocus required>真剣な婚活がしたい</option>
										<option value="共通の趣味の友達が欲しい" autofocus required>共通の趣味の友達がほしい</option>
										<option value="気軽に会える友達が欲しい" autofocus required>気軽に会える友達が欲しい</option>
										<option value="未選択" autofocus required>未選択</option>
									</select>
								</div>

								<!---好みの第一印象を教えて下さい-->
								<div class="form-group">
									<div class="checkbox1">好みの第一印象を教えてください</div>
									<label>
										<input type="checkbox" id="q1_html1" name="your_like[]" value="清潔感"><label for="q1_html1">清潔感がある人が好き</lebel><br>
										<input type="checkbox" id="q1_html2" name="your_like[]" value="優しい"><label for="q1_html2">優しい人が好き</label><br>
										<input type="checkbox" id="q1_html3" name="your_like[]" value="大人しい"><label for="q1_html3">おとなしい人が好き</label><br>
										<input type="checkbox" id="q1_html4" name="your_like[]" value="明るい"><label for="q1_html4">明るい人が好き</label><br>
										<input type="checkbox" id="q1_html5" name="your_like[]" value="クール"><label for="q1_html5">クール系好き</label><br>
										<input type="checkbox" id="q1_html6" name="your_like[]" value="かわいい"><label for="q1_html6">かわいい系が好き</label><br>
										<input type="checkbox" id="q1_html7" name="your_like[]" value="キレイ"><label for="q1_html7">キレイ系が好き</label><br>
										<input type="checkbox" id="q1_html8" name="your_like[]" value="低身長"><label for="q1_html8">低身長が好き</label><br>
										<input type="checkbox" id="q1_html9" name="your_like[]" value="高身長"><label for="q1_html9">高身長が好き</label><br>
										<input type="checkbox" id="q1_html10" name="your_like[]" value="肉食系"><label for="q1_html10">肉食系が好き</label><br>
										<input type="checkbox" id="q1_html11" name="your_like[]" value="草食系"><label for="q1_html11">草食系が好き</label><br>
										<input type="checkbox" id="q1_html12" name="your_like[]" value="一重が好き"><label for="q1_html12">一重が好き</label><br>
										<input type="checkbox" id="q1_html13" name="your_like[]" value="二重が好き"><label for="q1_html13">二重が好き</label><br>
										<input type="checkbox" id="q1_html14" name="your_like[]" value="彫りが深い人が好き"><label for="q1_html14">彫りが深い人が好き</label><br>
										<input type="checkbox" id="q1_html15" name="your_like[]" value="彫りが浅い人が好き"><label for="q1_html15">彫りが浅い人が好き</label><br>
										<input type="checkbox" id="q1_html16" name="your_like[]" value="スリム体型が好き"><label for="q1_html16">スリム体型が好き</label><br>
										<input type="checkbox" id="q1_html17" name="your_like[]" value="筋肉質な人が好き"><label for="q1_html17">筋肉質な人が好き</label><br>
										<input type="checkbox" id="q1_html18" name="your_like[]" value="ぽっちゃり体型が好き"><label for="q1_html18">ぽっちゃり体型が好き</label><br>
										<input type="checkbox" id="q1_html19" name="your_like[]" value="おしゃれな人が好き"><label for="q1_html19">おしゃれな人が好き</label><br>
									</label>


									<div class="checkbox2">あなたの好きなものを教えてください</div>
									<label>
										<input type="checkbox" id="q1_html20" name="your_hobby[]" value="サッカー"><label for="q1_html20">サッカー</label><br>
										<input type="checkbox" id="q1_html21" name="your_hobby[]" value="野球"><label for="q1_html21">野球</label><br>
										<input type="checkbox" id="q1_html22" name="your_hobby[]" value="フットサル"><label for="q1_html22">フットサル</label><br>
										<input type="checkbox" id="q1_html23" name="your_hobby[]" value="テニス"><label for="q1_html23">テニス</label><br>
										<input type="checkbox" id="q1_html24" name="your_hobby[]" value="バドミントン"><label for="q1_html24">バドミントン</label><br>
										<input type="checkbox" id="q1_html25" name="your_hobby[]" value="ソフトボール"><label for="q1_html25">ソフトボール</label><br>
										<input type="checkbox" id="q1_html26" name="your_hobby[]" value="水泳"><label for="q1_html26">水泳</label><br>
										<input type="checkbox" id="q1_html27" name="your_hobby[]" value="フリスビー"><label for="q1_html27">フリスビー</label><br>
										<input type="checkbox" id="q1_html28" name="your_hobby[]" value="ラグビー"><label for="q1_html28">ラグビー</label><br>
										<input type="checkbox" id="q1_html29" name="your_hobby[]" value="卓球"><label for="q1_html29">卓球</label><br>
										<input type="checkbox" id="q1_html30" name="your_hobby[]" value="ダーツ"><label for="q1_html30">ダーツ</label><br>
										<input type="checkbox" id="q1_html31" name="your_hobby[]" value="ビリヤード"><label for="q1_html31">ビリヤード</label><br>
										<input type="checkbox" id="q1_html32" name="your_hobby[]" value="登山"><label for="q1_html32">登山</label><br>
										<input type="checkbox" id="q1_html33" name="your_hobby[]" value="スキューバダイビング"><label for="q1_html33">スキューバダイビング</label><br>
										<input type="checkbox" id="q1_html34" name="your_hobby[]" value="旅行"><label for="q1_html34">旅行</label><br>
										<input type="checkbox" id="q1_html35" name="your_hobby[]" value="ランニング"><label for="q1_html35">ランニング</label><br>
										<input type="checkbox" id="q1_html36" name="your_hobby[]" value="筋トレ"><label for="q1_html36">筋トレ</label><br>
										<input type="checkbox" id="q1_html37" name="your_hobby[]" value="絵を書くのが好き"><label for="q1_html37">絵</label><br>
										<input type="checkbox" id="q1_html38" name="your_hobby[]" value="映画観賞"><label for="q1_html38">映画観賞</label><br>
										<input type="checkbox" id="q1_html39" name="your_hobby[]" value="ドッジボール"><label for="q1_html39">ドッジボール</label><br>
										<input type="checkbox" id="q1_html40" name="your_hobby[]" value="格闘技"><label for="q1_html40">格闘技</label><br>
										<input type="checkbox" id="q1_html41" name="your_hobby[]" value="ライブ"><label for="q1_html41">ライブ</label><br>
										<input type="checkbox" id="q1_html42" name="your_hobby[]" value="アイドル"><label for="q1_html42">アイドル</label><br>
										<input type="checkbox" id="q1_html43" name="your_hobby[]" value="バンド"><label for="q1_html43">バンド</label><br>
										<input type="checkbox" id="q1_html44" name="your_hobby[]" value="パンクロック"><label for="q1_html44">パンク</label><br>
										<input type="checkbox" id="q1_html45" name="your_hobby[]" value="ロック"><label for="q1_html45">ロック</label><br>
										<input type="checkbox" id="q1_html46" name="your_hobby[]" value="V系" ><label for="q1_html46">V系</label><br>
										<input type="checkbox" id="q1_html47" name="your_hobby[]" value="洋楽"><label for="q1_html47">洋楽</label><br>
										<input type="checkbox" id="q1_html48" name="your_hobby[]" value="EDM"><label for="q1_html48">EDM</label><br>
										<input type="checkbox" id="q1_html49" name="your_hobby[]" value="J-POP"><label for="q1_html49">J-POP</label><br>
										<input type="checkbox" id="q1_html50" name="your_hobby[]" value="K-POP"><label for="q1_html50">K-POP</label><br>
										<input type="checkbox" id="q1_html51" name="your_hobby[]" value="アニメ好き"><label for="q1_html51">アニメ</label><br>
										<input type="checkbox" id="q1_html52" name="your_hobby[]" value=”ゲーム”><label for="q1_html52">ゲーム</label><br>
									</label>


									<div class="checkbox3">あなたの性格を教えてください</div>
									<label>
										<input type="checkbox" id="q1_html53" name="your_personality[]" value="ポジティブ"><label for="q1_html53">ポジティブ</label><br>
										<input type="checkbox" id="q1_html54" name="your_personality[]" value="ネガティブ"><label for="q1_html54">ネガティブ</label><br>
										<input type="checkbox" id="q1_html55" name="your_personality[]" value="向上心が高い"><label for="q1_html55">向上心が高い</label><br>
										<input type="checkbox" id="q1_html56" name="your_personality[]" value="おっとりした性格"><label for="q1_html56">おっとりした性格</label><br>
										<input type="checkbox" id="q1_html57" name="your_personality[]" value="優しい"><label for="q1_html57">優しい</label><br>
										<input type="checkbox" id="q1_html58" name="your_personality[]" value="キレイ好き"><label for="q1_html58">キレイ好き</label><br>
										<input type="checkbox" id="q1_html59" name="your_personality[]" value="連絡マメ"><label for="q1_html59">連絡マメ</label><br>
										<input type="checkbox" id="q1_html60" name="your_personality[]" value="明るい性格だと思う"><label for="q1_html60">明るい性格だと思う</label><br>
										<input type="checkbox" id="q1_html61" name="your_personality[]" value="喋るのは苦手"><label for="q1_html61">喋るのは苦手</label><br>
										<input type="checkbox" id="q1_html62" name="your_personality[]" value="メンヘラです..."><label for="q1_html62">メンヘラです...</label><br>
										<input type="checkbox" id="q1_html63" name="your_personality[]" value="お豆腐メンタル"><label for="q1_html63">お豆腐メンタル</label><br>
										<input type="checkbox" id="q1_html64" name="your_personality[]" value="リードしてほしいタイプ"><label for="q1_html64">リードしてほしいタイプ</label><br>
										<input type="checkbox" id="q1_html65" name="your_personality[]" value="リードしたいタイプ"><label for="q1_html65">リードしたいタイプ</label><br>
										<input type="checkbox" id="q1_html66" name="your_personality[]" value="Sです"><label for="q1_html66">Sです</label><br>
										<input type="checkbox" id="q1_html67" name="your_personality[]" value="Mです"><label for="q1_html67">Mです</label><br>
										<input type="checkbox" id="q1_html68" name="your_personality[]" value="恐がり"><label for="q1_html68">恐がり</label><br>
										<input type="checkbox" id="q1_html69" name="your_personality[]" value="好奇心旺盛"><label for="q1_html69">好奇心旺盛</label><br>
										<input type="checkbox" id="q1_html70" name="your_personality[]" value="忘れっぽい"><label for="q1_html70">忘れっぽい</label><br>
										<input type="checkbox" id="q1_html71" name="your_personality[]" value="根に持たない"><label for="q1_html71">根に持たない</label><br>
										<input type="checkbox" id="q1_html72" name="your_personality[]" value="神経質"><label for="q1_html72">神経質</label><br>
										<input type="checkbox" id="q1_html73" name="your_personality[]" value="めんどくさがり"><label for="q1_html73">めんどくさがり</label><br>
										<input type="checkbox" id="q1_html74" name="your_personality[]" value="真面目"><label for="q1_html74">真面目</label><br>
										<input type="checkbox" id="q1_html75" name="your_personality[]" value="社交的"><label for="q1_html75">社交的</label><br>
										<input type="checkbox" id="q1_html76" name="your_personality[]" value="一人が好き"><label for="q1_html76">一人が好き</label><br>
									</label>
								</div>

								<!--自己紹介-->
								<div class="introduction">【自己紹介】</div>
								<textarea name ="text" class="textarea"></textarea>
								<input type="submit" value="送信" id="send" class="btn btn--orange" autofocus required>
								<script>
										function disp(){
											window.alert('訪問時のメッセージ');
										}
										function disp2(){
											window.alert('退去時のメッセージ');
										}
								</script>
								<div id="closeModal2" class="closeModal2">×</div>
							</section>
						</form>
					</div>
				</div>
			</section>





				
			<!--ユーザー様からのレビュー-->
			<a name="link4"></a>
			<section class="top-Happy">
				<h1 class="main-visual7-left__register-prompt" >
					数多くのユーザー様から<br>
					たくさんの感謝を頂きました！
				</h1>

				<div class="top-Happy_Inner">
					<div class="top-Happy_Headline"></div>
					<div class="top-happy-content">
						<div class="top-Happy_Reports">

							<!--レビュー1-->
							<section class="hr-AllReports_Item">
								<img width="330" height="191" src="beach.jpg" class="hr-AllReports_Thumbnail wp-post-image" alt="仲良しカップルビーチ">
								<h2 class="hr-AllReports_Info">
									<span class="hr-AllReports_Info">付き合うことができた！</span>
								</h2>
								<p class="hr-AllReports_Title">この出逢いを一生大切にしていこうと思います</p>
								<p class="hr-AllReports_Additional">
									<span class="text__date">2018年5月10日</span>
									<span class="text__name"> Nさん 31歳 大阪（女性）</span>
								</p>
							</section>

							<!--レビュー2-->
							<section class="hr-AllReports_Item">
								<img width="330" height="191" src="game.jpg" class="hr-AllReports_Thumbnail wp-post-image" alt="" >
								<h2 class="hr-AllReports_Info">
									<span class="hr-AllReports_Info">共通の趣味</span>
								</h2>
								<p class="hr-AllReports_Title">婚活アプリで初めて会った人が最後の人になりました!</p>
								<p class="hr-AllReports_Additional">
									<span class="text__date">2020年7月29日</span>
									<span class="text__name"> Yさん 31歳 東京（女性）</span>
								</p>
							</section>

							<!--レビュー3-->
							<section class="hr-AllReports_Item">
								<img width="330" height="191" src="sakewoman.jpg" class="hr-AllReports_Thumbnail wp-post-image" alt=”酒を飲む女２人”>
								<h2 class="hr-AllReports_Info">
									<span class="hr-AllReports_Info">飲み友達ができました！</span>
								</h2>
								<p class="hr-AllReports_Title">食べ物の好みが一致した2人の餃子デート！</p>
								<p class="hr-AllReports_Additional">
									<span class="text__date">2019年9月5日</span>
									<span class="text__name"> 28歳男性・29歳女性 東京</span>
								</p>
							</section>

							<!--レビュー4-->
							<section class="hr-AllReports_Item">
								<img width="330" height="191" src="sofa.jpg" class="hr-AllReports_Thumbnail wp-post-image" alt=”酒を飲む女２人”>
								<h2 class="hr-AllReports_Info">
									<span class="hr-AllReports_Info">結婚しました！</span>
								</h2>
								<p class="hr-AllReports_Title">食べ物の好みが一致した2人の餃子デート！</p>
								<p class="hr-AllReports_Additional">
									<span class="text__date">2019年9月5日</span>
									<span class="text__name"> 28歳男性・29歳女性 東京</span>
								</p>
							</section>


						</div>
					</div>
				</div>
			</section>






			<!--今すぐ登録して初めてみよう!-->
			<a name="link1"></a>
			<h1 class="main-visual3-left__register-prompt">
				今すぐ登録して始めよう！<br>登録は簡単３ステップ！</h1>

			<!---登録方法スクロール--->
			<section class="step-3content">
				<div class="area">
					<h1>ステップ１</h1>
					<h2>氏名、年齢、お住まいのエリア、パスワードなど<br>必要な情報を入力してください</h2>
					<img class="step-3content-img1" src="profile.png" alt="プロフィール入力画面">
				</div>


				<div class="area">
					<h1>ステップ2</h1>
					<h2>自分の好きなもの、苦手なものを</br>入力してプロフィールを完成させましょう</h2>
					<img class="step-3content-img2" src="prof.png" alt="プロフィール入力画面">
				</div>


				<div class="area">
					<h1>ステップ3</h1>
					<h2>ご本人確認ができる身分証をご提出下さい<br>※顔写真の入っている身分証</h2>
					<img class="step-3content-img3"  src="driveID.png" alt="免許証">
				</div>
			</section>
				






			<!--アプリご利用の流れ-->
			<section class="main-visual">
				<h1 class="main-visual4-left__register-prompt">スピードマッチング<br>〜アプリご利用の流れ〜</br></h1>
				<div class="main-visual-wrap2"><a name="link2"></a></div>
				<div class="step-3content2">
					<section class="area-2">
						<h1>コミュニティ機能</h1>
						<h2>相手の好きななもの、苦手なものがすぐ分かる！<br>相性の良いお相手を効率的に探すことができます</h2>
						<img class="step-3content2-img1" src="sarch.png" alt="条件検索">
					</section>

					<section class="area-2">
						<h1><em>いいね！</em>を送ろう</h1>
						<h2>気になるお相手へ<em>いいね！を送ります<em><br>「いいね！」</em>を送りましょう。</h2>
						<img class="step-3content2-img2" src="like-woman.png" alt="いいねを送る女性">
					</section>

					<section class="area-3">
						<h1>マッチング成功</h1>
						<h2>お相手から<em>「いいね！ありがとう」</em>が送られてきたらマッチング成功<br>その後、メッセージの交換ができるようになります。</h2>
						<img class="step-3content2-img3" src="matching.png" alt="いいねを送る女性">
					</section>
				</div>
			</section>








			<!--ログインモーダル-->
			<a name="link6"></a>
			<div class="login-section">
				<button id="openModal3">ログインする</button>
			</div>

			<section id="modalArea3" class="modalArea3">
				<div id="modalBg3" class="modalBg3"></div>
				<div class="modalWrapper3">
					<div class="modalContents3">
						<div class="modal-title3"></div>
					
						<form method="post" name="mail_form" action="matching.php?action=user_login">
							<!--メールアドレス-->
							<div class="login-mail">ご登録のメールアドレスを入力してください</div>
							<input type="email" id="email" name="mail" class="form-control3" placeholder="例：example@com" required><!--reqiredは値が間違っていた時にエラーメッセージを表示してくれる-->
							<input type="submit" value="送信" name="login" class="btn btn--orange3">
						</form>
						<div id="closeModal3" class="closeModal3">×</div>
					</div>
				</div>
			</section>









			<!--プライバシー-->
			<section>
				<h1 class="main-visual6-left__register-prompt">婚活マッチングは安心・安全！<br>個人情報をしっかり守ります</h1>
				<a name="link3"></a>
				<div class="main-visual">
					<div class="main-visual-wrap">
						<div class="section-security">
							<ul>
								<li><span>✓実名</span>は表示されません</li>
								<li><span>✓</span><span>不正ユーザー</span>を徹底防止！</li>
								<li><span>✓未成年</span>の方はご利用できません</li>
								<li><span>✓個人情報セキュリティ</span>も安心！</li>
								<li><span>✓</span>SNSには一切<span>投稿されません</span></li>
								<li><span>✓感染症防止対策</span>にご協力下さい</li>
							</ul>
						</div>
					</div>
				</div>
			</section>










			<!--お問い合わせ-->
			<section>
				<div class="main-visual8">
					<a name="link5"></a>
					<h1 class="main-visual8-left__register-prompt">
						皆さまのご意見を<br>お聞かせください！
					</h1>
				</div>
			
				<div class="main2">
					<div class="contact-form">
						<!--入力した名前,年齢などををサーバーへ送る記述-->
						<div class="form-title">お問い合わせ</div>
						<form method="post" action="matching.php?action=send">

							<!--お名前-->
							<div class="form-item">名前</div>
							<input type="text" name="name">
							
							<!--年齢入力-->
							<div class="form-item">年齢</div>
							<select name="age">
								<option value="未選択">選択してください</option>
								<?php
									for ($i=18; $i<=100;$i++) {
										echo "<option value='{$i}'>{$i}</option>";
									}
								?>
							</select>

							<!--お問い合わせの種類-->
							<div class="form-item">お問い合わせの種類</div>
							<?php
								$type = array('ご料金について','アプリの使用方法について','マッチングしたお相手について','プライバシーについて','登録や退会について','その他');
							?>
							
							<!--選択-->
							<select name="type">
								<option value="未選択">選択してください</option>
								<?php
									foreach($type as $types)
									{
										echo"<option value='{$types}'>{$types}</option>";
									}
								?>
							</select>

							<!--内容-->
							<div class="form-item">内容</div>
							<textarea name ="text"></textarea>
							<input type="submit" value="送信">
							<?php 
								echo $error_name;
								echo $error_body; 
								echo $error_succes;
							?>
						</form>
					</div>
				</div>
			</section>
		</main>





		<!--フッター部分-->
		<footer>
			<section class="FooterSection">
				<div class="Footer">
					<div class="Footer-Inner">
						<div class="Footer-Inner-List">
							<a href="matching.php" class="Footer-Inner-List-Item">上へ戻る</a>
							<a href="matching.php#link1" class="Footer-Inner-List-Item">アプリのご登録方法</a>
							<a href="matching.php#link2" class="Footer-Inner-List-Item">ご利用方法</a>
							<a href="matching.php#kink3" class="Footer-Inner-List-Item">プライバシーについて</a>
						</div>
						<div class="Footer-Inner-CopyRight">
							©2020 婚活マッチング制作
						</div>
					</div>
				</div>
			</section>
		</footer>


	</body>
</html>