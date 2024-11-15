<?php
include 'config.php';
session_start();

   $news_id = $_GET['new_id']; // Lấy ID của tin tức từ URL
   $query = mysqli_query($conn, "SELECT * FROM tin_tuc WHERE id = '$news_id'") or die('Query failed');
   if(mysqli_num_rows($query) == 0) {
      header('location: home.php'); // Nếu không tìm thấy tin tức thì quay về trang chủ
      exit;
   }
   $news = mysqli_fetch_assoc($query); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem chi tiết tin tức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="./css/new_style.css">
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
      .news-title {
         font-size: 32px;
         color: #00695c;
         margin-bottom: 10px;
         font-weight: bold;
      }
      .news-meta {
         font-size: 14px;
         color: #888;
         margin-bottom: 20px;
      }
      .news-meta span {
         margin-right: 10px;
      }
      .news-content {
         font-size: 18px;
         line-height: 1.6;
         color: #333;
         margin-bottom: 20px;
      }
      .news-img img {
         width: 70%;
         height: auto;
         border-radius: 8px;
         margin-bottom: 20px;
      }
      .back-btn {
         padding: 10px 20px;
         background-color: #009688;
         color: #fff;
         font-size: 16px;
         border: none;
         border-radius: 4px;
         cursor: pointer;
         text-decoration: none;
      }
      .back-btn:hover {
         background-color: #00796b;
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
   <div class="container my-5">
      <h1 class="news-title"><?php echo $news['tieu_de']; ?></h1>
      <div class="news-meta">
         <span><i class="fas fa-calendar-alt"></i> <?php echo date("d/m/Y", strtotime($news['ngay_dang'])); ?></span>
      </div>
      <div class="news-img">
         <img src="uploaded_img/<?php echo $news['hinh_anh']; ?>" alt="News Image">
      </div>
   <div class="news-content">
      <?php echo nl2br($news['noi_dung']); ?>
   </div>
   <a href="home.php" class="back-btn">Quay lại trang chủ</a>
</div>

    <script src="js/script.js"></script>
   <script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
