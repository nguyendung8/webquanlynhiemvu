<?php
   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thông tin</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Về Comic</h3>
   <p> <a href="home.php">Trang chủ</a> / Thông tin </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>Tại sao lại có Comic.</h3>
         <p>Từ những bạn trẻ đam mê đọc những quyển truyện trinh thám và tiểu thuyết, chúng mình đã xây dựng nên website này để giúp mọi người có thể dễ dàng tìm kiếm và mua được những quyển truyện yêu thích của mình.</p>
         <p>Qua một thời gian phát triển, Comic mong muốn mang đến những quyển truyện hay, sâu sắc và hấp dẫn đến tay các bạn đọc.</p>
         <a href="contact.php" class="btn">Liên hệ</a>
      </div>

   </div>

</section>


<section class="authors">

   <h1 class="title">Thành viên của Comic</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/author-2.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-instagram"></a>
         </div>
         <h3>Mina</h3>
      </div>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>