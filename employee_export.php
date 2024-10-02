<?php
include 'config.php';
session_start();

$warehouse_id = $_SESSION['warehouse_id'];

if (!isset($warehouse_id)) {
    header('location:login.php');
}

if (isset($_POST['add_export_receipt'])) {
    $MaNV = mysqli_real_escape_string($conn, $_POST['MaNV']);
    $NgayLap = mysqli_real_escape_string($conn, $_POST['NgayLap']);
    $TenKH = mysqli_real_escape_string($conn, $_POST['TenKH']);
    $MaSach = mysqli_real_escape_string($conn, $_POST['MaSach']);
    $SoLuong = mysqli_real_escape_string($conn, $_POST['SoLuong']);
    $ThanhTien = mysqli_real_escape_string($conn, $_POST['ThanhTien']);
    $MoTa = mysqli_real_escape_string($conn, $_POST['MoTa']);

    $add_export_query = mysqli_query($conn, "INSERT INTO `PhieuXuat` (MaNV, NgayLap, TenKH, MaSach, SoLuong, ThanhTien, MoTa) VALUES('$MaNV', '$NgayLap', '$TenKH', '$MaSach', '$SoLuong', '$ThanhTien', '$MoTa')") or die('query failed');

    if ($add_export_query) {
        $message[] = 'Thêm phiếu xuất thành công!';
    } else {
        $message[] = 'Thêm phiếu xuất thất bại!';
    }
}

// Xóa phiếu xuất
if (isset($_GET['delete'])) {
    $MaPX = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `PhieuXuat` WHERE MaPhieuXuat = '$MaPX'") or die('query failed');
    header('location:employee_export.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phiếu xuất</title>
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

<?php include 'warehouse_employee_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý phiếu xuất</h1>

    <form action="" method="post">
        <h3>Thêm phiếu xuất</h3>

        <select name="MaNV" class="box" required>
            <option value="">Chọn nhân viên</option>
            <?php
            $select_nv = mysqli_query($conn, "SELECT * FROM `NhanVien`") or die('query failed');
            while ($row_nv = mysqli_fetch_assoc($select_nv)) {
                echo "<option value='{$row_nv['MaNV']}'>{$row_nv['TenNV']}</option>";
            }
            ?>
        </select>

        <input type="date" name="NgayLap" class="box" placeholder="Ngày lập" required>
        <input type="text" name="TenKH" class="box" placeholder="Tên khách hàng" required>

        <select name="MaSach" class="box" required>
            <option value="">Chọn sách</option>
            <?php
            $select_sach = mysqli_query($conn, "SELECT * FROM `Sach`") or die('query failed');
            while ($row_sach = mysqli_fetch_assoc($select_sach)) {
                echo "<option value='{$row_sach['MaSach']}'>{$row_sach['TenSach']}</option>";
            }
            ?>
        </select>

        <input type="number" name="SoLuong" class="box" placeholder="Số lượng" required>
        <input type="number" name="ThanhTien" class="box" placeholder="Thành tiền" required>
        <textarea name="MoTa" class="box" placeholder="Mô tả" rows="4" required></textarea>

        <input type="submit" value="Thêm phiếu xuất" name="add_export_receipt" class="btn-primary" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;">
    </form>
</section>

<section class="show-products">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nhân Viên</th>
                    <th>Ngày Lập</th>
                    <th>Tên Khách Hàng</th>
                    <th>Sách</th>
                    <th>Số Lượng</th>
                    <th>Thành Tiền</th>
                    <th>Mô Tả</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_export_receipt = mysqli_query($conn, "SELECT * FROM `PhieuXuat`") or die('query failed');
                if (mysqli_num_rows($select_export_receipt) > 0) {
                    while ($fetch_export_receipt = mysqli_fetch_assoc($select_export_receipt)) {
                        $sach = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenSach FROM Sach WHERE MaSach = '{$fetch_export_receipt['MaSach']}'"));
                        $nhanvien = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenNV FROM NhanVien WHERE MaNV = '{$fetch_export_receipt['MaNV']}'"));
                        ?>
                        <tr>
                            <td><?php echo $nhanvien['TenNV']; ?></td>
                            <td><?php echo $fetch_export_receipt['NgayLap']; ?></td>
                            <td><?php echo $fetch_export_receipt['TenKH']; ?></td>
                            <td><?php echo $sach['TenSach']; ?></td>
                            <td><?php echo $fetch_export_receipt['SoLuong']; ?></td>
                            <td><?php echo $fetch_export_receipt['ThanhTien']; ?> VND</td>
                            <td><?php echo $fetch_export_receipt['MoTa']; ?></td>
                            <td>
                                <a href="employee_export.php?delete=<?php echo $fetch_export_receipt['MaPhieuXuat']; ?>" class="btn-danger btn-sm" style="padding: 10px; text-decoration: none; font-size: 14px;" onclick="return confirm('Bạn có chắc muốn xóa phiếu xuất này không?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="8">Không có phiếu xuất nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

</body>
</html>
