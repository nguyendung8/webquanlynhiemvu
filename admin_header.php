<?php
   //nhúng vào các trang quản trị
   if(isset($message)){
      foreach($message as $message){//in ra thông báo trên cùng khi biến message được gán giá trị từ các trang quản trị
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>';
      }
   }
?>

<header class="header">

   <div class="flex" style="padding: 10px 0 !important;">

      <div class="logo">
         <img width="90px" src="./image/gym-logo.png" alt="">
      </div>

      <nav style="margin-bottom: 0px !important; min-height: unset !important;" class="navbar">
         <a style="text-decoration: none !important;" href="admin_members.php">Quản lý thành viên</a>
         <a style="text-decoration: none !important;" href="admin_trainers.php">Quản lý huấn luyện viên</a>
      </nav>

      <a style="text-decoration: none" href="logout.php" class="delete-btn">Đăng xuất</a>

   </div>

</header>