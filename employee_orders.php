<?php
include 'config.php';
session_start();

$sale_id = $_SESSION['sale_id'];

if (!isset($sale_id)) {
    header('location:login.php');
}

if (isset($_POST['add_order'])) {
    $MaSach = mysqli_real_escape_string($conn, $_POST['MaSach']);
    $MaNV = mysqli_real_escape_string($conn, $_POST['MaNV']);
    $TenKH = mysqli_real_escape_string($conn, $_POST['TenKH']);
    $SoLuong = mysqli_real_escape_string($conn, $_POST['SoLuong']);
    $ThanhTien = mysqli_real_escape_string($conn, $_POST['ThanhTien']);
    $NgayLap = mysqli_real_escape_string($conn, $_POST['NgayLap']);

    $add_order_query = mysqli_query($conn, "INSERT INTO `DonDatHang` (MaSach, MaNV, TenKH, SoLuong, ThanhTien, TrangThai, NgayLap) VALUES('$MaSach', '$MaNV', '$TenKH', '$SoLuong', '$ThanhTien','Chưa xác nhận', '$NgayLap')") or die('query failed');

    if ($add_order_query) {
        $message[] = 'Thêm đơn hàng thành công!';
    } else {
        $message[] = 'Thêm đơn hàng thất bại!';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `DonDatHang` WHERE MaDonDatHang = '$delete_id'") or die('query failed');
    header('location:employee_orders.php');
}

if (isset($_GET['confirm'])) {
    $confirm_id = $_GET['confirm'];
    mysqli_query($conn, "UPDATE `DonDatHang` SET TrangThai = 'Xác nhận' WHERE MaDonDatHang = '$confirm_id'") or die('query failed');
    header('location:employee_orders.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
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

<?php include 'sale_employee_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý đơn hàng</h1>

    <form action="" method="post">
        <h3>Thêm đơn hàng</h3>
        <select name="MaSach" class="box" required>
            <option value="">Chọn sách</option>
            <?php
            $select_sach = mysqli_query($conn, "SELECT * FROM `Sach`") or die('query failed');
            while ($row_sach = mysqli_fetch_assoc($select_sach)) {
                echo "<option value='{$row_sach['MaSach']}'>{$row_sach['TenSach']}</option>";
            }
            ?>
        </select>

        <select name="MaNV" class="box" required>
            <option value="">Chọn nhân viên</option>
            <?php
            $select_nv = mysqli_query($conn, "SELECT * FROM `NhanVien`") or die('query failed');
            while ($row_nv = mysqli_fetch_assoc($select_nv)) {
                echo "<option value='{$row_nv['MaNV']}'>{$row_nv['TenNV']}</option>";
            }
            ?>
        </select>

        <input type="text" name="TenKH" class="box" placeholder="Tên khách hàng" required>
        <input type="number" name="SoLuong" class="box" placeholder="Số lượng" required>
        <input type="number" name="ThanhTien" class="box" placeholder="Thành tiền" required>
        <input type="date" name="NgayLap" class="box" placeholder="Ngày lập" required>

        <input type="submit" value="Thêm đơn hàng" name="add_order" class="btn-primary" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;">
    </form>
</section>

<section class="show-orders">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Tên Sách</th>
                    <th>Nhân Viên</th>
                    <th>Khách Hàng</th>
                    <th>Số Lượng</th>
                    <th>Thành Tiền</th>
                    <th>Ngày Lập</th>
                    <th>Trạng Thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_order = mysqli_query($conn, "SELECT * FROM `DonDatHang`") or die('query failed');
                if (mysqli_num_rows($select_order) > 0) {
                    while ($fetch_order = mysqli_fetch_assoc($select_order)) {
                        $sach = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenSach FROM Sach WHERE MaSach = '{$fetch_order['MaSach']}'"));
                        $nhanvien = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenNV FROM NhanVien WHERE MaNV = '{$fetch_order['MaNV']}'"));

                        ?>
                        <tr>
                            <td><?php echo $sach['TenSach']; ?></td>
                            <td><?php echo $nhanvien['TenNV']; ?></td>
                            <td><?php echo $fetch_order['TenKH']; ?></td>
                            <td><?php echo $fetch_order['SoLuong']; ?></td>
                            <td><?php echo $fetch_order['ThanhTien']; ?> VND</td>
                            <td><?php echo $fetch_order['NgayLap']; ?></td>
                            <td><?php echo $fetch_order['TrangThai'] == 'Xác nhận' ? 'Đã xác nhận' : 'Chưa xác nhận'; ?></td>
                            <td>
                                <?php
                                     if ($fetch_order['TrangThai'] == 'Chưa xác nhận') {
                                ?>
                                    <a href="employee_orders.php?confirm=<?php echo $fetch_order['MaDonDatHang']; ?>" class="btn-success btn-sm" style="padding: 10px; text-decoration: none; font-size: 14px;">Xác nhận</a>
                                <?php
                                     }
                                ?>
                                <a href="employee_orders.php?delete=<?php echo $fetch_order['MaDonDatHang']; ?>" class="btn-danger btn-sm" style="padding: 10px; text-decoration: none; font-size: 14px;" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="8">Không có đơn hàng nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

</body>
</html>
