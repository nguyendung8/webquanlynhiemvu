<?php
include 'config.php';
session_start();

if (!isset($_SESSION['patient_email'])) {
    header('location:home.php');
    exit();
}

$patient_email = $_SESSION['patient_email'];

// Lấy thông tin của bệnh nhân từ cơ sở dữ liệu
$query = "SELECT * FROM nguoi_dung WHERE email = '$patient_email' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $patient = mysqli_fetch_assoc($result);
    $patient_id = $patient['id'];
    $ten = $patient['ten'];
    $gioi_tinh = $patient['gioi_tinh'];
    $ngay_sinh = $patient['ngay_sinh'];
    $so_dien_thoai = $patient['so_dien_thoai'];
    $dia_chi = $patient['dia_chi'];
    $email = $patient['email'];

    // Lấy thêm thông tin từ bảng benh_nhan
    $benh_nhan_query = "SELECT * FROM benh_nhan WHERE id = '$patient_id' LIMIT 1";
    $benh_nhan_result = mysqli_query($conn, $benh_nhan_query);
    $benh_nhan = mysqli_fetch_assoc($benh_nhan_result);
    $nhom_mau = $benh_nhan['nhom_mau'];
    $tien_su_benh = $benh_nhan['tien_su_benh'];
}

if (isset($_POST['update'])) {
    // Cập nhật thông tin hồ sơ cá nhân
    $new_name = mysqli_real_escape_string($conn, $_POST['ten']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['so_dien_thoai']);
    $new_address = mysqli_real_escape_string($conn, $_POST['dia_chi']);
    $new_blood_type = mysqli_real_escape_string($conn, $_POST['nhom_mau']);
    $new_medical_history = mysqli_real_escape_string($conn, $_POST['tien_su_benh']);
    
    // Cập nhật vào bảng nguoi_dung
    $update_user_query = "UPDATE nguoi_dung SET ten = '$new_name', email = '$new_email', so_dien_thoai = '$new_phone', dia_chi = '$new_address' WHERE id = '$patient_id'";
    mysqli_query($conn, $update_user_query);

    // Cập nhật vào bảng benh_nhan
    $update_patient_query = "UPDATE benh_nhan SET nhom_mau = '$new_blood_type', tien_su_benh = '$new_medical_history' WHERE id = '$patient_id'";
    mysqli_query($conn, $update_patient_query);

    // Thông báo cập nhật thành công
    $message[] = 'Cập nhật thông tin thành công!!';
   header('location:patient_profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hồ sơ cá nhân</title>
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
</section>
    <div class="container my-5">
        <h2 class="text-center mb-4">Quản lý hồ sơ cá nhân</h2>

        <!-- Form cập nhật thông tin bệnh nhân -->
        <form method="POST">
            <div class="mb-3">
                <label for="ten" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="ten" name="ten" value="<?php echo $ten; ?>" required>
            </div>

            <div class="mb-3">
                <label for="gioi_tinh" class="form-label">Giới tính</label>
                <input type="text" class="form-control" id="gioi_tinh" name="gioi_tinh" value="<?php echo $gioi_tinh == 'male' ? 'Nam' : 'Nữ'  ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" value="<?php echo $ngay_sinh; ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>

            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" value="<?php echo $so_dien_thoai; ?>" required>
            </div>

            <div class="mb-3">
                <label for="dia_chi" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="dia_chi" name="dia_chi" value="<?php echo $dia_chi; ?>">
            </div>
<!-- 
            <div class="mb-3">
                <label for="nhom_mau" class="form-label">Nhóm máu</label>
                <input type="text" class="form-control" id="nhom_mau" name="nhom_mau" value="<?php echo $nhom_mau; ?>">
            </div> -->

            <div class="mb-3">
                <label for="tien_su_benh" class="form-label">Tiền sử bệnh</label>
                <textarea class="form-control" id="tien_su_benh" name="tien_su_benh" rows="3"><?php echo $tien_su_benh; ?></textarea>
            </div>

            <button type="submit" name="update"  class="new-btn btn-primary">Cập nhật</button>
        </form>
    </div>

    <script src="js/script.js"></script>
<script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
