<?php
include 'config.php';
session_start();

// Kiểm tra xem bác sĩ đã đăng nhập chưa
if (!isset($_SESSION['patient_id'])) {
    header('location:home.php');
    exit();
}

$patient_id = $_SESSION['patient_id'];

if (isset($patient_id)) {

    // Lấy thông tin cá nhân của bệnh nhân
    $query_patient = "SELECT * FROM nguoi_dung WHERE id = '$patient_id' AND vai_tro = 'benh_nhan' LIMIT 1";
    $result_patient = mysqli_query($conn, $query_patient);
    $patient = mysqli_fetch_assoc($result_patient);

    // Lấy lịch sử xét nghiệm của bệnh nhân
    $query_tests = "SELECT * FROM xet_nghiem WHERE benh_nhan_id = '$patient_id' ORDER BY ngay_thuc_hien DESC";
    $result_tests = mysqli_query($conn, $query_tests);

    // Lấy danh sách đơn thuốc của bệnh nhân
    $query_prescriptions = "SELECT * FROM don_thuoc WHERE benh_nhan_id = '$patient_id' ORDER BY ngay_ke DESC";
    $result_prescriptions = mysqli_query($conn, $query_prescriptions);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả khám bệnh</title>
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
        .list-group li {
            font-size: 16px;
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
    <h2 class="text-center mb-4">
        Danh Sách Kết Quả Khám Bệnh
    </h2>

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

    <!-- Lịch sử đơn thuốc -->
    <div class="card mb-4 box">
        <div class="card-header">
            <h5>Đơn Thuốc</h5>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result_prescriptions) > 0) { ?>
                <ul class="list-group">
                    <?php while ($prescription = mysqli_fetch_assoc($result_prescriptions)) { ?>
                        <li class="list-group-item">
                            <strong>Ngày kê:</strong> <?php echo date("d/m/Y H:i", strtotime($prescription['ngay_ke'])); ?><br>
                            <strong>Danh sách thuốc:</strong> <?php echo $prescription['danh_sach_thuoc']; ?><br>
                            <strong>Đơn giá:</strong> <?php echo number_format($prescription['don_gia']) . " VND"; ?><br>
                            <strong>Ghi chú:</strong> <?php echo $prescription['ghi_chu']; ?><br>
                            <strong>Trạng thái:</strong> <?php echo $prescription['trang_thai']; ?><br>

                            <?php if ($prescription['trang_thai'] == 'Chưa thanh toán') { ?>
                            <button type="button" class="btn btn-primary mt-2 fs-4" data-bs-toggle="modal" data-bs-target="#paymentModal-<?php echo $prescription['id']; ?>">
                                Thanh toán
                            </button>
                            <?php } ?>

                            <!-- Modal -->
                            <div class="modal fade" id="paymentModal-<?php echo $prescription['id']; ?>" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="paymentModalLabel">Thanh Toán Đơn Thuốc</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <p><strong>Số tiền cần thanh toán:</strong> <?php echo number_format($prescription['don_gia']) . " VND"; ?></p>
                                            <p>Quét mã QR dưới đây để thanh toán:</p>
                                            <img src="./image/qr_code.png" alt="QR Code" style="width: 200px; height: 200px;">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p>Chưa có đơn thuốc nào.</p>
            <?php } ?>
        </div>
    </div>
</div>

    <script src="js/script.js"></script>
    <script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
