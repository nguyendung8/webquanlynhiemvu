<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
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

// Thêm vào phần xử lý PHP ở đầu file
if(isset($_POST['submit_task'])){
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    
    $file = $_FILES['task_file'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Kiểm tra định dạng file
    $allowed = array('pdf', 'jpg', 'jpeg', 'png');
    if(in_array($file_ext, $allowed)){
        $new_file_name = uniqid() . '.' . $file_ext;
        move_uploaded_file($file_tmp, "uploads/" . $new_file_name);
        
        // Cập nhật DB
        mysqli_query($conn, "UPDATE tasks SET 
            file = '$new_file_name',
            is_done = 1 
            WHERE task_id = '$task_id'") or die('query failed');
            
        $message[] = 'Nộp bài thành công!';
    } else {
        $message[] = 'Chỉ chấp nhận file PDF hoặc ảnh!';
    }
}

// Thêm vào đầu file PHP
if(isset($_POST['accept_task'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    mysqli_query($conn, "UPDATE tasks SET acceptance_status = 'Đã chấp nhận' WHERE task_id = '$task_id'") or die('query failed');
    $message[] = 'Đã chấp nhận nhiệm vụ!';
}

if(isset($_POST['reject_task'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    mysqli_query($conn, "UPDATE tasks SET acceptance_status = 'Từ chối' WHERE task_id = '$task_id'") or die('query failed');
    $message[] = 'Đã từ chối nhiệm vụ!';
}

// Xử lý chấp nhận lời mời cộng tác
if(isset($_POST['accept_collab'])) {
    $collab_id = mysqli_real_escape_string($conn, $_POST['collab_id']);
    mysqli_query($conn, "UPDATE collaborations SET is_accept = '1' WHERE collaboration_id = '$collab_id'") or die('query failed');
    $message[] = 'Đã chấp nhận lời mời cộng tác!';
}

// Xử lý từ chối lời mời cộng tác  
if(isset($_POST['reject_collab'])) {
    $collab_id = mysqli_real_escape_string($conn, $_POST['collab_id']);
    mysqli_query($conn, "UPDATE collaborations SET is_accept = '2' WHERE collaboration_id = '$collab_id'") or die('query failed');
    $message[] = 'Đã từ chối lời mời cộng tác!';
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

      .action-btn {
         padding: 6px 12px !important;
         border-radius: 4px !important;
         font-size: 13px !important;
         margin-right: 5px !important;
         border: none !important;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="show-tasks">
    <div class="container">
        <h1 class="title mb-4">Danh sách nhiệm vụ</h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Trạng thái xác nhận</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_tasks = mysqli_query($conn, "SELECT * FROM `tasks` WHERE user_id = $user_id") or die('Query failed');
                if(mysqli_num_rows($select_tasks) > 0){
                    while($task = mysqli_fetch_assoc($select_tasks)){
                ?>
                <tr>
                    <td><?php echo $task['task_id']; ?></td>
                    <td><?php echo $task['title']; ?></td>
                    <td><?php echo $task['start_date']; ?></td>
                    <td><?php echo $task['end_date']; ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $task['priority'] == 'Cao' ? 'danger' : 
                            ($task['priority'] == 'Trung bình' ? 'warning' : 'info'); 
                        ?>"><?php echo $task['priority']; ?></span>
                    </td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $task['status'] == 'Hoàn thành' ? 'success' : 
                            ($task['status'] == 'Đang thực hiện' ? 'primary' : 'secondary'); 
                        ?>"><?php echo $task['status']; ?></span>
                    </td>
                    <td>
                        <?php if($task['acceptance_status'] == 'Chờ xác nhận'): ?>
                            <div class="d-flex gap-1">
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                    <button type="submit" name="accept_task" class="action-btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Nhận
                                    </button>
                                </form>
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                    <button type="submit" name="reject_task" class="action-btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            </div>
                        <?php elseif($task['acceptance_status'] == 'Từ chối'): ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle"></i> Từ chối
                            </span>
                        <?php else: ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Đã chấp nhận
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Nút xem chi tiết -->
                        <button type="button" class="action-btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $task['task_id']; ?>">
                            <i class="fas fa-eye"></i>
                        </button>

                        <!-- Nút cộng tác -->
                        <?php if($task['status'] == 'Đang thực hiện' && $task['acceptance_status'] == 'Đã chấp nhận'): ?>
                            <button type="button" class="action-btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#shareTaskModal<?php echo $task['task_id']; ?>">
                                <i class="fas fa-share-alt"></i> Cộng tác
                            </button>
                        <?php endif; ?>

                        <!-- Nút nộp bài nếu task đã hoàn thành -->
                        <?php if($task['status'] == 'Hoàn thành' && $task['is_done'] == 0): ?>
                            <button type="button" class="action-btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#submitModal<?php echo $task['task_id']; ?>">
                                <i class="fas fa-check"></i> Nộp bài
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modal Xem chi tiết -->
                <div class="modal fade" id="detailModal<?php echo $task['task_id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-size: 25px !important;">Chi tiết nhiệm vụ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="font-size: 18px;">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Tiêu đề:</strong> <?php echo $task['title']; ?></p>
                                        <p><strong>Mô tả:</strong> <?php echo $task['description']; ?></p>
                                        <p><strong>Thời gian ước tính:</strong> <?php echo $task['estimated_time']; ?> giờ</p>
                                        <p><strong>Nhãn:</strong> <?php echo $task['label']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Ngày bắt đầu:</strong> <?php echo $task['start_date']; ?></p>
                                        <p><strong>Ngày kết thúc:</strong> <?php echo $task['end_date']; ?></p>
                                        <p><strong>Ưu tiên:</strong> <?php echo $task['priority']; ?></p>
                                        <p><strong>Ghi chú:</strong> <?php echo $task['notes']; ?></p>
                                    </div>
                                </div>
                                <?php if($task['file']): ?>
                                <div class="mt-3">
                                    <h6>File đã nộp:</h6>
                                    <?php 
                                        $file_ext = pathinfo($task['file'], PATHINFO_EXTENSION);
                                        if(in_array($file_ext, ['jpg', 'jpeg', 'png'])):
                                    ?>
                                        <img src="uploads/<?php echo $task['file']; ?>" class="img-fluid">
                                    <?php else: ?>
                                        <a href="uploads/<?php echo $task['file']; ?>" target="_blank" class="btn btn-primary">
                                            <i class="fas fa-file-pdf"></i> Xem PDF
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Nộp bài -->
                <div class="modal fade" id="submitModal<?php echo $task['task_id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Nộp bài</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Chọn file (PDF hoặc ảnh)</label>
                                        <input type="file" class="form-control" name="task_file" accept=".pdf,.jpg,.jpeg,.png" required style="font-size: 15px;">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="action-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" name="submit_task" class="action-btn btn-primary">Nộp bài</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Thêm Modal cộng tác -->
                <div class="modal fade" id="shareTaskModal<?php echo $task['task_id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Chia sẻ nhiệm vụ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="" method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Chọn người dùng để cộng tác:</label>
                                        <select name="shared_with_user_id" class="form-select" required>
                                            <option value="">Chọn người dùng...</option>
                                            <?php
                                            $users_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id != '$user_id' and user_type = 'user'") or die('Query failed');
                                            while($user = mysqli_fetch_assoc($users_query)){
                                                echo "<option value='{$user['user_id']}'>{$user['name']} ({$user['email']})</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="action-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" name="share_task" class="action-btn btn-primary">
                                        <i class="fas fa-share-alt"></i> Chia sẻ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">Không có nhiệm vụ nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<section class="show-tasks mt-5">
    <div class="container">
        <h1 class="title mb-4">Danh sách nhiệm vụ được mời cộng tác</h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Người chia sẻ</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Lấy danh sách nhiệm vụ được chia sẻ
                $select_shared_tasks = mysqli_query($conn, "
                    SELECT t.*, c.collaboration_id, c.is_accept, u.name as shared_by_name 
                    FROM tasks t 
                    JOIN collaborations c ON t.task_id = c.task_id 
                    JOIN users u ON t.user_id = u.user_id
                    WHERE c.shared_with_user_id = $user_id
                ") or die('Query failed');

                if(mysqli_num_rows($select_shared_tasks) > 0){
                    while($shared_task = mysqli_fetch_assoc($select_shared_tasks)){
                ?>
                <tr>
                    <td><?php echo $shared_task['task_id']; ?></td>
                    <td><?php echo $shared_task['title']; ?></td>
                    <td><?php echo $shared_task['shared_by_name']; ?></td>
                    <td><?php echo $shared_task['start_date']; ?></td>
                    <td><?php echo $shared_task['end_date']; ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $shared_task['priority'] == 'Cao' ? 'danger' : 
                            ($shared_task['priority'] == 'Trung bình' ? 'warning' : 'info'); 
                        ?>"><?php echo $shared_task['priority']; ?></span>
                    </td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $shared_task['status'] == 'Hoàn thành' ? 'success' : 
                            ($shared_task['status'] == 'Đang thực hiện' ? 'primary' : 'secondary'); 
                        ?>"><?php echo $shared_task['status']; ?></span>
                    </td>
                    <td>
                        <?php if($shared_task['is_accept'] == '0'): ?>
                            <div class="d-flex gap-1">
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="collab_id" value="<?php echo $shared_task['collaboration_id']; ?>">
                                    <button type="submit" name="accept_collab" class="action-btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Đồng ý
                                    </button>
                                </form>
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="collab_id" value="<?php echo $shared_task['collaboration_id']; ?>">
                                    <button type="submit" name="reject_collab" class="action-btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            </div>
                        <?php elseif($shared_task['is_accept'] == '1'): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Đã chấp nhận
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle"></i> Đã từ chối
                            </span>
                        <?php endif; ?>

                        <!-- Thêm nút xem chi tiết -->
                        <button type="button" class="action-btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModalShared<?php echo $shared_task['task_id']; ?>">
                            <i class="fas fa-eye"></i>
                        </button>

                        <!-- Modal Xem chi tiết cho nhiệm vụ được chia sẻ -->
                        <div class="modal fade" id="detailModalShared<?php echo $shared_task['task_id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="font-size: 25px !important;">Chi tiết nhiệm vụ được chia sẻ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" style="font-size: 18px;">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Tiêu đề:</strong> <?php echo $shared_task['title']; ?></p>
                                                <p><strong>Mô tả:</strong> <?php echo $shared_task['description']; ?></p>
                                                <p><strong>Thời gian ước tính:</strong> <?php echo $shared_task['estimated_time']; ?> giờ</p>
                                                <p><strong>Nhãn:</strong> <?php echo $shared_task['label']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Ngày bắt đầu:</strong> <?php echo $shared_task['start_date']; ?></p>
                                                <p><strong>Ngày kết thúc:</strong> <?php echo $shared_task['end_date']; ?></p>
                                                <p><strong>Ưu tiên:</strong> <?php echo $shared_task['priority']; ?></p>
                                                <p><strong>Ghi chú:</strong> <?php echo $shared_task['notes']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">Không có nhiệm vụ được chia sẻ nào!</td></tr>';
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
