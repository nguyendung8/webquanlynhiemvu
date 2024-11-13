<?php
   //nhúng vào các trang bán hàng
   if(isset($message)){ // hiển thị thông báo sau khi thao tác với biến message được gán giá trị
      foreach($message as $msg){
         echo '
         <div class=" alert alert-info alert-dismissible fade show" role="alert">
            <span style="font-size: 16px;">'.$msg.'</span>
            <i style="font-size: 20px; cursor: pointer" class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>';
      }
   }
?>
<style>
   .header .flex .navbar a:hover,
   .header .flex .icons > *:hover,
   .header .flex .user-box p span {
    color: #0D6EFD !important;
}
</style>

<link rel="stylesheet" href="./css/new_style.css">

<header class="header">

   <div class="flex" style="padding: 10px 0 !important;">

      <div class="logo">
         <img width="70px" src="./image/logo-hospital.png" alt="">
      </div>

      <nav style="margin-bottom: 0px !important; min-height: unset !important;" class="navbar">
         <a style="text-decoration: none !important;" href="admin_doctors.php">Bác sĩ</a>
         <a style="text-decoration: none !important;" href="admin_patients.php">Bệnh nhân</a>
         <a style="text-decoration: none !important;" href="admin_schedule.php">Phân lịch làm việc</a>
         <a style="text-decoration: none !important;" href="admin_payment.php">Thanh toán</a>
         <a style="text-decoration: none !important;" href="admin_evaluate.php">Đánh giá</a>
         <a style="text-decoration: none !important;" href="admin_new.php">Tin tức</a>
      </nav>

      <a style="text-decoration: none" href="logout.php" class="delete-btn">Đăng xuất</a>

   </div>

</header>
