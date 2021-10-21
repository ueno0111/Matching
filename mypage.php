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
	<link rel="stylesheet" href="mypage.css">
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


		<!--------------------------お相手を検索する---------------------------------------->
		<a name="link6"></a>
		<section class="edit">
					<form action="history.php" method="post">
						<input type="submit" value="閲覧履歴" class="btn2">
					</form>

					<form action="like.php" method="post">
					<input type="submit" value="いいね！したお相手" class="btn2">
					</form>

					<button id="openModal">お相手を検索する</button>

			<section id="modalArea" class="modalArea">
			<div id="modalBg" class="modalBg"></div>
			<div class="modalWrapper">
				<div class="modalContents">
					<div class="modal-title">【お相手を検索】</div>

					<form method="post" action="search.php">
						<section class="check-box">

							<div class="gender">お相手の性別</div>
								<div class="gender-type" class="form-control">
									<input type="radio" name="gender" value="男性" id="gender1" required><label for="gender1">男性</label>
									<input type="radio" name="gender" value="女性" id="gender2" required><label for="gender2">女性</label>
									<input type="radio" name="gender" value="その他" id="gender3" required><label for="gender3">その他</label>
								</div>


							<div class="age">お相手のご年齢</div>
							<select name="age" class="form-control">
								<option value="未選択">選択してください</option>
									<?php
									for ($i=18; $i<=100;$i++) {
									echo "<option value='{$i}'>{$i}</option>";
									}
									?>
							</select>


							<div class="area">お住まいのエリア</span></div>
							<select name="area" class="form-control">
								<option value="">選択してください</option>
								<option value="北海道">北海道</option>
								<option value="青森">青森</option>
								<option value="岩手">岩手県</option>
								<option value="宮城">宮城</option>
								<option value="秋田">秋田</option>
								<option value="山形">山形</option>
								<option value="福島">福島</option>
								<option value="茨城">茨城</option>
								<option value="栃木">栃木</option>
								<option value="群馬">群馬</option>
								<option value="埼玉">埼玉</option>
								<option value="千葉">千葉</option>
								<option value="東京">東京</option>
								<option value="神奈川">神奈川</option>
								<option value="新潟">新潟</option>
								<option value="富山">富山</option>
								<option value="石川">石川</option>
								<option value="福井">福井</option>
								<option value="山口">山口</option>
								<option value="長野">長野</option>
								<option value="岐阜">岐阜</option>
								<option value="静岡">静岡</option>
								<option value="愛知">愛知</option>
								<option value="三重">三重</option>
								<option value="滋賀">滋賀</option>
								<option value="京都">京都</option>
								<option value="大阪">大阪</option>
								<option value="兵庫">兵庫</option>
								<option value="奈良">奈良</option>
								<option value="和歌山">和歌山</option>
								<option value="鳥取">鳥取</option>
								<option value="島根">島根</option>
								<option value="岡山">岡山</option>
								<option value="広島">広島</option>
								<option value="山口">山口</option>
								<option value="徳島">徳島</option>
								<option value="香川">香川</option>
								<option value="愛媛">愛媛</option>
								<option value="高知">高知</option>
								<option value="福岡">福岡</option>
								<option value="佐賀">佐賀</option>
								<option value="長崎">長崎</option>
								<option value="熊本">熊本</option>
								<option value="大分">大分</option>
								<option value="宮崎">宮崎</option>
								<option value="鹿児島">鹿児島</option>
								<option value="沖縄">沖縄</option>
							</select>

							<div class="job">お仕事</div>
							<select name="job" class="form-control">
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

							<div class="form-group">
								<div class="title-purpose">出会いの目的</div>
								<select id="purpose" name="purpose" class="form-control">
									<option value="">選択してください</option>
									<option value="真剣な婚活がしたい">真剣な婚活がしたい</option>
									<option value="共通の趣味の友達が欲しい">共通の趣味の友達がほしい</option>
									<option value="気軽に会える友達が欲しい">気軽に会える友達が欲しい</option>
									<option value="未選択">未選択</option>
								</select>
								
							</div>


							<div class="form-group">
								<div class="checkbox1">お相手の第一印象</div>
								<div class="your-like">
									<input type="checkbox" id="q1_html1" name="your_like[]" value="清潔感"><label for="q1_html1">清潔感がある人が好き<br></label>
									<input type="checkbox" id="q1_html2" name="your_like[]" value="優しい"><label for="q1_html2">優しい人が好き<br></label>
									<input type="checkbox" id="q1_html3" name="your_like[]" value="大人しい"><label for="q1_html3">おとなしい人が好き<br></label>
									<input type="checkbox" id="q1_html4" name="your_like[]" value="明るい"><label for="q1_html4">明るい人が好き<br></label>
									<input type="checkbox" id="q1_html5" name="your_like[]" value="クール"><label for="q1_html5">クール系好き<br></label>
									<input type="checkbox" id="q1_html6" name="your_like[]" value="かわいい"><label for="q1_html6">かわいい系が好き<br></label>
									<input type="checkbox" id="q1_html7" name="your_like[]" value="キレイ"><label for="q1_html7">キレイ系が好き<br></label>
									<input type="checkbox" id="q1_html8" name="your_like[]" value="低身長"><label for="q1_html8">低身長が好き<br></label>
									<input type="checkbox" id="q1_html9" name="your_like[]" value="高身長"><label for="q1_html9">高身長が好き<br></label>
									<input type="checkbox" id="q1_html10" name="your_like[]" value="肉食系"><label for="q1_html10">肉食系が好き<br></label>
									<input type="checkbox" id="q1_html11" name="your_like[]" value="草食系"><label for="q1_html11">草食系が好き<br></label>
									<input type="checkbox" id="q1_html12" name="your_like[]" value="一重が好き"><label for="q1_html12">一重が好き<br></label>
									<input type="checkbox" id="q1_html13" name="your_like[]" value="二重が好き"><label for="q1_html13">二重が好き<br></label>
									<input type="checkbox" id="q1_html14" name="your_like[]" value="彫りが深い人が好き"><label for="q1_html14">彫りが深い人が好き<br></label>
									<input type="checkbox" id="q1_html15" name="your_like[]" value="彫りが浅い人が好き"><label for="q1_html15">彫りが浅い人が好き<br></label>
									<input type="checkbox" id="q1_html16" name="your_like[]" value="スリム体型が好き"><label for="q1_html16">スリム体型が好き<br></label>
									<input type="checkbox" id="q1_html17" name="your_like[]" value="筋肉質な人が好き"><label for="q1_html17">筋肉質な人が好き<br></label>
									<input type="checkbox" id="q1_html18" name="your_like[]" value="ぽっちゃり体型が好き"><label for="q1_html18">ぽっちゃり体型が好き<br></label>
									<input type="checkbox" id="q1_html19" name="your_like[]" value="おしゃれな人が好き"><label for="q1_html19">おしゃれな人が好き<br></label>
							</div>


							<div class="checkbox2">お相手の好きなもの</div>
							<div class="your-hobby">
								<input type="checkbox" id="q1_html20" name="your_hobby[]" value="サッカー"><label for="q1_html20">サッカー<br></label>
								<input type="checkbox" id="q1_html21" name="your_hobby[]" value="野球"><label for="q1_html21">野球<br></label>
								<input type="checkbox" id="q1_html22" name="your_hobby[]" value="フットサル"><label for="q1_html22">フットサル<br></label>
								<input type="checkbox" id="q1_html23" name="your_hobby[]" value="テニス"><label for="q1_html23">テニス<br></label>
								<input type="checkbox" id="q1_html24" name="your_hobby[]" value="バドミントン"><label for="q1_html24">バドミントン<br></label>
								<input type="checkbox" id="q1_html25" name="your_hobby[]" value="ソフトボール"><label for="q1_html25">ソフトボール<br></label>
								<input type="checkbox" id="q1_html26" name="your_hobby[]" value="水泳"><label for="q1_html26">水泳<br></label>
								<input type="checkbox" id="q1_html27" name="your_hobby[]" value="フリスビー"><label for="q1_html27">フリスビー<br></label>
								<input type="checkbox" id="q1_html28" name="your_hobby[]" value="ラグビー"><label for="q1_html28">ラグビー<br></label>
								<input type="checkbox" id="q1_html29" name="your_hobby[]" value="卓球"><label for="q1_html29">卓球<br></label>
								<input type="checkbox" id="q1_html30" name="your_hobby[]" value="ダーツ"><label for="q1_html30">ダーツ<br></label>
								<input type="checkbox" id="q1_html31" name="your_hobby[]" value="ビリヤード"><label for="q1_html31">ビリヤード<br></label>
								<input type="checkbox" id="q1_html32" name="your_hobby[]" value="登山"><label for="q1_html32">登山<br></label>
								<input type="checkbox" id="q1_html33" name="your_hobby[]" value="スキューバダイビング"><label for="q1_html33">スキューバダイビング<br></label>
								<input type="checkbox" id="q1_html34" name="your_hobby[]" value="旅行"><label for="q1_html34">旅行<br></label>
								<input type="checkbox" id="q1_html35" name="your_hobby[]" value="ランニング"><label for="q1_html35">ランニング<br></label>
								<input type="checkbox" id="q1_html36" name="your_hobby[]" value="筋トレ"><label for="q1_html36">筋トレ<br></label>
								<input type="checkbox" id="q1_html37" name="your_hobby[]" value="絵を書くのが好き"><label for="q1_html37">絵<br></label>
								<input type="checkbox" id="q1_html38" name="your_hobby[]" value="映画観賞"><label for="q1_html38">映画観賞<br></label>
								<input type="checkbox" id="q1_html39" name="your_hobby[]" value="ドッジボール"><label for="q1_html39">ドッジボール<br></label>
								<input type="checkbox" id="q1_html40" name="your_hobby[]" value="格闘技"><label for="q1_html40">格闘技<br></label>
								<input type="checkbox" id="q1_html41" name="your_hobby[]" value="ライブ"><label for="q1_html41">ライブ<br></label>
								<input type="checkbox" id="q1_html42" name="your_hobby[]" value="アイドル"><label for="q1_html42">アイドル<br></label>
								<input type="checkbox" id="q1_html43" name="your_hobby[]" value="バンド"><label for="q1_html43">バンド<br></label>
								<input type="checkbox" id="q1_html44" name="your_hobby[]" value="パンク"><label for="q1_html44">パンク<br></label>
								<input type="checkbox" id="q1_html45" name="your_hobby[]" value="ロック"><label for="q1_html45">ロック<br></label>
								<input type="checkbox" id="q1_html46" name="your_hobby[]" value="V系"><label for="q1_html46">V系<br></label>
								<input type="checkbox" id="q1_html47" name="your_hobby[]" value="洋楽"><label for="q1_html47">洋楽<br></label>
								<input type="checkbox" id="q1_html48" name="your_hobby[]" value="EDM"><label for="q1_html48">EDM<br></label>
								<input type="checkbox" id="q1_html49" name="your_hobby[]" value="J-POP"><label for="q1_html49">J-POP<br></label>
								<input type="checkbox" id="q1_html50" name="your_hobby[]" value="K-POP"><label for="q1_html50">K-POP<br></label>
								<input type="checkbox" id="q1_html51" name="your_hobby[]" value="アニメ"><label for="q1_html51">アニメ<br></label>
								<input type="checkbox" id="q1_html52" name="your_hobby[]" value=”ゲーム”><label for="q1_html52">ゲーム<br></label>
								<input type="checkbox" id="q1_html53" name="your_hobby[]" value="オタク"><label for="q1_html53">オタク<br></label>
								<input type="checkbox" id="q1_html54" name="your_hobby[]" value="アイドル"><label for="q1_html54">アイドル<br></label>
								<input type="checkbox" id="q1_html55" name="your_hobby[]" value="映画"><label for="q1_html55">映画<br></label>
								<input type="checkbox" id="q1_html56" name="your_hobby[]" value="麻雀"><label for="q1_html56">麻雀<br></label>
								<input type="checkbox" id="q1_html57" name="your_hobby[]" value="ギャンブル"><label for="q1_html57">ギャンブル<br></label>
							</div>


							<div class="checkbox3">お相手の性格</div>
							<div class="your-personality">
								<input type="checkbox" id="q1_html58" name="your_personality[]" value="ポジティブ"><label for="q1_html58">ポジティブ<br></label>
								<input type="checkbox" id="q1_html59" name="your_personality[]" value="ネガティブ"><label for="q1_html59">ネガティブ<br></label>
								<input type="checkbox" id="q1_html60" name="your_personality[]" value="明るい性格"><label for="q1_html60">明るい性格だと思う<br></label>
								<input type="checkbox" id="q1_html61" name="your_personality[]" value="喋るのは苦手"><label for="q1_html61">喋るのが苦手<br></label>
								<input type="checkbox" id="q1_html62" name="your_personality[]" value="メンヘラです..."><label for="q1_html62">メンヘラです...<br></label>
								<input type="checkbox" id="q1_html63" name="your_personality[]" value="お豆腐メンタル"><label for="q1_html63">お豆腐メンタル<br></label>
								<input type="checkbox" id="q1_html64" name="your_personality[]" value="リードしてほしい"><label for="q1_html64">リードしてほしい<br></label>
								<input type="checkbox" id="q1_html65" name="your_personality[]" value="リードしたい"><label for="q1_html65">リードしたい<br></label>
								<input type="checkbox" id="q1_html66" name="your_personality[]" value="私はSです"><label for="q1_html66">私はSです<br></label>
								<input type="checkbox" id="q1_html67" name="your_personality[]" value="私はMです"><label for="q1_html67">私はMです<br></label>
								<input type="checkbox" id="q1_html68" name="your_personality[]" value="恐がりです"><label for="q1_html68">恐がりです<br></label>
								<input type="checkbox" id="q1_html69" name="your_personality[]" value="好奇心旺盛"><label for="q1_html69">好奇心旺盛<br></label>
								<input type="checkbox" id="q1_html70" name="your_personality[]" value="寝たらすぐ忘れる"><label for="q1_html70">寝たらすぐ忘れる<br></label>
								<input type="checkbox" id="q1_html71" name="your_personality[]" value="根に持たないタイプ"><label for="q1_html71">根に持たないタイプ<br></label>
								<input type="checkbox" id="q1_html72" name="your_personality[]" value="神経質"><label for="q1_html72">神経質<br></label>
								<input type="checkbox" id="q1_html73" name="your_personality[]" value="めんどくさがり"><label for="q1_html73">めんどくさがり<br></label>
								<input type="checkbox" id="q1_html74" name="your_personality[]" value="真面目"><label for="q1_html74">真面目<br></label>
								<input type="checkbox" id="q1_html75" name="your_personality[]" value="社交的"><label for="q1_html75">社交的<br></label>
								<input type="checkbox" id="q1_html76" name="your_personality[]" value="一人が好き"><label for="q1_html76">一人が好き<br></label>
							</div>

							<input type="submit" value="送信" class="btn btn--orange">

						</section>
							<div id="closeModal" class="closeModal">×</div>
					</form>
				</div>
			</div>
		</section>
	</div>
