<?php
include 'config.php';
session_start();

if (!isset($_SESSION['doctor_email'])) {
    header('location: login.php');
    exit();
}

$doctor_email = $_SESSION['doctor_email'];

// Lấy ID bác sĩ từ bảng nguoi_dung
$query = "SELECT id FROM nguoi_dung WHERE email = '$doctor_email' AND vai_tro = 'bac_si' LIMIT 1";
$result = mysqli_query($conn, $query);
$doctor = mysqli_fetch_assoc($result);
$doctor_id = $doctor['id'];

// Lấy danh sách bệnh nhân có lịch hẹn với bác sĩ này
$appointments_query = "SELECT lh.*, nd.ten AS ten_benh_nhan, nd.email AS email_benh_nhan
                       FROM lich_hen lh 
                       JOIN nguoi_dung nd ON lh.benh_nhan_id = nd.id 
                       WHERE lh.bac_si_id = '$doctor_id' 
                       ORDER BY lh.thoi_gian DESC";
$appointments_result = mysqli_query($conn, $appointments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hồ sơ bệnh nhân</title>
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
        <h2 class="text-center mb-4">Danh sách bệnh nhân</h2>

        <!-- Hiển thị danh sách bệnh nhân -->
        <?php if (mysqli_num_rows($appointments_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>STT</th>
                            <th>Tên bệnh nhân</th>
                            <th>Email bệnh nhân</th>
                            <th>Thời gian hẹn</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $stt = 1; ?>
                        <?php while ($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td><?php echo $appointment['ten_benh_nhan']; ?></td>
                                <td><?php echo $appointment['email_benh_nhan']; ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($appointment['thoi_gian'])); ?></td>
                                <td>
                                    <?php
                                        switch ($appointment['trang_thai']) {
                                            case 'cho_xac_nhan':
                                                echo "Chờ xác nhận";
                                                break;
                                            case 'da_xac_nhan':
                                                echo "Đã xác nhận";
                                                break;
                                            case 'huy':
                                                echo "Đã hủy";
                                                break;
                                            case 'hoan_thanh':
                                                echo "Hoàn thành";
                                                break;
                                        }
                                    ?>
                                </td>
                                <td class="action justify-content-center">
                                    <a href="doctor_view_patient_profile.php?patient_id=<?php echo $appointment['benh_nhan_id']; ?> " style="font-size: 15px;" class="new-btn btn-info view-product">Xem hồ sơ</a>
                                    <a href="doctor_medicine.php?patient_id=<?php echo $appointment['benh_nhan_id']; ?>" style="margin-left: 10px; font-size: 15px;" class="new-btn btn-primary view-product">Kê đơn thuốc</a>
                                    <a href="doctor_test.php?patient_id=<?php echo $appointment['benh_nhan_id']; ?>" style="margin-left: 10px; font-size: 15px;" class="new-btn btn-warning view-product">Xét nghiệm</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center fs-2">Không có lịch hẹn nào.</p>
        <?php endif; ?>
    </div>


    <script src="js/script.js"></script>
    <script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
