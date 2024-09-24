<?php
include 'config.php';
session_start();

if (isset($_GET['MaNCC'])) {
    $MaNCC = $_GET['MaNCC'];
    $select_supplier = mysqli_query($conn, "SELECT * FROM `ncc` WHERE MaNCC = '$MaNCC'") or die('query failed');

    if (mysqli_num_rows($select_supplier) > 0) {
        $fetch_supplier = mysqli_fetch_assoc($select_supplier);
    } else {
        header('location:owner_suppliers.php');
    }
}

if (isset($_POST['update_supplier'])) {
    $TenNCC = mysqli_real_escape_string($conn, $_POST['TenNCC']);
    $DiaChi = mysqli_real_escape_string($conn, $_POST['DiaChi']);
    $SDT = mysqli_real_escape_string($conn, $_POST['SDT']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);

    $update_query = "UPDATE `ncc` SET TenNCC='$TenNCC', DiaChi='$DiaChi', SDT='$SDT', Email='$Email' WHERE MaNCC='$MaNCC'";
    mysqli_query($conn, $update_query) or die('query failed');

    header('location:owner_suppliers.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Nhà Cung Cấp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .form-container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<section class="form-container">
    <h1 style="text-align: center;" class="title">Sửa thông tin nhà cung cấp</h1>

    <form action="" method="post">
        <div class="mb-3">
            <label for="TenNCC" class="form-label">Tên Nhà Cung Cấp</label>
            <input type="text" name="TenNCC" id="TenNCC" class="form-control" value="<?php echo $fetch_supplier['TenNCC']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="DiaChi" class="form-label">Địa Chỉ</label>
            <input type="text" name="DiaChi" id="DiaChi" class="form-control" value="<?php echo $fetch_supplier['DiaChi']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="SDT" class="form-label">Số Điện Thoại</label>
            <input type="text" name="SDT" id="SDT" class="form-control" value="<?php echo $fetch_supplier['SDT']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="Email" class="form-label">Email</label>
            <input type="email" name="Email" id="Email" class="form-control" value="<?php echo $fetch_supplier['Email']; ?>" required>
        </div>
        <button type="submit" name="update_supplier" style="padding: 8px 12px; text-decoration: none; font-size: 14px; border-radius: 4px;" class="btn-primary">Lưu</button>
        <a href="owner_suppliers.php" style="padding: 10px; text-decoration: none; font-size: 14px; border-radius: 4px;" class="btn-secondary">Quay lại</a>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
