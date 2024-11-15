<?php
include 'config.php';
session_start();

// Kiểm tra xem bác sĩ đã đăng nhập chưa
if (!isset($_SESSION['doctor_id'])) {
    header('location:login.php');
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

// Lấy thông tin bệnh nhân
if ($patient_id) {
    $query_patient = "SELECT * FROM nguoi_dung WHERE id = '$patient_id' AND vai_tro = 'benh_nhan' LIMIT 1";
    $result_patient = mysqli_query($conn, $query_patient);
    $patient = mysqli_fetch_assoc($result_patient);
}

// Xử lý form kê đơn thuốc
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $danh_sach_thuoc = mysqli_real_escape_string($conn, $_POST['danh_sach_thuoc']);
    $don_gia = mysqli_real_escape_string($conn, $_POST['don_gia']);
    $ghi_chu = mysqli_real_escape_string($conn, $_POST['ghi_chu']);
    $ngay_ke = date("Y-m-d H:i:s");

    $query_prescription = "INSERT INTO don_thuoc (benh_nhan_id, bac_si_id, ngay_ke, danh_sach_thuoc, don_gia, ghi_chu) VALUES ('$patient_id', '$doctor_id', '$ngay_ke', '$danh_sach_thuoc', '$don_gia', '$ghi_chu')";

    if (mysqli_query($conn, $query_prescription)) {
        $message[] = "Đơn thuốc đã được lưu thành công!";
    } else {
        $message[] = "Có lỗi xảy ra. Vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn thuốc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="./css/new_style.css">
    <style>
        .box p {
            font-size: 17px;
            padding-bottom: 5px;
        }
        h5 {
            color: blue !important;
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
            overflow: hidden;
        }
        .slide {
            display: none;
            animation: fade 2s ease-in-out infinite;
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
    </style>
</head>
<body>
    <?php include 'doctor_header.php'; ?>

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
    <a class="fs-3" href="./doctor_view_profile.php">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
    <h2 class="text-center mb-4">Kê Đơn Thuốc Cho Bệnh Nhân: <?php echo htmlspecialchars($patient['ten']); ?></h2>

    <form action="" method="POST">
        <div class="mb-3">
            <label for="danh_sach_thuoc" class="form-label">Danh Sách Thuốc</label>
            <textarea class="form-control" id="danh_sach_thuoc" name="danh_sach_thuoc" rows="4" required></textarea>
            <small class="form-text text-muted fs-3">Ghi rõ tên thuốc, liều lượng và cách dùng.</small>
        </div>
        <div class="mb-3">
            <label for="danh_sach_thuoc" class="form-label">Tổng đơn giá (VND)</label>
            <input type="text" class="form-label" name="don_gia" rows="4" required>
        </div>
        <div class="mb-3">
            <label for="ghi_chu" class="form-label">Ghi Chú</label>
            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3"></textarea>
        </div>
        <button type="submit" class="new-btn btn-primary">Lưu Đơn Thuốc</button>
    </form>
</div>

    <script src="js/script.js"></script>
    <script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
