<?php
include 'config.php';
session_start();

if (isset($_GET['MaSach'])) {
    $MaSach = $_GET['MaSach'];
    $select_book = mysqli_query($conn, "SELECT * FROM `Sach` WHERE MaSach = '$MaSach'") or die('query failed');

    if (mysqli_num_rows($select_book) > 0) {
        $fetch_book = mysqli_fetch_assoc($select_book);
    } else {
        header('location:owner_books.php');
    }
}

if (isset($_POST['update_book'])) {
    $TenSach = mysqli_real_escape_string($conn, $_POST['TenSach']);
    $MaTacGia = mysqli_real_escape_string($conn, $_POST['MaTacGia']);
    $MaTheLoai = mysqli_real_escape_string($conn, $_POST['MaTheLoai']);
    $MaNXB = mysqli_real_escape_string($conn, $_POST['MaNXB']);
    $SoLuong = mysqli_real_escape_string($conn, $_POST['SoLuong']);
    $DonViTinh = mysqli_real_escape_string($conn, $_POST['DonViTinh']);
    $MaDonGia = mysqli_real_escape_string($conn, $_POST['MaDonGia']);
    $MaNCC = mysqli_real_escape_string($conn, $_POST['MaNCC']);
    $MaNgan = mysqli_real_escape_string($conn, $_POST['MaNgan']);

    $update_query = "UPDATE `Sach` SET TenSach='$TenSach', MaTacGia='$MaTacGia', MaTheLoai='$MaTheLoai', 
                    MaNXB='$MaNXB', SoLuong='$SoLuong', DonViTinh='$DonViTinh', MaDonGia='$MaDonGia', 
                    MaNCC='$MaNCC', MaNgan='$MaNgan' WHERE MaSach='$MaSach'";
    mysqli_query($conn, $update_query) or die('query failed');

    header('location:owner_books.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Sách</title>
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
    <h1 style="text-align: center;" class="title">Cập Nhật Thông Tin Sách</h1>

    <form action="" method="post">
        <div class="mb-3">
            <label for="TenSach" class="form-label">Tên Sách</label>
            <input type="text" name="TenSach" class="form-control" placeholder="Tên sách" value="<?php echo $fetch_book['TenSach']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="MaTacGia" class="form-label">Tác Giả</label>
            <select name="MaTacGia" class="form-control" required>
                <option value="">Chọn tác giả</option>
                <?php
                $select_tacgia = mysqli_query($conn, "SELECT * FROM `TacGia`") or die('query failed');
                while ($row_tacgia = mysqli_fetch_assoc($select_tacgia)) {
                    $selected = ($row_tacgia['MaTG'] == $fetch_book['MaTacGia']) ? 'selected' : '';
                    echo "<option value='{$row_tacgia['MaTG']}' $selected>{$row_tacgia['TenTG']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="MaTheLoai" class="form-label">Thể Loại</label>
            <select name="MaTheLoai" class="form-control" required>
                <option value="">Chọn thể loại</option>
                <?php
                $select_theloai = mysqli_query($conn, "SELECT * FROM `TheLoai`") or die('query failed');
                while ($row_theloai = mysqli_fetch_assoc($select_theloai)) {
                    $selected = ($row_theloai['MaTheLoai'] == $fetch_book['MaTheLoai']) ? 'selected' : '';
                    echo "<option value='{$row_theloai['MaTheLoai']}' $selected>{$row_theloai['TenTheLoai']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="MaNXB" class="form-label">Nhà Xuất Bản</label>
            <select name="MaNXB" class="form-control" required>
                <option value="">Chọn nhà xuất bản</option>
                <?php
                $select_nxb = mysqli_query($conn, "SELECT * FROM `NXB`") or die('query failed');
                while ($row_nxb = mysqli_fetch_assoc($select_nxb)) {
                    $selected = ($row_nxb['MaNXB'] == $fetch_book['MaNXB']) ? 'selected' : '';
                    echo "<option value='{$row_nxb['MaNXB']}' $selected>{$row_nxb['TenNXB']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="SoLuong" class="form-label">Số Lượng</label>
            <input type="number" name="SoLuong" class="form-control" placeholder="Số lượng" value="<?php echo $fetch_book['SoLuong']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="DonViTinh" class="form-label">Đơn Vị Tính</label>
            <select name="DonViTinh" class="form-control" required>
                <option value="">Chọn đơn vị tính</option>
                <?php
                $select_dvt = mysqli_query($conn, "SELECT * FROM `DonViTinh`") or die('query failed');
                while ($row_dvt = mysqli_fetch_assoc($select_dvt)) {
                    $selected = ($row_dvt['MaDVT'] == $fetch_book['DonViTinh']) ? 'selected' : '';
                    echo "<option value='{$row_dvt['MaDVT']}' $selected>{$row_dvt['TenDVT']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="MaDonGia" class="form-label">Đơn Giá</label>
            <select name="MaDonGia" class="form-control" required>
                <option value="">Chọn đơn giá</option>
                <?php
                $select_dongia = mysqli_query($conn, "SELECT * FROM `DonGia`") or die('query failed');
                while ($row_dongia = mysqli_fetch_assoc($select_dongia)) {
                    $selected = ($row_dongia['MaDonGia'] == $fetch_book['MaDonGia']) ? 'selected' : '';
                    echo "<option value='{$row_dongia['MaDonGia']}' $selected>{$row_dongia['DonGia']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="MaNCC" class="form-label">Nhà Cung Cấp</label>
            <select name="MaNCC" class="form-control" required>
                <option value="">Chọn nhà cung cấp</option>
                <?php
                $select_ncc = mysqli_query($conn, "SELECT * FROM `ncc`") or die('query failed');
                while ($row_ncc = mysqli_fetch_assoc($select_ncc)) {
                    $selected = ($row_ncc['MaNCC'] == $fetch_book['MaNCC']) ? 'selected' : '';
                    echo "<option value='{$row_ncc['MaNCC']}' $selected>{$row_ncc['TenNCC']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="MaNgan" class="form-label">Ngăn</label>
            <select name="MaNgan" class="form-control" required>
                <option value="">Chọn ngăn</option>
                <?php
                $select_ngan = mysqli_query($conn, "SELECT * FROM `ngan`") or die('query failed');
                while ($row_ngan = mysqli_fetch_assoc($select_ngan)) {
                    $selected = ($row_ngan['MaNgan'] == $fetch_book['MaNgan']) ? 'selected' : '';
                    echo "<option value='{$row_ngan['MaNgan']}' $selected>{$row_ngan['TenNgan']}</option>";
                }
                ?>
            </select>
        </div>

        <input type="submit" value="Cập nhật sách" name="update_book" class="btn-primary" style="margin-top: 5px; padding: 7.5px 25px; border-radius: 5px; font-size: 18px;">
        <a href="owner_books.php" class="btn-secondary" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px; text-decoration: none;">Quay lại</a>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
