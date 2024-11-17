<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Thêm danh mục mới
if (isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    // Kiểm tra danh mục đã tồn tại
    $check_category_query = mysqli_query($conn, "SELECT * FROM `categories` WHERE name = '$name'") or die('Query failed');

    if (mysqli_num_rows($check_category_query) > 0) {
        $message[] = 'Danh mục đã tồn tại!';
    } else {
        // Thêm danh mục
        $insert_category_query = mysqli_query($conn, "INSERT INTO `categories` (name) VALUES ('$name')") or die('Query failed');
        if ($insert_category_query) {
            $message[] = 'Thêm danh mục thành công!';
        } else {
            $message[] = 'Thêm danh mục thất bại!';
        }
    }
}

// Xóa danh mục
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $delete_query = mysqli_query($conn, "DELETE FROM `categories` WHERE id = '$delete_id'") or die('Query failed');

    if ($delete_query) {
        $message[] = 'Xóa danh mục thành công!';
    } else {
        $message[] = 'Xóa danh mục thất bại!';
    }

    header('location:admin_categories.php');
}

// Cập nhật danh mục
if (isset($_POST['update_category'])) {
    $update_id = $_POST['update_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);

    $update_query = mysqli_query($conn, "UPDATE `categories` SET name = '$update_name' WHERE id = '$update_id'") or die('Query failed');

    if ($update_query) {
        $message[] = 'Cập nhật danh mục thành công!';
    } else {
        $message[] = 'Cập nhật danh mục thất bại!';
    }

    header('location:admin_categories.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục khóa học</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        th, td {
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý danh mục khóa học</h1>

    <form action="" method="post">
        <h3>Thêm danh mục mới</h3>
        <input type="text" name="name" class="box" placeholder="Tên danh mục" required>
        <input type="submit" value="Thêm danh mục" name="add_category" class="btn-primary" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;">
    </form>
</section>

<section class="show-categories">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_categories = mysqli_query($conn, "SELECT * FROM `categories`") or die('Query failed');
                if (mysqli_num_rows($select_categories) > 0) {
                    while ($category = mysqli_fetch_assoc($select_categories)) {
                ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo $category['name']; ?></td>
                            <td><?php echo $category['created_at']; ?></td>
                            <td>
                                <a href="admin_categories.php?edit=<?php echo $category['id']; ?>" class="btn-warning btn-sm" style="padding: 8px; text-decoration: none; font-size: 14px;">Sửa</a>
                                <a href="admin_categories.php?delete=<?php echo $category['id']; ?>" class="btn-danger btn-sm" style="padding: 8px; text-decoration: none; font-size: 14px;" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">Chưa có danh mục nào.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php if (isset($_GET['edit'])): 
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM `categories` WHERE id = '$edit_id'") or die('Query failed');
    if (mysqli_num_rows($edit_query) > 0):
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<section class="add-category">
    <form action="" method="post">
        <h3>Cập nhật danh mục</h3>
        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
        <input type="text" name="update_name" class="box" value="<?php echo $fetch_edit['name']; ?>" required>
        <input type="submit" value="Cập nhật" name="update_category" class="btn-primary" style="padding: 8px 10px; text-decoration: none; font-size: 14px;">
        <a href="admin_categories.php" class="btn-secondary" style="padding: 8px 10px; text-decoration: none; font-size: 14px;">Hủy</a>
    </form>
</section>
<?php endif; endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
