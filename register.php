<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mat_khau = mysqli_real_escape_string($conn, md5($_POST['mat_khau']));
    $cmat_khau = mysqli_real_escape_string($conn, md5($_POST['cmat_khau']));
    $vai_tro = 'benh_nhan';

    // Check if email already exists
    $select_user = mysqli_query($conn, "SELECT * FROM `nguoi_dung` WHERE email = '$email'") or die('Query failed');

    if (mysqli_num_rows($select_user) > 0) {
        $message[] = 'Email đã tồn tại!';
    } else {
        if ($mat_khau != $cmat_khau) {
            $message[] = 'Mật khẩu không khớp!';
        } else {
            // Insert into nguoi_dung table
            mysqli_query($conn, "INSERT INTO `nguoi_dung` (ten, mat_khau, vai_tro, email) VALUES('$ten', '$mat_khau', '$vai_tro', '$email')") or die('Query failed');

            // Get the last inserted user ID
            $last_id = mysqli_insert_id($conn);

            // Insert into benh_nhan table
            mysqli_query($conn, "INSERT INTO `benh_nhan` (id) VALUES('$last_id')") or die('Query failed');

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
        <input type="text" name="ten" placeholder="Nhập họ tên" required class="box">
        <input type="email" name="email" placeholder="Nhập email" required class="box">
        <input type="password" name="mat_khau" placeholder="Nhập mật khẩu" required class="box">
        <input type="password" name="cmat_khau" placeholder="Nhập lại mật khẩu" required class="box">
        <input type="submit" name="submit" value="Đăng ký ngay" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
        <p>Bạn đã có tài khoản? <a style="color: blue; text-decoration: none;" href="login.php">Đăng nhập</a></p>
    </form>
</div>

</body>
</html>
