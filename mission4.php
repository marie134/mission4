<html>
	<head>
		<meta charset="UTF-8">
		<title>4</title>
	</head>
	<body>
		・新規投稿をするときは名前、コメント、パスワードを入力し送信ボタンを押してください。<br>
		・削除するときは削除対象番号、パスワードを入力し削除ボタンを押してください。<br>
		・編集するときは編集対象番号、パスワードを入力し編集ボタンを押してください。<br>
		・名前とコメントが入力されて表示されるので、編集してから編集対象番号、パスワードを入力し編集ボタンを押してください。<br>
	</body>
</html>

<?php

//データベースに接続
$dsn = 'データベース名';
$user='ユーザ名';
$password='パスワード';
$pdo = new PDO($dsn,$user,$password);

//テーブル作成
$sql="CREATE TABLE keiziban"
."("
."id INT PRIMARY KEY,"
."name char(32),"
."comment TEXT,"
."day TEXT,"
."pass TEXT"
.");";
$stmt = $pdo->query($sql);


$name=$_POST['name'];
$comment=$_POST['comment'];
$dnumber=$_POST['dnumber'];
$hnumber=$_POST['hnumber'];
$comment_pass=$_POST['comment_pass'];
$dnumber_pass=$_POST['dnumber_pass'];
$hnumber_pass=$_POST['hnumber_pass'];


//名前・コメント
if(isset($_POST['okuru']))
{
	//入力漏れがあるとき
	if( (empty($name)) || (empty($comment)) || (empty($comment_pass)) )
	{
		echo "<p>入力してください</p>";
	}
	else//ちゃんと入力されてるとき
	{
		//行数確認
		$sql = 'select * from keiziban';
		$gyo = $pdo -> query($sql);
		$gyo -> execute();
		$count = $gyo -> rowCount();
		$count = $count + 1;
		$day = date("Y/m/d H:i:s");
		//echo $count;
		//echo $name;
		//echo $comment;
		//echo $day;
		//echo $comment_pass;

		//データベースに入れる
		$sql = $pdo -> prepare("INSERT INTO keiziban (id,name,comment,day,pass) VALUES (:id,:name,:comment,:day,:pass)");
		$sql -> bindParam(':id',$count,PDO::PARAM_STR);
		$sql -> bindParam(':name',$name,PDO::PARAM_STR);
		$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql -> bindParam(':day',$day,PDO::PARAM_STR);
		$sql -> bindParam(':pass',$comment_pass,PDO::PARAM_STR);
		$sql -> execute();

		//表示
		$sql = 'SELECT * FROM keiziban order by id';
		$result = $pdo -> query($sql);
		foreach ($result as $row)
		{
			echo $row['id'].',';
			echo $row['name'].',';
			echo $row['comment'].',';
			echo $row['day'].'<br>';
		}
	}
}

//削除
if(isset($_POST['dokuru']))
{
	if( (empty($dnumber)) || (empty($dnumber_pass)) )//入力漏れがあるとき
	{
		echo "<p>入力してください</p>";
	}
	else//ちゃんと入力されているとき
	{
		$sql = 'SELECT * FROM keiziban order by id';
		$result = $pdo -> query($sql);
		foreach ($result as $row)
		{
			//投稿番号と削除番号が一致したとき
			if(($row['id']) == $dnumber)
			{
				if($dnumber_pass != ($row['pass']))//パスワードが一致しなかったら
				{
					echo "<p>正しいパスワードを入力してください</p>";
				}
				else//パスワードが一致したら
				{
					$delete = 1;
					$sql = "delete from keiziban where id=$dnumber";
					$result = $pdo -> query($sql);
				}
			}
		}

		if($delete == 1)
		{
			$sql = 'SELECT * FROM keiziban order by id';
			$result = $pdo -> query($sql);
			foreach ($result as $row)
			{
				if(($row['id']) < $dnumber)
				{
				}
				elseif(($row['id']) == $dnumber)
				{
					//削除
					$sql = "delete from keiziban where id=$dnumber";
					$result = $pdo -> query($sql);
				}
				else
				{
					//番号ずらす
					$id = intval($row['id']);
					$name = intval($row['name']);
					$comment = intval($row['comment']);
					$day = intval($row['day']);
					$pass = intval($row['pass']);

					//中身確認
					//echo $row['id'].',';
					//echo $row['name'].',';
					//echo $row['comment'].',';
					//echo $row['day'].'<br>';

					//echo "<p>$id</p>";
					$hid = $id-1;
					//echo "<p>$id</p>";
					//番号編集
					$sql = "update keiziban set id='$hid' where id=$id";
					$result = $pdo -> query($sql);
				}
			}
		}
	}

	//表示
	$sql = 'SELECT * FROM keiziban order by id';
	$result = $pdo -> query($sql);
	foreach ($result as $row)
	{
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['day'].'<br>';
	}
}


