<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['patient_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:home.php');
   }


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang chủ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .box p {
         font-size: 17px;
         padding-bottom: 5px;
      }
      .action {
         display: flex;
         align-items: center;
      }
      .view-product {
         margin-top: 5px;
         padding: 5px 20px;
         background-color: burlywood;
         font-size: 16px;
         color: #fff;
         border-radius: 6px;
      }
      .view-product:hover {
         opacity: 0.9;
      }
      .slideshow-container {
         position: relative;
         max-width: 800px;
         margin: 0 auto;
         overflow: hidden; /* Để ẩn phần ngoài khung hình ảnh */
      }
      .slide {
         display: none;
         animation: fade 2s ease-in-out infinite; /* Sử dụng animation để thêm hiệu ứng lướt sang */
      }
      @keyframes fade {
         0%, 100% {
            opacity: 0;
         }
         25%, 75% {
            opacity: 1;
         }
      }
      .slide img {
         width: 100%;
         height: 485px;
         border-radius: 9px;
      }
      .borrow_book:hover { 
         opacity: 0.9;
      }
      .borrow_book {
         padding: 5px 25px;
         background-image: linear-gradient(to right, #ff9800, #F7695D);
         border-radius: 4px;
         cursor: pointer;
         font-size: 20px;
         color: #fff;
         font-weight: 700;
      }
      .home-banner {
         min-height: 70vh;
         background:linear-gradient(rgba(0,0,0,.1), rgba(0,0,0,.1)), url(./image/home_background.png) no-repeat;
         background-size: cover;
         background-position: center;
         display: flex;
         align-items: center;
         justify-content: center;
      }
      .title {
         font-size: 30px;
         color: #00695c;
         text-align: center;
         margin: 20px 0;
         font-weight: bold;
      }

      /* Phong cách cho box dịch vụ */
      .service-container {
         display: flex;
         justify-content: center;
         gap: 40px;
         padding: 20px 0;
      }
      .service-box {
         width: 300px;
         padding: 20px;
         border: 1px solid #ddd;
         border-radius: 8px;
         text-align: center;
         background-color: #fff;
         transition: all 0.3s ease;
         box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      }
      .service-box:hover {
         transform: translateY(-5px);
         box-shadow: 0px 6px 12px rgba(0,0,0,0.15);
      }
      .service-box i {
         font-size: 50px;
         color: #009688;
         margin-bottom: 15px;
      }
      .service-box a {
         font-size: 18px;
         font-weight: bold;
         color: #333;
         text-decoration: none;
         display: block;
         margin-top: 10px;
      }
      .service-box a:hover {
         color: #009688;
      }

      /* Phong cách cho danh sách tin tức */
      .news-container {
         max-width: 900px;
         margin: 0 auto;
         padding: 20px 0;
      }
      .news-item {
         display: flex;
         gap: 15px;
         padding: 15px;
         border-bottom: 1px solid #ddd;
         transition: all 0.3s ease;
         align-items: center;
      }
      .news-item:hover {
         background-color: #f0f0f0;
      }
      .news-img {
         width: 100px;
         height: 70px;
         border-radius: 6px;
         overflow: hidden;
      }
      .news-img img {
         width: 100%;
         height: 100%;
         object-fit: cover;
      }
      .news-details {
         display: flex;
         flex-direction: column;
      }
      .news-details h4 {
         font-size: 18px;
         margin: 0;
         font-weight: bold;
         color: #333;
      }
      .news-details p {
         font-size: 14px;
         color: #666;
         margin: 5px 0 0;
      }
   </style>
</head>
<body>
   
<?php include 'patient_header.php'; ?>

<section class="home home-banner">

<div class="content">
      <div class="slideshow-container">
         <div class="slide fade">
            <img src="./image/slider_1.jpg" alt="slide 1">
         </div>
         <div class="slide fade">
            <img src="./image/slider_2.jpg" alt="slide 2">
         </div>
         <div class="slide fade">
            <img src="./image/slider_3.jpg" alt="slide 3">
         </div>
         <div class="slide fade">
            <img src="./image/slider_4.jpg" alt="slide 3">
         </div>
      </div>
   </div>

</section>

<section class="home-service">
   <h1 class="title">Dịch Vụ Trực Tuyến</h1>

   <div class="service-container">
      <div class="service-box">
         <i class="fas fa-calendar-check"></i>
         <a href="./patient_schedule.php">Đặt lịch hẹn khám bệnh</a>
      </div>
      <div class="service-box">
         <i class="fas fa-search"></i>
         <a href="./patient_result_test.php">Tra cứu kết quả xét nghiệm</a>
      </div>
   </div>
</section>

<section class="news">
   <h1 class="title">Tin Tức Mới Nhất</h1>
   
   <div class="news-container">
      <?php
         $news_query = mysqli_query($conn, "SELECT * FROM `tin_tuc` ORDER BY ngay_dang DESC LIMIT 5") or die('query failed');
         if(mysqli_num_rows($news_query) > 0){
            while($news = mysqli_fetch_assoc($news_query)){
      ?>
         <div class="news-item">
            <div class="news-img">
               <img src="uploaded_img/<?php echo $news['hinh_anh']; ?>" alt="News Image">
            </div>
            <div class="news-details">
               <h4><?php echo $news['tieu_de']; ?></h4>
               <p><?php echo date("d/m/Y", strtotime($news['ngay_dang'])); ?></p>
            </div>
         </div>
      <?php
            }
         } else {
            echo '<p class="text-center">Chưa có tin tức nào.</p>';
         }
      ?>
   </div>
</section>

<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>