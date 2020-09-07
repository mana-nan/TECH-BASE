<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "utf-8">
    <title>5-1-3</title>
</head>
<body>


<?php
//データベースに接続
    $dsn = 'データベース';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, 
	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	//投稿
	$name=$_POST["name"];
    $comment = $_POST["comment"];
    $timestamp=date("Y/m/d H:i:s");
    $sendpass=$_POST["password"];
    $hidden=$_POST["hiddenNo"];
    //削除   
    $delete=$_POST["deleteNo"];
    $delpass=$_POST["delpass"];
    //編集  
    $edit=$_POST["editNo"];
    $editpass=$_POST["editpass"];
    
    //編集番号、パスワードが編集フォームに入力されたら
    if (!empty($edit) && !empty($editpass)){
        //m５テーブルから情報取得
        $sql="SELECT * FROM m5";
        $stmt=$pdo->query($sql);
        $results = $stmt->fetchAll();
       
        foreach($results as $row){
            $edID = $row["id"];
            $edNAME = $row["name"];
            $edCOMMENT = $row["comment"];
            $edPASS = $row["ps"]; 
            //番号、パスワードが一致したら編集
            if($edID==$edit&&$edPASS==$editpass){
                $editnum=$edID;
                $editname=$edNAME;
                $editcomment=$edCOMMENT; 
            }    
        //パスワードが一致しなければエラーメッセージを。
        }
        if ($edID!=$edit || $edPASS!=$editpass){
            //フォーム付近で表示するためにひとまず変数に代入
            $error_editpass = 'miss_editpass';
        }
    }

//隠れ箱含め、投稿フォームに全て入力されていたら編集
    if(!empty($hidden)&&!empty($name)&&!empty($comment)&&!empty($sendpass)){
        $sql = 'UPDATE m5 SET name=:name,comment=:comment,ts=:ts,ps=:ps WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam(':ts',$timestamp, PDO::PARAM_STR);
        $stmt -> bindParam(':ps', $sendpass, PDO::PARAM_STR);
        $stmt -> bindParam(':id', $hidden, PDO::PARAM_INT);
        $stmt -> execute(); 
    
    //隠れ箱が空の場合、新規投稿として処理
    } elseif (empty($hidden)&&!empty($name)&&!empty($comment)&&!empty($sendpass)){//もし($_POST["hiddennum"]が空欄でかつ)$name,$comment,$sendpassの全てが記入されていたら
        $sql = "CREATE TABLE IF NOT EXISTS m5"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "ts TEXT,"
        . "ps TEXT"
        .");";
        $stmt = $pdo->query($sql);
        $sql = $pdo -> prepare("INSERT INTO m5 (name,comment,ts,ps) VALUES ('$name', '$comment', '$timestamp', '$sendpass')");
        $sql -> execute();
    
    //削除フォームに番号、パスワードが表示されていたら
    } elseif(!empty($delete)&&!empty($delpass)){
        $sql="SELECT * FROM m5";
        $result=$pdo->query($sql);
        foreach($result as $row){
            $delID = $row["id"];
            $delPASS = $row["ps"];
            // 番号、パスワードが一致したら削除
            if($delID==$delete&&$delPASS==$delpass){
                $sql = "DELETE FROM m5 where id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        //一致しなければエラーメッセージを表示するための変数定義
        if ($delID != $delete || $delPASS != $delpass){
            $error_delpass = 'miss_delpass';
        }
    }    
        //以下、未入力の場合エラーメッセージを表示するための変数定義
    	//名前
    	if (empty($name) && !empty($comment) && !empty($sendpass)){
    		$error['name'] = 'blank';		
    	}//コメント
    	if (!empty($name) && empty($comment) && !empty($sendpass)){
    		$error['comment'] = 'blank';		
    	} //パスワード
    	if (!empty($name) && empty($comment) && empty($sendpass)){
    		$error['password'] = 'blank';		
    	}
    	//削除番号
    	if (empty($delete) && !empty($delpass)){
    	    $error['delete'] = 'blank';
    	}
    	//削除パスワード
    	if (!empty($delete) && empty($delpass)){
    	    $error['delpass'] = 'blank';
    	}
    	//編集番号
    	if (empty($edit) && !empty($editpass)){
    	    $error['edit'] = 'blank';
    	}
    	//編集パスワード
    	if (!empty($edit) && empty($editpass)){
    	    $error['editpass'] = 'blank';
    	}
?>

<form action="", method="post">
    <h1>おすすめの映画！！</h1>
    <h3>投稿フォーム</h3>
    名前：<input type="text" name="name" 
    value= <?php if($editnum!==""){echo $editname;} ?> ><br>          
    <?php if($error['name'] === 'blank'): ?>
    <p class="error"><font color="red">*名前を入力してください</font></p>
    <?php endif; ?>
    
    コメント：<input type="text" name ="comment"
    value= <?php if($editnum!==""){echo $editcomment;} ?> ><br>
    <?php if($error['comment'] === 'blank'): ?>
    <p class="error"><font color="red">*コメントを入力してください</font></p>
    <?php endif; ?>
            
    パスワード：<input type="password" name="password" autocomplete="new-password"><br>
    <?php if($error['password'] === 'blank'): ?>
    <p class="error"><font color="red">*パスワードを入力してください</font></p>
    <?php endif; ?>
    <?php if ($error_pass === 'miss_pass'): ?>
    <p class="error"><font color="red">*パスワードが違います</font></p>
    <?php endif; ?>
    
    <!--隠れ箱-->        
    <input type="hidden" name="hiddenNo"
    value= <?php if($editnum!==""){echo $editnum;} ?> >
    
    <input type="submit" name="send"><br><br>

           
    <h3>削除フォーム</h3>
    削除番号：<input type="text" name="deleteNo"><br>
    <?php if($error['delete'] === 'blank'): ?>
    <p class="error"><font color="red">*削除したい番号を入力してください</font></p>
    <?php endif; ?>
    
    パスワード：<input type="password" name="delpass"><br>
    <?php if($error['delpass'] === 'blank'): ?>
    <p class="error"><font color="red">*パスワードを入力してください</font></p>
    <?php endif; ?>
    
    <?php if ($error_delpass === 'miss_delpass'): ?>
    <p class="error"><font color="red">*パスワードが違います</font></p>
    <?php endif; ?>
    
    <input type="submit" name="delete" value="削除"><br><br>
           
    <h3>編集フォーム</h3>
    編集番号：<input type="text" name="editNo"><br>
    <?php if($error['edit'] === 'blank'): ?>
    <p class="error"><font color="red">*編集したい番号を入力してください</font></p>
    <?php endif; ?>
    
    パスワード：<input type="password" name="editpass"><br>
    <?php if($error['editpass'] === 'blank'): ?>
    <p class="error"><font color="red">*パスワードを入力してください</font></p>
    <?php endif; ?>
    
    <?php if ($error_editpass === 'miss_editpass'): ?>
    <p class="error"><font color="red">*パスワードが違います</font></p>
    <?php endif; ?>
    
    <input type="submit" value="編集"><br><br>
</form>   

    
<h3>投稿一覧</h3>
    
<?php    
    $sql="SELECT * FROM m5";
        $stmt=$pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row){
            echo $row["id"]." ";
            echo $row["name"]." ";
            echo $row["comment"]." ";
            echo $row["ts"]." ";
            echo "<br>";      
        }
?>       
    </body>
</html>