</header>
	<!-------------------------------------お相手えお検索するを終了--------------------------------------------->



	<!--------------------------------マイプロフィール（情報をPOSTで受け取っている画面）------------------------>
		<div class="header-title">
			<form method="post" action="matching.php?action=user_login">
			<div class="header-subtitle">マイページの確認</div>
		</div>
		</div>
               <div class="images">
				<img src="<?php echo $image[0]["file_name"]; ?>" alt="サンプル画像" class="img1">
                </div>
				
				<section class="check-box2">
								<div class="name2">【お名前】</div>
									<div class="name2-text"><?php echo $result[0]['name'];?></div>
						　　
								<div class="kana2">【フリガナ】</div>
									<div class="kana2-text"><?php echo $result[0]['kana']; ?></div>
											
								<div class="gender2">【性別】</div>
								<div class="gender-type2"><?php echo $result[0]['gender']; ?></div>
								
								<div class="age2">【ご年齢】</div>
								<div class="age2-text"><?php echo $result[0]['age'];?>才</div><!--selectedという文字列が入ってきたら選択状態になります。新規登録には必要ないが、更新にはデータを保持する場合に使う-->
								

								<div class="mail2">【メールアドレス】</div>
									<div class="mail2-text"><?php echo $result[0]['mail']; ?></div>

								<div class="tel2">【電話番号】</div>
								<div class="form-control2">
								<div class="tel-content">
			　　　　　　			   <?php echo $result[0]['tel']; ?>
			                    </div>
								</div>
								<br>
							
								<div class="area2">【お住まいのエリア】</div>
								<div class="form-control2">
									<?php echo $result[0]['area']; ?>
								</div>
								<br>
							
								<div class="job2">【お仕事】</div>
								<div class="form-control2">
									<?php echo $result[0]['job']; ?>
								</div>
								<br>
							
								<div class="form-group">
								<div class="title-purpose2">【出会いの目的】</div>
								<div class="form-control2">
									<?php echo $result[0]['purpose']; ?>
								</div>
									<br>
								</div>

								<div class="form-group">
									<div class="checkbox4">【好みの第一印象を教えてください】</div>
									<div class="your-like2">
									<?php echo $result[0]['your_like']; ?>
								</div>
								</div>

								<div class="checkbox5">【あなたの好きなものを教えてください】</div>
								<div class="your-hobby2">
									<?php echo $result[0]['your_hobby']; ?>
								</div>

								<div class="checkbox6">【あなたの性格を教えてください】</div>
								<div class="your-personality2">
									<?php echo $result[0]['your_personality']; ?>
								</div>

								　<div class="intro2">【自己紹介】</div>
				　　　　　<div class="textarea2"><?php echo $result[0]['text']; ?></div>
					</form>
					</div>
				</div>
			</div>
		</div>
		<div id="closeModal" class="closeModal">
		×
		</div>
	</section>


		<!----------------------------------------編集する部分------------------------------------------>
  <div class="edit2">
		<button id="openModal2">編集する</button>
	<section id="modalArea2" class="modalArea2">
		<div id="modalBg2" class="modalBg2"></div>
		<div class="modalWrapper2">
			<div class="modalContents2">
				<div class="modal-title2">【プロフィール更新】</div>
				<form method="post" action="mypage.php?action=user_update" enctype="multipart/form-data">

				<section class="check-box">

				<input type="file" name="main" class="prof-img3">

					<div class="name3"><span>お名前</span>    (必須)</div>
					<input type="text" id="name" name="name" class="form-control3" placeholder="例：山田　太郎" autofocus required value="<?php echo $result[0]['name']; ?>">
					<br>
				
					<div class="kana3"><span>フリガナ</span>    (必須)</div>
					<input type="text" id="kana" name="kana" class="form-control3" placeholder="例：ヤマダ　タロウ" autofocus required value="<?php echo $result[0]['kana']; ?>">
					<br>
				
					<div class="gender3"><span>性別</span>    (必須)</div>
					<div class="gender-type3">
						<input type="radio" name="gender" id="gender1" required value="男性" <?php if($result[0]['gender'] == "男性"){echo "checked";} ?>><label for="gender1">男性</label>
						<input type="radio" name="gender" id="gender2" required value="女性" <?php if($result[0]['gender'] == "女性"){echo "checked";} ?>><label for="gender2">女性</label>
						<input type="radio" name="gender" id="gender3" required value="その他" <?php if($result[0]['gender'] == "その他"){echo "checked";} ?>><label for="gender3">その他</label>
						<br>
					</div>

					<div class="age3"><span>ご年齢</span>    (必須)</div>
					<select name="age"class="form-control3">
						<option value="未選択">選択してください</option>
							<?php
						for ($i=18; $i<=100;$i++) {		
							$selected = "";

						if ($result[0]['age'] == $i) {
							$selected = "selected";
							}
							echo "<option value='{$i}' " . $selected . ">{$i}</option>"; 
						}//selectedという文字列が入ってきたら選択状態になります。新規登録には必要ないが、更新にはデータを保持する場合に使う
						?>
					</select>
					<br>

					<div class="mail3">メールアドレス</div>
					<input type="text" id="email" name="mail" class="form-control3" placeholder="例：example@.com" required value="<?php echo $result[0]['mail']; ?>">
					<br>

					<div class="tel3">電話番号</span>    (必須)</div>
					<input type="text" id="tel" name="tel" class="form-control3" autocomplete="tel" placeholder="例：080-1234-5678" required value="<?php echo $result[0]['tel']; ?>">
					<br>
				
					<div class="area3">お住まいのエリア<span>    (必須)</span></div>
					<select name="area" class="form-control3">
						<option value="">選択してください</option>
						<option value="hoxtukaido" <?php if($result[0]['area']=='北海道') {echo "selected";} ?> autofocus required>北海道</option>
						<option value="aomori" <?php if($result[0]['area']=='青森') {echo "selected";} ?> autofocus required>青森</option>
						<option value="iwate" <?php if($result[0]['area']=='岩手') {echo "selected";} ?> autofocus required>岩手</option>
						<option value="miyagi" <?php if($result[0]['area']=='宮城') {echo "selected";} ?> autofocus required>宮城</option>
						<option value="akita" <?php if($result[0]['area']=='秋田') {echo "selected";} ?> autofocus required>秋田</option>
						<option value="yamagata" <?php if($result[0]['area']=='山形') {echo "selected";} ?> autofocus required>山形</option>
						<option value="hukusima" <?php if($result[0]['area']=='福島') {echo "selected";} ?> autofocus required>福島</option>
						<option value="ibaraki" <?php if($result[0]['area']=='茨城') {echo "selected";} ?> autofocus required>茨城</option>
						<option value="totigi" <?php if($result[0]['area']=='栃木') {echo "selected";} ?> autofocus required>栃木</option>
						<option value="gunma" <?php if($result[0]['area']=='群馬') {echo "selected";} ?> autofocus required>群馬</option>
						<option value="saitama" <?php if($result[0]['area']=='埼玉') {echo "selected";} ?> autofocus required>埼玉</option>
						<option value="tiba" <?php if($result[0]['area']=='千葉') {echo "selected";} ?> autofocus required>千葉</option>
						<option value="tokyo" <?php if($result[0]['area']=='東京') {echo "selected";} ?> autofocus required>東京</option>
						<option value="kanagawa" <?php if($result[0]['area']=='神奈川') {echo "selected";} ?> autofocus required>神奈川</option>
						<option value="nigata" <?php if($result[0]['area']=='新潟') {echo "selected";} ?> autofocus required>新潟</option>
						<option value="toyama" <?php if($result[0]['area']=='富山') {echo "selected";} ?> autofocus required>富山</option>
						<option value="isikawa" <?php if($result[0]['area']=='石川') {echo "selected";} ?> autofocus required>石川</option>
						<option value="hukui" <?php if($result[0]['area']=='福井') {echo "selected";} ?> autofocus required>福井</option>
						<option value="yamanasi" <?php if($result[0]['area']=='山口') {echo "selected";} ?> autofocus required>山口</option>
						<option value="nagano" <?php if($result[0]['area']=='長野') {echo "selected";} ?> autofocus required>長野</option>
						<option value="gihu" <?php if($result[0]['area']=='岐阜') {echo "selected";} ?> autofocus required>岐阜</option>
						<option value="sizuoka" <?php if($result[0]['area']=='静岡') {echo "selected";} ?> autofocus required>静岡</option>
						<option value="aiti" <?php if($result[0]['area']=='愛知') {echo "selected";} ?> autofocus required>愛知</option>
						<option value="mie" <?php if($result[0]['area']=='三重') {echo "selected";} ?> autofocus required>三重</option>
						<option value="siga" <?php if($result[0]['area']=='滋賀') {echo "selected";} ?> autofocus required>滋賀</option>
						<option value="kyoto" <?php if($result[0]['area']=='京都') {echo "selected";} ?> autofocus required>京都</option>
						<option value="osaka" <?php if($result[0]['area']=='大阪') {echo "selected";} ?> autofocus required>大阪</option>
						<option value="hyougo" <?php if($result[0]['area']=='兵庫') {echo "selected";} ?> autofocus required>兵庫</option>
						<option value="nara" <?php if($result[0]['area']=='奈良') {echo "selected";} ?> autofocus required>奈良</option>
						<option value="wakayama" <?php if($result[0]['area']=='和歌山') {echo "selected";} ?> autofocus required>和歌山</option>
						<option value="toxtutori" <?php if($result[0]['area']=='鳥取') {echo "selected";} ?> autofocus required>鳥取</option>
						<option value="simane" <?php if($result[0]['area']=='島根') {echo "selected";} ?> autofocus required>島根</option>
						<option value="okayama" <?php if($result[0]['area']=='岡山') {echo "selected";} ?> autofocus required>岡山</option>
						<option value="hirosima" <?php if($result[0]['area']=='広島') {echo "selected";} ?> autofocus required>広島</option>
						<option value="yamaguti" <?php if($result[0]['area']=='山口') {echo "selected";} ?> autofocus required>山口</option>
						<option value="tokusima" <?php if($result[0]['area']=='徳島') {echo "selected";} ?> autofocus required>徳島</option>
						<option value="kagawa" <?php if($result[0]['area']=='香川') {echo "selected";} ?> autofocus required>香川</option>
						<option value="ehime" <?php if($result[0]['area']=='愛媛') {echo "selected";} ?> autofocus required>愛媛</option>
						<option value="kouti" <?php if($result[0]['area']=='高知') {echo "selected";} ?> autofocus required>高知</option>
						<option value="fukuoka" <?php if($result[0]['area']=='福岡') {echo "selected";} ?> autofocus required>福岡</option>
						<option value="saga" <?php if($result[0]['area']=='佐賀') {echo "selected";} ?> autofocus required>佐賀</option>
						<option value="nagasaki" <?php if($result[0]['area']=='長崎') {echo "selected";} ?> autofocus required>長崎</option>
						<option value="kumamoto" <?php if($result[0]['area']=='熊本') {echo "selected";} ?> autofocus required>熊本</option>
						<option value="oita" <?php if($result[0]['area']=='大分') {echo "selected";} ?> autofocus required>大分</option>
						<option value="miyazaki" <?php if($result[0]['area']=='宮崎') {echo "selected";} ?> autofocus required>宮崎</option>
						<option value="kagosima" <?php if($result[0]['area']=='鹿児島') {echo "selected";} ?> autofocus required>鹿児島</option>
						<option value="okinawa" <?php if($result[0]['area']=='沖縄') {echo "selected";} ?> autofocus required>沖縄</option>
					</select>
					<br>

					<div class="job3">お仕事</div>
					<select name="job" class="form-control3">
						<option value="">選択してください</option>
						<option value="事務" <?php if($result[0]['job']=='事務') {echo "selected";} ?> autofocus required>事務</option>
						<option value="教育" <?php if($result[0]['job']=='教育') {echo "selected";} ?> autofocus required>教育</option>
						<option value="不動産" <?php if($result[0]['job']=='不動産') {echo "selected";} ?> autofocus required>不動産</option>
						<option value="サービス業" <?php if($result[0]['job']=='サービス業') {echo "selected";} ?> autofocus required>サービス業</option>
						<option value="飲食業界" <?php if($result[0]['job']=='飲食業界') {echo "selected";} ?> autofocus required>飲食業界</option>
						<option value="運輸" <?php if($result[0]['job']=='運輸・物流') {echo "selected";} ?> autofocus required>運輸・物流</option>
						<option value="建設業" <?php if($result[0]['job']=='建設業') {echo "selected";} ?> autofocus required>建設業</option>
						<option value="製造業" <?php if($result[0]['job']=='製造業') {echo "selected";} ?> autofocus required>製造業</option>
						<option value="IT・エンジニア" <?php if($result[0]['job']=='IT・エンジニア') {echo "selected";} ?> autofocus required>IT・エンジニア</option>
						<option value="卸売業・小売業" <?php if($result[0]['job']=='卸売業・小売業') {echo "selected";} ?> autofocus required>卸売業・小売業</option>
						<option value="金融業" <?php if($result[0]['job']=='金融業') {echo "selected";} ?> autofocus required>金融業</option>
						<option value="医療" <?php if($result[0]['job']=='医療') {echo "selected";} ?> autofocus required>医療</option>
						<option value="観光" <?php if($result[0]['job']=='観光') {echo "selected";} ?> autofocus required>観光</option>
						<option value="貿易" <?php if($result[0]['job']=='貿易') {echo "selected";} ?> autofocus required>貿易</option>
						<option value="士業" <?php if($result[0]['job']=='士業') {echo "selected";} ?> autofocus required>士業</option>
						<option value="スポーツ" <?php if($result[0]['job']=='スポーツ') {echo "selected";} ?> autofocus required>スポーツ</option>
						<option value="公務員" <?php if($result[0]['job']=='公務員') {echo "selected";} ?> autofocus required>公務員</option>
						<option value="映像制作" <?php if($result[0]['job']=='映像制作') {echo "selected";} ?> autofocus required>映像制作</option>
						<option value="芸能" <?php if($result[0]['job']=='芸能') {echo "selected";} ?> autofocus required>芸能</option>
						<option value="ETC" <?php if($result[0]['job']=='その他') {echo "selected";} ?> autofocus required>その他</option>
					</select>
					<br>

					<div class="form-group2">
						<div class="title-purpose3">出会いの目的</span>    (必須)</div>
						<select id="purpose" name="purpose" class="form-control3">
							<option value="">選択してください</option>
							<option value="真剣な婚活がしたい" <?php if($result[0]['purpose']=='真剣な婚活がしたい') {echo "selected";} ?> autofocus required>真剣な婚活がしたい</option>
							<option value="共通の趣味の友達が欲しい" <?php if($result[0]['purpose']=='真剣な婚活がしたい') {echo "selected";} ?> autofocus required>共通の趣味の友達がほしい</option>
							<option value="気軽に会える友達が欲しい" <?php if($result[0]['purpose']=='真剣な婚活がしたい') {echo "selected";} ?> autofocus required>気軽に会える友達が欲しい</option>
							<option value="未選択" <?php if($result[0]['purpose']=='真剣な婚活がしたい') {echo "selected";} ?> autofocus required>未選択</option>
						</select>
						<br>
					</div>

					<div class="form-group2">
						<div class="checkbox7">好みの第一印象を教えてください</div>
						<label>
							<input type="checkbox" id="q1_html1" name="your_like[]" value="清潔感" <?php if(strpos($result[0]['your_like'],'清潔感') !== false){ echo "checked";} ?>><label for="q1_html1">清潔感がある人が好き<br></label><!--your_like中に対象のvalueが入ってた場合、チェックをつける-->
							<input type="checkbox" id="q1_html2" name="your_like[]" value="優しい" <?php if(strpos($result[0]['your_like'],'優しい') !== false){ echo "checked";} ?>><label for="q1_html2">優しい人が好き<br></label><!--全部つける-->
							<input type="checkbox" id="q1_html3" name="your_like[]" value="おとなしい" <?php if(strpos($result[0]['your_like'],'おとなしい') !== false){ echo "checked";} ?>><label for="q1_html3">おとなしい人が好き<br></label>
							<input type="checkbox" id="q1_html4" name="your_like[]" value="明るい" <?php if(strpos($result[0]['your_like'],'明るい') !== false){ echo "checked";} ?>><label for="q1_html4">明るい人が好き<br></label>
							<input type="checkbox" id="q1_html5" name="your_like[]" value="クール" <?php if(strpos($result[0]['your_like'],'クール') !== false){ echo "checked";} ?>><label for="q1_html5">クール系が好き<br></label>
							<input type="checkbox" id="q1_html6" name="your_like[]" value="かわいい" <?php if(strpos($result[0]['your_like'],'かわいい') !== false){ echo "checked";} ?>><label for="q1_html6">かわいい系が好き<br></label>
							<input type="checkbox" id="q1_html7" name="your_like[]" value="キレイ" <?php if(strpos($result[0]['your_like'],'キレイ') !== false){ echo "checked";} ?>><label for="q1_html7">キレイ系が好き<br></label>
							<input type="checkbox" id="q1_html8" name="your_like[]" value="低身長" <?php if(strpos($result[0]['your_like'],'低身長') !== false){ echo "checked";} ?>><label for="q1_html8">低身長が好き<br></label>
							<input type="checkbox" id="q1_html9" name="your_like[]" value="高身長" <?php if(strpos($result[0]['your_like'],'高身長') !== false){ echo "checked";} ?>><label for="q1_html9">高身長が好き<br></label>
							<input type="checkbox" id="q1_html10" name="your_like[]" value="肉食系" <?php if(strpos($result[0]['your_like'],'肉食系') !== false){ echo "checked";} ?>><label for="q1_html10">肉食系が好き<br></label>
							<input type="checkbox" id="q1_html11" name="your_like[]" value="草食系" <?php if(strpos($result[0]['your_like'],'草食系') !== false){ echo "checked";} ?>><label for="q1_html11">草食系が好き<br></label>
							<input type="checkbox" id="q1_html12" name="your_like[]" value="一重が好き" <?php if(strpos($result[0]['your_like'],'一重が好き') !== false){ echo "checked";} ?>><label for="q1_html12">一重が好き<br></label>
							<input type="checkbox" id="q1_html13" name="your_like[]" value="二重が好き" <?php if(strpos($result[0]['your_like'],'二重が好き') !== false){ echo "checked";} ?>><label for="q1_html13">二重が好き<br></label>
							<input type="checkbox" id="q1_html14" name="your_like[]" value="彫りが深い人が好き" <?php if(strpos($result[0]['your_like'],'彫りの深い人が好き') !== false){ echo "checked";} ?>><label for="q1_html14">彫りが深い人が好き<br></label>
							<input type="checkbox" id="q1_html15" name="your_like[]" value="彫りが浅い人が好き" <?php if(strpos($result[0]['your_like'],'彫りの浅い人が好き') !== false){ echo "checked";} ?>><label for="q1_html15">彫りが浅い人が好き<br></label>
							<input type="checkbox" id="q1_html16" name="your_like[]" value="スリム体型が好き" <?php if(strpos($result[0]['your_like'],'スリム体型が好き') !== false){ echo "checked";} ?>><label for="q1_html16">スリム体型が好き<br></label>
							<input type="checkbox" id="q1_html17" name="your_like[]" value="筋肉質な人が好き" <?php if(strpos($result[0]['your_like'],'筋肉質な人が好き') !== false){ echo "checked";} ?>><label for="q1_html17">筋肉質な人が好き<br></label>
							<input type="checkbox" id="q1_html18" name="your_like[]" value="ぽっちゃり体型が好き" <?php if(strpos($result[0]['your_like'],'ぽっちゃり体型が好き') !== false){ echo "checked";} ?>><label for="q1_html18">ぽっちゃり体型が好き<br></label>
							<input type="checkbox" id="q1_html19" name="your_like[]" value="おしゃれな人が好き" <?php if(strpos($result[0]['your_like'],'おしゃれな人が好き') !== false){ echo "checked";} ?>><label for="q1_html19">おしゃれな人が好き<br></label>
						</label>

						<div class="checkbox8">あなたの好きなものを教えてください</div>
						<label>
							<input type="checkbox" id="q1_html20" name="your_hobby[]" value="サッカー"<?php if(strpos($result[0]['your_hobby'],'サッカー') !== false){ echo "checked";} ?>><label for="q1_html20">サッカー<br></label>
							<input type="checkbox" id="q1_html21" name="your_hobby[]" value="野球"<?php if(strpos($result[0]['your_hobby'],'サッカー') !== false){ echo "checked";} ?>><label for="q1_html21">野球<br></label>
							<input type="checkbox" id="q1_html22" name="your_hobby[]" value="フットサル"<?php if(strpos($result[0]['your_hobby'],'フットサル') !== false){ echo "checked";} ?>><label for="q1_html22">フットサル<br></label>
							<input type="checkbox" id="q1_html23" name="your_hobby[]" value="テニス"<?php if(strpos($result[0]['your_hobby'],'テニス') !== false){ echo "checked";} ?>><label for="q1_html23">テニス<br></label>
							<input type="checkbox" id="q1_html24" name="your_hobby[]" value="バドミントン"<?php if(strpos($result[0]['your_hobby'],'バトミントン') !== false){ echo "checked";} ?>><label for="q1_html24">バドミントン<br></label>
							<input type="checkbox" id="q1_html25" name="your_hobby[]" value="ソフトボール"<?php if(strpos($result[0]['your_hobby'],'ソフトボール') !== false){ echo "checked";} ?>><label for="q1_html25">ソフトボール<br></label>
							<input type="checkbox" id="q1_html26" name="your_hobby[]" value="水泳"<?php if(strpos($result[0]['your_hobby'],'水泳') !== false){ echo "checked";} ?>><label for="q1_html26">水泳<br></label>
							<input type="checkbox" id="q1_html27" name="your_hobby[]" value="フリスビー"<?php if(strpos($result[0]['your_hobby'],'フリスビー') !== false){ echo "checked";} ?>><label for="q1_html27">フリスビー<br></label>
							<input type="checkbox" id="q1_html28" name="your_hobby[]" value="ラグビー"<?php if(strpos($result[0]['your_hobby'],'ラグビー') !== false){ echo "checked";} ?>><label for="q1_html28">ラグビー<br></label>
							<input type="checkbox" id="q1_html29" name="your_hobby[]" value="卓球"<?php if(strpos($result[0]['your_hobby'],'卓球') !== false){ echo "checked";} ?>><label for="q1_html29">卓球<br></label>
							<input type="checkbox" id="q1_html30" name="your_hobby[]" value="ダーツ"<?php if(strpos($result[0]['your_hobby'],'ダーツ') !== false){ echo "checked";} ?>><label for="q1_html30">ダーツ<br></label>
							<input type="checkbox" id="q1_html31" name="your_hobby[]" value="ビリヤード"<?php if(strpos($result[0]['your_hobby'],'ビリヤード') !== false){ echo "checked";} ?>><label for="q1_html31">ビリヤード<br></label>
							<input type="checkbox" id="q1_html32" name="your_hobby[]" value="登山"<?php if(strpos($result[0]['your_hobby'],'登山') !== false){ echo "checked";} ?>><label for="q1_html32">登山<br></label>
							<input type="checkbox" id="q1_html33" name="your_hobby[]" value="スキューバダイビング"<?php if(strpos($result[0]['your_hobby'],'スキューバダイビング') !== false){ echo "checked";} ?>><label for="q1_html33">スキューバダイビング<br></label>
							<input type="checkbox" id="q1_html34" name="your_hobby[]" value="旅行"<?php if(strpos($result[0]['your_hobby'],'旅行') !== false){ echo "checked";} ?>><label for="q1_html34">旅行<br></label>
							<input type="checkbox" id="q1_html35" name="your_hobby[]" value="ランニング"<?php if(strpos($result[0]['your_hobby'],'ランニング') !== false){ echo "checked";} ?>><label for="q1_html35">ランニング<br></label>
							<input type="checkbox" id="q1_html36" name="your_hobby[]" value="筋トレ"<?php if(strpos($result[0]['your_hobby'],'筋トレ') !== false){ echo "checked";} ?>><label for="q1_html36">筋トレ<br></label>
							<input type="checkbox" id="q1_html37" name="your_hobby[]" value="絵"<?php if(strpos($result[0]['your_hobby'],'絵') !== false){ echo "checked";} ?>><label for="q1_html37">絵<br></label>
							<input type="checkbox" id="q1_html38" name="your_hobby[]" value="映画観賞"<?php if(strpos($result[0]['your_hobby'],'映画観賞') !== false){ echo "checked";} ?>><label for="q1_html38">映画観賞<br></label>
							<input type="checkbox" id="q1_html39" name="your_hobby[]" value="ドッジボール"<?php if(strpos($result[0]['your_hobby'],'ドッジボール') !== false){ echo "checked";} ?>><label for="q1_html39">ドッジボール<br></label>
							<input type="checkbox" id="q1_html40" name="your_hobby[]" value="格闘技"<?php if(strpos($result[0]['your_hobby'],'格闘技') !== false){ echo "checked";} ?>><label for="q1_html40">格闘技<br></label>
							<input type="checkbox" id="q1_html41" name="your_hobby[]" value="ライブ"<?php if(strpos($result[0]['your_hobby'],'ライブ') !== false){ echo "checked";} ?>><label for="q1_html41">ライブ<br></label>
							<input type="checkbox" id="q1_html42" name="your_hobby[]" value="アイドル"<?php if(strpos($result[0]['your_hobby'],'アイドル') !== false){ echo "checked";} ?>><label for="q1_html42">アイドル<br></label>
							<input type="checkbox" id="q1_html43" name="your_hobby[]" value="バンド"<?php if(strpos($result[0]['your_hobby'],'バンド') !== false){ echo "checked";} ?>><label for="q1_html43">バンド<br></label>
							<input type="checkbox" id="q1_html44" name="your_hobby[]" value="パンク"<?php if(strpos($result[0]['your_hobby'],'パンクロック') !== false){ echo "checked";} ?>><label for="q1_html44">パンクロック<br></label>
							<input type="checkbox" id="q1_html45" name="your_hobby[]" value="ロック"<?php if(strpos($result[0]['your_hobby'],'ロック') !== false){ echo "checked";} ?>><label for="q1_html45">ロック<br></label>
							<input type="checkbox" id="q1_html46" name="your_hobby[]" value="V系"<?php if(strpos($result[0]['your_hobby'],'V系') !== false){ echo "checked";} ?>><label for="q1_html46">V系<br></label>
							<input type="checkbox" id="q1_html47" name="your_hobby[]" value="洋楽"<?php if(strpos($result[0]['your_hobby'],'洋楽') !== false){ echo "checked";} ?>><label for="q1_html47">洋楽<br></label>
							<input type="checkbox" id="q1_html48" name="your_hobby[]" value="EDM"<?php if(strpos($result[0]['your_hobby'],'EDM') !== false){ echo "checked";} ?>><label for="q1_html48">EDM<br></label>
							<input type="checkbox" id="q1_html49" name="your_hobby[]" value="J-POP"<?php if(strpos($result[0]['your_hobby'],'J-POP') !== false){ echo "checked";} ?>><label for="q1_html49">J-POP<br></label>
							<input type="checkbox" id="q1_html50" name="your_hobby[]" value="K-POP"<?php if(strpos($result[0]['your_hobby'],'K-POP') !== false){ echo "checked";} ?>><label for="q1_html50">K-POP<br></label>
							<input type="checkbox" id="q1_html51" name="your_hobby[]" value="アニメ"<?php if(strpos($result[0]['your_hobby'],'アニメ') !== false){ echo "checked";} ?>><label for="q1_html51">アニメ<br></label>
							<input type="checkbox" id="q1_html52" name="your_hobby[]" value=”ゲーム”<?php if(strpos($result[0]['your_hobby'],'ゲーム') !== false){ echo "checked";} ?>><label for="q1_html52">ゲーム<br></label>
							<input type="checkbox" id="q1_html53" name="your_hobby[]" value="アイドル"<?php if(strpos($result[0]['your_hobby'],'アイドル') !== false){ echo "checked";} ?>><label for="q1_html53">アイドル<br></label>
							<input type="checkbox" id="q1_html54" name="your_hobby[]" value="漫画"<?php if(strpos($result[0]['your_hobby'],'漫画') !== false){ echo "checked";} ?>><label for="q1_html54">漫画<br></label></label>
						</label>

						<div class="checkbox9">あなたの性格を教えてください</div>
						<label>
							<input type="checkbox" id="q1_html55" name="your_personality[]" value="ポジティブな性格" <?php if(strpos($result[0]['your_personality'],'ポジティブな性格') !== false){ echo "checked";} ?>><label for="q1_html55">ポジティブな性格<br></label>
							<input type="checkbox" id="q1_html56" name="your_personality[]" value="ネガティブな性格" <?php if(strpos($result[0]['your_personality'],'ネガティブな性格') !== false){ echo "checked";} ?>><label for="q1_html56">ネガティブな性格<br></label>
							<input type="checkbox" id="q1_html57" name="your_personality[]" value="向上心が高い" <?php if(strpos($result[0]['your_personality'],'向上心が高い') !== false){ echo "checked";} ?>><label for="q1_html57">向上心が高い<br></label>
							<input type="checkbox" id="q1_html58" name="your_personality[]" value="おっとりした性格" <?php if(strpos($result[0]['your_personality'],'おっとりした性格') !== false){ echo "checked";} ?>><label for="q1_html58">おっとりした性格<br></label>
							<input type="checkbox" id="q1_html59" name="your_personality[]" value="優しい" <?php if(strpos($result[0]['your_personality'],'優しい') !== false){ echo "checked";} ?>><label for="q1_html59">優しい<br></label>
							<input type="checkbox" id="q1_html60" name="your_personality[]" value="キレイ好き" <?php if(strpos($result[0]['your_personality'],'キレイ好き') !== false){ echo "checked";} ?>><label for="q1_html60">キレイ好き<br></label>
							<input type="checkbox" id="q1_html61" name="your_personality[]" value="連絡マメ" <?php if(strpos($result[0]['your_personality'],'連絡マメ') !== false){ echo "checked";} ?>><label for="q1_html61">連絡マメ<br></label>
							<input type="checkbox" id="q1_html62" name="your_personality[]" value="明るい性格" <?php if(strpos($result[0]['your_personality'],'明るい性格') !== false){ echo "checked";} ?>><label for="q1_html62">明るい性格<br></label>
							<input type="checkbox" id="q1_html63" name="your_personality[]" value="喋るのは苦手" <?php if(strpos($result[0]['your_personality'],'喋るのは苦手') !== false){ echo "checked";} ?>><label for="q1_html63">喋るのが苦手<br></label>
							<input type="checkbox" id="q1_html64" name="your_personality[]" value="メンヘラです..." <?php if(strpos($result[0]['your_personality'],'メンヘラです...') !== false){ echo "checked";} ?>><label for="q1_html64">メンヘラです...<br></label>
							<input type="checkbox" id="q1_html65" name="your_personality[]" value="お豆腐メンタル" <?php if(strpos($result[0]['your_personality'],'お豆腐メンタル') !== false){ echo "checked";} ?>><label for="q1_html65">お豆腐メンタル<br></label>
							<input type="checkbox" id="q1_html66" name="your_personality[]" value="リードしてほしい" <?php if(strpos($result[0]['your_personality'],'リードしてほしいタイプ') !== false){ echo "checked";} ?>><label for="q1_html66">リードしてほしいタイプ<br></label>
							<input type="checkbox" id="q1_html67" name="your_personality[]" value="リードしたい" <?php if(strpos($result[0]['your_personality'],'リードするタイプ') !== false){ echo "checked";} ?>><label for="q1_html67">リードするタイプ<br></label>
							<input type="checkbox" id="q1_html68" name="your_personality[]" value="Sです" <?php if(strpos($result[0]['your_personality'],'Sです') !== false){ echo "checked";} ?>><label for="q1_html68">私はSです<br></label>
							<input type="checkbox" id="q1_html69" name="your_personality[]" value="Mです" <?php if(strpos($result[0]['your_personality'],'Mです') !== false){ echo "checked";} ?>><label for="q1_html69">私はMです<br></label>
							<input type="checkbox" id="q1_html70" name="your_personality[]" value="恐がり" <?php if(strpos($result[0]['your_personality'],'恐がり') !== false){ echo "checked";} ?>><label for="q1_html70">恐がりです<br></label>
							<input type="checkbox" id="q1_html71" name="your_personality[]" value="好奇心旺盛" <?php if(strpos($result[0]['your_personality'],'好奇心旺盛') !== false){ echo "checked";} ?>><label for="q1_html71">好奇心旺盛<br></label>
							<input type="checkbox" id="q1_html72" name="your_personality[]" value="忘れっぽい" <?php if(strpos($result[0]['your_personality'],'忘れっぽい') !== false){ echo "checked";} ?>><label for="q1_html72">忘れっぽい<br></label>
							<input type="checkbox" id="q1_html73" name="your_personality[]" value="根に持たない" <?php if(strpos($result[0]['your_personality'],'根に持たない') !== false){ echo "checked";} ?>><label for="q1_html73">根に持たない<br></label>
							<input type="checkbox" id="q1_html74" name="your_personality[]" value="神経質" <?php if(strpos($result[0]['your_personality'],'神経質') !== false){ echo "checked";} ?>><label for="q1_html74">神経質<br></label>
							<input type="checkbox" id="q1_html75" name="your_personality[]" value="めんどくさがり" <?php if(strpos($result[0]['your_personality'],'めんどくさがり') !== false){ echo "checked";} ?>><label for="q1_html75">めんどくさがり<br></label>
							<input type="checkbox" id="q1_html76" name="your_personality[]" value="真面目" <?php if(strpos($result[0]['your_personality'],'真面目') !== false){ echo "checked";} ?>><label for="q1_html76">真面目<br></label>
							<input type="checkbox" id="q1_html77" name="your_personality[]" value="社交的" <?php if(strpos($result[0]['your_personality'],'社交的') !== false){ echo "checked";} ?>><label for="q1_html77">社交的<br></label>
							<input type="checkbox" id="q1_html78" name="your_personality[]" value="一人が好き" <?php if(strpos($result[0]['your_personality'],'一人が好き') !== false){ echo "checked";} ?>><label for="q1_html78">一人が好き<br></label>
						</label>

						<div class="intro3">自己紹介</div>
	　　　　　　		  <textarea name ="text" class="textarea3">　<?php echo $result[0]['text']; ?>　</textarea>
						<input type="submit" value="送信" class="btn btn--orange">
					</form>
					</div>
				</div>
			</div>
		</div>
		<div id="closeModal" class="closeModal2">
		×
		</div>
	</section>
	<a href="delete_id.php"><button id="openModal2">退会する</button></a>
</div>
</div>
</div>


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