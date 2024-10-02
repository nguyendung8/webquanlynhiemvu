<?php
   if(isset($user_id)){ 
      $user_id = $_SESSION['user_id']; // tạo session người dùng
   }
   //nhúng vào các trang bán hàng
   if(isset($message)){//hiển thị thông báo sau khi thao tác với biến message được gán giá trị
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>';//đóng thẻ này
      }
   }
?>

<header class="header">
   <div class="header-2">
      <div class="flex">
         <a href="home.php" class="logo"><img width="120px" height="104px" src="./image/logo-tv.png"></a>

         <nav class="navbar">
            <a href="home.php">Trang chủ</a>
            <a href="shop.php">Cửa hàng</a>
            <a href="orders.php">Đơn hàng</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <?php
               if(isset($user_id)) {
            ?>
               <div id="user-btn" class="fas fa-user"></div>
            <?php } else { ?>
               <a href="login.php" style="color: blue; text-decoration: none; font-size: 20px;">Đăng nhập</a>
               <a href="register.php" style="color: blue; text-decoration: none; font-size: 20px;">Đăng ký</a>
            <?php } ?>
            <?php
               if(isset($user_id)) {
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $cart_rows_number = mysqli_num_rows($select_cart);
               $total= 0;
               while($fetch_total=mysqli_fetch_assoc($select_cart)){
                  $total+=$fetch_total['quantity'] * $fetch_total['price'];
               }
            ?>
            <a href="cart.php"> <i class="fas fa-shopping-cart"></i> <span>(<?php echo $cart_rows_number; ?>)</span> <span>(<?php echo number_format($total,0,',','.' ); ?> VND)</span> </a>
            <?php } ?>
         </div>

         <div class="user-box">
            <?php 
               if (isset($user_id)) {
            ?>
               <p>Tên người dùng : <span><?php echo $_SESSION['user_name']; ?></span></p>
               <a href="logout.php" class="delete-btn">Đăng xuất</a>
            <?php } ?>
         </div>
      </div>
   </div>

</header>