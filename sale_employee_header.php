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
         <img width="110px" src="./image/logo-tv.png" alt="">
      </div>

      <nav style="margin-bottom: 0px !important;min-height: unset !important;" class="navbar">
         <a style="text-decoration: none !important;" href="employee_orders.php">Quản lý đơn hàng</a>
      </nav>

      <a style="text-decoration: none" href="logout.php" class="delete-btn">Đăng xuất</a>

   </div>

</header>