<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id']; // Tạo session admin

if (!isset($admin_id)) {
    header('location:home.php'); // Nếu không tồn tại session admin thì chuyển về trang đăng nhập
}

// Thêm thành viên mới
if (isset($_POST['add_member'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $join_date = date('Y-m-d'); // Lấy ngày hiện tại làm ngày tham gia
    $membership_status = $_POST['membership_status'];
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));

    // Kiểm tra email thành viên đã tồn tại chưa
    $select_member = mysqli_query($conn, "SELECT email FROM `members` WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select_member) > 0) {
        $message[] = 'Thành viên đã tồn tại!';
    } else {
        // Thêm thành viên mới
        $add_member_query = mysqli_query($conn, "INSERT INTO `members` (name, email, phone, join_date, membership_status, password) VALUES('$name', '$email', '$phone', '$join_date', '$membership_status', '$password')") or die('query failed');

        if ($add_member_query) {
            $message[] = 'Thêm thành viên thành công!';
        } else {
            $message[] = 'Thêm thành viên thất bại!';
        }
    }
}

// Xóa thành viên
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `members` WHERE member_id = '$delete_id'") or die('query failed');
    header('location:admin_members.php');
}

// Cập nhật thông tin thành viên
if (isset($_POST['update_member'])) {
    $update_id = $_POST['update_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_phone = mysqli_real_escape_string($conn, $_POST['update_phone']);
    $update_status = $_POST['update_membership_status'];

    // Cập nhật thông tin thành viên
    mysqli_query($conn, "UPDATE `members` SET name = '$update_name', email = '$update_email', phone = '$update_phone', membership_status = '$update_status' WHERE member_id = '$update_id'") or die('query failed');
    header('location:admin_members.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thành viên</title>

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
    <h1 class="title">Quản lý thành viên</h1>

    <form action="" method="post">
        <h3>Thêm thành viên</h3>
        <input type="text" name="name" class="box" placeholder="Tên thành viên" required>
        <input type="email" name="email" class="box" placeholder="Email" required>
        <input type="text" name="phone" class="box" placeholder="Số điện thoại" required>
        <select name="membership_status" class="box">
            <option value="active">Hoạt động</option>
            <option value="inactive">Không hoạt động</option>
        </select>
        <input type="password" name="password" class="box" placeholder="Mật khẩu" required>
        <input type="submit" value="Thêm thành viên" name="add_member" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;" class="btn-primary">
    </form>
</section>

<section class="show-members">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Mã Thành Viên</th>
                    <th>Tên Thành Viên</th>
                    <th>Email</th>
                    <th>Số Điện Thoại</th>
                    <th>Ngày Tham Gia</th>
                    <th>Trạng Thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_members = mysqli_query($conn, "SELECT * FROM `members`") or die('query failed');
                if (mysqli_num_rows($select_members) > 0) {
                    while ($fetch_members = mysqli_fetch_assoc($select_members)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_members['member_id']; ?></td>
                            <td><?php echo $fetch_members['name']; ?></td>
                            <td><?php echo $fetch_members['email']; ?></td>
                            <td><?php echo $fetch_members['phone']; ?></td>
                            <td><?php echo $fetch_members['join_date']; ?></td>
                            <td><?php echo ucfirst($fetch_members['membership_status']); ?></td>
                            <td>
                                <a href="admin_members.php?edit=<?php echo $fetch_members['member_id']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-warning btn-sm">Sửa</a>
                                <a href="admin_members.php?delete=<?php echo $fetch_members['member_id']; ?>" style="padding: 10px; text-decoration: none; font-size: 14px;" class="btn-danger btn-sm" onclick="return confirm('Xóa thành viên này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center" style="font-size: 25px;">Không có thành viên nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Form cập nhật thành viên -->
<?php if (isset($_GET['edit'])): 
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM `members` WHERE member_id = '$edit_id'") or die('query failed');
    if (mysqli_num_rows($edit_query) > 0):
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<section class="add-products">
    <form action="" method="post">
        <h3>Cập nhật thông tin thành viên</h3>
        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['member_id']; ?>">
        <input type="text" name="update_name" class="box" value="<?php echo $fetch_edit['name']; ?>" required>
        <input type="email" name="update_email" class="box" value="<?php echo $fetch_edit['email']; ?>" required>
        <input type="text" name="update_phone" class="box" value="<?php echo $fetch_edit['phone']; ?>" required>
        <select name="update_membership_status" class="box">
            <option value="active" <?php if ($fetch_edit['membership_status'] == 'active') echo 'selected'; ?>>Hoạt động</option>
            <option value="inactive" <?php if ($fetch_edit['membership_status'] == 'inactive') echo 'selected'; ?>>Không hoạt động</option>
        </select>
        <input type="submit" value="Cập nhật" name="update_member"  style="padding: 8px 10px; text-decoration: none; font-size: 14px;" class="btn-primary">
        <a href="admin_members.php" class="btn-secondary"  style="padding: 10px; text-decoration: none; font-size: 14px;">Hủy</a>
    </form>
</section>
<?php endif; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
