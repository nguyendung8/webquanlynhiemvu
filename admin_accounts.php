<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id']; // Tạo session admin

if (!isset($admin_id)) {
    header('location:login.php'); // Nếu không tồn tại session admin thì chuyển về trang đăng nhập
}

if (isset($_POST['add_account'])) { // Thêm tài khoản mới
    $TenTK = mysqli_real_escape_string($conn, $_POST['TenTK']);
    $MatKhau = mysqli_real_escape_string($conn, md5($_POST['MatKhau']));
    $MaPhanQuyen = $_POST['MaPhanQuyen'];

    // Kiểm tra tài khoản đã tồn tại chưa
    $select_account = mysqli_query($conn, "SELECT TenTK FROM `TaiKhoan` WHERE TenTK = '$TenTK'") or die('query failed');

    if (mysqli_num_rows($select_account) > 0) {
        $message[] = 'Tài khoản đã tồn tại!';
    } else {
        // Thêm tài khoản mới
        $add_account_query = mysqli_query($conn, "INSERT INTO `TaiKhoan` (TenTK, MatKhau, MaPhanQuyen) VALUES('$TenTK', '$MatKhau', '$MaPhanQuyen')") or die('query failed');

        if ($add_account_query) {
            $message[] = 'Thêm tài khoản thành công!';
        } else {
            $message[] = 'Thêm tài khoản thất bại!';
        }
    }
}

if (isset($_GET['delete'])) { // Xóa tài khoản
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `TaiKhoan` WHERE TenTK = '$delete_id'") or die('query failed');
    header('location:admin_accounts.php');
}

if (isset($_POST['update_account'])) { // Cập nhật tài khoản
    $update_TenTK = $_POST['update_TenTK'];
    $update_MaPhanQuyen = $_POST['update_MaPhanQuyen'];

    // Cập nhật thông tin tài khoản
    mysqli_query($conn, "UPDATE `TaiKhoan` SET MaPhanQuyen='$update_MaPhanQuyen' WHERE TenTK='$update_TenTK'") or die('query failed');
    header('location:admin_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="./css/style.css">
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
    <h1 class="title">Quản lý tài khoản</h1>

    <form action="" method="post">
        <h3>Thêm tài khoản</h3>
        <input type="text" name="TenTK" class="box" placeholder="Tên tài khoản" required>
        <input type="password" name="MatKhau" class="box" placeholder="Mật khẩu" required>
        <select name="MaPhanQuyen" class="box">
            <?php
            $select_roles = mysqli_query($conn, "SELECT * FROM `PhanQuyen` WHERE MaPhanQuyen != 1 AND MaPhanQuyen != 6") or die('query failed');
            if (mysqli_num_rows($select_roles) > 0) {
                while ($fetch_roles = mysqli_fetch_assoc($select_roles)) {
                    echo "<option value='" . $fetch_roles['MaPhanQuyen'] . "'>" . $fetch_roles['TenPhanQuyen'] . "</option>";
                }
            } else {
                echo "<option>Không có phân quyền nào.</option>";
            }
            ?>
        </select>
        <input type="submit" value="Thêm" name="add_account" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;" class="btn-primary">
    </form>

</section>

<section class="show-products">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                     <th>Mã Tài Khoản</th>
                     <th>Tên Tài Khoản</th>
                     <th>Phân Quyền</th>
                     <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_accounts = mysqli_query($conn, "SELECT * FROM `TaiKhoan` WHERE MaPhanQuyen != 1 AND MaPhanQuyen != 6") or die('query failed');
                if (mysqli_num_rows($select_accounts) > 0) {
                    while ($fetch_accounts = mysqli_fetch_assoc($select_accounts)) {
                        // Lấy tên phân quyền dựa vào MaPhanQuyen
                        $MaPhanQuyen = $fetch_accounts['MaPhanQuyen'];
                        $result = mysqli_query($conn, "SELECT TenPhanQuyen FROM `phanquyen` WHERE MaPhanQuyen = '$MaPhanQuyen'") or die('query failed');
                        $role_name = mysqli_fetch_assoc($result);
                ?>
                        <tr>
                           <td><?php echo $fetch_accounts['MaTK']; ?></td>
                           <td><?php echo $fetch_accounts['TenTK']; ?></td>
                           <td><?php echo $role_name['TenPhanQuyen']; ?></td>
                           <td>
                           <a href="change_password.php?TenTK=<?php echo $fetch_accounts['TenTK']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-primary btn-sm">Đổi Mật Khẩu</a>
                           <a href="admin_accounts.php?delete=<?php echo $fetch_accounts['TenTK']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Xóa tài khoản này?');">Xóa</a>
                           </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center" style="font-size: 25px;">Không có tài khoản nào được thêm!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/admin_script.js"></script>

</body>
</html>
