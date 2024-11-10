<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) { // Lấy thông tin đăng nhập từ form với submit name='submit'

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = mysqli_real_escape_string($conn, md5($_POST['password'])); // Mã hóa mật khẩu bằng md5

   // Truy vấn kiểm tra thông tin đăng nhập trong bảng `quan_tri_vien`
   $select_admin = mysqli_query($conn, "SELECT * FROM `quan_tri_vien` WHERE email = '$email' AND mat_khau = '$password'") or die('query failed');

   // Kiểm tra xem người dùng có phải là quản trị viên không
   if (mysqli_num_rows($select_admin) > 0) {
       $row = mysqli_fetch_assoc($select_admin);
       $_SESSION['admin_name'] = $row['email'];
       $_SESSION['admin_id'] = $row['id'];
       header('location:admin_patients.php'); // Chuyển đến trang admin
       exit(); // Dừng thực thi mã sau khi chuyển hướng

   } else {
       // Nếu không phải quản trị viên, kiểm tra trong bảng `nguoi_dung`
       $select_user = mysqli_query($conn, "SELECT * FROM `nguoi_dung` WHERE email = '$email' AND mat_khau = '$password'") or die('query failed');
       
       if (mysqli_num_rows($select_user) > 0) {
           $user = mysqli_fetch_assoc($select_user);
           // Kiểm tra vai trò của người dùng
           if ($user['vai_tro'] == 'benh_nhan') {
               // Nếu là bệnh nhân
               $_SESSION['patient_email'] = $user['email'];
               $_SESSION['patient_id'] = $user['id'];
               header('location:patients.php'); // Chuyển đến trang bệnh nhân
               exit();
           } elseif ($user['vai_tro'] == 'bac_si') {
               // Nếu là bác sĩ
               $_SESSION['doctor_email'] = $user['email'];
               $_SESSION['doctor_id'] = $user['id'];
               header('location:doctors.php'); // Chuyển đến trang bác sĩ
               exit();
           }
       } else {
           $message[] = 'Tên tài khoản hoặc mật khẩu không chính xác!';
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
if (isset($message)) {
    foreach ($message as $message) {
        echo '
        <div class="message">
           <span>' . $message . '</span>
           <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<div class="form-container">
   <form action="" method="post">
      <h3>Đăng nhập</h3>
      <input type="email" name="email" placeholder="Nhập tên tài khoản" required class="box">
      <input type="password" name="password" placeholder="Mật khẩu" required class="box">
      <input type="submit" name="submit" value="Đăng nhập" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
      <br>
   </form>
</div>

</body>
</html>
