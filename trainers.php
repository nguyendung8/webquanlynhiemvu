<?php
include 'config.php';
session_start();

// Kiểm tra nếu người dùng đã đăng nhập với tư cách huấn luyện viên
if (!isset($_SESSION['trainer_id'])) {
    header('location:home.php'); // Nếu chưa đăng nhập thì chuyển về trang đăng nhập
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

// Kiểm tra nếu có yêu cầu cập nhật trạng thái tham dự
if (isset($_POST['update_attendance'])) {
    $member_id = $_POST['member_id'];
    $status = $_POST['status'];
    $attendance_date = date('Y-m-d H:i:s'); // Ngày giờ hiện tại

    // Kiểm tra xem đã có bản ghi tham dự cho thành viên này chưa
    $check_attendance = mysqli_query($conn, "SELECT * FROM `attendance` WHERE member_id = '$member_id' AND trainer_id = '$trainer_id' AND attendance_date = '$attendance_date'") or die('query failed');

    if (mysqli_num_rows($check_attendance) > 0) {
        // Nếu có rồi, cập nhật trạng thái
        $update_query = mysqli_query($conn, "UPDATE `attendance` SET status = '$status' WHERE member_id = '$member_id' AND trainer_id = '$trainer_id' AND attendance_date = '$attendance_date'") or die('query failed');
    } else {
        // Nếu chưa có, thêm mới
        $insert_query = mysqli_query($conn, "INSERT INTO `attendance` (member_id, trainer_id, attendance_date, status) VALUES('$member_id', '$trainer_id', '$attendance_date', '$status')") or die('query failed');
    }

    if (@$update_query || $insert_query) {
        $message[] = 'Trạng thái tham dự đã được cập nhật!';
    } else {
        $message[] = 'Cập nhật thất bại!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Huấn Luyện Viên</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_style.css">
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

<?php include 'trainer_header.php'; // Header cho huấn luyện viên ?>

<section class="show-members">
    <h1 class="title">Danh Sách Thành Viên</h1>
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID Thành Viên</th>
                    <th>Tên Thành Viên</th>
                    <th>Trạng Thái Tham Dự</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Lấy danh sách tất cả các thành viên
                $select_members = mysqli_query($conn, "SELECT * FROM `members`") or die('query failed');
                if (mysqli_num_rows($select_members) > 0) {
                    while ($fetch_member = mysqli_fetch_assoc($select_members)) {
                        $member_id = $fetch_member['member_id'];
                    
                        // Lấy trạng thái tham dự cho thành viên này, nếu đã có trong ngày hiện tại
                        $attendance_query = mysqli_query($conn, "SELECT * FROM `attendance` WHERE member_id = '$member_id' AND trainer_id = '$trainer_id' AND DATE(attendance_date) = CURDATE() ORDER BY attendance_date DESC LIMIT 1") or die('query failed');
                        $attendance_status = mysqli_fetch_assoc($attendance_query);
                        
                        // Kiểm tra xem trạng thái có tồn tại hay không
                        if ($attendance_status) {
                            $status = $attendance_status['status'];
                        } else {
                            $status = 'Chưa có'; // Nếu không có bản ghi, gán trạng thái là 'Chưa có'
                        }
                    
                        ?>
                        <tr>
                            <td><?php echo $fetch_member['member_id']; ?></td>
                            <td><?php echo $fetch_member['name']; ?></td>
                            <td><?php echo ($status == 'absent') ? 'Vắng mặt' : 'Có mặt'; ?></td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                                    <select style="margin-top: 5px; padding: 7px 10px; border-radius: 5px; font-size: 15px; cursor: pointer;" name="status" required>
                                        <option value="present" <?php echo ($status == 'present') ? 'selected' : ''; ?>>Có mặt</option>
                                        <option value="absent" <?php echo ($status == 'absent') ? 'selected' : ''; ?>>Vắng mặt</option>
                                    </select>
                                    <input type="submit" name="update_attendance" value="Cập Nhật" style="margin-top: 5px; padding: 7px 10px; border-radius: 5px; font-size: 15px;" class="btn-primary">
                                </form>
                            </td>
                        </tr>
                        <?php
                    }                    
                } else {
                    echo '<tr><td colspan="4" class="text-center">Chưa có thành viên nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
