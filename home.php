<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

// Thêm nhiệm vụ
if (isset($_POST['add_task'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $start_date = date('Y-m-d H:i:s');
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $estimated_time = mysqli_real_escape_string($conn, $_POST['estimated_time']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $label = mysqli_real_escape_string($conn, $_POST['label']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $status = 'Chưa bắt đầu';

    $insert_task = mysqli_query($conn, "INSERT INTO `tasks` 
        (title, description, start_date, end_date, estimated_time, priority, label, notes, status, user_id) 
        VALUES 
        ('$title', '$description', '$start_date', '$end_date', '$estimated_time', '$priority', '$label', '$notes', '$status', '$user_id')") 
        or die('Query failed');

    if ($insert_task) {
        $message[] = 'Thêm nhiệm vụ thành công!';
    } else {
        $message[] = 'Thêm nhiệm vụ thất bại!';
    }
}

// Xóa nhiệm vụ
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $delete_task = mysqli_query($conn, "DELETE FROM `tasks` WHERE task_id = '$delete_id'") or die('Query failed');
    if ($delete_task) {
        $message[] = 'Xóa nhiệm vụ thành công!';
    } else {
        $message[] = 'Xóa nhiệm vụ thất bại!';
    }
    header('location:home.php');
}

// Cập nhật nhiệm vụ
if (isset($_POST['update_task'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $estimated_time = mysqli_real_escape_string($conn, $_POST['estimated_time']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $label = mysqli_real_escape_string($conn, $_POST['label']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_task = mysqli_query($conn, "UPDATE `tasks` SET 
        title = '$title', description = '$description', estimated_time = '$estimated_time', 
        priority = '$priority', label = '$label', notes = '$notes', 
        status = '$status' WHERE task_id = '$task_id'") 
        or die('Query failed');

    if ($update_task) {
        $message[] = 'Cập nhật nhiệm vụ thành công!';
    } else {
        $message[] = 'Cập nhật nhiệm vụ thất bại!';
    }
}
if (isset($_POST['share_task'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $shared_with_user_id = mysqli_real_escape_string($conn, $_POST['shared_with_user_id']);

    // Kiểm tra xem nhiệm vụ đã được chia sẻ với người dùng này chưa
    $check_collaboration = mysqli_query($conn, "SELECT * FROM `collaborations` WHERE task_id = '$task_id' AND shared_with_user_id = '$shared_with_user_id'") or die('Query failed');
    if (mysqli_num_rows($check_collaboration) > 0) {
        $message[] = 'Nhiệm vụ đã được chia sẻ với người dùng này!';
    } else {
        // Thêm bản ghi chia sẻ vào bảng collaborations
        $insert_collaboration = mysqli_query($conn, "INSERT INTO `collaborations` (task_id, shared_with_user_id) VALUES ('$task_id', '$shared_with_user_id')") or die('Query failed');
        if ($insert_collaboration) {
            $message[] = 'Chia sẻ nhiệm vụ thành công!';
        } else {
            $message[] = 'Chia sẻ nhiệm vụ thất bại!';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý nhiệm vụ</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .card {
         border-radius: 12px !important;
      }
      label {
        float: left;
      }
      .card-img-top {
         height: 203px;
         width: 305px;
         border-top-left-radius: 12px !important;
         border-top-right-radius: 12px !important;
      }
      .form-select {
         height: 84%;
      }
      .card-body {
         font-size: 16px;
      }
      .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Thêm hiệu ứng chuyển động và đổ bóng */
         }

         /* Khi hover, thẻ card sẽ nổi lên */
      .card:hover {
         cursor: pointer;
         transform: translateY(-10px); /* Nổi lên một chút */
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Thêm đổ bóng nhẹ để tạo hiệu ứng nổi */
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý nhiệm vụ</h1>

    <!-- Form thêm nhiệm vụ -->
    <form action="" method="post">
        <h3>Thêm nhiệm vụ mới</h3>
        <input type="text" name="title" class="box" placeholder="Tiêu đề nhiệm vụ" required>
        <textarea name="description" class="box" placeholder="Mô tả nhiệm vụ"></textarea>
        <input type="number" name="estimated_time" class="box" placeholder="Thời gian ước tính (giờ)">
        <label for="">Mức ưu tiên</label>
        <select name="priority" class="box" required>
            <option value="Cao">Cao</option>
            <option value="Trung bình">Trung bình</option>
            <option value="Thấp">Thấp</option>
        </select>
        <label for="">Nhãn</label>
        <select name="label" class="box" required>
            <option value="Học tập">Học tập</option>
            <option value="Công việc">Công việc</option>
            <option value="Cá nhân">Cá nhân</option>
        </select>
        <label for="">Ngày kết thúc dự kiến</label>
        <input type="date" name="end_date" class="form-control" required>
        <textarea name="notes" class="box" placeholder="Ghi chú"></textarea>
        <input type="submit" name="add_task" value="Thêm nhiệm vụ" class="new-btn btn-primary">
    </form>
</section>

<section class="show-tasks">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_tasks = mysqli_query($conn, "SELECT * FROM `tasks` Where user_id = $user_id") or die('Query failed');
                if (mysqli_num_rows($select_tasks) > 0) {
                    while ($task = mysqli_fetch_assoc($select_tasks)) {
                ?>
                        <tr>
                            <td><?php echo $task['task_id']; ?></td>
                            <td><?php echo $task['title']; ?></td>
                            <td><?php echo $task['start_date']; ?></td>
                            <td><?php echo $task['end_date']; ?></td>
                            <td><?php echo $task['priority']; ?></td>
                            <td><?php echo $task['status']; ?></td>
                            <td>
                                <button type="button" class="fs-3 btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#shareTaskModal<?php echo $task['task_id']; ?>">Chia sẻ</button>
                                <button type="button" class="fs-4 btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateTaskModal<?php echo $task['task_id']; ?>">Sửa</button>
                                <a href="home.php?delete=<?php echo $task['task_id']; ?>" class="fs-3 btn-danger btn-sm" onclick="return confirm('Xóa nhiệm vụ này?');">Xóa</a>
                            </td>
                        </tr>

                        <!-- Modal chia sẻ -->
                        <div class="modal fade" id="shareTaskModal<?php echo $task['task_id']; ?>" tabindex="-1" aria-labelledby="shareTaskModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="shareTaskModalLabel">Chia sẻ nhiệm vụ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                            <label for="shared_with_user_id">Chọn người dùng:</label>
                                            <select name="shared_with_user_id" class="form-control" required>
                                                <option value="">Chọn...</option>
                                                <?php
                                                // Lấy danh sách người dùng khác để chia sẻ
                                                $users_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id != '$user_id'") or die('Query failed');
                                                while ($user = mysqli_fetch_assoc($users_query)) {
                                                    echo "<option value='{$user['user_id']}'>{$user['name']} ({$user['email']})</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="new-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <input type="submit" name="share_task" value="Chia sẻ" class="new-btn btn-info">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal chỉnh sửa -->
                        <div class="modal fade" id="updateTaskModal<?php echo $task['task_id']; ?>" tabindex="-1" aria-labelledby="updateTaskModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateTaskModalLabel">Cập nhật nhiệm vụ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                            <input type="text" name="title" class="form-control" value="<?php echo $task['title']; ?>" required>
                                            <textarea name="description" class="form-control"><?php echo $task['description']; ?></textarea>
                                            <input type="number" name="estimated_time" class="form-control" value="<?php echo $task['estimated_time']; ?>">
                                            <select name="priority" class="form-control" required>
                                                <option value="Cao" <?php echo $task['priority'] == 'Cao' ? 'selected' : ''; ?>>Cao</option>
                                                <option value="Trung bình" <?php echo $task['priority'] == 'Trung bình' ? 'selected' : ''; ?>>Trung bình</option>
                                                <option value="Thấp" <?php echo $task['priority'] == 'Thấp' ? 'selected' : ''; ?>>Thấp</option>
                                            </select>
                                            <select name="label" class="form-control">
                                                <option value="Học tập" <?php echo $task['label'] == 'Học tập' ? 'selected' : ''; ?>>Học tập</option>
                                                <option value="Công việc" <?php echo $task['label'] == 'Công việc' ? 'selected' : ''; ?>>Công việc</option>
                                                <option value="Cá nhân" <?php echo $task['label'] == 'Cá nhân' ? 'selected' : ''; ?>>Cá nhân</option>
                                            </select>
                                            <textarea name="notes" class="form-control"><?php echo $task['notes']; ?></textarea>
                                            <select name="status" class="form-control">
                                                <option value="Chưa bắt đầu" <?php echo $task['status'] == 'Chưa bắt đầu' ? 'selected' : ''; ?>>Chưa bắt đầu</option>
                                                <option value="Đang thực hiện" <?php echo $task['status'] == 'Đang thực hiện' ? 'selected' : ''; ?>>Đang thực hiện</option>
                                                <option value="Hoàn thành" <?php echo $task['status'] == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="new-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <input type="submit" name="update_task" value="Lưu thay đổi" class="new-btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center">Không có nhiệm vụ nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>
