<?php
// Kết nối cơ sở dữ liệu
include 'config.php'; 

if(isset($_GET['TenTK'])) {
    $TenTK = $_GET['TenTK'];
} else {
    die('Không tìm thấy tài khoản!');
}

if(isset($_POST['submit'])) {
    $new_password = mysqli_real_escape_string($conn, md5($_POST['new_password']));
    $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm_password']));

    if($new_password != $confirm_password) {
        $message = "Mật khẩu không trùng khớp!";
    } else {
        // Mã hóa mật khẩu mới trước khi lưu
        $new_password_hashed = md5($new_password);

        // Cập nhật mật khẩu mới
        $update_password = mysqli_query($conn, "UPDATE `taikhoan` SET MatKhau = '$new_password_hashed' WHERE TenTK = '$TenTK'") or die('query failed');

        if($update_password) {
            $message = "Mật khẩu đã được cập nhật!";
        } else {
            $message = "Đổi mật khẩu thất bại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Đổi mật khẩu cho tài khoản: <?php echo $TenTK; ?></h2>
        <?php if(isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="new_password">Mật khẩu mới:</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Đổi Mật Khẩu</button>
            <a href="admin_accounts.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
