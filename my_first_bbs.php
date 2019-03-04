<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <title>mission_4</title>
  <link rel="stylesheet" href="my_first_bbs.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
  <link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Permanent+Marker" rel="stylesheet">
</head>

<?php
//　DB接続 ////////////////////////////////////////////////////////////////////////////////
  $dsn = 'DATABASE_NAME';
  $user = 'USER_NAME';
  $password = 'PASSWORD';
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//////////////////////////////////////////////////////////////////////////////////////////
 ?>

 <?php
//　カレンダー ////////////////////////////////////////////////////////////////////////////
   date_default_timezone_set('Asia/Tokyo');
   //年
   $year = date("Y");
   //月
   $month = date("m");
   switch ($month) {
     case '01':
       $month = "January";
       break;
     case '02':
       $month = "February";
       break;
     case '03':
       $month = "March";
       break;
     case '04':
       $month = "April";
       break;
     case '05':
       $month = "May";
       break;
     case '06':
       $month = "June";
       break;
     case '07':
       $month = "July";
       break;
     case '08':
       $month = "August";
       break;
     case '09':
       $month = "September";
       break;
     case '10':
       $month = "October";
       break;
     case '11':
       $month = "November";
       break;
     case '12':
       $month = "December";
       break;
   }
   //日
   $day = date("d");

   //曜日
   $w = date("w");
   $week_name = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
///////////////////////////////////////////////////////////////////////////////////////
  ?>

<body>
<header class="rogo_area">
  <img src="images/EXCITE.png"  width="160px" height="90px"/>
</header>



<div class="image_area">
  <div class="color_overlay">
    <div class="day_number"><?php echo $day; ?></div>
    <div class="date_right">
      <div class="day_name"><?php echo $week_name[$w]; ?></div>
      <div class="month"><?php echo $month. " ".$year; ?></div>
    </div>
  </div>
</div>


<?php
if($_POST['btn_mod']){
  $mod_post_id = $_POST['post_id'];
  $confilm_password = $_POST['confilm_password'];
  if($mod_post_id != ""){
    if(ctype_digit($mod_post_id)){
      $sql = 'SELECT * FROM mission_4 WHERE POST_ID = '.$mod_post_id ;
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row) {

        $password = $row['PASSWORD'];
        if($password == $confilm_password){
          $message = $row['MESSAGE'];
          $user_name = $row['USER_NAME'];
        }
      }
    }
  }
}
 ?>

<form method="post">
  <div class="wrapper">

    <div class="post_area">
      <ul>
        <li>
          <label>USER_NAME</label><br>
          <input type="text" name="user_name" value="<?php echo "$user_name";?>"/>
        </li>
        <li>
          <label>PASSWORD</label><br>
          <input type="password" name="password" />
        </li>
        <li>
          <label>MESSAGE</label><br>
          <input type="text" name="message"value="<?php echo "$message";?>"/>
        </li>
      </ul>
    </div>

    <div class="mod_area" >
      <ul>
        <li>
          <label>POST_ID</label><br>
          <input type="text" name="post_id" />
        </li>

        <li>
          <label>CONFILM_PASSWORD</label><br>
          <input type="password" name="confilm_password"/>
        </li>

        <li>
          <input type="text" name="receive_mod_post_id" value="<?php echo "$mod_post_id";?>" hidden='hiddened'/>
        </li>
      </ul>
    </div>

  </div>


  <div class="btn_area">



      <div class="btn_post">
        <input type="submit" value="POST" class="button_post" name="btn_post">
      </div>

      <div class="btn_delete">
        <input type="submit" value="DELETE" class="button_delete" name="btn_delete">
      </div>

      <div class="btn_mod">
        <input type="submit" value="MOD" class="button_mod" name="btn_mod">
      </div>


  </div>
</form>

