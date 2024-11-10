<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:home.php');
}

// Thêm bệnh nhân mới
if (isset($_POST['add_patient'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $medical_history = mysqli_real_escape_string($conn, $_POST['medical_history']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $role = 'benh_nhan';

    // Kiểm tra email bệnh nhân đã tồn tại chưa
    $select_user = mysqli_query($conn, "SELECT email FROM `nguoi_dung` WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select_user) > 0) {
        $message[] = 'Bệnh nhân đã tồn tại!';
    } else {
        // Thêm bệnh nhân mới vào bảng `nguoi_dung`
        $add_user_query = mysqli_query($conn, "INSERT INTO `nguoi_dung` (ten, mat_khau, vai_tro, email, so_dien_thoai, dia_chi, ngay_sinh, gioi_tinh) VALUES('$name', '$password', '$role', '$email', '$phone', '$address', '$dob', '$gender')") or die('query failed');
        
        if ($add_user_query) {
            $patient_id = mysqli_insert_id($conn); // Lấy ID bệnh nhân vừa tạo
            // Thêm thông tin bổ sung vào bảng `benh_nhan`
            $add_patient_query = mysqli_query($conn, "INSERT INTO `benh_nhan` (id, nhom_mau, tien_su_benh) VALUES('$patient_id', '$blood_group', '$medical_history')") or die('query failed');
            
            if ($add_patient_query) {
                $message[] = 'Thêm bệnh nhân thành công!';
            } else {
                $message[] = 'Thêm bệnh nhân thất bại!';
            }
        }
    }
}

// Xóa bệnh nhân
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0") or die('query failed');

    mysqli_query($conn, "DELETE FROM `nguoi_dung` WHERE id = '$delete_id' AND vai_tro = 'benh_nhan'") or die('query failed');
    mysqli_query($conn, "DELETE FROM `benh_nhan` WHERE id = '$delete_id'") or die('query failed');

    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1") or die('query failed');

    header('location:admin_patients.php');
}

// Cập nhật thông tin bệnh nhân
if (isset($_POST['update_patient'])) {
    $update_id = $_POST['update_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_phone = mysqli_real_escape_string($conn, $_POST['update_phone']);
    $update_address = mysqli_real_escape_string($conn, $_POST['update_address']);
    $update_dob = $_POST['update_dob'];
    $update_gender = $_POST['update_gender'];
    $update_blood_group = $_POST['update_blood_group'];
    $update_medical_history = mysqli_real_escape_string($conn, $_POST['update_medical_history']);

    // Cập nhật thông tin bảng `nguoi_dung`
    mysqli_query($conn, "UPDATE `nguoi_dung` SET ten = '$update_name', email = '$update_email', so_dien_thoai = '$update_phone', dia_chi = '$update_address', ngay_sinh = '$update_dob', gioi_tinh = '$update_gender' WHERE id = '$update_id'") or die('query failed');

    // Cập nhật thông tin bảng `benh_nhan`
    mysqli_query($conn, "UPDATE `benh_nhan` SET nhom_mau = '$update_blood_group', tien_su_benh = '$update_medical_history' WHERE id = '$update_id'") or die('query failed');

    header('location:admin_patients.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bệnh nhân</title>

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
    <h1 class="title">Quản lý bệnh nhân</h1>

    <form action="" method="post">
        <h3>Thêm bệnh nhân</h3>
        <input type="text" name="name" class="box" placeholder="Tên bệnh nhân" required>
        <input type="email" name="email" class="box" placeholder="Email" required>
        <input type="text" name="phone" class="box" placeholder="Số điện thoại" required>
        <input type="text" name="address" class="box" placeholder="Địa chỉ" required>
        <input type="date" name="dob" class="box" placeholder="Ngày sinh" required>
        <select name="gender" class="box">
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
        </select>
        <select name="blood_group" class="box">
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="AB">AB</option>
            <option value="O">O</option>
        </select>
        <textarea name="medical_history" class="box" placeholder="Tiền sử bệnh"></textarea>
        <input type="password" name="password" class="box" placeholder="Mật khẩu" required>
        <input type="submit" value="Thêm bệnh nhân" name="add_patient" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
    </form>
</section>

<section class="show-patients">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Nhóm máu</th>
                    <th>Tiền sử bệnh</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_patients = mysqli_query($conn, "SELECT nguoi_dung.*, benh_nhan.nhom_mau, benh_nhan.tien_su_benh FROM nguoi_dung JOIN benh_nhan ON nguoi_dung.id = benh_nhan.id WHERE vai_tro = 'benh_nhan'") or die('query failed');
                if (mysqli_num_rows($select_patients) > 0) {
                    while ($fetch_patient = mysqli_fetch_assoc($select_patients)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_patient['id']; ?></td>
                            <td><?php echo $fetch_patient['ten']; ?></td>
                            <td><?php echo $fetch_patient['email']; ?></td>
                            <td><?php echo $fetch_patient['so_dien_thoai']; ?></td>
                            <td><?php echo $fetch_patient['dia_chi']; ?></td>
                            <td><?php echo $fetch_patient['ngay_sinh']; ?></td>
                            <td><?php echo ucfirst($fetch_patient['gioi_tinh']); ?></td>
                            <td><?php echo $fetch_patient['nhom_mau']; ?></td>
                            <td><?php echo $fetch_patient['tien_su_benh']; ?></td>
                            <td>
                                <a href="admin_patients.php?edit=<?php echo $fetch_patient['id']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-warning btn-sm">Sửa</a>
                                <a href="admin_patients.php?delete=<?php echo $fetch_patient['id']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Xóa bệnh nhân này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="10" class="text-center" style="font-size: 25px;">Không có bệnh nhân nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php if (isset($_GET['edit'])): 
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT nguoi_dung.*, benh_nhan.nhom_mau, benh_nhan.tien_su_benh FROM nguoi_dung JOIN benh_nhan ON nguoi_dung.id = benh_nhan.id WHERE nguoi_dung.id = '$edit_id'") or die('query failed');
    if (mysqli_num_rows($edit_query) > 0):
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<section class="add-products">
    <form action="" method="post">
        <h3>Cập nhật thông tin bệnh nhân</h3>
        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
        <input type="text" name="update_name" class="box" value="<?php echo $fetch_edit['ten']; ?>" required>
        <input type="email" name="update_email" class="box" value="<?php echo $fetch_edit['email']; ?>" required>
        <input type="text" name="update_phone" class="box" value="<?php echo $fetch_edit['so_dien_thoai']; ?>" required>
        <input type="text" name="update_address" class="box" value="<?php echo $fetch_edit['dia_chi']; ?>" required>
        <input type="date" name="update_dob" class="box" value="<?php echo $fetch_edit['ngay_sinh']; ?>" required>
        <select name="update_gender" class="box">
            <option value="male" <?php if ($fetch_edit['gioi_tinh'] == 'male') echo 'selected'; ?>>Nam</option>
            <option value="female" <?php if ($fetch_edit['gioi_tinh'] == 'female') echo 'selected'; ?>>Nữ</option>
        </select>
        <select name="update_blood_group" class="box">
            <option value="A" <?php if ($fetch_edit['nhom_mau'] == 'A') echo 'selected'; ?>>A</option>
            <option value="B" <?php if ($fetch_edit['nhom_mau'] == 'B') echo 'selected'; ?>>B</option>
            <option value="AB" <?php if ($fetch_edit['nhom_mau'] == 'AB') echo 'selected'; ?>>AB</option>
            <option value="O" <?php if ($fetch_edit['nhom_mau'] == 'O') echo 'selected'; ?>>O</option>
        </select>
        <textarea name="update_medical_history" class="box"><?php echo $fetch_edit['tien_su_benh']; ?></textarea>
        <input type="submit" value="Cập nhật" style="padding: 8px 10px; text-decoration: none; font-size: 14px;" name="update_patient" class="btn-primary">
        <a href="admin_patients.php" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-secondary">Hủy</a>
    </form>
</section>
<?php endif; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
