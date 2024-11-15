<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Thêm bác sĩ mới
if (isset($_POST['add_doctor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);
    $experience = $_POST['experience'];
    $short_intro = mysqli_real_escape_string($conn, $_POST['short_intro']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $role = 'bac_si';

    // Kiểm tra email bác sĩ đã tồn tại chưa
    $select_user = mysqli_query($conn, "SELECT email FROM `nguoi_dung` WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select_user) > 0) {
        $message[] = 'Bác sĩ đã tồn tại!';
    } else {
        // Thêm bác sĩ mới vào bảng `nguoi_dung`
        $add_user_query = mysqli_query($conn, "INSERT INTO `nguoi_dung` (ten, mat_khau, vai_tro, email, so_dien_thoai, dia_chi, ngay_sinh, gioi_tinh) VALUES('$name', '$password', '$role', '$email', '$phone', '$address', '$dob', '$gender')") or die('query failed');
        
        if ($add_user_query) {
            $doctor_id = mysqli_insert_id($conn); // Lấy ID bác sĩ vừa tạo
            // Thêm thông tin bổ sung vào bảng `bac_si`
            $add_doctor_query = mysqli_query($conn, "INSERT INTO `bac_si` (id, chuyen_khoa, kinh_nghiem, gioi_thieu_ngan_gon) VALUES('$doctor_id', '$specialty', '$experience', '$short_intro')") or die('query failed');
            
            if ($add_doctor_query) {
                $message[] = 'Thêm bác sĩ thành công!';
            } else {
                $message[] = 'Thêm bác sĩ thất bại!';
            }
        }
    }
}

// Xóa bác sĩ
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    
    // Vô hiệu hóa kiểm tra khóa ngoại
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0") or die('query failed');
    
    // Xóa bác sĩ khỏi bảng `nguoi_dung` và `bac_si`
    mysqli_query($conn, "DELETE FROM `nguoi_dung` WHERE id = '$delete_id' AND vai_tro = 'bac_si'") or die('query failed');
    mysqli_query($conn, "DELETE FROM `bac_si` WHERE id = '$delete_id'") or die('query failed');
    
    // Bật lại kiểm tra khóa ngoại
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1") or die('query failed');
    
    header('location:admin_doctors.php');
}

// Cập nhật thông tin bác sĩ
if (isset($_POST['update_doctor'])) {
    $update_id = $_POST['update_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_phone = mysqli_real_escape_string($conn, $_POST['update_phone']);
    $update_chuyen_khoa = mysqli_real_escape_string($conn, $_POST['update_chuyen_khoa']);
    $update_kinh_nghiem = $_POST['update_kinh_nghiem'];
    $update_gioi_thieu_ngan_gon = mysqli_real_escape_string($conn, $_POST['update_gioi_thieu_ngan_gon']);

    mysqli_query($conn, "UPDATE `nguoi_dung` SET ten = '$update_name', email = '$update_email', so_dien_thoai = '$update_phone' WHERE id = '$update_id'") or die('query failed');
    mysqli_query($conn, "UPDATE `bac_si` SET chuyen_khoa = '$update_chuyen_khoa', kinh_nghiem = '$update_kinh_nghiem', gioi_thieu_ngan_gon = '$update_gioi_thieu_ngan_gon' WHERE id = '$update_id'") or die('query failed');

    header('location:admin_doctors.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bác sĩ</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
      th {
         font-size: 20px;
         text-align: center;
      }
      td {
         font-size: 18px;
         padding: 1.5rem 0.5rem !important;
         text-align: center;
      }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý bác sĩ</h1>

    <form action="" method="post">
        <h3>Thêm bác sĩ</h3>
        <input type="text" name="name" class="box" placeholder="Tên bác sĩ" required>
        <input type="email" name="email" class="box" placeholder="Email" required>
        <input type="text" name="phone" class="box" placeholder="Số điện thoại" required>
        <input type="text" name="address" class="box" placeholder="Địa chỉ" required>
        <input type="date" name="dob" class="box" placeholder="Ngày sinh" required>
        <select name="gender" class="box">
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
        </select>
        <input type="text" name="specialty" class="box" placeholder="Chuyên khoa" required>
        <input type="number" name="experience" class="box" placeholder="Kinh nghiệm (năm)" required>
        <textarea name="short_intro" class="box" placeholder="Giới thiệu ngắn gọn"></textarea>
        <input type="password" name="password" class="box" placeholder="Mật khẩu" required>
        <input type="submit" value="Thêm bác sĩ" name="add_doctor" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
    </form>
</section>

<section class="show-doctors">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Giới tính</th>
                    <th>Chuyên khoa</th>
                    <th>Kinh nghiệm</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_doctors = mysqli_query($conn, "SELECT nguoi_dung.*, bac_si.chuyen_khoa, bac_si.kinh_nghiem, bac_si.gioi_thieu_ngan_gon FROM nguoi_dung JOIN bac_si ON nguoi_dung.id = bac_si.id WHERE vai_tro = 'bac_si'") or die('query failed');
                if (mysqli_num_rows($select_doctors) > 0) {
                    while ($fetch_doctor = mysqli_fetch_assoc($select_doctors)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_doctor['id']; ?></td>
                            <td><?php echo $fetch_doctor['ten']; ?></td>
                            <td><?php echo $fetch_doctor['email']; ?></td>
                            <td><?php echo $fetch_doctor['so_dien_thoai']; ?></td>
                            <td><?php echo ucfirst($fetch_doctor['gioi_tinh']); ?></td>
                            <td><?php echo $fetch_doctor['chuyen_khoa']; ?></td>
                            <td><?php echo $fetch_doctor['kinh_nghiem']; ?> năm</td>
                            <td>
                                <a href="admin_doctors.php?edit=<?php echo $fetch_doctor['id']; ?>"style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-warning btn-sm">Sửa</a>
                                <a href="admin_doctors.php?delete=<?php echo $fetch_doctor['id']; ?>"  style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bác sĩ này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="11" class="text-center">Chưa có bác sĩ nào.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php if (isset($_GET['edit'])): 
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT nguoi_dung.*, bac_si.chuyen_khoa, bac_si.kinh_nghiem FROM nguoi_dung JOIN bac_si ON nguoi_dung.id = bac_si.id WHERE nguoi_dung.id = '$edit_id'") or die('query failed');
    if (mysqli_num_rows($edit_query) > 0):
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<section class="add-products">
    <form action="" method="post">
        <h3>Cập nhật thông tin bác sĩ</h3>
        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
        <input type="text" name="update_name" class="box" value="<?php echo $fetch_edit['ten']; ?>" required>
        <input type="email" name="update_email" class="box" value="<?php echo $fetch_edit['email']; ?>" required>
        <input type="text" name="update_phone" class="box" value="<?php echo $fetch_edit['so_dien_thoai']; ?>" required>
        <input type="text" name="update_chuyen_khoa" class="box" value="<?php echo $fetch_edit['chuyen_khoa']; ?>" required>
        <input type="number" name="update_kinh_nghiem" class="box" value="<?php echo $fetch_edit['kinh_nghiem']; ?>" required>
        <input type="submit" value="Cập nhật" style="padding: 8px 10px; text-decoration: none; font-size: 14px;" name="update_doctor" class="btn-primary">
        <a href="admin_doctors.php" style="padding: 10px; text-decoration: none; font-size: 14px;"  class="btn-secondary">Hủy</a>
    </form>
</section>
<?php endif; endif; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
