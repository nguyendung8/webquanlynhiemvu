<?php
// đăng ký tài khoản người dùng
include 'config.php';

if(isset($_POST['submit'])){

   $tenTK = mysqli_real_escape_string($conn, $_POST['name']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $maPhanQuyen = 6; // MaPhanQuyen cố định là 6

   // kiểm tra tên tài khoản đã tồn tại
   $select_users = mysqli_query($conn, "SELECT * FROM `TaiKhoan` WHERE TenTK = '$tenTK'") or die('Query failed');

   if(mysqli_num_rows($select_users) > 0){ // tên tài khoản đã tồn tại
      $message[] = 'Tên tài khoản đã tồn tại!';
   } else { // chưa tồn tại, kiểm tra mật khẩu xác nhận và tạo tài khoản
      if($pass != $cpass){
         $message[] = 'Mật khẩu không khớp!';
      } else {
         // Đăng ký tài khoản vào bảng TaiKhoan
         mysqli_query($conn, "INSERT INTO `TaiKhoan` (TenTK, MatKhau, MaPhanQuyen) VALUES('$tenTK', '$pass', '$maPhanQuyen')") or die('Query failed');
         $message[] = 'Đăng ký thành công!';
         header('location:home.php');
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng ký</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">

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
      <h3>Đăng ký</h3>
      <input type="text" name="name" placeholder="Nhập tên tài khoản" required class="box">
      <input type="password" name="password" placeholder="Nhập mật khẩu" required class="box">
      <input type="password" name="cpassword" placeholder="Nhập lại mật khẩu" required class="box">
      <input type="submit" name="submit" value="Đăng ký ngay" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
      <p>Bạn đã có tài khoản? <a style="color: blue; text-decoration: none;" href="home.php">Đăng nhập</a></p>
   </form>

</div>

</body>
</html>
