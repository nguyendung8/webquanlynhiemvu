<?php
include 'config.php';
session_start();

$owner_id = $_SESSION['owner_id'];

if (!isset($owner_id)) {
    header('location:login.php');
}

if (isset($_POST['add_book'])) {
    $TenSach = mysqli_real_escape_string($conn, $_POST['TenSach']);
    $MaTacGia = mysqli_real_escape_string($conn, $_POST['MaTacGia']);
    $MaTheLoai = mysqli_real_escape_string($conn, $_POST['MaTheLoai']);
    $MaNXB = mysqli_real_escape_string($conn, $_POST['MaNXB']);
    $SoLuong = mysqli_real_escape_string($conn, $_POST['SoLuong']);
    $DonViTinh = mysqli_real_escape_string($conn, $_POST['DonViTinh']);
    $MaDonGia = mysqli_real_escape_string($conn, $_POST['MaDonGia']);
    $MaNCC = mysqli_real_escape_string($conn, $_POST['MaNCC']);
    $MaNgan = mysqli_real_escape_string($conn, $_POST['MaNgan']);

    $add_book_query = mysqli_query($conn, "INSERT INTO `sach` (TenSach, MaTacGia, MaTheLoai, MaNXB, SoLuong, DonViTinh, MaDonGia, MaNCC, MaNgan) VALUES('$TenSach', '$MaTacGia', '$MaTheLoai', '$MaNXB', '$SoLuong', '$DonViTinh', '$MaDonGia', '$MaNCC', '$MaNgan')") or die('query failed');

    if ($add_book_query) {
        $message[] = 'Thêm sách thành công!';
    } else {
        $message[] = 'Thêm sách thất bại!';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        mysqli_query($conn, "DELETE * FROM `sach` WHERE MaSach = '$delete_id'") or die('query failed');
        header('location:owner_books.php');
    } catch (mysqli_sql_exception $e) {
        $message[] = 'Không thể xóa sách này!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sách</title>
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
    <h1 class="title">Quản lý sách</h1>

    <form action="" method="post">
        <h3>Thêm sách</h3>
        <input type="text" name="TenSach" class="box" placeholder="Tên sách" required>
        
        <select name="MaTacGia" class="box" required>
            <option value="">Chọn tác giả</option>
            <?php
            $select_tacgia = mysqli_query($conn, "SELECT * FROM `TacGia`") or die('query failed');
            while ($row_tacgia = mysqli_fetch_assoc($select_tacgia)) {
                echo "<option value='{$row_tacgia['MaTG']}'>{$row_tacgia['TenTG']}</option>";
            }
            ?>
        </select>

        <select name="MaTheLoai" class="box" required>
            <option value="">Chọn thể loại</option>
            <?php
            $select_theloai = mysqli_query($conn, "SELECT * FROM `TheLoai`") or die('query failed');
            while ($row_theloai = mysqli_fetch_assoc($select_theloai)) {
                echo "<option value='{$row_theloai['MaTheLoai']}'>{$row_theloai['TenTheLoai']}</option>";
            }
            ?>
        </select>

        <select name="MaNXB" class="box" required>
            <option value="">Chọn nhà xuất bản</option>
            <?php
            $select_nxb = mysqli_query($conn, "SELECT * FROM `NXB`") or die('query failed');
            while ($row_nxb = mysqli_fetch_assoc($select_nxb)) {
                echo "<option value='{$row_nxb['MaNXB']}'>{$row_nxb['TenNXB']}</option>";
            }
            ?>
        </select>

        <input type="number" name="SoLuong" class="box" placeholder="Số lượng" required>
        <select name="DonViTinh" class="box" required>
            <option value="">Chọn đơn vị tính</option>
            <?php
            $select_dvt = mysqli_query($conn, "SELECT * FROM `DonViTinh`") or die('query failed');
            while ($row_dvt = mysqli_fetch_assoc($select_dvt)) {
                echo "<option value='{$row_dvt['MaDVT']}'>{$row_dvt['TenDVT']}</option>";
            }
            ?>
        </select>

        <select name="MaDonGia" class="box" required>
            <option value="">Chọn đơn giá</option>
            <?php
            $select_dongia = mysqli_query($conn, "SELECT * FROM `DonGia`") or die('query failed');
            while ($row_dongia = mysqli_fetch_assoc($select_dongia)) {
                echo "<option value='{$row_dongia['MaDonGia']}'>{$row_dongia['DonGia']}</option>";
            }
            ?>
        </select>

        <select name="MaNCC" class="box" required>
            <option value="">Chọn nhà cung cấp</option>
            <?php
            $select_ncc = mysqli_query($conn, "SELECT * FROM `ncc`") or die('query failed');
            while ($row_ncc = mysqli_fetch_assoc($select_ncc)) {
                echo "<option value='{$row_ncc['MaNCC']}'>{$row_ncc['TenNCC']}</option>";
            }
            ?>
        </select>

        <select name="MaNgan" class="box" required>
            <option value="">Chọn ngăn</option>
            <?php
            $select_ngan = mysqli_query($conn, "SELECT * FROM `ngan`") or die('query failed');
            while ($row_ngan = mysqli_fetch_assoc($select_ngan)) {
                echo "<option value='{$row_ngan['MaNgan']}'>{$row_ngan['TenNgan']}</option>";
            }
            ?>
        </select>

        <input type="submit" value="Thêm sách" name="add_book" class="btn-primary" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;">
    </form>

</section>

<section class="show-products">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Tên Sách</th>
                    <th>Tác Giả</th>
                    <th>Thể Loại</th>
                    <th>Nhà Xuất Bản</th>
                    <th>Số Lượng</th>
                    <th>Đơn Giá</th>
                    <th>Nhà Cung Cấp</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_book = mysqli_query($conn, "SELECT * FROM `sach`") or die('query failed');
                if (mysqli_num_rows($select_book) > 0) {
                    while ($fetch_book = mysqli_fetch_assoc($select_book)) {
                        // Lấy thông tin chi tiết
                        $tacgia = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenTG FROM TacGia WHERE MaTG = '{$fetch_book['MaTacGia']}'"));
                        $theloai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenTheLoai FROM TheLoai WHERE MaTheLoai = '{$fetch_book['MaTheLoai']}'"));
                        $nxb = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenNXB FROM NXB WHERE MaNXB = '{$fetch_book['MaNXB']}'"));
                        $dongia = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DonGia FROM DonGia WHERE MaDonGia = '{$fetch_book['MaDonGia']}'"));
                        $ncc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenNCC FROM ncc WHERE MaNCC = '{$fetch_book['MaNCC']}'"));
                        $ngan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT TenNgan FROM ngan WHERE MaNgan = '{$fetch_book['MaNgan']}'"));

                ?>
                        <tr>
                            <td><?php echo $fetch_book['TenSach']; ?></td>
                            <td><?php echo $tacgia['TenTG']; ?></td>
                            <td><?php echo $theloai['TenTheLoai']; ?></td>
                            <td><?php echo $nxb['TenNXB']; ?></td>
                            <td><?php echo $fetch_book['SoLuong']; ?></td>
                            <td><?php echo $dongia['DonGia']; ?></td>
                            <td><?php echo $ncc['TenNCC']; ?></td>
                            <td>
                                <a href="update_book.php?MaSach=<?php echo $fetch_book['MaSach']; ?>"  class="btn-warning btn-sm" style="padding: 10px; text-decoration: none; font-size: 14px;">Sửa</a>
                                <a href="owner_books.php?delete=<?php echo $fetch_book['MaSach']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa sách này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="11">Không có sách nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

</body>
</html>
