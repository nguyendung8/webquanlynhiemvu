<?php
include 'config.php';
session_start();

// Kiểm tra xem bác sĩ đã đăng nhập chưa
if (!isset($_SESSION['doctor_id'])) {
    header('location:home.php');
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Lấy thông tin bệnh nhân từ cơ sở dữ liệu
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    // Lấy thông tin cá nhân của bệnh nhân
    $query_patient = "SELECT * FROM nguoi_dung WHERE id = '$patient_id' AND vai_tro = 'benh_nhan' LIMIT 1";
    $result_patient = mysqli_query($conn, $query_patient);
    $patient = mysqli_fetch_assoc($result_patient);

    // Lấy thông tin sức khỏe cơ bản của bệnh nhân
    $query_health = "SELECT * FROM benh_nhan WHERE id = '$patient_id'";
    $result_health = mysqli_query($conn, $query_health);
    $health_info = mysqli_fetch_assoc($result_health);

    // Lấy lịch sử khám bệnh của bệnh nhân
    $query_history = "SELECT * FROM ho_so_benh_nhan WHERE benh_nhan_id = '$patient_id' AND bac_si_id = '$doctor_id' ORDER BY ngay_kham DESC";
    $result_history = mysqli_query($conn, $query_history);

    // Lấy lịch sử xét nghiệm của bệnh nhân
    $query_tests = "SELECT * FROM xet_nghiem WHERE benh_nhan_id = '$patient_id' AND bac_si_id = '$doctor_id' ORDER BY ngay_thuc_hien DESC";
    $result_tests = mysqli_query($conn, $query_tests);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hồ sơ bác sĩ</title>
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
            <i class="fas fa-arrow-left"></i>
            Quay lại
        </a>
        <h2 class="text-center mb-4">
            Hồ Sơ Bệnh Nhân
            <?php echo $patient['ten']; ?>
        </h2>

        <!-- Thông tin cơ bản -->
        <div class="card mb-4 box">
            <div class="card-header">
                <h5>Thông Tin Cá Nhân</h5>
            </div>
            <div class="card-body">
                <p><strong>Họ và tên:</strong> <?php echo $patient['ten']; ?></p>
                <p><strong>Giới tính:</strong> <?php echo $patient['gioi_tinh'] == 'male' ? 'Nam' : 'Nữ'; ?></p>
                <p><strong>Ngày sinh:</strong> <?php echo date("d/m/Y", strtotime($patient['ngay_sinh'])); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo $patient['so_dien_thoai']; ?></p>
                <p><strong>Email:</strong> <?php echo $patient['email']; ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo $patient['dia_chi']; ?></p>
            </div>
        </div>

        <!-- Thông tin sức khỏe -->
        <div class="card mb-4 box">
            <div class="card-header">
                <h5>Thông Tin Sức Khỏe Cơ Bản</h5>
            </div>
            <div class="card-body">
                <p><strong>Nhóm máu:</strong> <?php echo $health_info['nhom_mau']; ?></p>
                <p><strong>Tiền sử bệnh:</strong> <?php echo $health_info['tien_su_benh']; ?></p>
            </div>
        </div>

        <!-- Lịch sử khám bệnh -->
        <div class="card mb-4 box">
            <div class="card-header">
                <h5>Lịch Sử Khám Bệnh</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_history) > 0) { ?>
                    <ul class="list-group">
                        <?php while ($history = mysqli_fetch_assoc($result_history)) { ?>
                            <li class="list-group-item">
                                <strong>Ngày khám:</strong> <?php echo date("d/m/Y H:i", strtotime($history['ngay_kham'])); ?><br>
                                <strong>Chẩn đoán:</strong> <?php echo $history['chan_doan']; ?><br>
                                <strong>Kết quả xét nghiệm:</strong> <?php echo $history['ket_qua_xet_nghiem']; ?><br>
                                <strong>Ghi chú bác sĩ:</strong> <?php echo $history['ghi_chu_bac_si']; ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>Chưa có lịch sử khám bệnh.</p>
                <?php } ?>
            </div>
        </div>

        <!-- Lịch sử xét nghiệm -->
        <div class="card mb-4 box">
            <div class="card-header">
                <h5>Lịch Sử Xét Nghiệm</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_tests) > 0) { ?>
                    <ul class="list-group">
                        <?php while ($test = mysqli_fetch_assoc($result_tests)) { ?>
                            <li class="list-group-item">
                                <strong>Loại xét nghiệm:</strong> <?php echo $test['loai_xet_nghiem']; ?><br>
                                <strong>Ngày thực hiện:</strong> <?php echo date("d/m/Y H:i", strtotime($test['ngay_thuc_hien'])); ?><br>
                                <strong>Kết quả:</strong> <?php echo $test['ket_qua']; ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>Chưa có lịch sử xét nghiệm.</p>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
