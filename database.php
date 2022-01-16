<?php
require_once('matching.php');
require_once('data.php');

class Database{
    //お問い合わせ登録
    public function Contact(){
		$dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';
		$user = 'takeshiueno_0111';
		$password = '5050Rock';

		try {
			$dbh = new PDO($dsn, $user, $password);
			$sql = 'INSERT INTO matching_contact (name,age,type,text,in_date,up_date) VALUE (:name, :age,:type,:text,now(),now())';
			$prepare = $dbh->prepare($sql);
			$prepare->bindValue(':name', $_POST["name"], PDO::PARAM_STR);
			$prepare->bindValue(':age', $_POST["age"], PDO::PARAM_INT);
			$prepare->bindValue(':type', $_POST["type"], PDO::PARAM_STR);
			$prepare->bindValue(':text', $_POST["text"], PDO::PARAM_STR);
			$prepare->execute();

			} catch (PDOException $e) {
					echo "接続失敗: " . $e->getMessage() . "\n";
					exit();
			}
    }

    //ログイン
    public function Login(){
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

			$error = "";
			if(isset($_POST['login'])) {
				$mail = $_POST['mail'];
				try {
                    $dbh = new PDO($dsn, $user, $password);
                    $dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';
                    $user = 'takeshiueno_0111';
                    $password = '5050Rock';

                    $sql = 'SELECT * FROM user where mail = :mail';
                    $prepare = $dbh->prepare($sql);
                    $prepare->bindValue(':mail', $_POST["mail"], PDO::PARAM_STR);
                    $prepare->execute();
                    $result = $prepare->fetch();
                    $dbh = null;

                    //ログイン認証
                    if(isset($result)&&!empty($result)) {
                        $_SESSION['user_id'] = $result['user_id'];
                        $_SESSION['mail'] = $result['mail'];
                        header('Location:http://takeshiueno.php.xdomain.jp/matching/mypage.php?action=user_login',true,307);//hader関数は３つ引数を入れられる。入力した値をリダイレクト時にPOSTする（307をつけないとmailの情報がPOSTされない)
                    }else{
                        if($result==false){
                            $alert="";
                            $alert="<script type='text/javascript'>alert('メールアドレスが間違っています。');</script>";
                            echo $alert;
                        }
                    }
                }
                    catch (PDOException $e) {
                    echo "接続失敗: " . $e->getMessage() . "\n";
                    exit();
				}
			}
		}
    }


    //新規登録
    public function Signup(){
        $dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';
		$user = 'takeshiueno_0111';
		$password = '5050Rock';

		if($_GET["action"] == "user_insert"){
			try {
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'INSERT INTO user (name,kana,gender,age,mail,tel,area,job,purpose,your_like,your_hobby,your_personality,text,in_date,up_date) VALUE (:name,:kana,:gender,:age,:mail,:tel,:area,:job,:purpose,:your_like,:your_hobby,:your_personality,:text,now(),now())';//now()はMYSQLの標準日時取得する
				$prepare = $dbh->prepare($sql);
				$prepare->bindValue(':name', $_POST["name"], PDO::PARAM_STR);
				$prepare->bindValue(':kana', $_POST["kana"], PDO::PARAM_STR);
				$prepare->bindValue(':gender', $_POST["gender"], PDO::PARAM_STR);
				$prepare->bindValue(':age', $_POST["age"], PDO::PARAM_INT);
				$prepare->bindValue(':mail', $_POST["mail"], PDO::PARAM_STR);
				$prepare->bindValue(':tel', $_POST["tel"], PDO::PARAM_STR);
				$prepare->bindValue(':area', $_POST["area"], PDO::PARAM_STR);
				$prepare->bindValue(':job', $_POST["job"], PDO::PARAM_STR);
				$prepare->bindValue(':purpose', $_POST["purpose"], PDO::PARAM_STR);
				$prepare->bindValue(':text', $_POST["text"], PDO::PARAM_STR);

				$your_like = ""; 
				$spl = "";
				//チェックがついてあるものを取ってくる
				foreach($_POST["your_like"] as $your_likes){
					$your_like = $your_like.$spl.$your_likes;
					$spl = ",";
				}
					$prepare->bindValue(':your_like', $your_like, PDO::PARAM_STR);

				//別のやり方
				if (isset($_POST['your_hobby']) && is_array($_POST['your_hobby'])) {
					$your_hobby = implode(",", $_POST["your_hobby"]);
				}
					$prepare->bindValue(':your_hobby', $your_hobby, PDO::PARAM_STR);

				if (isset($_POST['your_personality']) && is_array($_POST['your_personality'])) {
					$your_personality = implode("、", $_POST["your_personality"]);
				}
					$prepare->bindValue(':your_personality', $your_personality, PDO::PARAM_STR);
					$prepare->execute();
			}	catch (PDOException $e) {
						echo "接続失敗: " . $e->getMessage() . "\n";
						exit();
			}
		}
    }


    //画像を登録する
    public function Image(){ 
        $dsn = 'mysql:dbname=takeshiueno_database1;host=mysql1.php.xdomain.ne.jp';
		$user = 'takeshiueno_0111';
		$password = '5050Rock';
            try {
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'SELECT * FROM user order by user_id desc limit 1';
				$prepare = $dbh->prepare($sql);
				$prepare->execute();
				$result = $prepare->fetch();
				
				//対象の相手の画像を一旦削除//
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'DELETE from upload_image where user_id =:user_id';
				$prepare = $dbh->prepare($sql);
				$prepare->bindValue(':user_id', $result["user_id"], PDO::PARAM_INT);
				$prepare->execute();

				//formで送信されたFILEはテンポラリフォルダというディレクトリに保存される。これを指定したディレクトリに移す
				move_uploaded_file($_FILES["main"]["tmp_name"],'img/'.$_FILES["main"]["name"]);

				//画像を新たに登録処理をする
				$dbh = new PDO($dsn, $user, $password);
				$sql = 'INSERT INTO upload_image(user_id,file_name,file_category,description,insert_time,update_time) VALUES(:user_id,:file_name,:file_category,:description,now(),now())';
				$prepare = $dbh->prepare($sql);
				$prepare->bindValue(':user_id', $result["user_id"], PDO::PARAM_INT);
				$prepare->bindValue(':file_name', 'img/'.$_FILES["main"]["name"], PDO::PARAM_STR);
				$prepare->bindValue(':file_category', 1, PDO::PARAM_INT);
				$prepare->bindValue(':description', $_FILES["main"]['type'], PDO::PARAM_STR);
				$prepare->execute();
            }catch (PDOException $e) {
                    echo "接続失敗: " . $e->getMessage() . "\n";
                    exit();
        }
    }

    
}



?>