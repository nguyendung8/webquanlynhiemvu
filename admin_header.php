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
<link rel="stylesheet" href="./css/admin_style.css">
<header class="header">

   <div class="flex" style="padding: 0px !important;">

      <a href="admin_page.php" class="logo">
         <img width="80px" height="80px" src="./image/logo_course.png">
      </a>

      <nav style="margin-bottom: 0px !important;min-height: unset !important;" class="navbar">
         <a style="text-decoration: none !important;" href="admin_page.php">Trang chủ</a>
         <a style="text-decoration: none !important;" href="admin_tasks.php">Nhiệm vụ</a>
         <a style="text-decoration: none !important;" href="admin_users.php">Người dùng</a></a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="account-box">
         <p>Tên người dùng : <span><?php echo $_SESSION['admin_name']; ?></span></p>
         <a href="logout.php" class="delete-btn">Đăng xuất</a>
      </div>

   </div>

</header>