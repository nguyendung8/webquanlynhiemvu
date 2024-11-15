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

    // Lấy kết quả xét nghiệm cuối cùng của bệnh nhân
    $query_last_test = "SELECT * FROM xet_nghiem WHERE benh_nhan_id = '$patient_id' AND bac_si_id = '$doctor_id' ORDER BY ngay_thuc_hien DESC LIMIT 1";
    $result_last_test = mysqli_query($conn, $query_last_test);
    $last_test = mysqli_fetch_assoc($result_last_test);
}

// Xử lý form thêm/sửa kết quả xét nghiệm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loai_xet_nghiem = mysqli_real_escape_string($conn, $_POST['loai_xet_nghiem']);
    $ngay_thuc_hien = mysqli_real_escape_string($conn, $_POST['ngay_thuc_hien']);
    $ket_qua = mysqli_real_escape_string($conn, $_POST['ket_qua']);

    if ($last_test) {
        // Cập nhật kết quả xét nghiệm nếu đã có sẵn
        $query_update_test = "UPDATE xet_nghiem SET loai_xet_nghiem = '$loai_xet_nghiem', ngay_thuc_hien = '$ngay_thuc_hien', ket_qua = '$ket_qua' WHERE id = '{$last_test['id']}'";
        if (mysqli_query($conn, $query_update_test)) {
            $message[] = "Kết quả xét nghiệm đã được cập nhật thành công!";
        } else {
            $message[] = "Có lỗi xảy ra. Vui lòng thử lại.";
        }
    } else {
        // Thêm mới kết quả xét nghiệm nếu chưa có
        $query_insert_test = "INSERT INTO xet_nghiem (benh_nhan_id, bac_si_id, loai_xet_nghiem, ngay_thuc_hien, ket_qua) VALUES ('$patient_id', '$doctor_id', '$loai_xet_nghiem', '$ngay_thuc_hien', '$ket_qua')";
        if (mysqli_query($conn, $query_insert_test)) {
            $message[] = "Kết quả xét nghiệm mới đã được lưu thành công!";
        } else {
            $message[] = "Có lỗi xảy ra. Vui lòng thử lại.";
        }
    }
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
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
    <h2 class="text-center mb-4">Cập nhật Kết Quả Xét Nghiệm Cho Bệnh Nhân: <?php echo htmlspecialchars($patient['ten']); ?></h2>

    <form action="" method="POST">
        <div class="mb-3">
            <label for="loai_xet_nghiem" class="form-label">Loại Xét Nghiệm</label>
            <input type="text" class="form-control" id="loai_xet_nghiem" name="loai_xet_nghiem" required value="<?php echo isset($last_test['loai_xet_nghiem']) ? htmlspecialchars($last_test['loai_xet_nghiem']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="ngay_thuc_hien" class="form-label">Ngày Thực Hiện</label>
            <input type="datetime-local" class="form-control" id="ngay_thuc_hien" name="ngay_thuc_hien" required value="<?php echo isset($last_test['ngay_thuc_hien']) ? date('Y-m-d\TH:i', strtotime($last_test['ngay_thuc_hien'])) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="ket_qua" class="form-label">Kết Quả</label>
            <textarea class="form-control" id="ket_qua" name="ket_qua" rows="4" required><?php echo isset($last_test['ket_qua']) ? htmlspecialchars($last_test['ket_qua']) : ''; ?></textarea>
        </div>
        <button type="submit" class="new-btn btn-primary">Lưu Kết Quả Xét Nghiệm</button>
    </form>
</div>

    <script src="js/script.js"></script>
    <script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
