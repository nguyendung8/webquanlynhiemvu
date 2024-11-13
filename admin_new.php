<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:home.php');
}

// Thêm tin tức mới
if (isset($_POST['add_news'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $date_posted = date('Y-m-d H:i:s');

    // Upload hình ảnh
    $image_name = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image_name;

    if (move_uploaded_file($image_tmp_name, $image_folder)) {
        $add_news_query = mysqli_query($conn, "INSERT INTO `tin_tuc` (tieu_de, noi_dung, hinh_anh, ngay_dang) VALUES('$title', '$content', '$image_name', '$date_posted')") or die('query failed');
        if ($add_news_query) {
            $message[] = 'Thêm tin tức thành công!';
        } else {
            $message[] = 'Thêm tin tức thất bại!';
        }
    } else {
        $message[] = 'Lỗi khi tải lên hình ảnh!';
    }
}

// Xóa tin tức
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Xóa hình ảnh khỏi thư mục
    $delete_image_query = mysqli_query($conn, "SELECT hinh_anh FROM `tin_tuc` WHERE id = '$delete_id'") or die('query failed');
    $image = mysqli_fetch_assoc($delete_image_query);
    unlink('uploaded_img/' . $image['hinh_anh']);

    // Xóa tin tức
    mysqli_query($conn, "DELETE FROM `tin_tuc` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_new.php');
}

// Cập nhật tin tức
if (isset($_POST['update_news'])) {
    $update_id = $_POST['update_id'];
    $update_title = mysqli_real_escape_string($conn, $_POST['update_title']);
    $update_content = mysqli_real_escape_string($conn, $_POST['update_content']);

    mysqli_query($conn, "UPDATE `tin_tuc` SET tieu_de = '$update_title', noi_dung = '$update_content' WHERE id = '$update_id'") or die('query failed');

    // Cập nhật hình ảnh nếu có tải lên mới
    if (!empty($_FILES['update_image']['name'])) {
        $update_image = $_FILES['update_image']['name'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/' . $update_image;

        move_uploaded_file($update_image_tmp_name, $update_image_folder);
        mysqli_query($conn, "UPDATE `tin_tuc` SET hinh_anh = '$update_image' WHERE id = '$update_id'") or die('query failed');
    }

    header('location:admin_new.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tin tức</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="css/new_style.css">
    <style>
      th, td {
         font-size: 18px;
         padding: 1rem;
         text-align: center;
      }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý tin tức</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Thêm tin tức mới</h3>
        <input type="text" name="title" class="box" placeholder="Tiêu đề" required>
        <textarea name="content" class="box" placeholder="Nội dung" required></textarea>
        <input type="file" name="image" class="box" accept="image/*" required>
        <input type="submit" value="Thêm tin tức" name="add_news" class="new-btn btn-primary">
    </form>
</section>

<section class="show-news">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th style="max-width: 300px;">Tiêu đề</th>
                    <th>Hình ảnh</th>
                    <th>Ngày đăng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_news = mysqli_query($conn, "SELECT * FROM `tin_tuc`") or die('query failed');
                if (mysqli_num_rows($select_news) > 0) {
                    while ($news = mysqli_fetch_assoc($select_news)) {
                ?>
                        <tr>
                            <td><?php echo $news['id']; ?></td>
                            <td style="max-width: 300px;"><?php echo $news['tieu_de']; ?></td>
                            <td><img src="uploaded_img/<?php echo $news['hinh_anh']; ?>" width="100"></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($news['ngay_dang'])); ?></td>
                            <td>
                                <a href="admin_new.php?edit=<?php echo $news['id']; ?>" class="fs-3 btn-warning btn-sm">Sửa</a>
                                <a href="admin_new.php?delete=<?php echo $news['id']; ?>" class="fs-3 btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa tin tức này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">Chưa có tin tức nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php if (isset($_GET['edit'])): 
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM `tin_tuc` WHERE id = '$edit_id'") or die('query failed');
    if (mysqli_num_rows($edit_query) > 0):
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<section class="edit-news">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Cập nhật tin tức</h3>
        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
        <input type="text" name="update_title" class="box" value="<?php echo $fetch_edit['tieu_de']; ?>" required>
        <textarea name="update_content" class="box"><?php echo $fetch_edit['noi_dung']; ?></textarea>
        <input type="submit" value="Cập nhật" name="update_news" class="new-btn btn-primary">
        <a href="admin_new.php" class="new-btn btn-secondary">Hủy</a>
    </form>
</section>
<?php endif; endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
