<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Thêm lịch làm việc mới
if (isset($_POST['add_schedule'])) {
    $bac_si_id = $_POST['bac_si_id'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];
    $trang_thai = $_POST['trang_thai'];

    // Thêm lịch làm việc vào cơ sở dữ liệu
    $insert_schedule = mysqli_query($conn, "INSERT INTO `lich_lam_viec` (bac_si_id, ngay_bat_dau, ngay_ket_thuc, trang_thai) VALUES('$bac_si_id', '$ngay_bat_dau', '$ngay_ket_thuc', '$trang_thai')") or die('query failed');

    if ($insert_schedule) {
        $message[] = 'Lịch làm việc đã được thiết lập thành công!';
    }
}

// Xóa lịch làm việc
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM `lich_lam_viec` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_schedule.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch làm việc bác sĩ</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        label {
            font-size: 17px;
            float: left;
        }
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
    <h1 class="title">Quản lý lịch làm việc bác sĩ</h1>

    <form action="" method="post">
        <h3>Thêm lịch làm việc</h3>
        <select name="bac_si_id" id="bac_si_id" class="box" required>
            <option value="">Chọn bác sĩ</option>
            <?php
            // Lấy danh sách bác sĩ
            $select_doctors = mysqli_query($conn, "SELECT * FROM `nguoi_dung` JOIN `bac_si` ON nguoi_dung.id = bac_si.id WHERE vai_tro = 'bac_si'") or die('query failed');
            while ($fetch_doctor = mysqli_fetch_assoc($select_doctors)) {
                echo "<option value='".$fetch_doctor['id']."'>".$fetch_doctor['ten']."</option>";
            }
            ?>
        </select>

        <label for="ngay_bat_dau">Ngày bắt đầu:</label>
        <input type="datetime-local" name="ngay_bat_dau" class="box" required>

        <label for="ngay_ket_thuc">Ngày kết thúc:</label>
        <input type="datetime-local" name="ngay_ket_thuc" class="box" required>

        <label for="trang_thai">Trạng thái:</label>
        <select name="trang_thai" id="trang_thai" class="box" required>
            <option value="co_san">Có sẵn</option>
            <option value="dang_ban">Đang bận</option>
        </select>

        <input type="submit" value="Thêm lịch làm việc" name="add_schedule" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
    </form>
</section>

<section class="show-schedules">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bác sĩ</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_schedules = mysqli_query($conn, "SELECT lich_lam_viec.*, nguoi_dung.ten FROM lich_lam_viec JOIN nguoi_dung ON lich_lam_viec.bac_si_id = nguoi_dung.id") or die('query failed');
                if (mysqli_num_rows($select_schedules) > 0) {
                    while ($fetch_schedule = mysqli_fetch_assoc($select_schedules)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_schedule['id']; ?></td>
                            <td><?php echo $fetch_schedule['ten']; ?></td>
                            <td><?php echo $fetch_schedule['ngay_bat_dau']; ?></td>
                            <td><?php echo $fetch_schedule['ngay_ket_thuc']; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $fetch_schedule['trang_thai'])); ?></td>
                            <td>
                                <a href="admin_schedule.php?delete=<?php echo $fetch_schedule['id']; ?>"  style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Xóa lịch làm việc này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">Chưa có lịch làm việc nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

</body>
</html>
