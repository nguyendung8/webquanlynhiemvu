<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['update_cart'])){//cập nhật giỏ hàng từ form submit name='update_cart'
      $cart_id = $_POST['cart_id'];
      $cart_quantity = $_POST['cart_quantity'];
      mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
      $message[] = 'Giỏ hàng đã được cập nhật!';
   }

   if(isset($_GET['delete'])){//xóa sách khỏi giỏ hàng từ onclick href='delete'
      $delete_id = $_GET['delete'];
      mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('query failed');
      header('location:cart.php');
   }

   if(isset($_GET['delete_all'])){//xóa tất cả sách khỏi giỏ hàng của người dùng từ onclick href='delete_all'
      mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      header('location:cart.php');
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Giỏ hàng</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .head {
         background: url(./images/head_img.jpg) no-repeat;
         background-size: cover;
         background-position: center;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="shopping-cart">

   <h1 class="title">Sách đã được thêm vào giỏ hàng</h1>

   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');//lấy ra giỏ hàng tương ứng với id người dùng
         if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){ 
               $name_product = $fetch_cart['name'];
               $select_quantity = mysqli_query($conn, "SELECT * FROM `Sach` WHERE TenSach='$name_product'");
               $fetch_quantity = mysqli_fetch_assoc($select_quantity); 
               $select_price = mysqli_query($conn, 
                  "SELECT DonGia.DonGia 
                  FROM `Sach` 
                  JOIN `DonGia` ON Sach.MaDonGia = DonGia.MaDonGia 
                  WHERE Sach.TenSach = '$name_product'") or die('query failed');

               $fetch_price = mysqli_fetch_assoc($select_price);
               $price = $fetch_price['DonGia'];
         ?>
               <div style="height: -webkit-fill-available;" class="box">
                  <a style="width: 25px; height: 25px; display: flex; align-items: center; justify-content: center" href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Xóa khỏi giỏ hàng?');"></a>
                  <div style="margin-top: 8px" class="name"><?php echo $fetch_cart['name']; ?></div>
                  <div class="price"><?php echo number_format($fetch_cart['price'],0,',','.' ); ?> VND / quyển</div>
                  <form action="" method="post">
                     <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                     <input type="number" min="1" max="<?=$fetch_quantity['SoLuong']?>" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                     <input type="submit" name="update_cart" value="Cập nhật" class="option-btn">
                  </form>
                  <div class="sub-total"> Số tiền : <span><?php $sub_total = ($fetch_cart['quantity'] * $price); echo number_format($sub_total,0,',','.' ) ?> VND</span> </div>
               </div>
      <?php
               $grand_total += $sub_total;
            }
         }else{
            echo '<p class="empty">Giỏ hàng của bạn trống!</p>';
         }
      ?>
   </div>

   <div style="margin-top: 2rem; text-align:center;">
      <a href="cart.php?delete_all" class="delete-btn" onclick="return confirm('Xóa tất cả giỏ hàng?');">Xóa tất cả</a>
   </div>

   <div class="cart-total">
      <p>Tổng tiền : <span><?php echo number_format($grand_total,0,',','.' ); ?> VND</span></p>
      <div class="flex">
         <a href="shop.php" class="option-btn">Tiếp tục mua hàng</a>
         <a href="checkout.php" class="btn">Tiến hành thanh toán</a>
      </div>
   </div>

</section>


<script src="js/script.js"></script>

</body>
</html>