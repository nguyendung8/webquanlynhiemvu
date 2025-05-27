<?php
   include 'config.php';
   session_start();

   $admin_id = $_SESSION['admin_id'];

   if(!isset($admin_id)){
      header('location:login.php');
   }

   // Thêm nhiệm vụ
   if(isset($_POST['add_task'])){
      $title = mysqli_real_escape_string($conn, $_POST['title']);
      $description = mysqli_real_escape_string($conn, $_POST['description']);
      $start_date = date('Y-m-d H:i:s');
      $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
      $estimated_time = mysqli_real_escape_string($conn, $_POST['estimated_time']);
      $priority = mysqli_real_escape_string($conn, $_POST['priority']);
      $label = mysqli_real_escape_string($conn, $_POST['label']);
      $notes = mysqli_real_escape_string($conn, $_POST['notes']);
      $status = 'Chưa bắt đầu';
      $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
      $acceptance_status = 'Chờ xác nhận';

      $insert_task = mysqli_query($conn, "INSERT INTO `tasks` 
         (title, description, start_date, end_date, estimated_time, priority, label, notes, status, user_id, acceptance_status) 
         VALUES 
         ('$title', '$description', '$start_date', '$end_date', '$estimated_time', '$priority', '$label', '$notes', '$status', '$user_id', '$acceptance_status')") 
         or die('Query failed');

      if($insert_task){
         $message[] = 'Thêm nhiệm vụ thành công!';
      }else{
         $message[] = 'Thêm nhiệm vụ thất bại!';
      }
   }

   // Xóa nhiệm vụ
   if(isset($_GET['delete'])){
      $task_id = mysqli_real_escape_string($conn, $_GET['delete']);
      $delete_task = mysqli_query($conn, "DELETE FROM `tasks` WHERE task_id = '$task_id'") or die('Query failed');
      if($delete_task){
         $message[] = 'Xóa nhiệm vụ thành công!';
      }else{
         $message[] = 'Xóa nhiệm vụ thất bại!';
      }
   }

   // Cập nhật nhiệm vụ
   if(isset($_POST['update_task'])){
      $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
      $title = mysqli_real_escape_string($conn, $_POST['title']);
      $description = mysqli_real_escape_string($conn, $_POST['description']);
      $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
      $estimated_time = mysqli_real_escape_string($conn, $_POST['estimated_time']);
      $priority = mysqli_real_escape_string($conn, $_POST['priority']);
      $label = mysqli_real_escape_string($conn, $_POST['label']);
      $notes = mysqli_real_escape_string($conn, $_POST['notes']);
      $status = mysqli_real_escape_string($conn, $_POST['status']);
      $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

      $update_task = mysqli_query($conn, "UPDATE `tasks` SET 
         title = '$title', description = '$description', end_date = '$end_date', estimated_time = '$estimated_time', 
         priority = '$priority', label = '$label', notes = '$notes', status = '$status', 
         user_id = '$user_id' WHERE task_id = '$task_id'") or die('Query failed');

      if($update_task){
         $message[] = 'Cập nhật nhiệm vụ thành công!';
      }else{
         $message[] = 'Cập nhật nhiệm vụ thất bại!';
      }
   }

   // Lấy danh sách nhiệm vụ
   $select_tasks = mysqli_query($conn, "SELECT tasks.*, users.name as user_name FROM `tasks` 
      JOIN users ON tasks.user_id = users.user_id") or die('Query failed');

   // Thêm đoạn code này vào đầu file, cùng chỗ với các xử lý khác
   if(isset($_GET['approve'])){
      $task_id = $_GET['approve'];
      $update_task = mysqli_query($conn, "UPDATE `tasks` SET is_done = 2 WHERE task_id = '$task_id'") or die('Query failed');
      if($update_task){
         $message[] = 'Đã phê duyệt nhiệm vụ thành công!';
      }
      header('location:admin_tasks.php');
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý nhiệm vụ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
      .dashboard {
         padding: 20px;
         background: #f8f9fa;
      }

      .task-form {
         background: white;
         padding: 25px;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.1);
         margin-bottom: 30px;
      }

      .form-group {
         margin-bottom: 20px;
      }

      .form-label {
         font-weight: 600;
         margin-bottom: 8px;
         display: block;
         color: #333;
         text-align: left;
         font-size: 18px;
      }

      .form-control {
         width: 100%;
         padding: 10px;
         border: 1px solid #ddd;
         border-radius: 5px;
         font-size: 14px;
      }

      .form-control:focus {
         border-color: #4CAF50;
         box-shadow: 0 0 5px rgba(76,175,80,0.2);
      }

      .btn-submit {
         background: #4CAF50;
         color: white;
         padding: 12px 25px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         font-size: 16px;
         transition: background 0.3s;
      }

      .btn-submit:hover {
         background: #45a049;
      }

      .task-table {
         background: white;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.1);
         overflow: hidden;
      }

      .task-table table {
         width: 100%;
         border-collapse: collapse;
      }

      .task-table th {
         background: #f1f1f1;
         padding: 15px;
         text-align: left;
         font-weight: 600;
         color: #333;
         font-size: 18px;
      }

      .task-table td {
         padding: 15px;
         border-bottom: 1px solid #eee;
         font-size: 16px;
      }

      .status-badge {
         padding: 5px 10px;
         border-radius: 15px;
         font-size: 12px;
         font-weight: 500;
      }

      .status-pending {
         background: #fff3cd;
         color: #856404;
      }

      .status-accepted {
         background: #d4edda;
         color: #155724;
      }

      .status-rejected {
         background: #f8d7da;
         color: #721c24;
      }

      .action-btn {
         padding: 6px 12px;
         border-radius: 4px;
         font-size: 13px;
         margin-right: 5px;
         border: none;
         cursor: pointer;
      }

      .btn-edit {
         background: #007bff;
         color: white;
      }

      .btn-delete {
         background: #dc3545;
         color: white;
      }

      .modal-content {
         border-radius: 10px;
      }

      .modal-header {
         background: #f8f9fa;
         border-bottom: 1px solid #eee;
      }

      .modal-title {
         font-weight: 600;
         color: #333;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="dashboard">
   <div class="task-form add-products">
      <h2 class="mb-4 title">Thêm nhiệm vụ mới</h2>
      <form action="" method="post">
         <div class="form-group">
            <label class="form-label">Tiêu đề nhiệm vụ</label>
            <input type="text" name="title" class="form-control" required>
         </div>

         <div class="form-group">
            <label class="form-label">Mô tả nhiệm vụ</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
         </div>

         <div class="row">
            <div class="col-md-6">
               <div class="form-group">
                  <label class="form-label">Thời gian ước tính (giờ)</label>
                  <input type="number" name="estimated_time" class="form-control">
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                  <label class="form-label">Ngày kết thúc</label>
                  <input type="date" name="end_date" class="form-control" required>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-4">
               <div class="form-group">
                  <label class="form-label">Mức ưu tiên</label>
                  <select name="priority" class="form-control" required>
                     <option value="Cao">Cao</option>
                     <option value="Trung bình">Trung bình</option>
                     <option value="Thấp">Thấp</option>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label class="form-label">Nhãn</label>
                  <select name="label" class="form-control" required>
                     <option value="Học tập">Học tập</option>
                     <option value="Công việc">Công việc</option>
                     <option value="Cá nhân">Cá nhân</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="row">
          <div class="col-md-10">
               <div class="form-group">
                  <label class="form-label">Người thực hiện</label>
                  <select name="user_id" class="form-control" required>
                     <?php
                        $users_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('Query failed');
                        while($user = mysqli_fetch_assoc($users_query)){
                           echo "<option value='{$user['user_id']}'>{$user['name']} ({$user['email']})</option>";
                        }
                     ?>
                  </select>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="form-label">Ghi chú</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
         </div>

         <button type="submit" name="add_task" class="btn-submit">
            <i class="fas fa-plus"></i> Thêm nhiệm vụ
         </button>
      </form>
   </div>

   <div class="task-table">
      <table>
         <thead>
            <tr>
               <th>ID</th>
               <th>Tiêu đề</th>
               <th>Người thực hiện</th>
               <th>Ưu tiên</th>
               <th>Trạng thái</th>
               <th>Trạng thái chấp nhận</th>
               <th>Trạng thái nộp</th>
               <th>Thao tác</th>
            </tr>
         </thead>
         <tbody>
            <?php
            $select_tasks = mysqli_query($conn, "SELECT tasks.*, users.name as user_name FROM `tasks` 
               JOIN users ON tasks.user_id = users.user_id") or die('Query failed');
            if(mysqli_num_rows($select_tasks) > 0){
               while($task = mysqli_fetch_assoc($select_tasks)){
            ?>
               <tr>
                  <td><?php echo $task['task_id']; ?></td>
                  <td><?php echo $task['title']; ?></td>
                  <td><?php echo $task['user_name']; ?></td>
                  <td>
                     <span class="status-badge <?php 
                        echo $task['priority'] == 'Cao' ? 'status-high' : 
                        ($task['priority'] == 'Trung bình' ? 'status-medium' : 'status-low'); 
                     ?>">
                        <?php echo $task['priority']; ?>
                     </span>
                  </td>
                  <td>
                     <span class="status-badge <?php 
                        echo $task['status'] == 'Hoàn thành' ? 'status-accepted' : 
                        ($task['status'] == 'Đang thực hiện' ? 'status-pending' : 'status-rejected'); 
                     ?>">
                        <?php echo $task['status']; ?>
                     </span>
                  </td>
                  <td>
                     <span class="status-badge <?php 
                        echo $task['acceptance_status'] == 'Đã chấp nhận' ? 'status-accepted' : 
                        ($task['acceptance_status'] == 'Chờ xác nhận' ? 'status-pending' : 'status-rejected'); 
                     ?>">
                        <?php echo $task['acceptance_status']; ?>
                     </span>
                  </td>
                  <td>
                     <?php 
                        if($task['is_done'] == 0) {
                           echo '<span class="badge bg-secondary">Chưa nộp</span>';
                        } else if($task['is_done'] == 1 || $task['is_done'] == 2) {
                           echo '<span class="badge bg-warning">Chờ duyệt</span>';
                           if($task['file']) {
                              $file_ext = pathinfo($task['file'], PATHINFO_EXTENSION);
                              if(in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
                                 echo '<button type="button" class="action-btn btn-info btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#fileModal'.$task['task_id'].'">
                                    <i class="fas fa-image"></i> Xem ảnh
                                 </button>';
                              } else {
                                 echo '<a href="uploads/'.$task['file'].'" target="_blank" class="action-btn btn-info btn-sm ms-2">
                                    <i class="fas fa-file-pdf"></i> Xem File
                                 </a>';
                              }
                           }
                        } else {
                           echo '<span class="badge bg-success">Đã duyệt</span>';
                        }
                     ?>
                  </td>
                  <td>
                     <button type="button" class="action-btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateTaskModal<?php echo $task['task_id']; ?>">
                        <i class="fas fa-edit"></i>
                     </button>
                     <a href="admin_tasks.php?delete=<?php echo $task['task_id']; ?>" class="action-btn btn-delete" onclick="return confirm('Xóa nhiệm vụ này?');">
                        <i class="fas fa-trash"></i>
                     </a>
                     <?php if($task['is_done'] == 1): ?>
                     <a href="admin_tasks.php?approve=<?php echo $task['task_id']; ?>" class="action-btn btn-success btn-sm">
                        <i class="fas fa-check"></i> Duyệt
                     </a>
                     <?php endif; ?>
                  </td>
               </tr>

               <!-- Modal chỉnh sửa -->
               <div class="modal fade" id="updateTaskModal<?php echo $task['task_id']; ?>" data-bs-backdrop="static" tabindex="-1" aria-labelledby="updateTaskModalLabel<?php echo $task['task_id']; ?>" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="updateTaskModalLabel<?php echo $task['task_id']; ?>">Cập nhật nhiệm vụ</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="" method="post">
                           <div class="modal-body">
                              <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                              
                              <div class="mb-3">
                                 <label for="title" class="form-label">Tiêu đề</label>
                                 <input type="text" class="form-control" id="title" name="title" value="<?php echo $task['title']; ?>" required>
                              </div>

                              <div class="mb-3">
                                 <label for="description" class="form-label">Mô tả</label>
                                 <textarea class="form-control" id="description" name="description" rows="3"><?php echo $task['description']; ?></textarea>
                              </div>

                              <div class="row mb-3">
                                 <div class="col-md-6">
                                    <label for="estimated_time" class="form-label">Thời gian ước tính (giờ)</label>
                                    <input type="number" class="form-control" id="estimated_time" name="estimated_time" value="<?php echo $task['estimated_time']; ?>">
                                 </div>
                                 <div class="col-md-6">
                                    <label for="end_date" class="form-label">Ngày kết thúc</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $task['end_date']; ?>" required>
                                 </div>
                              </div>

                              <div class="row mb-3">
                                 <div class="col-md-4">
                                    <label for="priority" class="form-label">Mức ưu tiên</label>
                                    <select class="form-select" id="priority" name="priority" required>
                                       <option value="Cao" <?php echo $task['priority'] == 'Cao' ? 'selected' : ''; ?>>Cao</option>
                                       <option value="Trung bình" <?php echo $task['priority'] == 'Trung bình' ? 'selected' : ''; ?>>Trung bình</option>
                                       <option value="Thấp" <?php echo $task['priority'] == 'Thấp' ? 'selected' : ''; ?>>Thấp</option>
                                    </select>
                                 </div>
                                 <div class="col-md-4">
                                    <label for="label" class="form-label">Nhãn</label>
                                    <select class="form-select" id="label" name="label" required>
                                       <option value="Học tập" <?php echo $task['label'] == 'Học tập' ? 'selected' : ''; ?>>Học tập</option>
                                       <option value="Công việc" <?php echo $task['label'] == 'Công việc' ? 'selected' : ''; ?>>Công việc</option>
                                       <option value="Cá nhân" <?php echo $task['label'] == 'Cá nhân' ? 'selected' : ''; ?>>Cá nhân</option>
                                    </select>
                                 </div>
                                 <div class="col-md-4">
                                    <label for="user_id" class="form-label">Người thực hiện</label>
                                    <select class="form-select" id="user_id" name="user_id" required>
                                       <?php
                                          $users_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('Query failed');
                                          while($user = mysqli_fetch_assoc($users_query)){
                                             $selected = ($user['user_id'] == $task['user_id']) ? 'selected' : '';
                                             echo "<option value='{$user['user_id']}' {$selected}>{$user['name']} ({$user['email']})</option>";
                                          }
                                       ?>
                                    </select>
                                 </div>
                              </div>
                              

                              <div class="row mb-3">
                                 <div class="col-md-6">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status" required>
                                       <option value="Chưa bắt đầu" <?php echo $task['status'] == 'Chưa bắt đầu' ? 'selected' : ''; ?>>Chưa bắt đầu</option>
                                       <option value="Đang thực hiện" <?php echo $task['status'] == 'Đang thực hiện' ? 'selected' : ''; ?>>Đang thực hiện</option>
                                       <option value="Hoàn thành" <?php echo $task['status'] == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                                    </select>
                                 </div>
                                 <!-- <div class="col-md-6">
                                    <label for="acceptance_status" class="form-label">Trạng thái chấp nhận</label>
                                    <select class="form-select" id="acceptance_status" name="acceptance_status" required>
                                       <option value="Đã chấp nhận" <?php echo $task['acceptance_status'] == 'Đã chấp nhận' ? 'selected' : ''; ?>>Đã chấp nhận</option>
                                       <option value="Chờ xác nhận" <?php echo $task['acceptance_status'] == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                       <option value="Từ chối" <?php echo $task['acceptance_status'] == 'Từ chối' ? 'selected' : ''; ?>>Từ chối</option>
                                    </select>
                                 </div> -->
                              </div>

                              <div class="mb-3">
                                 <label for="notes" class="form-label">Ghi chú</label>
                                 <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $task['notes']; ?></textarea>
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="action-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                              <button type="submit" name="update_task" class="action-btn btn-primary">
                                 <i class="fas fa-save"></i> Cập nhật
                              </button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>

               <!-- Modal xem ảnh -->
               <?php if($task['is_done'] == 1 && $task['file'] && in_array($file_ext, ['jpg', 'jpeg', 'png'])): ?>
               <div class="modal fade" id="fileModal<?php echo $task['task_id']; ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title">File nộp của nhiệm vụ: <?php echo $task['title']; ?></h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                           <img src="uploads/<?php echo $task['file']; ?>" class="img-fluid">
                        </div>
                     </div>
                  </div>
               </div>
               <?php endif; ?>
            <?php
               }
            }else{
               echo '<tr><td colspan="9" class="text-center">Không có nhiệm vụ nào!</td></tr>';
            }
            ?>
         </tbody>
      </table>
   </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/admin_script.js"></script>

</body>
</html>