if(isset($_POST['hokuru']))//編集
{
	if( (empty($hnumber)) || (empty($hnumber_pass)) )//入力漏れがあるとき
	{
		echo "<p>入力してください</p>";
	}
	else//ちゃんと入力されているとき
	{
		//編集内容取得
		$hensyu = 0;
		$sql = 'SELECT * FROM keiziban order by id';
		$result = $pdo -> query($sql);
		foreach ($result as $row)
		{
			//投稿番号と編集番号が一致したとき
			if(($row['id']) == $hnumber)
			{
				if($hnumber_pass != ($row['pass']))//パスワードが一致しなかったら
				{
					echo "<p>正しいパスワードを入力してください</p>";
				}
				else//パスワードが一致したら
				{
					$hensyu = 1;
				}
			}
		}

		//henyuに１が入っているとき(パスワードが一致したとき)
		if($hensyu == 1)
		{
			//name,commentを表示する
			$sql = 'SELECT * FROM keiziban order by id';
			$result = $pdo -> query($sql);
			foreach ($result as $row)
			{
				if(($row['id']) == $hnumber)
				{
					//情報取得
					$hid = intval($row['id']);
					$hname= $row['name'];
					$hcomment = $row['comment'];

					//echo $hid.'<br>';
					//echo $hname.'<br>';
					//echo $hcomment.'<br>';
				}
			}

			//名前、コメント、パスワード、編集番号、編集パスワードが入力されてるとき
			if( (!empty($name)) && (!empty($comment)) && (!empty($comment_pass)) && (!empty($hnumber)) && (!empty($hnumber_pass)) )
			{
				$sql = 'SELECT * FROM keiziban order by id';
				$result = $pdo -> query($sql);
				foreach ($result as $row)
				{
					if(($row['id']) == $hnumber)
					{
						//編集
						$hid2 = intval($row['id']);
						$hname = $name;
						$hcomment = $comment;
						$hpass = $comment_pass;
						$hday = date("Y/m/d H:i:s");
						$hpass2 = $hnumber_pass;

						$sql = "update keiziban set id='$hid2', name='$hname', comment='$hcomment', day='$hday', pass='$hpass' where id=$hid";
						$result = $pdo -> query($sql);

						//中身無くす
						unset($hname);
						unset($hcomment);
						unset($hid);
					}
				}
			}
		}
		//表示
		$sql = 'SELECT * FROM keiziban order by id';
		$result = $pdo -> query($sql);
		foreach ($result as $row)
		{
			echo $row['id'].',';
			echo $row['name'].',';
			echo $row['comment'].',';
			echo $row['day'].'<br>';
		}
	}
}

?>

<html>
	<body>
		<form action="mission_4-1.php" method="post">
		<p>名前:<input type="text" name="name" value="<?php echo $hname ; ?>" ></p>
		<p>コメント:<input type="text" name="comment" value="<?php echo $hcomment ; ?>" size="30"></p>
		<p>パスワード:<input type="text" name="comment_pass"></p>
		<input type="submit" name="okuru" value="送信">

		<p>削除対象番号:<input type="number" name="dnumber"></p>
		<p>パスワード:<input type="text" name="dnumber_pass"></p>
		<input type="submit" name="dokuru" value="削除">

		<p>編集対象番号:<input type="number" name="hnumber" value="<?php echo $hid ; ?>"></p>
		<p>パスワード:<input type="text" name="hnumber_pass"></p>
		<input type="submit" name="hokuru" value="編集">
		</form>
	</body>
</html>
