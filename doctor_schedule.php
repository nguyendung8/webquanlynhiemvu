<?php
include 'config.php';
session_start();

// Kiểm tra xem bác sĩ đã đăng nhập chưa
if (!isset($_SESSION['doctor_id'])) {
    header('location:home.php');
    exit();
}

$doctor_email = $_SESSION['doctor_email'];

// Lấy ID bác sĩ từ bảng nguoi_dung
$query = "SELECT id FROM nguoi_dung WHERE email = '$doctor_email' AND vai_tro = 'bac_si' LIMIT 1";
$result = mysqli_query($conn, $query);
$doctor = mysqli_fetch_assoc($result);
$doctor_id = $doctor['id'];

// Thêm lịch làm việc mới
if (isset($_POST['add_schedule'])) {
    $start_date = $_POST['ngay_bat_dau'];
    $end_date = $_POST['ngay_ket_thuc'];
    $status = $_POST['trang_thai'];

    $insert_query = "INSERT INTO lich_lam_viec (bac_si_id, ngay_bat_dau, ngay_ket_thuc, trang_thai) VALUES ('$doctor_id', '$start_date', '$end_date', '$status')";
    mysqli_query($conn, $insert_query);
    header('location: doctor_schedule.php');
    exit();
}

// Lấy danh sách lịch làm việc
$schedule_query = "SELECT * FROM lich_lam_viec WHERE bac_si_id = '$doctor_id' ORDER BY ngay_bat_dau DESC";
$schedule_result = mysqli_query($conn, $schedule_query);

// Lấy danh sách lịch hẹn với bác sĩ
$appointment_query = "SELECT lh.*, bn.ten AS ten_benh_nhan, bn.so_dien_thoai AS so_dien_thoai_benh_nhan 
                      FROM lich_hen lh 
                      JOIN nguoi_dung bn ON lh.benh_nhan_id = bn.id 
                      WHERE lh.bac_si_id = '$doctor_id' 
                      ORDER BY lh.thoi_gian DESC";
$appointment_result = mysqli_query($conn, $appointment_query);

// Cập nhật trạng thái lịch hẹn
if (isset($_POST['update_appointment_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['trang_thai'];
    $update_query = "UPDATE lich_hen SET trang_thai = '$new_status' WHERE id = '$appointment_id' AND bac_si_id = '$doctor_id'";
    mysqli_query($conn, $update_query);
    $message[] = "Cập nhật trạng thái lịch hẹn thành công.";
    header('location: doctor_schedule.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý lịch làm việc và lịch hẹn</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h2 class="text-center mb-4">Quản lý lịch làm việc và lịch hẹn</h2>

        <!-- Form thêm lịch làm việc -->
        <div class="card mb-5">
            <div class="card-body">
                <h4 class="card-title">Thêm lịch làm việc mới</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu</label>
                        <input type="datetime-local" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" required>
                    </div>
                    <div class="mb-3">
                        <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc</label>
                        <input type="datetime-local" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
                    </div>
                    <div class="mb-3">
                        <label for="trang_thai" class="form-label">Trạng thái</label>
                        <select class="form-select" id="trang_thai" name="trang_thai" required>
                            <option value="co_san">Có sẵn</option>
                            <option value="dang_ban">Đang bận</option>
                        </select>
                    </div>
                    <button type="submit" name="add_schedule" class="new-btn btn-primary">Thêm lịch làm việc</button>
                </form>
            </div>
        </div>

        <!-- Hiển thị lịch làm việc -->
        <h4 class="mb-3">Danh sách lịch làm việc</h4>
        <table class="table table-bordered mb-5">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; ?>
                <?php while ($schedule = mysqli_fetch_assoc($schedule_result)): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($schedule['ngay_bat_dau'])); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($schedule['ngay_ket_thuc'])); ?></td>
                        <td><?php echo $schedule['trang_thai'] == 'co_san' ? 'Có sẵn' : 'Đang bận'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Hiển thị lịch hẹn -->
        <h4 class="mb-3">Danh sách lịch hẹn với bệnh nhân</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Bệnh nhân</th>
                    <th>Thời gian hẹn</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; ?>
                <?php while ($appointment = mysqli_fetch_assoc($appointment_result)): ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><?php echo $appointment['ten_benh_nhan']; ?><br><small><?php echo $appointment['so_dien_thoai_benh_nhan']; ?></small></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($appointment['thoi_gian'])); ?></td>
                        <td>
                            <?php
                            switch ($appointment['trang_thai']) {
                                case 'cho_xac_nhan': echo 'Chờ xác nhận'; break;
                                case 'da_xac_nhan': echo 'Đã xác nhận'; break;
                                case 'huy': echo 'Đã hủy'; break;
                                case 'hoan_thanh': echo 'Hoàn thành'; break;
                            }
                            ?>
                        </td>
                        <td><?php echo $appointment['ghi_chu']; ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                <select name="trang_thai" class="form-select form-select-sm mb-2">
                                    <option value="cho_xac_nhan" <?php if ($appointment['trang_thai'] == 'cho_xac_nhan') echo 'selected'; ?>>Chờ xác nhận</option>
                                    <option value="da_xac_nhan" <?php if ($appointment['trang_thai'] == 'da_xac_nhan') echo 'selected'; ?>>Đã xác nhận</option>
                                    <option value="huy" <?php if ($appointment['trang_thai'] == 'huy') echo 'selected'; ?>>Hủy</option>
                                    <option value="hoan_thanh" <?php if ($appointment['trang_thai'] == 'hoan_thanh') echo 'selected'; ?>>Hoàn thành</option>
                                </select>
                                <button type="submit" name="update_appointment_status" style="padding: 5px 7px; font-size: 15px;" class="btn btn-primary btn-sm">Cập nhật</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="js/script.js"></script>
    <script src="js/slide_show.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script></body>
</html>
