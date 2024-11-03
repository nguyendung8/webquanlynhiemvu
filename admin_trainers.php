<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:home.php'); // Nếu không tồn tại session admin thì chuyển về trang đăng nhập
}

// Thêm huấn luyện viên mới
if (isset($_POST['add_trainer'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select_trainer = mysqli_query($conn, "SELECT email FROM `trainers` WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select_trainer) > 0) {
        $message[] = 'Huấn luyện viên đã tồn tại!';
    } else {
        $add_trainer_query = mysqli_query($conn, "INSERT INTO `trainers` (name, email, phone, shift, password) VALUES('$name', '$email', '$phone', '$shift', '$password')") or die('query failed');

        if ($add_trainer_query) {
            $message[] = 'Thêm huấn luyện viên thành công!';
        } else {
            $message[] = 'Thêm huấn luyện viên thất bại!';
        }
    }
}

// Xóa huấn luyện viên
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `trainers` WHERE trainer_id = '$delete_id'") or die('query failed');
    header('location:admin_trainers.php');
}

// Cập nhật ca huấn luyện viên
if (isset($_POST['update_trainer'])) {
    $update_id = $_POST['update_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_phone = mysqli_real_escape_string($conn, $_POST['update_phone']);
    $update_shift = mysqli_real_escape_string($conn, $_POST['update_shift']);

    mysqli_query($conn, "UPDATE `trainers` SET name = '$update_name', email = '$update_email', phone = '$update_phone', shift = '$update_shift' WHERE trainer_id = '$update_id'") or die('query failed');
    header('location:admin_trainers.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý huấn luyện viên</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

<?php include 'admin_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý huấn luyện viên</h1>

    <form action="" method="post">
        <h3>Thêm huấn luyện viên</h3>
        <input type="text" name="name" class="box" placeholder="Tên huấn luyện viên" required>
        <input type="email" name="email" class="box" placeholder="Email" required>
        <input type="text" name="phone" class="box" placeholder="Số điện thoại" required>
        <select name="shift" class="box">
            <option value="morning">Sáng</option>
            <option value="afternoon">Chiều</option>
            <option value="evening">Tối</option>
        </select>
        <input type="password" name="password" class="box" placeholder="Mật khẩu" required>
        <input type="submit" value="Thêm huấn luyện viên" name="add_trainer" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;" class="btn-primary">
    </form>
</section>

<section class="show-trainers">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Mã Huấn Luyện Viên</th>
                    <th>Tên Huấn Luyện Viên</th>
                    <th>Email</th>
                    <th>Số Điện Thoại</th>
                    <th>Ca Làm Việc</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_trainers = mysqli_query($conn, "SELECT * FROM `trainers`") or die('query failed');
                if (mysqli_num_rows($select_trainers) > 0) {
                    while ($fetch_trainers = mysqli_fetch_assoc($select_trainers)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_trainers['trainer_id']; ?></td>
                            <td><?php echo $fetch_trainers['name']; ?></td>
                            <td><?php echo $fetch_trainers['email']; ?></td>
                            <td><?php echo $fetch_trainers['phone']; ?></td>
                            <td><?php echo ucfirst($fetch_trainers['shift']); ?></td>
                            <td>
                                <a href="admin_trainers.php?edit=<?php echo $fetch_trainers['trainer_id']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-warning btn-sm">Sửa</a>
                                <a href="admin_trainers.php?delete=<?php echo $fetch_trainers['trainer_id']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Xóa huấn luyện viên này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center" style="font-size: 25px;">Không có huấn luyện viên nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Form cập nhật huấn luyện viên -->
<?php if (isset($_GET['edit'])): 
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM `trainers` WHERE trainer_id = '$edit_id'") or die('query failed');
    if (mysqli_num_rows($edit_query) > 0):
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<section class="add-products">
    <form action="" method="post">
        <h3>Cập nhật thông tin huấn luyện viên</h3>
        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['trainer_id']; ?>">
        <input type="text" name="update_name" class="box" value="<?php echo $fetch_edit['name']; ?>" required>
        <input type="email" name="update_email" class="box" value="<?php echo $fetch_edit['email']; ?>" required>
        <input type="text" name="update_phone" class="box" value="<?php echo $fetch_edit['phone']; ?>" required>
        <select name="update_shift" class="box">
            <option value="morning" <?php if ($fetch_edit['shift'] == 'morning') echo 'selected'; ?>>Sáng</option>
            <option value="afternoon" <?php if ($fetch_edit['shift'] == 'afternoon') echo 'selected'; ?>>Chiều</option>
            <option value="evening" <?php if ($fetch_edit['shift'] == 'evening') echo 'selected'; ?>>Tối</option>
        </select>
        <input type="submit" value="Cập nhật" name="update_trainer"  style="padding: 8px 10px; text-decoration: none; font-size: 14px;" class="btn-primary">
        <a href="admin_trainers.php" class="btn-secondary"  style="padding: 10px; text-decoration: none; font-size: 14px;">Hủy</a>
    </form>
</section>
<?php endif; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
