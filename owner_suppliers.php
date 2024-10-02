<?php

include 'config.php';
session_start();

$owner_id = $_SESSION['owner_id']; // Tạo session owner

if (!isset($owner_id)) {
    header('location:login.php'); // Nếu không tồn tại session owner thì chuyển về trang đăng nhập
}

if (isset($_POST['add_ncc'])) { // Thêm nhà cung cấp mới
    $TenNCC = mysqli_real_escape_string($conn, $_POST['TenNCC']);
    $DiaChi = mysqli_real_escape_string($conn, $_POST['DiaChi']);
    $SDT = mysqli_real_escape_string($conn, $_POST['SDT']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);

    // Kiểm tra nhà cung cấp đã tồn tại chưa
    $select_ncc = mysqli_query($conn, "SELECT TenNCC FROM `ncc` WHERE TenNCC = '$TenNCC'") or die('query failed');

    if (mysqli_num_rows($select_ncc) > 0) {
        $message[] = 'Nhà cung cấp đã tồn tại!';
    } else {
        // Thêm nhà cung cấp mới
        $add_ncc_query = mysqli_query($conn, "INSERT INTO `ncc` (TenNCC, DiaChi, SDT, Email) VALUES('$TenNCC', '$DiaChi', '$SDT', '$Email')") or die('query failed');

        if ($add_ncc_query) {
            $message[] = 'Thêm nhà cung cấp thành công!';
        } else {
            $message[] = 'Thêm nhà cung cấp thất bại!';
        }
    }
}

if (isset($_GET['delete'])) { // Xóa nhà cung cấp
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `ncc` WHERE MaNCC = '$delete_id'") or die('query failed');
    header('location:owner_suppliers.php');
}

if (isset($_POST['update_ncc'])) { // Cập nhật nhà cung cấp
    $update_MaNCC = $_POST['update_MaNCC'];
    $update_TenNCC = $_POST['update_TenNCC'];
    $update_DiaChi = $_POST['update_DiaChi'];
    $update_SDT = $_POST['update_SDT'];
    $update_Email = $_POST['update_Email'];

    // Cập nhật thông tin nhà cung cấp
    mysqli_query($conn, "UPDATE `ncc` SET TenNCC='$update_TenNCC', DiaChi='$update_DiaChi', SDT='$update_SDT', Email='$update_Email' WHERE MaNCC='$update_MaNCC'") or die('query failed');
    header('location:owner_suppliers.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhà cung cấp</title>

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

<?php include 'owner_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý nhà cung cấp</h1>

    <form action="" method="post">
        <h3>Thêm nhà cung cấp</h3>
        <input type="text" name="TenNCC" class="box" placeholder="Tên nhà cung cấp" required>
        <input type="text" name="DiaChi" class="box" placeholder="Địa chỉ" required>
        <input type="text" name="SDT" class="box" placeholder="Số điện thoại" required>
        <input type="email" name="Email" class="box" placeholder="Email" required>
        <input type="submit" value="Thêm" name="add_ncc" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;" class="btn-primary">
    </form>

</section>

<section class="show-products">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Mã Nhà Cung Cấp</th>
                    <th>Tên Nhà Cung Cấp</th>
                    <th>Địa Chỉ</th>
                    <th>Số Điện Thoại</th>
                    <th>Email</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_ncc = mysqli_query($conn, "SELECT * FROM `ncc`") or die('query failed');
                if (mysqli_num_rows($select_ncc) > 0) {
                    while ($fetch_ncc = mysqli_fetch_assoc($select_ncc)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_ncc['MaNCC']; ?></td>
                            <td><?php echo $fetch_ncc['TenNCC']; ?></td>
                            <td><?php echo $fetch_ncc['DiaChi']; ?></td>
                            <td><?php echo $fetch_ncc['SDT']; ?></td>
                            <td><?php echo $fetch_ncc['Email']; ?></td>
                            <td>
                                <a href="edit_supplier.php?MaNCC=<?php echo $fetch_ncc['MaNCC']; ?>" class="btn-warning btn-sm" style="padding: 10px; text-decoration: none; font-size: 14px;">Sửa</a>
                                <a href="owner_suppliers.php?delete=<?php echo $fetch_ncc['MaNCC']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Xóa nhà cung cấp này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center" style="font-size: 25px;">Không có nhà cung cấp nào!</td></tr>';
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