<?php
$date = date("Y/m/d H:i:s");
//　投稿処理 ////////////////////////////////////////////////////////////////////////////////
if($_POST['btn_post']){
  $user_name = $_POST['user_name'];
  $password = $_POST['password'];
  $message = $_POST['message'];
  $receive_mod_post_id = $_POST['receive_mod_post_id'];

  // 編集機能　//////////////////////////////////////////////////////////////////////////////
  if($user_name != "" && $message != "" && $receive_mod_post_id != ""){
    if(ctype_digit($receive_mod_post_id)){
      $sql = 'UPDATE mission_4 SET USER_NAME=:USER_NAME,MESSAGE=:MESSAGE,PASSWORD=:PASSWORD,DATE=:DATE WHERE POST_ID=:POST_ID';
      $stmt = $pdo -> prepare($sql);

      $stmt -> bindParam(':POST_ID', $receive_mod_post_id, PDO::PARAM_INT);
      $stmt -> bindParam(':USER_NAME',$user_name, PDO::PARAM_STR);
      $stmt -> bindParam(':MESSAGE', $message, PDO::PARAM_STR);
      $stmt -> bindParam(':PASSWORD', $password, PDO::PARAM_STR);
      $stmt -> bindParam(':DATE', $date, PDO::PARAM_STR);

      $stmt -> execute();

    }
  }
  // 新規投稿機能　//////////////////////////////////////////////////////////////////////////////
  elseif($user_name != "" && $message != ""){
    //カウント処理
    //ファイルの中の数字を取得、その数字を投稿番号とする。
    $fp = fopen("mission4.txt","r+");
    $count = fgets($fp,10);
    $count = $count + 1;
    rewind($fp);
    fputs($fp,$count);
    fclose($fp);

    $post_id = $count;
    //データベース格納
    $sql = $pdo -> prepare("INSERT INTO mission_4(POST_ID,USER_NAME,MESSAGE,PASSWORD,DATE) VALUES (:POST_ID, :USER_NAME, :MESSAGE ,:PASSWORD,:DATE)");

    $sql -> bindParam(':POST_ID', $post_id, PDO::PARAM_INT);
    $sql -> bindParam(':USER_NAME',$user_name, PDO::PARAM_STR);
    $sql -> bindParam(':MESSAGE', $message, PDO::PARAM_STR);
    $sql -> bindParam(':PASSWORD', $password, PDO::PARAM_STR);
    $sql -> bindParam(':DATE', $date, PDO::PARAM_STR);

    $sql -> execute();



  }
}
////////////////////////////////////////////////////////////////////////////////////////////
// 削除機能 /////////////////////////////////////////////////////////////////////////////////
elseif ($_POST['btn_delete']) {
  $delete_post_id = $_POST['post_id'];
  $confilm_password = $_POST['confilm_password'];
  if($delete_post_id != ""){
    if(ctype_digit($delete_post_id)){
      $sql = 'SELECT * FROM mission_4 WHERE POST_ID = '.$delete_post_id ;
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row) {

        $password = $row['PASSWORD'];

        if($password == $confilm_password){
          $sql = 'delete from mission_4 where post_id=:POST_ID';
      		$stmt = $pdo->prepare($sql);
      		$stmt->bindParam(':POST_ID',$delete_post_id, PDO::PARAM_INT);
      		$stmt->execute();
        }
      }
    }
  }
}
////////////////////////////////////////////////////////////////////////////////////////////
 ?>


<div class="container">
  <ul>

    <?php
    $sql = 'SELECT * FROM mission_4 ORDER BY POST_ID';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
      echo "<li>";
      echo "<span></span>";
      echo "<div class='POST_ID'>".$row['POST_ID'].".</div>";
      echo "<div class='USER_NAME'>".$row['USER_NAME']."</div>";
      echo "<div class='MESSAGE'>".$row['MESSAGE']."</div>";
      echo "<div class='TIME_STAMP'>".$row['DATE']."</div>";
      echo "</li>";
    }
     ?>

  </ul>
</div>

<footer class="footer">
  <div class="product">
    <p>
      Mede by nissy222.
    </p>
  </div>
</footer>
<?php  ?>
</body>
</html>
