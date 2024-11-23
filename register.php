<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm_password']));

    // Kiểm tra email đã tồn tại chưa
    $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('Query failed');

    if (mysqli_num_rows($select_user) > 0) {
        $message[] = 'Email đã tồn tại!';
    } else {
        if ($password != $confirm_password) {
            $message[] = 'Mật khẩu không khớp!';
        } else {
            // Thêm tài khoản vào bảng `users`
            mysqli_query($conn, "INSERT INTO `users` (name, email, password, created_at) VALUES('$name', '$email', '$password', NOW())") or die('Query failed');
            $message[] = 'Đăng ký thành công!';
            header('location:login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="d-flex justify-content-between align-items-center alert alert-info alert-dismissible fade show" role="alert">
            <span style="font-size: 16px;">' . $msg . '</span>
            <i style="font-size: 20px; cursor: pointer" class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<div class="form-container">
    <form action="" method="post">
        <h3>Đăng ký</h3>
        <input type="text" name="name" placeholder="Nhập họ tên" required class="box">
        <input type="email" name="email" placeholder="Nhập email" required class="box">
        <input type="password" name="password" placeholder="Nhập mật khẩu" required class="box">
        <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required class="box">
        <input type="submit" name="submit" value="Đăng ký ngay" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
        <p>Bạn đã có tài khoản? <a style="color: blue; text-decoration: none;" href="login.php">Đăng nhập</a></p>
    </form>
</div>

</body>
</html>
