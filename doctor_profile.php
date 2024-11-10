<?php
include 'config.php';
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['doctor_id'])) {
    header('location:home.php');
    exit();
}

$doctor_email = $_SESSION['doctor_email'];

// Lấy thông tin của bác sĩ từ cơ sở dữ liệu
$query = "SELECT * FROM nguoi_dung WHERE email = '$doctor_email' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $doctor = mysqli_fetch_assoc($result);
    $doctor_id = $doctor['id'];
    $ten = $doctor['ten'];
    $gioi_tinh = $doctor['gioi_tinh'];
    $ngay_sinh = $doctor['ngay_sinh'];
    $so_dien_thoai = $doctor['so_dien_thoai'];
    $dia_chi = $doctor['dia_chi'];
    $email = $doctor['email'];

    // Lấy thêm thông tin từ bảng bac_si
    $doctor_query = "SELECT * FROM bac_si WHERE id = '$doctor_id' LIMIT 1";
    $doctor_result = mysqli_query($conn, $doctor_query);
    $doctor_details = mysqli_fetch_assoc($doctor_result);
    $chuyen_khoa = $doctor_details['chuyen_khoa'];
    $kinh_nghiem = $doctor_details['kinh_nghiem'];
    $gioi_thieu_ngan_gon = $doctor_details['gioi_thieu_ngan_gon'];
}

if (isset($_POST['update'])) {
    // Cập nhật thông tin hồ sơ bác sĩ
    $new_name = mysqli_real_escape_string($conn, $_POST['ten']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['so_dien_thoai']);
    $new_address = mysqli_real_escape_string($conn, $_POST['dia_chi']);
    $new_specialization = mysqli_real_escape_string($conn, $_POST['chuyen_khoa']);
    $new_experience = mysqli_real_escape_string($conn, $_POST['kinh_nghiem']);
    $new_intro = mysqli_real_escape_string($conn, $_POST['gioi_thieu_ngan_gon']);
    
    // Cập nhật vào bảng nguoi_dung
    $update_user_query = "UPDATE nguoi_dung SET ten = '$new_name', email = '$new_email', so_dien_thoai = '$new_phone', dia_chi = '$new_address' WHERE id = '$doctor_id'";
    mysqli_query($conn, $update_user_query);

    // Cập nhật vào bảng bac_si
    $update_doctor_query = "UPDATE bac_si SET chuyen_khoa = '$new_specialization', kinh_nghiem = '$new_experience', gioi_thieu_ngan_gon = '$new_intro' WHERE id = '$doctor_id'";
    mysqli_query($conn, $update_doctor_query);

    // Thông báo cập nhật thành công
    $message[] = 'Cập nhật thông tin thành công!';
    header('location:doctor_profile.php');
    exit();
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
        <h2 class="text-center mb-4">Quản lý hồ sơ bác sĩ</h2>

        <!-- Form cập nhật thông tin bác sĩ -->
        <form method="POST">
            <div class="mb-3">
                <label for="ten" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="ten" name="ten" value="<?php echo $ten; ?>" required>
            </div>

            <div class="mb-3">
                <label for="gioi_tinh" class="form-label">Giới tính</label>
                <input type="text" class="form-control" id="gioi_tinh" name="gioi_tinh" value="<?php echo $gioi_tinh == 'male' ? 'Nam' : 'Nữ' ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" value="<?php echo date("Y-m-d", strtotime($ngay_sinh)); ?>" disabled>
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

            <div class="mb-3">
                <label for="chuyen_khoa" class="form-label">Chuyên khoa</label>
                <input type="text" class="form-control" id="chuyen_khoa" name="chuyen_khoa" value="<?php echo $chuyen_khoa; ?>" required>
            </div>

            <div class="mb-3">
                <label for="kinh_nghiem" class="form-label">Kinh nghiệm (năm)</label>
                <input type="number" class="form-control" id="kinh_nghiem" name="kinh_nghiem" value="<?php echo $kinh_nghiem; ?>" required>
            </div>

            <div class="mb-3">
                <label for="gioi_thieu_ngan_gon" class="form-label">Giới thiệu ngắn gọn</label>
                <textarea class="form-control" id="gioi_thieu_ngan_gon" name="gioi_thieu_ngan_gon" rows="3"><?php echo $gioi_thieu_ngan_gon; ?></textarea>
            </div>

            <button type="submit" name="update" class="new-btn btn-primary">Cập nhật</button>
        </form>
    </div>

    <script src="js/script.js"></script>
    <script src="js/slide_show.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
