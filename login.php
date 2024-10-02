<?php

   include 'config.php';
   session_start();

   if(isset($_POST['submit'])){//lấy thông tin đăng nhập từ form submit name='submit'

      $tentk = mysqli_real_escape_string($conn, $_POST['tentk']);
      $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

      $select_users = mysqli_query($conn, "SELECT * FROM `TaiKhoan` WHERE TenTK = '$tentk' AND MatKhau = '$pass'") or die('query failed');

      if(mysqli_num_rows($select_users) > 0){//kiểm tra tài khoản có tồn tại không

         $row = mysqli_fetch_assoc($select_users);
         //kiểm tra quyền của tài khoản và đưa đến trang tương ứng
         if($row['MaPhanQuyen'] == 1){

            $_SESSION['admin_name'] = $row['TenTK'];
            $_SESSION['admin_id'] = $row['MaTK'];
            header('location:admin_accounts.php');

         } elseif($row['MaPhanQuyen'] == 2){

            $_SESSION['owner_name'] = $row['TenTK'];
            $_SESSION['owner_id'] = $row['MaTK'];
            header('location:owner_books.php');

         }

         elseif($row['MaPhanQuyen'] == 3){

            $_SESSION['sale_name'] = $row['TenTK'];
            $_SESSION['sale_id'] = $row['MaTK'];
            header('location:employee_orders.php');

         }
         elseif($row['MaPhanQuyen'] == 4){

            $_SESSION['warehouse_name'] = $row['TenTK'];
            $_SESSION['warehouse_id'] = $row['MaTK'];
            header('location:employee_entry.php');

         }

         elseif($row['MaPhanQuyen'] == 6){

            $_SESSION['user_name'] = $row['TenTK'];
            $_SESSION['user_id'] = $row['MaTK'];
            header('location:home.php');

         }

      }else{
         $message[] = 'Email hoặc mật khẩu không chính xác!';
      }

   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng nhập</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .forget-btn {
         font-size: 20px;
         color: #9C27B0;
      }
      .forget-btn:hover {
         opacity: 0.8;
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Đăng nhập</h3>
      <input type="text" name="tentk" placeholder="Nhập Tên TK" required class="box">
      <input type="password" name="password" placeholder="Mật khẩu" required class="box">
      <input type="submit" name="submit" value="Đăng nhập" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
      <br>
      <a class="forget-btn" style="color: blue; text-decoration: none;" href="forget_password.php">Quên mật khẩu ?</a>
      <p>Bạn chưa có tài khoản? <a style="color: blue; text-decoration: none;" href="register.php">Đăng ký</a></p>
   </form>

</div>

</body>
</html>